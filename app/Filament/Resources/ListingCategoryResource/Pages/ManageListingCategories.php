<?php

namespace App\Filament\Resources\ListingCategoryResource\Pages;

use App\Filament\Resources\ListingCategoryResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageListingCategories extends ManageRecords
{
    protected static string $resource = ListingCategoryResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
