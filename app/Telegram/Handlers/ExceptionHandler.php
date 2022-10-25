<?php

namespace App\Telegram\Handlers;

use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;
use Throwable;

class ExceptionHandler
{
    public function __invoke(Nutgram $bot, Throwable $e): void
    {
        $bot->sendMessage(__('telegram.exception'), [
            'parse_mode' => ParseMode::HTML,
            'disable_web_page_preview' => true,
        ]);
        Log::error($e);
    }
}
