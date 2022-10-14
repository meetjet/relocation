<?php

namespace App\Telegram\Handlers;

use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Nutgram;
use Throwable;

class ApiErrorHandler
{
    public function __invoke(Nutgram $bot, Throwable $e): void
    {
        Log::error("Telegram outgoing request: " . $e->getMessage());
    }
}
