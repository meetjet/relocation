<?php

namespace App\Observers;

use App\Enums\FaqStatus;
use App\Jobs\TelegramNotifyQuestionAnsweredJob;
use App\Models\Faq;

class FaqObserver
{
    public $afterCommit = true;

    /**
     * Handle the Faq "created" event.
     *
     * @param Faq $faq
     * @return void
     */
    public function created(Faq $faq): void
    {
        //
    }

    /**
     * Handle the Faq "updated" event.
     *
     * @param Faq $faq
     * @return void
     */
    public function updated(Faq $faq): void
    {
        if (
            $faq->status === FaqStatus::PUBLISHED
            && $faq->telegram_chat_id
            && is_null($faq->telegram_published_notify_sent)
        ) {
            TelegramNotifyQuestionAnsweredJob::dispatch($faq);
        }
    }

    /**
     * Handle the Faq "deleted" event.
     *
     * @param Faq $faq
     * @return void
     */
    public function deleted(Faq $faq): void
    {
        //
    }

    /**
     * Handle the Faq "restored" event.
     *
     * @param Faq $faq
     * @return void
     */
    public function restored(Faq $faq): void
    {
        //
    }

    /**
     * Handle the Faq "force deleted" event.
     *
     * @param Faq $faq
     * @return void
     */
    public function forceDeleted(Faq $faq): void
    {
        //
    }
}
