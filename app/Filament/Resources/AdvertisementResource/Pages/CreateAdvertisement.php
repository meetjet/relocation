<?php

namespace App\Filament\Resources\AdvertisementResource\Pages;

use App\Enums\AdvertisementStatus;
use App\Facades\Countries;
use App\Filament\Resources\AdvertisementResource;
use Filament\Forms\Components;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Request;

class CreateAdvertisement extends CreateRecord
{
    protected static string $resource = AdvertisementResource::class;

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
                                ->options(AdvertisementStatus::asSelectArray())
                                ->disablePlaceholderSelection()
                                ->default(AdvertisementStatus::CREATED),

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
