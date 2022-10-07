<?php

namespace App\Filament\Actions\Tables;

use Filament\Tables\Actions\ForceDeleteBulkAction as DefaultForceDeleteBulkAction;

class ForceDeleteBulkAction extends DefaultForceDeleteBulkAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->modalHeading(fn(): string => __('filament-support::actions/force-delete.multiple.label'));
    }
}
