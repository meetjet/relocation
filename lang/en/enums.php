<?php

use App\Enums\AdvertisementStatus;
use App\Enums\CategoryStatus;
use App\Enums\EventPaymentType;
use App\Enums\PlaceStatus;
use App\Enums\EventStatus;
use App\Enums\FaqStatus;
use App\Enums\ListingCategoryStatus;
use App\Enums\ListingItemSource;
use App\Enums\ListingItemStatus;
use App\Enums\PlaceType;
use App\Enums\PropertyRoomsNumber;
use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use App\Enums\TelegramBotImageMimeType;
use App\Enums\TelegramBotType;

return [
    FaqStatus::class => [
        FaqStatus::CREATED => 'Created',
        FaqStatus::PUBLISHED => 'Published',
        FaqStatus::REJECTED => 'Rejected',
    ],
    CategoryStatus::class => [
        CategoryStatus::ACTIVE => 'Active',
        CategoryStatus::INACTIVE => 'Inactive',
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
    PropertyStatus::class => [
        PropertyStatus::CREATED => 'Created',
        PropertyStatus::PUBLISHED => 'Published',
        PropertyStatus::REJECTED => 'Rejected',
    ],
    PropertyType::class => [
        PropertyType::APARTMENT => 'Apartment',
        PropertyType::HOME => 'Home',
    ],
    PropertyRoomsNumber::class => [
        PropertyRoomsNumber::ONE_ROOM => 'One room',
        PropertyRoomsNumber::TWO_ROOMS => 'Two rooms',
        PropertyRoomsNumber::THREE_ROOMS => 'Three rooms',
        PropertyRoomsNumber::FOUR_ROOMS => 'Four rooms',
        PropertyRoomsNumber::FIVE_ROOMS => 'Five rooms',
    ],
    AdvertisementStatus::class => [
        AdvertisementStatus::CREATED => 'Created',
        AdvertisementStatus::PUBLISHED => 'Published',
        AdvertisementStatus::REJECTED => 'Rejected',
    ],
];
