<?php

use App\Enums\Countries;
use App\Enums\FaqStatus;
use App\Enums\ListingCategoryStatus;
use App\Enums\ListingItemStatus;
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
    Countries::class => [
        Countries::ARM => 'Армения',
        Countries::GEO => 'Грузия',
        Countries::TUR => 'Турция',
    ],
    TelegramBotType::class => [
        TelegramBotType::DEFAULT => 'По умолчанию',
        TelegramBotType::ARMENIAN => 'Армянский',
        TelegramBotType::GEORGIAN => 'Грузинский',
        TelegramBotType::TURKISH => 'Турецкий',
    ],
];
