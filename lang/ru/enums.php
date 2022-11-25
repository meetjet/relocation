<?php

use App\Enums\EventCategoryStatus;
use App\Enums\EventPaymentType;
use App\Enums\EventPointStatus;
use App\Enums\EventStatus;
use App\Enums\FaqStatus;
use App\Enums\ListingCategoryStatus;
use App\Enums\ListingItemStatus;
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
    ],
    TelegramBotImageMimeType::class => [
        TelegramBotImageMimeType::JPEG => 'JPEG',
        TelegramBotImageMimeType::PNG => 'PNG',
    ],
    EventCategoryStatus::class => [
        EventCategoryStatus::ACTIVE => 'Активна',
        EventCategoryStatus::INACTIVE => 'Неактивна',
    ],
    EventPointStatus::class => [
        EventPointStatus::ACTIVE => 'Активно',
        EventPointStatus::INACTIVE => 'Неактивно',
    ],
    EventPaymentType::class => [
        EventPaymentType::FREE => 'Бесплатно',
        EventPaymentType::PAID => 'Платно',
        EventPaymentType::DONATION => 'Донат',
    ],
];
