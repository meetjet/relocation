<?php

namespace App\Telegram\Actions;

use App\Enums\FaqStatus;
use App\Models\Faq;
use Exception;
use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;
use Throwable;

class NotifyQuestionAnsweredAction
{
    /**
     * @param Faq $faq
     */
    public function execute(Faq $faq): void
    {
        if ($faq->status === FaqStatus::PUBLISHED && is_null($faq->telegram_published_notify_sent)) {
            try {
                app(Nutgram::class)
                    ->sendMessage(__('telegram.bot.question.reply', [
//                    'link' => route("faq"), // TODO: replace to route
                        'link' => "https://relocation.digital/faqs/{$faq->slug}",
                    ], $faq->telegram_user_language_code), [
                        // @see https://core.telegram.org/bots/api#sendmessage
                        'chat_id' => $faq->telegram_chat_id,
                        'reply_to_message_id' => $faq->telegram_message_id,
                        'parse_mode' => ParseMode::HTML,
                        'disable_web_page_preview' => true,
                        'allow_sending_without_reply' => true,
                    ]);

                $faq->forceFill([
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
