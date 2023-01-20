<?php

namespace App\Observers;

use App\Jobs\TelegramAttachImagesJob;
use App\Models\Event;

class EventObserver
{
    public bool $afterCommit = true;

    /**
     * Handle the Event "created" event.
     *
     * @param Event $event
     */
    public function created(Event $event): void
    {
        if ($event->telegram_bot_type && $event->telegram_attached_images) {
            TelegramAttachImagesJob::dispatch($event);
        }
    }

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
