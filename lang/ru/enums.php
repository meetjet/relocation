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
        FaqStatus::CREATED => 'Создан',
        FaqStatus::PUBLISHED => 'Опубликован',
        FaqStatus::REJECTED => 'Отклонён',
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
    EventCategoryStatus::class => [
        EventCategoryStatus::ACTIVE => 'Активна',
        EventCategoryStatus::INACTIVE => 'Неактивна',
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
];
