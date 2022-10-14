<?php

namespace App\Filament\Actions\Tables;

use Filament\Tables\Actions\DeleteAction as DefaultDeleteAction;

class DeleteAction extends DefaultDeleteAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->modalHeading(fn(): string => __('filament-support::actions/delete.single.label'));
    }
}
