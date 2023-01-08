<?php

namespace App\Filament\Resources\PropertyResource\Pages;

use App\Enums\PropertyRoomsNumber;
use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use App\Facades\Countries;
use App\Facades\Locations;
use App\Filament\Resources\PropertyResource;
use Closure;
use Filament\Forms\Components;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Request;

class CreateProperty extends CreateRecord
{
    protected static string $resource = PropertyResource::class;

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
                            Components\RichEditor::make('description')
                                ->label(__('Description'))
                                ->disableToolbarButtons([
                                    'attachFiles',
                                    'codeBlock',
                                ])
                                ->nullable(),

                            Components\SpatieTagsInput::make('tags')
                                ->label(__('Tags'))
                                ->type("properties"),

                            Components\Grid::make()
                                ->schema([
                                    Components\Select::make('type')
                                        ->label(__('Type'))
                                        ->options(PropertyType::asSelectArray())
                                        ->placeholder("-")
                                        ->required(),

                                    Components\Select::make('rooms_number')
                                        ->label(__('Rooms number'))
                                        ->options(PropertyRoomsNumber::asSelectArray())
                                        ->placeholder("-")
                                        ->nullable(),
                                ]),

                            Components\Grid::make()
                                ->schema([
                                    Components\Select::make('country')
                                        ->label(__('Country'))
                                        ->options(Countries::asSelectArray())
                                        ->placeholder("-")
                                        ->reactive()
                                        ->afterStateUpdated(fn(Closure $set) => $set('location', ""))
                                        ->required(),

                                    Components\Select::make('location')
                                        ->label(__('Location'))
                                        ->placeholder("-")
                                        ->options(fn(Closure $get): array => Locations::asSelectArray($get('country')))
                                        ->required(),
                                ]),

                            Components\Grid::make()
                                ->schema([
                                    Components\TextInput::make('address_ru')
                                        ->label(__('Address RU'))
                                        ->requiredWithout('address_en'),

                                    Components\TextInput::make('address_en')
                                        ->label(__('Address EN'))
                                        ->requiredWithout('address_ru'),
                                ]),

                            Components\Hidden::make('user_id')
                                ->default(fn(): int => Request::user()->id),
                        ]),

                    Components\Section::make(__('Property owner'))
                        ->schema([
                            Components\TextInput::make('contact.nickname')
                                ->label(__('Real owner nickname'))
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
                                ->requiredWithoutAll(['contact.nickname', 'email', 'phone']),

                            Components\TextInput::make('email')
                                ->label(__('Owner email'))
                                ->helperText(__('Requested from the user if he does not have a nickname'))
                                ->email()
                                ->requiredWithoutAll(['contact.nickname', 'custom_nickname', 'phone']),

                            Components\TextInput::make('phone')
                                ->label(__('Owner phone'))
                                ->helperText(__('Requested from the user if he does not have a nickname'))
                                ->requiredWithoutAll(['contact.nickname', 'custom_nickname', 'email']),
                        ])->columns()->collapsible(),
                ])
                ->columnSpan(['lg' => 2]),

            Components\Group::make()
                ->schema([
                    Components\Card::make()
                        ->schema([
                            Components\Select::make('status')
                                ->label(__('Status'))
                                ->options(PropertyStatus::asSelectArray())
                                ->placeholder("-")
                                ->default(PropertyStatus::PUBLISHED)
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
        // Remove redundant line breaks.
        if ($data['description']) {
            $data['description'] = strReplace("<br><br><br>", "<br><br>", $data['description']);
        }

        return $data;
    }
}
