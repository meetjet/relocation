<?php

namespace App\Jobs;

use App\Models\Event;
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
 * Telegram send event to channel.
 *
 * @package App\Jobs
 */
class TelegramSendEventToChannelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Event $event;

    /**
     * Create a new job instance.
     *
     * @param Event $event
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
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
            $bot = $telegram->getChannelBotByCountry($this->event->country);
            $chatId = config("nutgram.event_{$this->event->country}_channel_id");

            Log::error("Try send event to channel with chatId = " . $chatId);

            $picture = $this->event->firstPicture()->first();

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
        } catch (Exception $e) {
            Log::error("Telegram send event to channel: " . $e->getMessage());
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
            route('events.show', [
                $this->event->category->slug,
                $this->event->uuid,
            ]),
            $this->event->country
        );

        // Replace tags and entities.
        $description = str($this->event->description)->replace(["<br>", "</p><p>", "&nbsp;"], ["\n", "\n\n", " "]);
        // Remove unsupported tags.
        $description = strip_tags($description, '<b><strong><i><em><u><ins><s><strike><del><a>');
        // Strip whitespace from the beginning and end of a string.
        $description = str($description)->trim()->value();

        $text = "<b>{$this->event->title}</b>";

        if ($description) {
            $text .= "\n\n{$description}";
        }

        return __("telegram.{$this->event->country}.event-add.send-to-channel", [
            'datetime' => $this->event->frontend_start_datetime,
            'text' => $text,
            'address' => $this->event->frontend_address,
            'price' => str($this->event->frontend_price)->lower(),
            'organizer' => $this->getContact(),
            'link' => '<a href="' . $link . '">' . __('Link') . '</a>',
        ]);
    }


    /**
     * @return string
     */
    public function getContact(): string
    {
        if ($this->event->custom_nickname) {
            return "@" . $this->event->custom_nickname;
        }

        if ($this->event->contact && $this->event->contact->nickname) {
            return "@" . $this->event->contact->nickname;
        }

        if ($this->event->email) {
            return $this->event->email;
        }

        if ($this->event->phone) {
            return $this->event->phone;
        }

        return str(__('Not found'))->lower();
    }
}
