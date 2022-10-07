<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Forms\Components;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Rules\Password;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

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

                    Components\TextInput::make('password')
                        ->label(__('Password'))
                        ->required()
                        ->rules(['required', 'string', new Password]),
                ])
                ->columns(),
        ];
    }

    /**
     * @param array $data
     * @return array
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['password'] = Hash::make($data['password']);

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
