<?php

namespace App\Observers;

use App\Enums\ListingItemStatus;
use App\Jobs\TelegramAttachImagesJob;
use App\Jobs\TelegramNotifyAnnouncementPublishedJob;
use App\Models\ListingItem;

class ListingItemObserver
{
    public bool $afterCommit = true;

    /**
     * Handle the ListingItem "created" event.
     *
     * @param ListingItem $listingItem
     */
    public function created(ListingItem $listingItem): void
    {
        if ($listingItem->telegram_bot_type && $listingItem->telegram_attached_images) {
            TelegramAttachImagesJob::dispatch($listingItem);
        }
    }

    /**
     * Handle the ListingItem "forceDeleted" event.
     *
     * @param ListingItem $listingItem
     * @return void
     */
    public function forceDeleted(ListingItem $listingItem): void
    {
        $listingItem->pictures->each(function ($_picture) {
            $_picture->delete();
        });
    }

    /**
     * Handle the ListingItem "updated" event.
     *
     * @param ListingItem $listingItem
     * @return void
     */
    public function updated(ListingItem $listingItem): void
    {
        if (
            $listingItem->status === ListingItemStatus::PUBLISHED
            && $listingItem->visibility === true
            && $listingItem->slug
            && $listingItem->telegram_chat_id
            && is_null($listingItem->telegram_published_notify_sent)
        ) {
            TelegramNotifyAnnouncementPublishedJob::dispatch($listingItem);
        }
    }
}
