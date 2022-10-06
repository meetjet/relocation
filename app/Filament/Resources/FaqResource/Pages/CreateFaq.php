<?php

namespace App\Filament\Resources\FaqResource\Pages;

use App\Filament\Resources\FaqResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

/**
 * @package App\Filament\Resources\FaqResource\Pages
 * @deprecated
 */
class CreateFaq extends CreateRecord
{
    protected static string $resource = FaqResource::class;
}
