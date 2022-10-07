<?php

namespace App\Filament\Actions\Tables;

use Filament\Tables\Actions\RestoreBulkAction as DefaultRestoreBulkAction;

class RestoreBulkAction extends DefaultRestoreBulkAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->modalHeading(fn(): string => __('filament-support::actions/restore.multiple.label'));
    }
}
