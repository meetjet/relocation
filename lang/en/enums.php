<?php

use App\Enums\EventCategoryStatus;
use App\Enums\EventPaymentType;
use App\Enums\PlaceStatus;
use App\Enums\EventStatus;
use App\Enums\FaqStatus;
use App\Enums\ListingCategoryStatus;
use App\Enums\ListingItemSource;
use App\Enums\ListingItemStatus;
use App\Enums\PlaceType;
use App\Enums\TelegramBotImageMimeType;
use App\Enums\TelegramBotType;

return [
    FaqStatus::class => [
        FaqStatus::CREATED => 'Created',
        FaqStatus::PUBLISHED => 'Published',
        FaqStatus::REJECTED => 'Rejected',
    ],
    ListingCategoryStatus::class => [
        ListingCategoryStatus::ACTIVE => 'Active',
        ListingCategoryStatus::INACTIVE => 'Inactive',
    ],
    ListingItemStatus::class => [
        ListingItemStatus::CREATED => 'Created',
        ListingItemStatus::PUBLISHED => 'Published',
        ListingItemStatus::REJECTED => 'Rejected',
    ],
    EventStatus::class => [
        EventStatus::CREATED => 'Created',
        EventStatus::PUBLISHED => 'Published',
        EventStatus::REJECTED => 'Rejected',
    ],
    TelegramBotType::class => [
        TelegramBotType::DEFAULT => 'Default',
        TelegramBotType::ARMENIA => 'Armenia',
        TelegramBotType::GEORGIA => 'Georgia',
        TelegramBotType::THAILAND => 'Thailand',
        TelegramBotType::TURKEY => 'Turkey',
        TelegramBotType::LITHUANIA => 'Lithuania',
    ],
    TelegramBotImageMimeType::class => [
        TelegramBotImageMimeType::JPEG => 'JPEG',
        TelegramBotImageMimeType::PNG => 'PNG',
    ],
    EventCategoryStatus::class => [
        EventCategoryStatus::ACTIVE => 'Active',
        EventCategoryStatus::INACTIVE => 'Inactive',
    ],
    PlaceStatus::class => [
        PlaceStatus::ACTIVE => 'Active',
        PlaceStatus::INACTIVE => 'Inactive',
    ],
    PlaceType::class => [
        PlaceType::BAR => 'Bar',
        PlaceType::RESTAURANT => 'Restaurant',
        PlaceType::CAFE => 'Cafe',
    ],
    EventPaymentType::class => [
        EventPaymentType::FREE => 'Free',
        EventPaymentType::PAID => 'Paid',
        EventPaymentType::DONATION => 'Donation',
    ],
    ListingItemSource::class => [
        ListingItemSource::BOT => 'Bot',
        ListingItemSource::ADMIN => 'Admin',
    ],
];
