<?php

namespace App\Observers;

use App\Models\Advertisement;

class AdvertisementObserver
{
    public bool $afterCommit = true;

    /**
     * Handle the Advertisement "forceDeleted" event.
     *
     * @param Advertisement $advertisement
     * @return void
     */
    public function forceDeleted(Advertisement $advertisement): void
    {
        $advertisement->pictures->each(function ($_picture) {
            $_picture->delete();
        });
    }
}
