<?php

namespace App\Filament\Resources\EventPointResource\Pages;

use App\Filament\Resources\EventPointResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageEventPoints extends ManageRecords
{
    protected static string $resource = EventPointResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
