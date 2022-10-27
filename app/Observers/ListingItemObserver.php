<?php

namespace App\Observers;

use App\Jobs\TelegramAttachImagesJob;
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
}
