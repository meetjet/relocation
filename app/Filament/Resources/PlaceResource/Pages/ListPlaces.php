<?php

namespace App\Filament\Resources\PlaceResource\Pages;

use App\Enums\PlaceStatus;
use App\Enums\PlaceType;
use App\Facades\Countries;
use App\Facades\Locations;
use App\Filament\Resources\PlaceResource;
use App\Models\Place;
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
        return $table->defaultSort('title');
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

            Filters\Filter::make('status')
                ->form([
                    Components\Select::make('status')
                        ->label(__('Status'))
                        ->placeholder("-")
                        ->options(PlaceStatus::asSelectArray()),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query->when(
                        $data['status'],
                        fn(Builder $query, $status): Builder => $query->where('status', $status),
                    );
                })
                ->indicateUsing(function (array $data): ?string {
                    if ($data['status']) {
                        return __('Status') . ' "' . PlaceStatus::getDescription($data['status']) . '"';
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
        ];
    }

    /**
     * @return string
     */
//    protected function getTableFiltersFormWidth(): string
//    {
//        return 'md';
//    }
}
