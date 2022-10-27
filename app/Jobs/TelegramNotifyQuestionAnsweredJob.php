<?php

namespace App\Jobs;

use App\Enums\TelegramBotType;
use App\Models\Faq;
use App\Telegram\Telegram;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;
use Throwable;

/**
 * Telegram notifies about the answer to the question.
 *
 * @package App\Jobs
 */
class TelegramNotifyQuestionAnsweredJob implements ShouldQueue
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
     * @param Telegram $telegram
     * @return void
     */
    public function handle(Telegram $telegram): void
    {
        try {
            $telegram
                ->getBotByType((string)$this->faq->telegram_bot_type)
                ->sendMessage($this->getMessageText(), [
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

    /**
     * @return string
     */
    private function getMessageText(): string
    {
        $langKey = match ((string)$this->faq->telegram_bot_type) {
            TelegramBotType::ARMENIA => "telegram.armenia.question.reply",
            default => "telegram.default.question.reply",
        };

        return __($langKey, [
            'link' => route("faqs.show", ['slug' => $this->faq->slug]),
        ], $this->faq->telegram_user_language_code);
    }
}
