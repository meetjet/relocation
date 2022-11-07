<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Filament\Resources\EventResource\RelationManagers;
use App\Models\Event;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?int $navigationSort = 3;

    /**
     * @return string
     */
    public static function getNavigationLabel(): string
    {
        return __('Events');
    }

    /**
     * @return string
     */
    public static function getModelLabel(): string
    {
        return __('Event');
    }

    /**
     * @return string
     */
    public static function getPluralModelLabel(): string
    {
        return __('Events');
    }

    /**
     * @return array
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
//            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
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
