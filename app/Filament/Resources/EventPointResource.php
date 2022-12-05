<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventPointResource\Pages;
use App\Filament\Resources\EventPointResource\RelationManagers;
use App\Models\EventPoint;
use App\Traits\PageListHelpers;
use Filament\Resources\Resource;
use App\Filament\Actions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EventPointResource extends Resource
{
    use PageListHelpers;

    protected static ?string $model = EventPoint::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $slug = 'events/points';

    protected static ?int $navigationSort = 2;

    /**
     * @return string
     */
    public static function getNavigationGroup(): string
    {
        return __('Events');
    }

    /**
     * @return string
     */
    public static function getNavigationLabel(): string
    {
        return __('Points');
    }

    /**
     * @return string
     */
    public static function getModelLabel(): string
    {
        return __('Point');
    }

    /**
     * @return string
     */
    public static function getPluralModelLabel(): string
    {
        return __('Point');
    }

    /**
     * @return array
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEventPoints::route('/'),
            'create' => Pages\CreateEventPoint::route('/create'),
            'edit' => Pages\EditEventPoint::route('/{record}/edit'),
        ];
    }

    /**
     * @return Builder
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    /**
     * @return string[]
     */
    public static function getRelations(): array
    {
        return [
            RelationManagers\PicturesRelationManager::class,
        ];
    }
}
