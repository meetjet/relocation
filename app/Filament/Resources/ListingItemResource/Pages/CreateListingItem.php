<?php

namespace App\Filament\Resources\ListingItemResource\Pages;

use App\Enums\Countries;
use App\Enums\ListingItemStatus;
use App\Filament\Resources\ListingItemResource;
use Filament\Forms\Components;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Request;

/**
 * @package App\Filament\Resources\ListingItemResource\Pages
 * @deprecated
 */
class CreateListingItem extends CreateRecord
{
    protected static string $resource = ListingItemResource::class;

    /**
     * @return array
     */
    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->context('create')
                ->model($this->getModel())
                ->schema($this->getFormSchema())
                ->columns(3)
                ->statePath('data')
                ->inlineLabel(config('filament.layout.forms.have_inline_labels')),
        ];
    }

    /**
     * @return array
     */
    protected function getFormSchema(): array
    {
        return [
            Components\Group::make()
                ->schema([
                    Components\Card::make()
                        ->schema([
                            Components\Textarea::make('title')
                                ->label(__('Title'))
                                ->rows(2)
                                ->required()
                                ->autofocus(),

                            Components\TextInput::make('slug')
                                ->label(__('Slug'))
                                ->hint(__('If this field is left blank, the link will be generated automatically'))
                                ->unique(ignoreRecord: true),

                            Components\RichEditor::make('description')
                                ->label(__('Description'))
                                ->disableToolbarButtons([
                                    'attachFiles',
                                    'codeBlock',
                                ])
                                ->nullable(),

                            Components\SpatieTagsInput::make('tags')
                                ->label(__('Tags'))
                                ->type("listing-items"),

                            Components\Hidden::make('user_id')
                                ->default(fn(): int => Request::user()->id),
                        ]),
                ])
                ->columnSpan(['lg' => 2]),

            Components\Group::make()
                ->schema([
                    Components\Card::make()
                        ->schema([
                            Components\Select::make('status')
                                ->label(__('Status'))
                                ->options(ListingItemStatus::asSelectArray())
                                ->placeholder("-")
                                ->default(ListingItemStatus::CREATED)
                                ->required(),

                            Components\Toggle::make('visibility')
                                ->label(__('Visibility'))
                                ->default(false),
                        ]),

                    Components\Card::make()
                        ->schema([
                            Components\Select::make('category_id')
                                ->label(__('Category'))
                                ->relationship('category', 'title')
                                ->placeholder("-")
                                ->nullable(),

                            Components\Select::make('country')
                                ->label(__('Country'))
                                ->options(Countries::asSelectArray())
                                ->placeholder("-")
                                ->nullable(),
                        ]),
                ])
                ->columnSpan(['lg' => 1]),
        ];
    }

    /**
     * @return string
     */
    protected function getRedirectUrl(): string
    {
        return self::getResource()::getUrl('index');
    }
}
