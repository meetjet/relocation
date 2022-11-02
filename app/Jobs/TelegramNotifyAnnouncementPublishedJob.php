<?php

namespace App\Jobs;

use App\Enums\TelegramBotType;
use App\Models\Faq;
use App\Models\ListingItem;
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
class TelegramNotifyAnnouncementPublishedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private ListingItem $listingItem;

    /**
     * Create a new job instance.
     *
     * @param ListingItem $listingItem
     */
    public function __construct(ListingItem $listingItem)
    {
        $this->listingItem = $listingItem;
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
                ->getBotByType((string)$this->listingItem->telegram_bot_type)
                ->sendMessage($this->getMessageText(), [
                    // @see https://core.telegram.org/bots/api#sendmessage
                    'chat_id' => $this->listingItem->telegram_chat_id,
                    'reply_to_message_id' => $this->listingItem->telegram_message_id,
                    'parse_mode' => ParseMode::HTML,
                    'disable_web_page_preview' => true,
                    'allow_sending_without_reply' => true,
                ]);

            $this->listingItem->forceFill([
                'telegram_published_notify_sent' => true,
            ])->save();
        } catch (Exception $e) {
            Log::error("Telegram notifies about the published announcement: " . $e->getMessage());
        } catch (Throwable $e) {
            Log::error($e);
        }
    }

    /**
     * @return string
     */
    private function getMessageText(): string
    {
        $langKey = match ((string)$this->listingItem->telegram_bot_type) {
            TelegramBotType::ARMENIA => "telegram.armenia.listing-add.reply",
            default => "telegram.default.listing-add.reply",
        };

        return __($langKey, [
            'link' => route("listings.show", $this->listingItem->slug),
        ], $this->listingItem->telegram_user_language_code);
    }
}
