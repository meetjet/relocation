<?php

namespace App\Observers;

use App\Models\Faq;

class FaqObserver
{
    public bool $afterCommit = true;

    /**
     * Handle the Faq "created" event.
     *
     * @param Faq $faq
     * @return void
     */
    public function created(Faq $faq): void
    {
        // Default slug for new questions from the bot. Required for the correct functioning of the admin panel.
        if ($faq->telegram_bot_type && is_null($faq->title)) {
            $faq->update(['slug' => "new-faq-{$faq->id}"]);
        }
    }
}
