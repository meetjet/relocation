<?php

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
    ]
];
