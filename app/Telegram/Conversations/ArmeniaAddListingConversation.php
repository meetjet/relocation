<?php

namespace App\Telegram\Conversations;

use App\Enums\TelegramBotImageMimeType;
use App\Enums\TelegramBotType;
use App\Facades\Cities;
use App\Models\ListingCategory;
use App\Models\ListingItem;
use App\Telegram\Actions\CreateUserAction;
use Psr\SimpleCache\InvalidArgumentException;
use SergiX44\Nutgram\Conversations\InlineMenu;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\MessageTypes;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use Throwable;

/**
 * Add announcement dialog.
 *
 * @package App\Telegram\Conversations
 */
class ArmeniaAddListingConversation extends InlineMenu
{
    protected ?string $step = "start";
    protected int $maxPictures = 3;
    protected ?string $city = null;
    protected ?int $categoryId = null;
    protected ?string $title;
    protected ?string $description;
    protected ?array $pictures = null;
    protected ?int $price;
    protected ?int $announcementMessageId;


    /**
     * @param Nutgram $bot
     * @throws InvalidArgumentException
     */
    public function start(Nutgram $bot): void
    {
        $bot->sendMessage(__('telegram.armenia.listing-add.start'), [
            'parse_mode' => ParseMode::HTML,
        ]);
        $this->askCity($bot);
    }

    /**
     * @param Nutgram $bot
     * @throws InvalidArgumentException
     */
    public function askCity(Nutgram $bot): void
    {
        $menu = $this->menuText(__('telegram.armenia.listing-add.ask-city'));

        collect(Cities::asSelectArray('armenia'))->each(function ($_value, $_key) use ($menu) {
            $menu->addButtonRow(InlineKeyboardButton::make($_value, callback_data: "{$_key}@handleCity"));
        });

        $menu->orNext("handleWrongCity")->showMenu();
    }

    /**
     * @param Nutgram $bot
     * @throws InvalidArgumentException
     */
    public function handleWrongCity(Nutgram $bot): void
    {
        $this->clearButtons()->closeMenu();
        $bot->sendMessage(__('telegram.armenia.listing-add.ask-city-error'));
        $this->askCity($bot);
    }

    /**
     * @param Nutgram $bot
     * @throws InvalidArgumentException
     */
    public function handleCity(Nutgram $bot): void
    {
        $this->clearButtons()->closeMenu();
        $this->city = $bot->callbackQuery()->data;
        $bot->sendMessage(__('telegram.armenia.listing-add.ask-city-chosen', [
            'city' => Cities::getDescription("armenia", $this->city),
        ]));
        $this->askCategory($bot);
    }

    /**
     * @param Nutgram $bot
     * @throws InvalidArgumentException
     */
    public function askCategory(Nutgram $bot): void
    {
        $categoryList = ListingCategory::active()->get()->pluck('title', 'id');

        if ($categoryList->count()) {
            $menu = $this->menuText(__('telegram.armenia.listing-add.ask-category'));

            $categoryList->each(function ($_value, $_key) use ($menu) {
                $menu->addButtonRow(InlineKeyboardButton::make($_value, callback_data: "{$_key}@handleCategory"));
            });

            $menu->orNext("handleWrongCategory")->showMenu();
        } else {
            $this->askTitle($bot);
        }
    }

    /**
     * @param Nutgram $bot
     * @throws InvalidArgumentException
     */
    public function handleWrongCategory(Nutgram $bot): void
    {
        $this->clearButtons()->closeMenu();
        $bot->sendMessage(__('telegram.armenia.listing-add.ask-category-error'));
        $this->askCategory($bot);
    }

    /**
     * @param Nutgram $bot
     * @throws InvalidArgumentException
     */
    public function handleCategory(Nutgram $bot): void
    {
        $this->clearButtons()->closeMenu();
        $this->categoryId = (int)$bot->callbackQuery()->data;
        $category = ListingCategory::find($this->categoryId);
        $bot->sendMessage(__('telegram.armenia.listing-add.ask-category-chosen', [
            'category' => $category->title,
        ]));
        $this->askTitle($bot);
    }

    /**
     * @param Nutgram $bot
     * @throws InvalidArgumentException
     */
    public function askTitle(Nutgram $bot): void
    {
        $bot->sendMessage(__('telegram.armenia.listing-add.ask-title'));
        $this->next("handleTitle");
    }

    /**
     * @param Nutgram $bot
     * @throws InvalidArgumentException
     */
    public function handleTitle(Nutgram $bot): void
    {
        $message = $bot->message();

        if ($message && $message->getType() === MessageTypes::TEXT) {
            $this->title = (string)$message->text;
            $this->askDescription($bot);
            return;
        }

        // Error title type.
        $bot->sendMessage(__('telegram.armenia.listing-add.ask-title-error'), [
            'parse_mode' => ParseMode::HTML,
        ]);
        $this->askTitle($bot);
    }

    /**
     * @param Nutgram $bot
     * @throws InvalidArgumentException
     */
    public function askDescription(Nutgram $bot): void
    {
        $bot->sendMessage(__('telegram.armenia.listing-add.ask-description'));
        $this->next("handleDescription");
    }

    /**
     * @param Nutgram $bot
     * @throws InvalidArgumentException
     */
    public function handleDescription(Nutgram $bot): void
    {
        $message = $bot->message();

        if ($message && $message->getType() === MessageTypes::TEXT) {
            $this->description = (string)$message->text;
            $this->askPrice($bot);
            return;
        }

        // Error description type.
        $bot->sendMessage(__('telegram.armenia.listing-add.ask-description-error'), [
            'parse_mode' => ParseMode::HTML,
        ]);
        $this->askDescription($bot);
    }

    /**
     * @param Nutgram $bot
     * @throws InvalidArgumentException
     */
    public function askPrice(Nutgram $bot): void
    {
        $bot->sendMessage(__('telegram.armenia.listing-add.ask-price'));
        $this->next("handlePrice");
    }

    /**
     * @param Nutgram $bot
     * @throws InvalidArgumentException
     * @throws Throwable
     */
    public function handlePrice(Nutgram $bot): void
    {
        $message = $bot->message();

        if ($message && $message->getType() === MessageTypes::TEXT) {
            $price = abs((int)$message->text);

            if ($price >= 0 && (string)$price === $message->text) {
                $this->price = $price;
                $this->askPicture($bot);
                return;
            }
        }

        // Error price type.
        $bot->sendMessage(__('telegram.armenia.listing-add.ask-price-error'), [
            'parse_mode' => ParseMode::HTML,
        ]);
        $this->askPrice($bot);
    }

    /**
     * @param Nutgram $bot
     * @throws InvalidArgumentException
     */
    public function askPicture(Nutgram $bot): void
    {
        $bot->sendMessage(__('telegram.armenia.listing-add.ask-picture'));
        $this->next("handlePicture");
    }

    /**
     * @param Nutgram $bot
     * @throws InvalidArgumentException
     * @throws Throwable
     */
    public function handlePicture(Nutgram $bot): void
    {
        $message = $bot->message();

        // Compressed picture.
        if ($message && $message->getType() === MessageTypes::PHOTO) {
            // Get the picture with the maximum file size.
            $picture = collect($message->photo)
                ->map(function ($_item) {
                    return (array)$_item;
                })
                ->sortByDesc('file_size')
                ->first();
            $this->pictures[] = [
                'file_type' => MessageTypes::PHOTO,
                'file_id' => $picture['file_id'],
                'file_unique_id' => $picture['file_unique_id'],
                'caption' => $message->caption,
            ];
            $this->askMorePicture($bot);
            return;
        }

        // Attached picture.
        if ($message && $message->getType() === MessageTypes::DOCUMENT) {
            $file = $message->document;

            if (in_array($file->mime_type, TelegramBotImageMimeType::getValues(), true)) {
                $this->pictures[] = [
                    'file_type' => MessageTypes::DOCUMENT,
                    'file_id' => $file->file_id,
                    'file_unique_id' => $file->file_unique_id,
                    'caption' => $message->caption,
                ];
                $this->askMorePicture($bot);
                return;
            }

            // Wrong MIME type.
            $bot->sendMessage(__('telegram.armenia.listing-add.ask-picture-mime-error', [
                'mime_types' => implode(", ", TelegramBotImageMimeType::getKeys(TelegramBotImageMimeType::getValues())),
            ]), [
                'parse_mode' => ParseMode::HTML,
            ]);
            return;
        }

        // Error: wrong file type.
        $bot->sendMessage(__('telegram.armenia.listing-add.ask-picture-type-error'));
    }

    /**
     * @param Nutgram $bot
     * @throws InvalidArgumentException
     * @throws Throwable
     */
    public function askMorePicture(Nutgram $bot): void
    {
        if (count($this->pictures) === $this->maxPictures) {
            $this->announcementPreview($bot);
            return;
        }

        $this->menuText(__('telegram.armenia.listing-add.ask-picture-more'))
            ->addButtonRow(
                InlineKeyboardButton::make(__('Yes'), callback_data: "true"),
                InlineKeyboardButton::make(__('No'), callback_data: "false")
            )
            ->showMenu();

        $this->next("handlePictureMore");
    }

    /**
     * @param Nutgram $bot
     * @throws InvalidArgumentException
     * @throws Throwable
     */
    public function handlePictureMore(Nutgram $bot): void
    {
        if ($bot->isCallbackQuery()) {
            $isPictureMore = json_decode($bot->callbackQuery()->data, false, 512, JSON_THROW_ON_ERROR);

            if ($isPictureMore) {
                $this->clearButtons()
                    ->closeMenu(__('telegram.armenia.listing-add.ask-picture'));
                $this->next("handlePicture");
            } else {
                $this->clearButtons()
                    ->closeMenu();
                $this->announcementPreview($bot);
            }
        }
    }

    /**
     * @param Nutgram $bot
     * @throws InvalidArgumentException
     */
    public function announcementPreview(Nutgram $bot): void
    {
        $category = ListingCategory::find($this->categoryId);

        $message = $bot->sendMessage(__('telegram.armenia.listing-add.announcement-preview', [
            'city' => Cities::getDescription("armenia", $this->city),
            'category' => $category ? $category->title : __('No'),
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'images' => count($this->pictures),
        ]), [
            'parse_mode' => ParseMode::HTML,
        ]);

        $this->announcementMessageId = $message->message_id;

        // Ask confirmation.
        $this->menuText(__('telegram.armenia.listing-add.ask-confirmation'))
            ->addButtonRow(
                InlineKeyboardButton::make(__('Yes'), callback_data: "true@handleConfirmation"),
                InlineKeyboardButton::make(__('No'), callback_data: "false@handleConfirmation")
            )
            ->showMenu();
    }

    /**
     * @param Nutgram $bot
     * @throws InvalidArgumentException|Throwable
     */
    public function handleConfirmation(Nutgram $bot): void
    {
        $isConfirmed = json_decode($bot->callbackQuery()->data, false, 512, JSON_THROW_ON_ERROR);

        if ($isConfirmed) {
            ListingItem::forceCreate([
                'user_id' => app(CreateUserAction::class)->execute($bot->user()),
                'category_id' => $this->categoryId,
                'country' => "armenia",
                'city' => $this->city,
                'title' => $this->title,
                'description' => $this->description,
                'price' => $this->price,
                'currency' => config('countries.armenia.currency.code'),
                'telegram_bot_type' => TelegramBotType::ARMENIA,
                'telegram_user_id' => $bot->userId(),
                'telegram_user_language_code' => $bot->user()->language_code,
                'telegram_chat_id' => $bot->chatId(),
                'telegram_message_id' => $this->announcementMessageId,
                'telegram_attached_images' => $this->pictures,
            ]);

            $this->clearButtons()
                ->closeMenu(__('telegram.armenia.listing-add.confirmation-successful'));
        } else {
            $this->clearButtons()
                ->closeMenu(__('telegram.armenia.listing-add.confirmation-canceled'));
        }

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
        return __('telegram.armenia.listing-add.description');
    }
}
