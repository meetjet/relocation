<?php

namespace App\Observers;

use App\Models\Property;

class PropertyObserver
{
    public bool $afterCommit = true;

    /**
     * Handle the Property "forceDeleted" event.
     *
     * @param Property $property
     * @return void
     */
    public function forceDeleted(Property $property): void
    {
        $property->pictures->each(function ($_picture) {
            $_picture->delete();
        });
    }
}
