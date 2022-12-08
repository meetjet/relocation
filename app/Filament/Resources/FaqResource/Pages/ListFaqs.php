<?php

namespace App\Filament\Resources\FaqResource\Pages;

use App\Enums\FaqStatus;
use App\Facades\Countries;
use App\Filament\Resources\FaqResource;
use App\Traits\PageListHelpers;
use Exception;
use Filament\Forms\Components;
use Filament\Pages;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Table;
use App\Filament\Actions;
use Filament\Tables\Columns;
use Filament\Tables\Filters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ListFaqs extends ListRecords
{
    use PageListHelpers;

    protected static string $resource = FaqResource::class;

    /**
     * @return array
     * @throws Exception
     */
    protected function getActions(): array
    {
        return [
            Pages\Actions\CreateAction::make(),
        ];
    }

    /**
     * @param Table $table
     * @return Table
     */
    public function table(Table $table): Table
    {
        return $table->defaultSort('created_at', 'desc');
    }

    /**
     * @return array
     */
    protected function getTableColumns(): array
    {
        return [
            Columns\TextColumn::make('original')
                ->getStateUsing(fn($record): ?string => $record->title ?: $record->original)
                ->label(__('Question'))
                ->description(fn($record) => (!$record->deleted_at && $record->status === FaqStatus::PUBLISHED && $record->visibility && $record->slug)
                    ? static::externalLink(addSubdomainToUrl(route('faqs.show', $record->slug), $record->country), Str::lower(__('Link')))
                    : null)
                ->limit(200)
                ->wrap()
                ->searchable(query: function (Builder $query, string $search): Builder {
                    return $query
                        ->where('original', 'ilike', "%{$search}%")
                        ->orWhere('title', 'ilike', "%{$search}%")
                        ->orWhere('question', 'ilike', "%{$search}%");
                })
                ->color(fn($record): ?string => is_null($record->deleted_at) ? null : "danger")
                ->sortable(),

            Columns\TextColumn::make('country')
                ->label(__('Country'))
                ->enum(Countries::asSelectArray())
                ->sortable()
                ->toggleable(),

            Columns\SpatieTagsColumn::make('tags')
                ->label(__('Tags'))
                ->type("faqs")
                ->toggleable(),

            Columns\TextColumn::make('created_at')
                ->label(__('Created at'))
                ->date("j M Y")
                ->sortable()
                ->toggleable(),

            Columns\BadgeColumn::make('status')
                ->label(__('Status'))
                ->enum(FaqStatus::asSelectArray())
                ->sortable()
                ->colors([
                    'secondary' => FaqStatus::CREATED,
                    'success' => FaqStatus::PUBLISHED,
                    'danger' => FaqStatus::REJECTED,
                ])
                ->toggleable(),

            Columns\IconColumn::make('visibility')
                ->label(__('Visibility'))
                ->boolean()
                ->sortable()
                ->toggleable(),
        ];
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function getTableActions(): array
    {
        return [
            Actions\Tables\EditAction::make()
                ->label("")
                ->tooltip(__('Edit')),
            Actions\Tables\DeleteAction::make()
                ->label("")
                ->tooltip(__('Delete')),
            Actions\Tables\RestoreAction::make()
                ->label("")
                ->tooltip(__('Restore')),
        ];
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function getTableBulkActions(): array
    {
        return [
            Actions\Tables\DeleteBulkAction::make(),
            Actions\Tables\RestoreBulkAction::make(),
            Actions\Tables\ForceDeleteBulkAction::make(),
        ];
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function getTableFilters(): array
    {
        return [
            Filters\TrashedFilter::make(),

            Filters\Filter::make('country')
                ->form([
                    Components\Select::make('country')
                        ->label(__('Country'))
                        ->placeholder("-")
                        ->options(array_merge(Countries::asSelectArray(), ["no_country" => __("No")])),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query->when(
                        $data['country'],
                        fn(Builder $query, $country): Builder => $country === "no_country"
                            ? $query->whereNull('country')
                            : $query->where('country', $country),
                    );
                })
                ->indicateUsing(function (array $data): ?string {
                    if ($data['country']) {
                        $country = $data['country'] === "no_country"
                            ? __("No")
                            : Countries::getDescription($data['country']);
                        return __('Country') . ' "' . $country . '"';
                    }

                    return null;
                }),

            Filters\Filter::make('status')
                ->form([
                    Components\Select::make('status')
                        ->label(__('Status'))
                        ->placeholder("-")
                        ->options(FaqStatus::asSelectArray()),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query->when(
                        $data['status'],
                        fn(Builder $query, $status): Builder => $query->where('status', $status),
                    );
                })
                ->indicateUsing(function (array $data): ?string {
                    if ($data['status']) {
                        return __('Status') . ' "' . FaqStatus::getDescription($data['status']) . '"';
                    }

                    return null;
                }),

            Filters\Filter::make('visibility')
                ->form([
                    Components\Select::make('visibility')
                        ->label(__('Visibility'))
                        ->placeholder("-")
                        ->options([
                            'true' => __("Yes"),
                            'false' => __("No"),
                        ]),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query->when(
                        $data['visibility'],
                        fn(Builder $query, $visibility): Builder => $query->where('visibility', json_decode($visibility)),
                    );
                })
                ->indicateUsing(function (array $data): ?string {
                    if ($data['visibility']) {
                        return __('Visibility') . ' "' . (json_decode($data['visibility']) ? __("Yes") : __("No")) . '"';
                    }

                    return null;
                }),

            Filters\Filter::make('created_at')
                ->form([
                    Components\DatePicker::make('published_from')
                        ->label(__('Created from'))
                        ->displayFormat("j M Y"),
                    Components\DatePicker::make('published_until')
                        ->label(__('Created until'))
                        ->displayFormat("j M Y"),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['published_from'],
                            fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                        )
                        ->when(
                            $data['published_until'],
                            fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                        );
                })
                ->indicateUsing(function (array $data): array {
                    $indicators = [];
                    if ($data['published_from'] ?? null) {
                        $indicators['published_from'] = __('Created from') . ' ' . Carbon::parse($data['published_from'])
                                ->translatedFormat("j M Y");
                    }
                    if ($data['published_until'] ?? null) {
                        $indicators['published_until'] = __('Created until') . ' ' . Carbon::parse($data['published_until'])
                                ->translatedFormat("j M Y");
                    }

                    return $indicators;
                }),
        ];
    }

    /**
     * @return string
     */
    protected function getTableFiltersFormWidth(): string
    {
        return 'md';
    }
}
