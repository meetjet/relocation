<?php

namespace App\Telegram\Handlers;

use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Nutgram;
use Throwable;

class ApiErrorHandler
{
    /**
     * @param Nutgram $bot
     * @param Throwable $e
     */
    public function __invoke(Nutgram $bot, Throwable $e): void
    {
//        $bot->sendMessage('Whoops, Telegram!\nSomething went wrong!');
        Log::error($e);
    }
}
