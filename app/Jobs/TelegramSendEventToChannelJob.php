<?php

namespace App\Jobs;

use App\Facades\Locations;
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

            logger("Trying to send event {$this->event->id} to channel with chat ID = {$chatId}");

            $picture = $this->event->firstPicture()->first();

            if ($picture && $picture->uploadio_file_path) {
                $message = $bot->sendPhoto($picture->medium, [
                    // @see https://core.telegram.org/bots/api#sendphoto
                    'chat_id' => $chatId,
                    'caption' => $this->getMessageText(),
                    'parse_mode' => ParseMode::HTML,
                    'allow_sending_without_reply' => true,
                ]);
            } else {
                $message = $bot->sendMessage($this->getMessageText(), [
                    // @see https://core.telegram.org/bots/api#sendmessage
                    'chat_id' => $chatId,
                    'parse_mode' => ParseMode::HTML,
                    'disable_web_page_preview' => true,
                    'allow_sending_without_reply' => true,
                ]);
            }

            $this->event->forceFill([
                'telegram_to_channel_sent' => true,
                'telegram_chat_id' => $chatId,
                'telegram_message_id' => $message->message_id,
            ])->saveQuietly();
        } catch (Exception $e) {
            Log::error("Telegram send event {$this->event->id} to channel: " . $e->getMessage());
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
        $description = strip_tags($description->value(), '<b><strong><i><em><u><ins><s><strike><del><a>');
        // Remove redundant line breaks.
        $description = strReplace("\n\n\n", "\n\n", $description);

        $descriptionString = str($description);
        // Get a template for the "Learn more" button.
        $moreTemplate = config('filament.rich_editor_more_template');

        if ($descriptionString->contains($moreTemplate)) {
            // Add "Learn more" link to description.
            $description = $descriptionString->before($moreTemplate)->trim()->value();
            $description .= "\n\n" . '<a href="' . $link . '">' . __('Learn more on the site') . '</a>';
        } else {
            $description = $descriptionString->trim()->value();
        }

        $text = "<b>{$this->event->title}</b>";

        if ($description) {
            $text .= "\n\n{$description}";
        }

        return __("telegram.{$this->event->country}.event-add.send-to-channel", [
            'datetime' => $this->event->frontend_start_datetime,
            'text' => $text,
            'address' => $this->getAddress(),
            'price' => $this->getPrice(),
            'organizer' => $this->getContact(),
            'link' => '<a href="' . $link . '">' . __('Link') . '</a>',
        ]);
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        $address = $this->event->frontend_address;

        if ($this->event->location) {
            $address = $address
                ? Locations::getDescription($this->event->country, $this->event->location) . ', ' . $address
                : Locations::getDescription($this->event->country, $this->event->location);
        }

        return $address ?? str(__("No"))->lower()->value();
    }

    /**
     * @return string|null
     */
    public function getPrice(): ?string
    {
        if ($this->event->frontend_price) {
            return "\n\n&#128176;" . __('Price') . ": " . str($this->event->frontend_price)->lower()->value();
        }

        return null;
    }

    /**
     * @return string
     */
    public function getContact(): string
    {
        if ($this->event->email) {
            return $this->event->email;
        }

        if ($this->event->phone) {
            return $this->event->phone;
        }

        if ($this->event->custom_nickname) {
            return "@" . $this->event->custom_nickname;
        }

        if ($this->event->contact && $this->event->contact->nickname) {
            return "@" . $this->event->contact->nickname;
        }

        return str(__('Not found'))->lower()->value();
    }
}
