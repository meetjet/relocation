<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Exception;
use Filament\Forms\Components;
use App\Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Rules\Password;

class EditUser extends EditRecord
{
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
    protected function getFormSchema(): array
    {
        return [
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
        return self::getResource()::getUrl('index');
    }
}
