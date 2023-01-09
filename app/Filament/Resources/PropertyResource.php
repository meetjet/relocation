<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PropertyResource\Pages;
use App\Filament\Resources\PropertyResource\RelationManagers;
use App\Models\Property;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static ?string $navigationIcon = 'heroicon-o-office-building';

    protected static ?string $slug = 'properties';

    protected static ?int $navigationSort = 0;

    /**
     * @return string
     */
    public static function getNavigationGroup(): string
    {
        return __('Properties');
    }

    /**
     * @return string
     */
    public static function getNavigationLabel(): string
    {
        return __('Properties');
    }

    /**
     * @return string
     */
    public static function getModelLabel(): string
    {
        return __('Property');
    }

    /**
     * @return string
     */
    public static function getPluralModelLabel(): string
    {
        return __('Property');
    }

    /**
     * @return array
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProperties::route('/'),
            'create' => Pages\CreateProperty::route('/create'),
            'edit' => Pages\EditProperty::route('/{record}/edit'),
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
