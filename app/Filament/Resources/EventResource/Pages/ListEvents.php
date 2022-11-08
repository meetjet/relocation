<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Enums\EventStatus;
use App\Facades\Cities;
use App\Facades\Countries;
use App\Filament\Resources\EventResource;
use App\Traits\PageListHelpers;
use Closure;
use Exception;
use Filament\Forms\Components;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Table;
use App\Filament\Actions;
use Filament\Tables\Columns;
use Filament\Tables\Filters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ListEvents extends ListRecords
{
    use PageListHelpers;

    protected static string $resource = EventResource::class;

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
                ->description(fn($record) => (!$record->deleted_at && $record->slug)
                    ? static::externalLink(route('dashboard'), $record->slug)
                    : null)
                ->limit(200)
                ->wrap()
                ->searchable()
                ->sortable()
                ->color(fn($record): ?string => is_null($record->deleted_at) ?: "danger"),

            Columns\SpatieTagsColumn::make('tags')
                ->label(__('Tags'))
                ->type("events")
                ->toggleable(),

            Columns\TextColumn::make('formatted_price')
                ->label(__('Price'))
                ->toggleable(),

            Columns\TextColumn::make('country')
                ->label(__('Country'))
                ->enum(Countries::asSelectArray())
                ->toggleable(),

            Columns\TextColumn::make('city')
                ->label(__('City'))
                ->getStateUsing(fn($record): string => Cities::getDescription($record->country, $record->city))
                ->toggleable(),

            Columns\TextColumn::make('created_at')
                ->label(__('Created'))
                ->date("j M Y")
                ->sortable()
                ->toggleable(),

            Columns\BadgeColumn::make('status')
                ->label(__('Status'))
                ->enum(EventStatus::asSelectArray())
                ->sortable()
                ->colors([
                    'secondary' => EventStatus::CREATED,
                    'success' => EventStatus::PUBLISHED,
                    'danger' => EventStatus::REJECTED,
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

            Filters\Filter::make('status_and_visibility')
                ->form([
                    Components\Select::make('status')
                        ->label(__('Status'))
                        ->placeholder("-")
                        ->options(EventStatus::asSelectArray()),

                    Components\Select::make('visibility')
                        ->label(__('Visibility'))
                        ->placeholder("-")
                        ->options([
                            'true' => __("Yes"),
                            'false' => __("No"),
                        ]),
                ])
                ->columns()
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['status'],
                            fn(Builder $query, $status): Builder => $query->where('status', $status),
                        )
                        ->when(
                            $data['visibility'],
                            fn(Builder $query, $visibility): Builder => $query->where('visibility', json_decode($visibility)),
                        );
                })
                ->indicateUsing(function (array $data): array {
                    $indicators = [];

                    if ($data['status'] ?? null) {
                        $indicators['country'] = __('Status') . ' "' . EventStatus::getDescription($data['status']) . '"';
                    }

                    if ($data['visibility'] ?? null) {
                        $visibility = json_decode($data['visibility'])
                            ? __("Yes")
                            : __("No");
                        $indicators['visibility'] = __('Visibility') . ' "' . $visibility . '"';
                    }

                    return $indicators;
                }),

            Filters\Filter::make('country_and_city')
                ->form([
                    Components\Select::make('country')
                        ->label(__('Country'))
                        ->placeholder("-")
                        ->options(collect(Countries::asSelectArray())->put('no_country', __("No")))
                        ->reactive()
                        ->afterStateUpdated(fn(Closure $set) => $set('city', "")),

                    Components\Select::make('city')
                        ->label(__('City'))
                        ->placeholder("-")
                        ->options(fn(Closure $get): Collection => collect(Cities::asSelectArray($get('country')))->put('no_city', __("No"))),
                ])
                ->columns()
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['country'],
                            fn(Builder $query, $country): Builder => $country === "no_country"
                                ? $query->whereNull('country')
                                : $query->where('country', $country),
                        )
                        ->when(
                            $data['city'],
                            fn(Builder $query, $city): Builder => $city === "no_city"
                                ? $query->whereNull('city')
                                : $query->where('city', $city),
                        );
                })
                ->indicateUsing(function (array $data): array {
                    $indicators = [];

                    if ($data['country'] ?? null) {
                        $country = $data['country'] === "no_country"
                            ? __("No")
                            : Countries::getDescription($data['country']);
                        $indicators['country'] = __('Country') . ' "' . $country . '"';
                    }

                    if ($data['city'] ?? null) {
                        $city = $data['city'] === "no_city"
                            ? __("No")
                            : Cities::getDescription($data['country'], $data['city']);
                        $indicators['city'] = __('City') . ' "' . $city . '"';
                    }

                    return $indicators;
                }),

            Filters\Filter::make('published_from_to')
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
                ->columns()
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

    /**
     * @return string
     */
    protected function getTableFiltersFormWidth(): string
    {
        return 'md';
    }
}
