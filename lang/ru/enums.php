<?php

use App\Enums\Countries;
use App\Enums\FaqStatus;

return [
    FaqStatus::class => [
        FaqStatus::CREATED => 'Создано',
        FaqStatus::PUBLISHED => 'Опубликовано',
        FaqStatus::REJECTED => 'Отклонено',
    ],
    Countries::class => [
        Countries::ARM => 'Армения',
        Countries::GEO => 'Грузия',
        Countries::TUR => 'Турция',
    ],
];
