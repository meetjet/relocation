<?php

namespace App\Filament\Resources\PlaceResource\Pages;

use App\Enums\PlaceStatus;
use App\Enums\PlaceType;
use App\Facades\Countries;
use App\Facades\Locations;
use App\Filament\Resources\PlaceResource;
use App\Models\Place;
use App\Traits\PageListHelpers;
use Closure;
use Exception;
use Filament\Forms\Components;
use Filament\Pages;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Table;
use App\Filament\Actions;
use Filament\Tables\Columns;
use Filament\Tables\Filters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * @package App\Filament\Resources\EventPointResource\Pages
 */
class ListPlaces extends ListRecords
{
    use PageListHelpers;

    protected static string $resource = PlaceResource::class;

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
            Columns\TextColumn::make('title')
                ->label(__('Title'))
                ->searchable()
                ->sortable()
                ->color(fn($record): ?string => is_null($record->deleted_at) ? null : "danger"),

            Columns\SpatieTagsColumn::make('tags')
                ->label(__('Tags'))
                ->type("places")
                ->toggleable(),

            Columns\TextColumn::make('type')
                ->label(__('Type'))
                ->enum(PlaceType::asSelectArray())
                ->sortable()
                ->toggleable(),

            Columns\TextColumn::make('country')
                ->label(__('Country'))
                ->enum(Countries::asSelectArray())
                ->toggleable(),

            Columns\TextColumn::make('location')
                ->label(__('Location'))
                ->getStateUsing(fn($record): string => Locations::getDescription($record->country, $record->location))
                ->toggleable(),

            Columns\TextColumn::make('events')
                ->label(__('Events'))
                ->getStateUsing(fn(Place $record): ?string => $record->events()->count())
                ->toggleable(),

            Columns\BadgeColumn::make('status')
                ->label(__('Status'))
                ->enum(PlaceStatus::asSelectArray())
                ->sortable()
                ->colors([
                    'secondary' => PlaceStatus::INACTIVE,
                    'success' => PlaceStatus::ACTIVE,
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
                ->tooltip(__('Delete'))
                ->visible(fn(Place $record): bool => !$record->events()->exists()),
            Actions\Tables\RestoreAction::make()
                ->label("")
                ->tooltip(__('Restore')),
            Actions\Tables\ForceDeleteAction::make()
                ->label("")
                ->tooltip(__('Delete permanently')),
        ];
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function getTableBulkActions(): array
    {
        return [];
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function getTableFilters(): array
    {
        return [
            Filters\TrashedFilter::make(),

            Filters\Filter::make('type')
                ->form([
                    Components\Select::make('type')
                        ->label(__('Type'))
                        ->placeholder("-")
                        ->options(PlaceType::asSelectArray()),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query->when(
                        $data['type'],
                        fn(Builder $query, $status): Builder => $query->where('type', $status),
                    );
                })
                ->indicateUsing(function (array $data): ?string {
                    if ($data['type']) {
                        return __('Type') . ' "' . PlaceType::getDescription($data['type']) . '"';
                    }

                    return null;
                }),

            Filters\Filter::make('country_and_location')
                ->form([
                    Components\Select::make('country')
                        ->label(__('Country'))
                        ->placeholder("-")
                        ->options(collect(Countries::asSelectArray())->put('no_country', __("No")))
                        ->reactive()
                        ->afterStateUpdated(fn(Closure $set) => $set('location', "")),

                    Components\Select::make('location')
                        ->label(__('Location'))
                        ->placeholder("-")
                        ->options(fn(Closure $get): Collection => collect(Locations::asSelectArray($get('country')))->put('no_location', __("No"))),
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
                            $data['location'],
                            fn(Builder $query, $location): Builder => $location === "no_location"
                                ? $query->whereNull('location')
                                : $query->where('location', $location),
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

                    if ($data['location'] ?? null) {
                        $location = $data['location'] === "no_location"
                            ? __("No")
                            : Locations::getDescription($data['country'], $data['location']);
                        $indicators['location'] = __('Location') . ' "' . $location . '"';
                    }

                    return $indicators;
                }),

            Filters\Filter::make('status_and_visibility')
                ->form([
                    Components\Select::make('status')
                        ->label(__('Status'))
                        ->placeholder("-")
                        ->options(PlaceStatus::asSelectArray()),

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
                        $indicators['country'] = __('Status') . ' "' . PlaceStatus::getDescription($data['status']) . '"';
                    }

                    if ($data['visibility'] ?? null) {
                        $visibility = json_decode($data['visibility'])
                            ? __("Yes")
                            : __("No");
                        $indicators['visibility'] = __('Visibility') . ' "' . $visibility . '"';
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
