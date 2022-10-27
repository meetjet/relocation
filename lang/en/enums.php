<?php

use App\Enums\Countries;
use App\Enums\FaqStatus;
use App\Enums\ListingCategoryStatus;
use App\Enums\ListingItemStatus;
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
    Countries::class => [
        Countries::ARM => 'Armenia',
        Countries::GEO => 'Georgia',
        Countries::TUR => 'Turkey',
    ],
    TelegramBotType::class => [
        TelegramBotType::DEFAULT => 'Default',
        TelegramBotType::ARMENIA => 'Armenia',
        TelegramBotType::GEORGIA => 'Georgia',
        TelegramBotType::TURKEY => 'Turkey',
    ],
];
