<?php

namespace App\Observers;

use App\Models\Event;

class EventObserver
{
    public bool $afterCommit = true;

    /**
     * Handle the Event "forceDeleted" event.
     *
     * @param Event $event
     * @return void
     */
    public function forceDeleted(Event $event): void
    {
        $event->pictures->each(function ($_picture) {
            $_picture->delete();
        });
    }
}
