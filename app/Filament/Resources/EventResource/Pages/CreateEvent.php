<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Enums\ListingItemStatus;
use App\Facades\Cities;
use App\Facades\Countries;
use App\Filament\Resources\EventResource;
use Closure;
use Filament\Forms\Components;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Request;

/**
 * @package App\Filament\Resources\EventResource\Pages
 * @deprecated
 */
class CreateEvent extends CreateRecord
{
    protected static string $resource = EventResource::class;

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
                                ->type("events"),

                            Components\TextInput::make('price')
                                ->label(__('Price'))
                                ->numeric()
                                ->nullable(),

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
                            Components\Select::make('country')
                                ->label(__('Country'))
                                ->options(Countries::asSelectArray())
                                ->placeholder("-")
                                ->reactive()
                                ->afterStateUpdated(fn(Closure $set) => $set('city', ""))
                                ->nullable(),

                            Components\Select::make('city')
                                ->label(__('City'))
                                ->placeholder("-")
                                ->options(fn(Closure $get): array => Cities::asSelectArray($get('country')))
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
