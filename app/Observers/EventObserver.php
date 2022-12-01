<?php

namespace App\Observers;

use App\Enums\EventStatus;
use App\Jobs\TelegramAttachImagesJob;
use App\Jobs\TelegramNotifyAnnouncementPublishedJob;
use App\Jobs\TelegramSendAnnouncementToChannelJob;
use App\Jobs\TelegramSendEventToChannelJob;
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

    /**
     * Handle the Event "updated" event.
     *
     * @param Event $event
     * @return void
     */
    public function updated(Event $event): void
    {
        if (
            $event->status === EventStatus::PUBLISHED
            && $event->visibility === true
            && $event->country
            && $event->uuid
            && is_null($event->telegram_to_channel_sent)
        ) {
            TelegramSendEventToChannelJob::dispatch($event);

            $event->forceFill([
                'telegram_to_channel_sent' => true,
            ])->save();
        }
    }
}
