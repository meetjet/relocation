<?php

namespace App\Filament\Actions\Tables;

use Filament\Tables\Actions\DeleteBulkAction as DefaultDeleteBulkAction;

class DeleteBulkAction extends DefaultDeleteBulkAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->modalHeading(fn(): string => __('filament-support::actions/delete.multiple.label'));
        $this->modalButton(__('filament-support::actions/force-delete.multiple.modal.actions.delete.label'));
    }
}
