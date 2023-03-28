<?php

namespace App\Filament\Resources\PlaceResource\Pages;

use App\Enums\PlaceSocialNetwork;
use App\Enums\PlaceStatus;
use App\Facades\Countries;
use App\Facades\Currencies;
use App\Facades\Locations;
use App\Filament\Actions\Pages\DeleteAction;
use App\Filament\Actions\Pages\ForceDeleteAction;
use App\Filament\Actions\Pages\RestoreAction;
use App\Filament\Resources\PlaceResource;
use App\Traits\PageListHelpers;
use Closure;
use Exception;
use Filament\Forms\Components;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Builder;

/**
 * @package App\Filament\Resources\EventPointResource\Pages
 */
class EditPlace extends EditRecord
{
    use PageListHelpers;

    protected static string $resource = PlaceResource::class;

    /**
     * @return array
     * @throws Exception
     */
    protected function getActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
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
            Components\Card::make()
                ->schema([
                    Components\Group::make()
                        ->schema([
                            Components\TextInput::make('title')
                                ->label(__('Title'))
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

                            Components\Repeater::make('phones')
                                ->label(__('Phone numbers'))
                                ->schema([
                                    Components\TextInput::make('number')
                                        ->disableLabel()
                                        ->tel()
                                        ->validationAttribute('')
                                        ->required(),
                                ])
                                ->createItemButtonLabel(__('Add phone number'))
                                ->grid(3)
                                ->disableItemMovement(),

                            Components\Repeater::make('sites')
                                ->label(__('Websites'))
                                ->schema([
                                    Components\TextInput::make('url')
                                        ->disableLabel()
                                        ->url()
                                        ->validationAttribute('')
                                        ->required(),
                                ])
                                ->createItemButtonLabel(__('Add website'))
                                ->grid()
                                ->disableItemMovement(),

                            Components\Repeater::make('social_media')
                                ->label(__('Social media'))
                                ->schema([
                                    Components\Select::make('network')
                                        ->disableLabel()
                                        ->placeholder("-")
                                        ->options(PlaceSocialNetwork::asSelectArray())
                                        ->required(),

                                    Components\TextInput::make('url')
                                        ->disableLabel()
                                        ->url()
                                        ->validationAttribute('')
                                        ->required(),
                                ])
                                ->createItemButtonLabel(__('Add social network'))
                                ->grid()
                                ->disableItemMovement(),
                        ]),
                ])
                ->columnSpan(['lg' => 2]),

            Components\Group::make()
                ->schema([
                    Components\Card::make()
                        ->schema([
                            Components\Placeholder::make('created_at')
                                ->label(__('Created at'))
                                ->content(fn($record): string => $record->created_at->diffForHumans()),

                            Components\Placeholder::make('updated_at')
                                ->label(__('Last modified at'))
                                ->content(fn($record): string => $record->updated_at->diffForHumans()),
                        ]),

                    Components\Card::make()
                        ->schema([
                            Components\Select::make('status')
                                ->label(__('Status'))
                                ->options(PlaceStatus::asSelectArray())
                                ->disablePlaceholderSelection(),

                            Components\Toggle::make('visibility')
                                ->label(__('Visibility')),
                        ]),
                ])
                ->columnSpan(['lg' => 1]),
        ];
    }

    /**
     * @param array $data
     * @return array
     */
    protected function mutateFormDataBeforeSave(array $data): array
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

    /**
     * @return string
     */
    protected function getRedirectUrl(): string
    {
        return self::getResource()::getUrl('index');
    }
}
