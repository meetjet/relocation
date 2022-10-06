<?php

use App\Enums\FaqStatus;

return [
    FaqStatus::class => [
        FaqStatus::CREATED => 'Создано',
        FaqStatus::PUBLISHED => 'Опубликовано',
        FaqStatus::REJECTED => 'Отклонено',
    ],
];
