<?php

namespace App\Filament\Resources\ListingItemResource\Pages;

use App\Enums\Countries;
use App\Enums\ListingItemStatus;
use App\Filament\Resources\ListingItemResource;
use App\Models\ListingCategory;
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

class ListListingItems extends ListRecords
{
    use PageListHelpers;

    protected static string $resource = ListingItemResource::class;

    /**
     * @return array
     * @throws Exception
     */
    protected function getActions(): array
    {
        return [
//            Pages\Actions\CreateAction::make(),
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
            Columns\TextColumn::make('title')
                ->label(__('Title'))
                ->description(fn($record) => (!$record->deleted_at && $record->category && $record->category->slug && $record->slug)
                    ? static::externalLink(route("dashboard"), $record->slug) // TODO: replace with correct route
                    : null)
                ->limit(200)
                ->wrap()
                ->searchable()
                ->sortable()
                ->color(fn($record): ?string => is_null($record->deleted_at) ?: "danger"),

            Columns\SpatieTagsColumn::make('tags')
                ->label(__('Tags'))
                ->type("listing-items")
                ->toggleable(),

            Columns\TextColumn::make('country')
                ->label(__('Country'))
                ->enum(Countries::asSelectArray())
                ->sortable()
                ->toggleable(),

            Columns\TextColumn::make('category.title')
                ->label(__('Category'))
                ->wrap()
                ->sortable()
                ->toggleable(),

            Columns\TextColumn::make('created_at')
                ->label(__('Created'))
                ->date("j M Y")
                ->sortable()
                ->toggleable(),

            Columns\BadgeColumn::make('status')
                ->label(__('Status'))
                ->enum(ListingItemStatus::asSelectArray())
                ->sortable()
                ->colors([
                    'secondary' => ListingItemStatus::CREATED,
                    'success' => ListingItemStatus::PUBLISHED,
                    'danger' => ListingItemStatus::REJECTED,
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
                        ->options(collect(Countries::asSelectArray())->put('no_country', __("No"))),
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

            Filters\Filter::make('category_id')
                ->form([
                    Components\Select::make('category_id')
                        ->label(__('Category'))
                        ->placeholder("-")
                        ->options(ListingCategory::all()->pluck('title', 'id')->put('no_category', __("No"))),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query->when(
                        $data['category_id'],
                        fn(Builder $query, $category_id): Builder => $category_id === "no_category"
                            ? $query->whereNull('category_id')
                            : $query->where('category_id', $category_id),
                    );
                })
                ->indicateUsing(function (array $data): ?string {
                    if ($data['category_id']) {
                        $category = $data['category_id'] === "no_category"
                            ? __("No")
                            : ListingCategory::find($data['category_id'])->title;
                        return __('Category') . ' "' . $category . '"';
                    }

                    return null;
                }),

            Filters\Filter::make('status')
                ->form([
                    Components\Select::make('status')
                        ->label(__('Status'))
                        ->placeholder("-")
                        ->options(ListingItemStatus::asSelectArray()),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query->when(
                        $data['status'],
                        fn(Builder $query, $status): Builder => $query->where('status', $status),
                    );
                })
                ->indicateUsing(function (array $data): ?string {
                    if ($data['status']) {
                        return __('Status') . ' "' . ListingItemStatus::getDescription($data['status']) . '"';
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
                        ->displayFormat("j M Y")
                        ->maxDate(Carbon::today())
                        ->placeholder(fn($state): string => Carbon::parse(now())
                            ->setTimezone(config('app.timezone'))
                            ->translatedFormat("j M Y")),
                    Components\DatePicker::make('published_until')
                        ->label(__('Created until'))
                        ->displayFormat("j M Y")
                        ->maxDate(Carbon::today())
                        ->placeholder(fn($state): string => Carbon::parse(now())
                            ->setTimezone(config('app.timezone'))
                            ->translatedFormat("j M Y")),
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
                                ->setTimezone(config('app.timezone'))
                                ->translatedFormat("j M Y");
                    }
                    if ($data['published_until'] ?? null) {
                        $indicators['published_until'] = __('Created until') . ' ' . Carbon::parse($data['published_until'])
                                ->setTimezone(config('app.timezone'))
                                ->translatedFormat("j M Y");
                    }

                    return $indicators;
                }),
        ];
    }
}
