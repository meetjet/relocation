<?php

namespace App\Filament\Resources\PlaceResource\Pages;

use App\Enums\PlaceStatus;
use App\Enums\PlaceType;
use App\Facades\Countries;
use App\Facades\Currencies;
use App\Facades\Locations;
use App\Filament\Resources\PlaceResource;
use Closure;
use Filament\Forms\Components;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Builder;

/**
 * @package App\Filament\Resources\EventPointResource\Pages
 */
class CreatePlace extends CreateRecord
{
    protected static string $resource = PlaceResource::class;

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
            Components\Card::make()
                ->schema([
                    Components\Group::make()
                        ->schema([
                            Components\TextInput::make('title')
                                ->label(__('Title'))
                                ->autofocus()
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
                                ->type("places"),

                            Components\Grid::make(3)
                                ->schema([
                                    Components\Select::make('category_id')
                                        ->label(__('Category'))
                                        ->relationship(
                                            'category',
                                            'title',
                                            fn(Builder $query): Builder => $query->orderBy('id')
                                        )
                                        ->placeholder("-")
                                        ->required(),

                                    Components\Select::make('country')
                                        ->label(__('Country'))
                                        ->options(Countries::asSelectArray())
                                        ->placeholder("-")
                                        ->reactive()
                                        ->afterStateUpdated(function (Closure $set, Closure $get) {
                                            $set('location', "");
                                            $set('currency', Currencies::getCodeByCountry($get('country')));
                                        })
                                        ->nullable(),

                                    Components\Select::make('location')
                                        ->label(__('Location'))
                                        ->placeholder("-")
                                        ->options(fn(Closure $get): array => Locations::asSelectArray($get('country')))
                                        ->nullable(),
                                ]),

                            Components\Grid::make()
                                ->schema([
                                    Components\TextInput::make('address_ru')
                                        ->label(__('Address RU'))
                                        ->nullable(),

                                    Components\TextInput::make('address_en')
                                        ->label(__('Address EN'))
                                        ->nullable(),
                                ]),

                            Components\Grid::make()
                                ->schema([
                                    Components\TextInput::make('latitude')
                                        ->label(__('Latitude'))
                                        ->numeric()
                                        ->requiredWith('longitude'),

                                    Components\TextInput::make('longitude')
                                        ->label(__('Longitude'))
                                        ->numeric()
                                        ->requiredWith('latitude'),
                                ]),
                        ]),
                ])
                ->columnSpan(['lg' => 2]),

            Components\Group::make()
                ->schema([
                    Components\Card::make()
                        ->schema([
                            Components\Select::make('status')
                                ->label(__('Status'))
                                ->options(PlaceStatus::asSelectArray())
                                ->placeholder("-")
                                ->default(PlaceStatus::ACTIVE)
                                ->required(),

                            Components\Toggle::make('visibility')
                                ->label(__('Visibility'))
                                ->default(false),
                        ]),
                ])
                ->columnSpan(['lg' => 1]),
        ];
    }

    /**
     * @param array $data
     * @return array
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if ($data['latitude']) {
            $data['latitude'] = (double)$data['latitude'];
        }

        if ($data['longitude']) {
            $data['longitude'] = (double)$data['longitude'];
        }

        // Remove redundant line breaks.
        if ($data['description']) {
            $data['description'] = strReplace("<br><br><br>", "<br><br>", $data['description']);
        }

        return $data;
    }
}
