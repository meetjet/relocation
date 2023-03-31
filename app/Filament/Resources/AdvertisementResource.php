<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdvertisementResource\Pages;
use App\Filament\Resources\AdvertisementResource\RelationManagers;
use App\Models\Advertisement;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AdvertisementResource extends Resource
{
    protected static ?string $model = Advertisement::class;

    protected static ?string $navigationIcon = 'heroicon-o-speakerphone';

    protected static ?string $slug = 'advertisements';

    protected static ?int $navigationSort = 0;

    /**
     * @return string
     */
    public static function getNavigationGroup(): string
    {
        return __('Advertisements');
    }

    /**
     * @return string
     */
    public static function getNavigationLabel(): string
    {
        return __('Advertisements');
    }

    /**
     * @return string
     */
    public static function getModelLabel(): string
    {
        return __('Advertisement');
    }

    /**
     * @return string
     */
    public static function getPluralModelLabel(): string
    {
        return __('Advertisements');
    }

    /**
     * @return array
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdvertisements::route('/'),
            'create' => Pages\CreateAdvertisement::route('/create'),
            'edit' => Pages\EditAdvertisement::route('/{record}/edit'),
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
