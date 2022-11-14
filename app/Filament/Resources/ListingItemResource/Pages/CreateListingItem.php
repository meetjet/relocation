<?php

namespace App\Filament\Resources\ListingItemResource\Pages;

use App\Enums\ListingItemStatus;
use App\Facades\Cities;
use App\Facades\Countries;
use App\Facades\Currencies;
use App\Filament\Resources\ListingItemResource;
use Closure;
use Filament\Forms\Components;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Request;

class CreateListingItem extends CreateRecord
{
    protected static string $resource = ListingItemResource::class;
    protected static bool $canCreateAnother = false;

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

                            Components\Grid::make()
                                ->schema([
                                    Components\TextInput::make('price')
                                        ->label(__('Price'))
                                        ->numeric()
                                        ->required(),

                                    Components\Select::make('currency')
                                        ->label(__('Currency'))
                                        ->hint(__('Selected automatically based on country'))
                                        ->placeholder("-")
                                        ->options(Currencies::asSelectArray())
                                        ->default(Currencies::getCodeByCountry("armenia"))
                                        ->disabled(),
                                ]),

                            Components\Hidden::make('user_id')
                                ->default(fn(): int => Request::user()->id),
                        ]),

                    Components\Section::make(__('Announcement owner'))
                        ->schema([
                            Components\TextInput::make('contact.nickname')
                                ->label(__('Real owner nickname'))
                                ->placeholder(__('No'))
                                ->default(function () {
                                    $user = Request::user();

                                    return $user && $user->contact
                                        ? $user->contact->nickname
                                        : null;
                                })
                                ->disabled()
                                ->dehydrated(false),

                            Components\TextInput::make('custom_nickname')
                                ->label(__('Custom nickname'))
                                ->placeholder(__('No'))
                                ->required(fn(Closure $get): bool => is_null($get('contact.nickname'))),
                        ])->columns()->collapsible(),
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
                                ->default(ListingItemStatus::PUBLISHED)
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
                                ->reactive()
                                ->afterStateUpdated(function (Closure $set, Closure $get) {
                                    $set('city', "");
                                    $set('currency', Currencies::getCodeByCountry($get('country')));
                                })
                                ->default("armenia")
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
}
