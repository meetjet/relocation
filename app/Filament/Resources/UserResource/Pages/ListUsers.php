<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
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

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

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
        return $table->defaultSort('id', 'desc');
    }

    /**
     * @return array
     */
    protected function getTableColumns(): array
    {
        return [
            Columns\TextColumn::make('id')
                ->label(__('ID'))
                ->sortable()
                ->toggleable(),

            Columns\TextColumn::make('name')
                ->label(__('Name'))
                ->searchable()
                ->sortable()
                ->color(fn($record): ?string => is_null($record->deleted_at) ? null : "danger"),

            Columns\TextColumn::make('email')
                ->label(__('Email'))
                ->searchable()
                ->sortable()
                ->toggleable(),

            Columns\TextColumn::make('created_at')
                ->label(__('Created'))
                ->date("j M Y, H:i")
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
            Actions\Tables\EditAction::make(),
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
