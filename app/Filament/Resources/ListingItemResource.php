<?php

namespace App\Filament\Resources;

use App\Enums\ListingItemStatus;
use App\Filament\Resources\ListingItemResource\Pages;
use App\Models\ListingItem;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ListingItemResource extends Resource
{
    protected static ?string $model = ListingItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-lightning-bolt';

    protected static ?string $slug = 'listing/items';

    protected static ?int $navigationSort = 0;

    /**
     * @return string
     */
    public static function getNavigationGroup(): string
    {
        return __('Flea market');
    }

    /**
     * @return string
     */
    public static function getNavigationLabel(): string
    {
        return __('Announcements');
    }

    /**
     * @return string
     */
    public static function getModelLabel(): string
    {
        return __('Announcement');
    }

    /**
     * @return string
     */
    public static function getPluralModelLabel(): string
    {
        return __('Announcements');
    }

    /**
     * @return array
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListListingItems::route('/'),
            'create' => Pages\CreateListingItem::route('/create'),
            'edit' => Pages\EditListingItem::route('/{record}/edit'),
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
        return self::$model::where('status', ListingItemStatus::PUBLISHED)
            ->where('visibility', true)
            ->count();
    }
}
