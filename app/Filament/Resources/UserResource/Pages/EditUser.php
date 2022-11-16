<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Forms\Components\ConnectedAccount;
use App\Models\User;
use App\Traits\PageListHelpers;
use Exception;
use Filament\Forms\Components;
use App\Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Rules\Password;

class EditUser extends EditRecord
{
    use PageListHelpers;

    protected static string $resource = UserResource::class;

    /**
     * @return array
     * @throws Exception
     */
    protected function getActions(): array
    {
        return [
            Actions\Pages\DeleteAction::make(),
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
                            Components\TextInput::make('name')
                                ->label(__('Name'))
                                ->required(),

                            Components\TextInput::make('email')
                                ->label(__('Email'))
                                ->email()
                                ->required(),

                            Components\TextInput::make('new_password')
                                ->label(__('New password'))
                                ->rules(['nullable', 'string', new Password]),
                        ])
                        ->columns(),

                    Components\Section::make(__('Connected account information'))
                        ->schema([
                            Components\ViewField::make('contact.avatar_path')
                                ->view('forms.components.contact-avatar')
                                ->label(__('Avatar'))
                                ->hidden(fn(User $record): bool => is_null($record->contact->avatar_path)),

                            Components\Placeholder::make('contact.name')
                                ->label(__('Name'))
                                ->content(fn(User $record): string => $record->contact->name)
                                ->hidden(fn(User $record): bool => is_null($record->contact->name)),

                            Components\Placeholder::make('contact.nickname')
                                ->label(__('Nickname'))
                                ->content(fn(User $record): string => $record->contact->nickname)
                                ->hidden(fn(User $record): bool => is_null($record->contact->nickname)),

                            Components\Placeholder::make('contact.email')
                                ->label(__('Email'))
                                ->content(fn(User $record): string => $record->contact->email)
                                ->hidden(fn(User $record): bool => is_null($record->contact->email)),

                            Components\Placeholder::make('contact.telephone')
                                ->label(__('Phone number'))
                                ->content(fn(User $record): string => $record->contact->telephone)
                                ->hidden(fn(User $record): bool => is_null($record->contact->telephone)),
                        ])->hidden(fn(User $record): bool => is_null($record->contact)),
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
        if ($data['new_password']) {
            $data['password'] = Hash::make($data['new_password']);
        }
        unset($data['new_password']);

        return $data;
    }

    /**
     * @return string
     */
    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? self::getResource()::getUrl('index');
    }
}
