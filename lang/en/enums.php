<?php

use App\Enums\Countries;
use App\Enums\FaqStatus;

return [
    FaqStatus::class => [
        FaqStatus::CREATED => 'Created',
        FaqStatus::PUBLISHED => 'Published',
        FaqStatus::REJECTED => 'Rejected',
    ],
    Countries::class => [
        Countries::ARM => 'Armenia',
        Countries::GEO => 'Georgia',
        Countries::TUR => 'Turkey',
    ],
];
