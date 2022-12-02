<?php

namespace App\Filament\Resources\EventPointResource\Pages;

use App\Enums\EventPointStatus;
use App\Filament\Resources\EventPointResource;
use App\Models\EventPoint;
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
class ListEventPoints extends ListRecords
{
    use PageListHelpers;

    protected static string $resource = EventPointResource::class;

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

            Columns\TextColumn::make('address')
                ->label(__('Address'))
                ->searchable(),

            Columns\TextColumn::make('events')
                ->label(__('Events'))
                ->getStateUsing(fn(EventPoint $record): ?string => $record->events()->count())
                ->toggleable(),

            Columns\BadgeColumn::make('status')
                ->label(__('Status'))
                ->enum(EventPointStatus::asSelectArray())
                ->sortable()
                ->colors([
                    'secondary' => EventPointStatus::INACTIVE,
                    'success' => EventPointStatus::ACTIVE,
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
                ->visible(fn(EventPoint $record): bool => !$record->events()->exists()),
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
                        ->options(EventPointStatus::asSelectArray()),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query->when(
                        $data['status'],
                        fn(Builder $query, $status): Builder => $query->where('status', $status),
                    );
                })
                ->indicateUsing(function (array $data): ?string {
                    if ($data['status']) {
                        return __('Status') . ' "' . EventPointStatus::getDescription($data['status']) . '"';
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
