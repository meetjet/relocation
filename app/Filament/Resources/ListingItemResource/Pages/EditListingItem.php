<?php

namespace App\Filament\Resources\ListingItemResource\Pages;

use App\Enums\ListingItemStatus;
use App\Facades\Countries;
use App\Filament\Actions\Pages\DeleteAction;
use App\Filament\Resources\ListingItemResource;
use Filament\Forms\Components;
use Filament\Resources\Pages\EditRecord;

class EditListingItem extends EditRecord
{
    protected static string $resource = ListingItemResource::class;

    /**
     * @return array
     * @throws \Exception
     */
    protected function getActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * @return array
     */
    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->context('edit')
                ->model($this->getRecord())
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
                                ->required(),

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
                        ]),

                    Components\Section::make(__('Additional information'))
                        ->schema([
                            Components\TextInput::make('price')
                                ->label(__('Price'))
                                ->disabled()
                                ->dehydrated(false),
                        ])->collapsible(),
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
                                ->required(),

                            Components\Toggle::make('visibility')
                                ->label(__('Visibility')),
                        ]),

                    Components\Card::make()
                        ->schema([
                            Components\Select::make('category_id')
                                ->label(__('Category'))
                                ->relationship('category', 'title')
                                ->placeholder("-")
//                                ->searchable()
//                                ->getSearchResultsUsing(fn(string $search) => ListingCategory::where('title', 'ilike', "%{$search}%")->limit(10)->pluck('title', 'id'))
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
        return $this->previousUrl ?? self::getResource()::getUrl('index');
    }
}
