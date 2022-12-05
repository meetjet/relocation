<?php

namespace App\Filament\Actions\Pages;

use Filament\Pages\Actions\RestoreAction as DefaultRestoreAction;

class RestoreAction extends DefaultRestoreAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->modalHeading(fn(): string => __('filament-support::actions/restore.single.label'));
    }
}
