<?php

namespace App\Telegram\Conversations;

use App\Enums\ListingItemSource;
use App\Enums\TelegramBotImageMimeType;
use App\Enums\TelegramBotType;
use App\Facades\Locations;
use App\Models\ListingCategory;
use App\Models\ListingItem;
use App\Telegram\Actions\CreateUserAction;
use Illuminate\Support\Str;
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
class GeorgiaAddListingConversation extends InlineMenu
{
    protected ?string $step = "start";
    protected int $maxPictures = 3;
    protected ?string $location = null;
    protected ?int $categoryId = null;
    protected ?string $title;
    protected ?string $description;
    protected ?array $pictures = null;
    protected ?int $price;
    protected ?string $customContactValue = null;
    protected ?string $customContactType = null;
    protected ?int $announcementMessageId;


    /**
     * @param Nutgram $bot
     * @throws InvalidArgumentException
     */
    public function start(Nutgram $bot): void
    {
        $bot->sendMessage(__('telegram.georgia.listing-add.start'), [
            'parse_mode' => ParseMode::HTML,
        ]);
        $this->askLocation($bot);
    }

    /**
     * @param Nutgram $bot
     * @throws InvalidArgumentException
     */
    public function askLocation(Nutgram $bot): void
    {
        $menu = $this->menuText(__('telegram.georgia.listing-add.ask-location'));

        collect(Locations::asSelectArray('georgia'))->each(function ($_value, $_key) use ($menu) {
            $menu->addButtonRow(InlineKeyboardButton::make($_value, callback_data: "{$_key}@handleLocation"));
        });

        $menu->orNext("handleWrongLocation")->showMenu();
    }

    /**
     * @param Nutgram $bot
     * @throws InvalidArgumentException
     */
    public function handleWrongLocation(Nutgram $bot): void
    {
        $this->clearButtons()->closeMenu();
        $bot->sendMessage(__('telegram.georgia.listing-add.ask-location-error'));
        $this->askLocation($bot);
    }

    /**
     * @param Nutgram $bot
     * @throws InvalidArgumentException
     */
    public function handleLocation(Nutgram $bot): void
    {
        $this->location = $bot->callbackQuery()->data;
        $this->clearButtons()->closeMenu(__('telegram.georgia.listing-add.ask-location-chosen', [
            'location' => Locations::getDescription("georgia", $this->location),
        ]));
        $this->askCategory($bot);
    }

    /**
     * @param Nutgram $bot
     * @throws InvalidArgumentException
     */
    public function askCategory(Nutgram $bot): void
    {
        $categoryList = ListingCategory::active()->orderBy('id')->get()->pluck('title', 'id');

        if ($categoryList->count()) {
            $menu = $this->menuText(__('telegram.georgia.listing-add.ask-category'));

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
        $bot->sendMessage(__('telegram.georgia.listing-add.ask-category-error'));
        $this->askCategory($bot);
    }

    /**
     * @param Nutgram $bot
     * @throws InvalidArgumentException
     */
    public function handleCategory(Nutgram $bot): void
    {
        $this->categoryId = (int)$bot->callbackQuery()->data;
        $category = ListingCategory::find($this->categoryId);
        $this->clearButtons()->closeMenu(__('telegram.georgia.listing-add.ask-category-chosen', [
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
        $bot->sendMessage(__('telegram.georgia.listing-add.ask-title'));
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
        $bot->sendMessage(__('telegram.georgia.listing-add.ask-title-error'), [
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
        $bot->sendMessage(__('telegram.georgia.listing-add.ask-description'));
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
        $bot->sendMessage(__('telegram.georgia.listing-add.ask-description-error'), [
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
        $bot->sendMessage(__('telegram.georgia.listing-add.ask-price'));
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
            $price = (int)Str::remove(" ", $message->text);

            if ($price >= 0) {
                $this->price = $price;
                $this->askPicture($bot);
                return;
            }
        }

        // Error price type.
        $bot->sendMessage(__('telegram.georgia.listing-add.ask-price-error'), [
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
        $bot->sendMessage(__('telegram.georgia.listing-add.ask-picture'));
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
            $bot->sendMessage(__('telegram.georgia.listing-add.ask-picture-mime-error', [
                'mime_types' => implode(", ", TelegramBotImageMimeType::getKeys(TelegramBotImageMimeType::getValues())),
            ]), [
                'parse_mode' => ParseMode::HTML,
            ]);
            return;
        }

        // Error: wrong file type.
        $bot->sendMessage(__('telegram.georgia.listing-add.ask-picture-type-error'));
    }

    /**
     * @param Nutgram $bot
     * @throws InvalidArgumentException
     * @throws Throwable
     */
    public function askMorePicture(Nutgram $bot): void
    {
        if (count($this->pictures) === $this->maxPictures) {
            $this->askContact($bot);
            return;
        }

        $this->menuText(__('telegram.georgia.listing-add.ask-picture-more'))
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
                $this->clearButtons()->closeMenu(__('telegram.georgia.listing-add.ask-picture'));
                $this->next("handlePicture");
            } else {
                $this->clearButtons()->closeMenu();
                $this->askContact($bot);
            }
        }
    }

    /**
     * @param Nutgram $bot
     * @throws InvalidArgumentException
     */
    public function askContact(Nutgram $bot): void
    {
        $user = $bot->user();

        if ($user && $user->username) {
            $this->announcementPreview($bot);
            return;
        }

        $bot->sendMessage(__('telegram.georgia.listing-add.ask-contact'));
        $this->next("handleContact");
    }

    /**
     * @param Nutgram $bot
     * @throws InvalidArgumentException
     */
    public function handleContact(Nutgram $bot): void
    {
        $message = $bot->message();

        if ($message && $message->getType() === MessageTypes::TEXT) {
            $contactValue = Str::remove(" ", $message->text);

            if (filter_var($contactValue, FILTER_VALIDATE_EMAIL)) {
                // Email received.
                $this->customContactValue = $contactValue;
                $this->customContactType = "email";
                $this->announcementPreview($bot);
                return;
            }

            $phoneNumber = parsePhoneNumber($contactValue, "georgia");

            if ($phoneNumber && Str::length($phoneNumber) >= 6) {
                // Phone number received.
                $this->customContactValue = $phoneNumber;
                $this->customContactType = "phone";
                $this->announcementPreview($bot);
                return;
            }

            // Error contact value.
            $bot->sendMessage(__('telegram.georgia.listing-add.ask-contact-error-value'), [
                'parse_mode' => ParseMode::HTML,
            ]);
            $this->askContact($bot);
            return;
        }

        // Error contact format.
        $bot->sendMessage(__('telegram.georgia.listing-add.ask-contact-error-format'), [
            'parse_mode' => ParseMode::HTML,
        ]);
        $this->askContact($bot);
    }

    /**
     * @param Nutgram $bot
     * @throws InvalidArgumentException
     */
    public function announcementPreview(Nutgram $bot): void
    {
        $category = ListingCategory::find($this->categoryId);

        if ($this->customContactType === "email") {
            $contact = $this->customContactValue;
        } elseif ($this->customContactType === "phone") {
            $contact = formatPhoneNumber($this->customContactValue, "georgia");
        } else {
            $contact = "@{$bot->user()->username}";
        }

        $message = $bot->sendMessage(__('telegram.georgia.listing-add.announcement-preview', [
            'location' => Locations::getDescription("georgia", $this->location),
            'category' => $category ? $category->title : __('No'),
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'images' => count($this->pictures),
            'contact' => $contact,
        ]), [
            'parse_mode' => ParseMode::HTML,
        ]);

        $this->announcementMessageId = $message->message_id;

        // Ask confirmation.
        $this->menuText(__('telegram.georgia.listing-add.ask-confirmation'))
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
                'country' => "georgia",
                'location' => $this->location,
                'title' => $this->title,
                'original' => $this->description,
                'price' => $this->price,
                'currency' => config('countries.georgia.currency.code'),
                'email' => $this->customContactType === "email" ? $this->customContactValue : null,
                'phone' => $this->customContactType === "phone" ? $this->customContactValue : null,
                'source' => ListingItemSource::BOT,
                'telegram_bot_type' => TelegramBotType::GEORGIA,
                'telegram_user_id' => $bot->userId(),
                'telegram_user_language_code' => $bot->user()->language_code,
                'telegram_chat_id' => $bot->chatId(),
                'telegram_message_id' => $this->announcementMessageId,
                'telegram_attached_images' => $this->pictures,
            ]);

            $this->clearButtons()->closeMenu(__('telegram.georgia.listing-add.confirmation-successful', [
                'link' => addSubdomainToUrl(route('listings.index'), "georgia"),
            ]));
        } else {
            $this->clearButtons()->closeMenu(__('telegram.georgia.listing-add.confirmation-canceled'));
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
        return __('telegram.georgia.listing-add.description');
    }
}
