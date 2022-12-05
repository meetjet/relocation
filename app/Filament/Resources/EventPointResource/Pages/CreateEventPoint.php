<?php

namespace App\Filament\Resources\EventPointResource\Pages;

use App\Enums\EventPointStatus;
use App\Filament\Resources\EventPointResource;
use Filament\Forms\Components;
use Filament\Resources\Pages\CreateRecord;

/**
 * @package App\Filament\Resources\EventPointResource\Pages
 */
class CreateEventPoint extends CreateRecord
{
    protected static string $resource = EventPointResource::class;

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

                            Components\TextInput::make('address')
                                ->label(__('Address'))
                                ->required(),

                            Components\RichEditor::make('description')
                                ->label(__('Description'))
                                ->disableToolbarButtons([
                                    'attachFiles',
                                    'codeBlock',
                                ])
                                ->nullable(),
                        ]),
                ])
                ->columnSpan(['lg' => 2]),

            Components\Group::make()
                ->schema([
                    Components\Card::make()
                        ->schema([
                            Components\Select::make('status')
                                ->label(__('Status'))
                                ->options(EventPointStatus::asSelectArray())
                                ->placeholder("-")
                                ->default(EventPointStatus::ACTIVE)
                                ->required(),

                            Components\Toggle::make('visibility')
                                ->label(__('Visibility'))
                                ->default(false),
                        ]),
                ])
                ->columnSpan(['lg' => 1]),
        ];
    }
}
