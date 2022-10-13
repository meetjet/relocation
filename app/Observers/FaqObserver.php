<?php

namespace App\Observers;

use App\Models\Faq;
use App\Telegram\Actions\NotifyQuestionAnsweredAction;

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
        app(NotifyQuestionAnsweredAction::class)->execute($faq);
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
