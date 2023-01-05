<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlaceResource\Pages;
use App\Filament\Resources\PlaceResource\RelationManagers;
use App\Models\Place;
use App\Traits\PageListHelpers;
use Filament\Resources\Resource;
use App\Filament\Actions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlaceResource extends Resource
{
    use PageListHelpers; // TODO

    protected static ?string $model = Place::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $slug = 'places';

    protected static ?int $navigationSort = 0;

    /**
     * @return string
     */
    public static function getNavigationGroup(): string
    {
        return __('Places');
    }

    /**
     * @return string
     */
    public static function getNavigationLabel(): string
    {
        return __('Places');
    }

    /**
     * @return string
     */
    public static function getModelLabel(): string
    {
        return __('Place');
    }

    /**
     * @return string
     */
    public static function getPluralModelLabel(): string
    {
        return __('Place');
    }

    /**
     * @return array
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlaces::route('/'),
            'create' => Pages\CreatePlace::route('/create'),
            'edit' => Pages\EditPlace::route('/{record}/edit'),
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
     * @return string|null
     */
    protected static function getNavigationBadge(): ?string
    {
        return self::$model::active()->count();
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
