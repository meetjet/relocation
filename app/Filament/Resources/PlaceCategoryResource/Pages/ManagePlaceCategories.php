<?php

namespace App\Filament\Resources\EventCategoryResource\Pages;

use App\Filament\Resources\PlaceCategoryResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePlaceCategories extends ManageRecords
{
    protected static string $resource = PlaceCategoryResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
