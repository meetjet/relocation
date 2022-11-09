<?php

namespace App\Filament\Resources\ListingItemResource\RelationManagers;

use App\Filament\Actions;
use App\Forms\Components\PicturePreview;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;

class PicturesRelationManager extends RelationManager
{
    protected static string $relationship = 'pictures';

    /**
     * @return string
     */
    public static function getModelLabel(): string
    {
        return __('Picture');
    }

    /**
     * @return string
     */
    protected static function getPluralModelLabel(): string
    {
        return __('Pictures');
    }

    /**
     * @param Form $form
     * @return Form
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                PicturePreview::make('medium')
                    ->disableLabel()
                    ->dehydrated(false),

                Forms\Components\TextInput::make('caption')
                    ->label(__('Caption'))
                    ->nullable(),
            ])
            ->columns(1);
    }

    /**
     * @param Table $table
     * @return Table
     * @throws \Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail_square')
                    ->label(__('Picture'))
                    ->height(200)
                    ->extraImgAttributes(['class' => "border"]),

                Tables\Columns\TextColumn::make('caption')
                    ->label(__('Caption')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                Actions\Tables\EditAction::make(),
                Actions\Tables\DeleteAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }
}
