<?php

namespace App\Filament\Resources\PropertyResource\Pages;

use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use App\Facades\Countries;
use App\Facades\Locations;
use App\Filament\Resources\PropertyResource;
use App\Traits\PageListHelpers;
use Closure;
use Exception;
use Filament\Forms\Components;
use Filament\Pages;
use Filament\Tables\Columns;
use Filament\Tables\Filters;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Table;
use App\Filament\Actions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ListProperties extends ListRecords
{
    use PageListHelpers;

    protected static string $resource = PropertyResource::class;

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
            Columns\TextColumn::make('type')
                ->label(__('Type'))
                ->enum(PropertyType::asSelectArray())
                ->sortable()
                ->toggleable(),

            Columns\SpatieTagsColumn::make('tags')
                ->label(__('Tags'))
                ->type("properties")
                ->toggleable(),

            Columns\TextColumn::make('country')
                ->label(__('Country'))
                ->enum(Countries::asSelectArray())
                ->toggleable(),

            Columns\TextColumn::make('location')
                ->label(__('Location'))
                ->getStateUsing(fn($record): string => Locations::getDescription($record->country, $record->location))
                ->toggleable(),

            Columns\BadgeColumn::make('status')
                ->label(__('Status'))
                ->enum(PropertyStatus::asSelectArray())
                ->sortable()
                ->colors([
                    'secondary' => PropertyStatus::CREATED,
                    'success' => PropertyStatus::PUBLISHED,
                    'danger' => PropertyStatus::REJECTED,
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

            Filters\Filter::make('type')
                ->form([
                    Components\Select::make('type')
                        ->label(__('Type'))
                        ->placeholder("-")
                        ->options(PropertyType::asSelectArray()),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query->when(
                        $data['type'],
                        fn(Builder $query, $status): Builder => $query->where('type', $status),
                    );
                })
                ->indicateUsing(function (array $data): ?string {
                    if ($data['type']) {
                        return __('Type') . ' "' . PropertyType::getDescription($data['type']) . '"';
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
                        ->options(PropertyStatus::asSelectArray()),

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
                        $indicators['country'] = __('Status') . ' "' . PropertyStatus::getDescription($data['status']) . '"';
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
