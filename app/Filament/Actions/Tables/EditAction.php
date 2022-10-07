<?php

namespace App\Filament\Actions\Tables;

use Filament\Tables\Actions\EditAction as DefaultEditAction;

class EditAction extends DefaultEditAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->modalHeading(fn(): string => __('filament-support::actions/edit.single.label'));
    }
}
