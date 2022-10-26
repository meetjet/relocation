<?php

namespace App\Telegram\Conversations;

use App\Enums\Countries;
use App\Enums\TelegramBotType;
use App\Models\ListingItem;
use App\Telegram\Actions\CreateUserAction;
use Illuminate\Support\Str;
use Psr\SimpleCache\InvalidArgumentException;
use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\MessageTypes;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use Throwable;

/**
 * Add announcement dialog.
 *
 * @package App\Telegram\Conversations
 */
class ArmenianAddListingConversation extends Conversation
{
    protected ?string $step = "askTitle";
    public string $title;
    public array|null $images = null;

    /**
     * @param Nutgram $bot
     * @throws InvalidArgumentException
     */
    public function askTitle(Nutgram $bot): void
    {
        $bot->sendMessage(__('telegram.armenian.listing-add.ask-title'));
        $this->next("checkTitle");
    }

    /**
     * @param Nutgram $bot
     * @throws InvalidArgumentException
     */
    public function checkTitle(Nutgram $bot): void
    {
        $message = $bot->message();

        if (is_null($message) || $message->getType() !== MessageTypes::TEXT) {
            $this->askTitle($bot);
            return;
        }

        $this->title = (string)$message->text;
        $this->askImage($bot);
    }

    /**
     * @param Nutgram $bot
     * @throws InvalidArgumentException
     */
    public function askImage(Nutgram $bot): void
    {
        $bot->sendMessage(__('telegram.armenian.listing-add.ask-image'), [
            'reply_markup' => InlineKeyboardMarkup::make()
                ->addRow(
                    InlineKeyboardButton::make(__('Yes'), callback_data: "true"),
                    InlineKeyboardButton::make(__('No'), callback_data: "false")
                ),
        ]);
        $this->next("checkReplyImage");
    }

    /**
     * @param Nutgram $bot
     * @throws InvalidArgumentException
     */
    public function askMoreImage(Nutgram $bot): void
    {
        $bot->sendMessage(__('telegram.armenian.listing-add.ask-more-image'), [
            'reply_markup' => InlineKeyboardMarkup::make()
                ->addRow(
                    InlineKeyboardButton::make(__('Yes'), callback_data: "true"),
                    InlineKeyboardButton::make(__('No'), callback_data: "false")
                ),
        ]);
        $this->next("checkReplyImage");
    }

    /**
     * @param Nutgram $bot
     * @throws InvalidArgumentException|Throwable
     */
    public function checkReplyImage(Nutgram $bot): void
    {
        if ($bot->isCallbackQuery()) {
            $isAttachImage = json_decode($bot->callbackQuery()->data);

            if ($isAttachImage) {
                $this->attachImage($bot);
            } else {
                $this->done($bot);
            }
        } else {
            $message = $bot->message();

            if ($message && $message->getType() !== MessageTypes::DOCUMENT) {
                if (is_null($this->images)) {
                    $this->askImage($bot);
                } else {
                    $this->askMoreImage($bot);
                }
            }
        }
    }

    /**
     * @param Nutgram $bot
     * @throws InvalidArgumentException
     */
    public function attachImage(Nutgram $bot): void
    {
        $bot->sendMessage(__('telegram.armenian.listing-add.attach-image', [
            'command' => Str::lower(__('Cancel')),
        ]));
        $this->next("checkImage");

    }

    /**
     * @param Nutgram $bot
     * @throws InvalidArgumentException|Throwable
     */
    public function checkImage(Nutgram $bot): void
    {
        $message = $bot->message();

        // Cancel detection.
        if ($message && $message->getType() === MessageTypes::TEXT) {
            $text = Str::lower($message->text);

            if ($text === Str::lower(__('Cancel'))) {
                $bot->sendMessage(__('telegram.armenian.listing-add.attach-image-canceled'));
                $this->done($bot);
                return;
            }

            $this->attachImage($bot);
            return;
        }

        // Wrong type detection.
        if (is_null($message) || $message->getType() !== MessageTypes::DOCUMENT) {
            $this->attachImage($bot);
            return;
        }

        // TODO: need checking MIME type!

        $this->images[] = [
            'file_id' => $message->document->file_id,
            'file_unique_id' => $message->document->file_unique_id,
            'file_name' => $message->document->file_name,
            'mime_type' => $message->document->mime_type,
            'file_size' => $message->document->file_size,
            'caption' => $message->caption,
        ];

        $bot->sendMessage(__('telegram.armenian.listing-add.attach-image-successfully', [
            'image' => $message->document->file_name,
        ]), [
            'parse_mode' => ParseMode::HTML,
            'disable_web_page_preview' => true,
        ]);
        $this->askMoreImage($bot);
    }

    /**
     * @param Nutgram $bot
     * @throws InvalidArgumentException|Throwable
     */
    public function done(Nutgram $bot): void
    {
        ListingItem::forceCreate([
            'user_id' => app(CreateUserAction::class)->execute($bot->user()),
            'country' => Countries::ARM,
            'title' => $this->title,
            'telegram_bot_type' => TelegramBotType::ARMENIAN,
            'telegram_user_id' => $bot->userId(),
            'telegram_user_language_code' => $bot->user()->language_code,
            'telegram_chat_id' => $bot->chatId(),
            'telegram_attached_images' => $this->images,
        ]);

        $bot->sendMessage(__('telegram.armenian.listing-add.end'));
        $this->end();
    }

    /**
     * @return string
     */
    public static function getName(): string
    {
        return "listing";
    }

    /**
     * @return string
     */
    public static function getDescription(): string
    {
        return __('telegram.armenian.listing-add.description');
    }
}
