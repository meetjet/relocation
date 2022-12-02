<?php

namespace App\Filament\Actions\Pages;

use Filament\Pages\Actions\ForceDeleteAction as DefaultForceDeleteAction;

class ForceDeleteAction extends DefaultForceDeleteAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->modalHeading(fn(): string => __('filament-support::actions/force-delete.single.label'));
    }
}
