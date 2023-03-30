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
        FaqStatus::CREATED => 'Создан',
        FaqStatus::PUBLISHED => 'Опубликован',
        FaqStatus::REJECTED => 'Отклонён',
    ],
    CategoryStatus::class => [
        CategoryStatus::ACTIVE => 'Active',
        CategoryStatus::INACTIVE => 'Inactive',
    ],
    ListingCategoryStatus::class => [
        ListingCategoryStatus::ACTIVE => 'Активна',
        ListingCategoryStatus::INACTIVE => 'Неактивна',
    ],
    ListingItemStatus::class => [
        ListingItemStatus::CREATED => 'Создано',
        ListingItemStatus::PUBLISHED => 'Опубликовано',
        ListingItemStatus::REJECTED => 'Отклонено',
    ],
    EventStatus::class => [
        EventStatus::CREATED => 'Создано',
        EventStatus::PUBLISHED => 'Опубликовано',
        EventStatus::REJECTED => 'Отклонено',
    ],
    TelegramBotType::class => [
        TelegramBotType::DEFAULT => 'По умолчанию',
        TelegramBotType::ARMENIA => 'Армения',
        TelegramBotType::GEORGIA => 'Грузия',
        TelegramBotType::THAILAND => 'Таиланд',
        TelegramBotType::TURKEY => 'Турция',
        TelegramBotType::LITHUANIA => 'Литва',
    ],
    TelegramBotImageMimeType::class => [
        TelegramBotImageMimeType::JPEG => 'JPEG',
        TelegramBotImageMimeType::PNG => 'PNG',
    ],
    PlaceStatus::class => [
        PlaceStatus::ACTIVE => 'Активно',
        PlaceStatus::INACTIVE => 'Неактивно',
    ],
    PlaceType::class => [
        PlaceType::BAR => 'Бар',
        PlaceType::RESTAURANT => 'Ресторан',
        PlaceType::CAFE => 'Кафе',
    ],
    EventPaymentType::class => [
        EventPaymentType::FREE => 'Бесплатно',
        EventPaymentType::PAID => 'Платно',
        EventPaymentType::DONATION => 'Донат',
    ],
    ListingItemSource::class => [
        ListingItemSource::BOT => 'Бот',
        ListingItemSource::ADMIN => 'Админка',
    ],
    PropertyStatus::class => [
        PropertyStatus::CREATED => 'Создано',
        PropertyStatus::PUBLISHED => 'Опубликовано',
        PropertyStatus::REJECTED => 'Отклонено',
    ],
    PropertyType::class => [
        PropertyType::APARTMENT => 'Квартира',
        PropertyType::HOME => 'Дом',
    ],
    PropertyRoomsNumber::class => [
        PropertyRoomsNumber::ONE_ROOM => 'Одна комната',
        PropertyRoomsNumber::TWO_ROOMS => 'Две комнаты',
        PropertyRoomsNumber::THREE_ROOMS => 'Три комнаты',
        PropertyRoomsNumber::FOUR_ROOMS => 'Четыре комнаты',
        PropertyRoomsNumber::FIVE_ROOMS => 'Пять комнат',
    ],
    AdvertisementStatus::class => [
        AdvertisementStatus::CREATED => 'Создано',
        AdvertisementStatus::PUBLISHED => 'Опубликовано',
        AdvertisementStatus::REJECTED => 'Отклонено',
    ],
];
