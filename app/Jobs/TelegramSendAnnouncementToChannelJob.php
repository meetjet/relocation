<?php

namespace App\Jobs;

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
class TelegramSendAnnouncementToChannelJob implements ShouldQueue
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
            $bot = $telegram->getChannelBotByCountry($this->listingItem->country);
            $chatId = config("nutgram.listing_{$this->listingItem->country}_channel_id");
            $picture = $this->listingItem->firstPicture()->first();

            if ($picture) {
                $bot->sendPhoto($picture->medium, [
                    // @see https://core.telegram.org/bots/api#sendphoto
                    'chat_id' => $chatId,
                    'caption' => $this->getMessageText(),
                    'parse_mode' => ParseMode::HTML,
                    'allow_sending_without_reply' => true,
                ]);
            } else {
                $bot->sendMessage($this->getMessageText(), [
                    // @see https://core.telegram.org/bots/api#sendmessage
                    'chat_id' => $chatId,
                    'parse_mode' => ParseMode::HTML,
                    'disable_web_page_preview' => true,
                    'allow_sending_without_reply' => true,
                ]);
            }
            Log::debug("Try send announcement to channel with chatId = " . $chatId);
        } catch (Exception $e) {
            Log::error("Telegram send announcement to channel: " . $e->getMessage());
        } catch (Throwable $e) {
            Log::error($e);
        }
    }

    /**
     * @return string
     */
    private function getMessageText(): string
    {
        $link = addSubdomainToUrl(
            route('listings.show', [
                $this->listingItem->category->slug,
                $this->listingItem->uuid,
            ]),
            $this->listingItem->country
        );

        // TODO: remove unsupported tags from description
        $description = strip_tags($this->listingItem->description, '<b><strong><i><em><u><ins><s><strike><del><a>');

        $text = "<b>{$this->listingItem->title}</b>";

        if ($description) {
            $text .= "\n\n{$description}";
        }

        return __("telegram.{$this->listingItem->country}.listing-add.send-to-channel", [
            'location' => locations()->getDescription($this->listingItem->country, $this->listingItem->location),
            'text' => $text,
            'price' => $this->listingItem->price . ' ' . currencies()->getSign($this->listingItem->currency),
            'contact' => $this->getContact(),
            'link' => '<a href="' . $link . '">' . __('Link') . '</a>',
        ]);
    }


    /**
     * @return string
     */
    public function getContact(): string
    {
        if ($this->listingItem->custom_nickname) {
            return "@" . $this->listingItem->custom_nickname;
        }

        if ($this->listingItem->contact && $this->listingItem->contact->nickname) {
            return "@" . $this->listingItem->contact->nickname;
        }

        if ($this->listingItem->email) {
            return $this->listingItem->email;
        }

        if ($this->listingItem->phone) {
            return $this->listingItem->phone;
        }

        return str(__('Not found'))->lower();
    }
}
