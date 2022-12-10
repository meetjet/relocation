<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FaqResource\Pages;
use App\Models\Faq;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $slug = 'faqs';

    protected static ?int $navigationSort = 0;

    /**
     * @return string
     */
    public static function getNavigationGroup(): string
    {
        return __('FAQ');
    }

    /**
     * @return string
     */
    public static function getNavigationLabel(): string
    {
        return __('FAQ');
    }

    /**
     * @return string
     */
    public static function getModelLabel(): string
    {
        return __('FAQ');
    }

    /**
     * @return string
     */
    public static function getPluralModelLabel(): string
    {
        return __('FAQ');
    }

    /**
     * @return array
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFaqs::route('/'),
            'create' => Pages\CreateFaq::route('/create'),
            'edit' => Pages\EditFaq::route('/{record}/edit'),
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
}
