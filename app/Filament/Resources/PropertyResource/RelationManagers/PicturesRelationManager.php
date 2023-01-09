<?php

namespace App\Filament\Resources\PropertyResource\RelationManagers;

use App\Filament\Actions;
use App\Forms\Components\PicturePreview;
use App\Models\Picture;
use Exception;
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
                    ->hidden(fn($record): bool => is_null($record))
                    ->dehydrated(false),

                Forms\Components\FileUpload::make('tmp_image')
                    ->disableLabel()
                    ->image()
                    ->imagePreviewHeight('480')
                    ->directory('form-attachments-tmp')
                    ->hidden(fn($record): bool => !is_null($record)),

                Forms\Components\TextInput::make('caption')
                    ->label(__('Caption'))
                    ->nullable(),
            ])
            ->columns(1);
    }

    /**
     * @param Table $table
     * @return Table
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail_square')
                    ->label(__('Picture'))
                    ->width(200)
                    ->height(200)
                    ->extraImgAttributes(['class' => "object-cover"])
                    ->getStateUsing(function (Picture $record): string {
                        return $record->thumbnail_square ?: asset("images/image-in-progress.jpg");
                    }),

                Tables\Columns\TextColumn::make('caption')
                    ->label(__('Caption')),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Actions\Tables\EditAction::make()
                    ->mutateRecordDataUsing(function (array $data): array {
                        $data['medium'] = $data['medium'] ?: asset("images/image-in-progress.jpg");

                        return $data;
                    }),
                Actions\Tables\DeleteAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }

    /**
     * @return bool
     */
    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }
}
