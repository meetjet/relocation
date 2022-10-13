<?php

namespace App\Jobs;

use App\Enums\FaqStatus;
use App\Models\Faq;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;
use Throwable;

class SendNotifyQuestionAnsweredJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Faq $faq;

    /**
     * Create a new job instance.
     *
     * @param Faq $faq
     */
    public function __construct(Faq $faq)
    {
        $this->faq = $faq;
    }

    /**
     * Execute the job.
     *
     * @param Nutgram $bot
     * @return void
     */
    public function handle(Nutgram $bot): void
    {
        if ($this->faq->status === FaqStatus::PUBLISHED && is_null($this->faq->telegram_published_notify_sent)) {
            try {
                $bot->sendMessage(__('telegram.bot.question.reply', [
//                    'link' => route("faq"), // TODO: replace to route
                    'link' => "https://relocation.digital/faqs/{$this->faq->slug}",
                ], $this->faq->telegram_user_language_code), [
                    // @see https://core.telegram.org/bots/api#sendmessage
                    'chat_id' => $this->faq->telegram_chat_id,
                    'reply_to_message_id' => $this->faq->telegram_message_id,
                    'parse_mode' => ParseMode::HTML,
                    'disable_web_page_preview' => true,
                    'allow_sending_without_reply' => true,
                ]);

                $this->faq->forceFill([
                    'telegram_published_notify_sent' => true,
                ])->save();
            } catch (Exception $e) {
                Log::error("Telegram notifies about the answer to the question: " . $e->getMessage());
            } catch (Throwable $e) {
                Log::error($e);
            }
        }
    }
}
