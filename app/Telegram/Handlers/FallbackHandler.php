<?php

namespace App\Telegram\Handlers;

use SergiX44\Nutgram\Nutgram;

class FallbackHandler
{
    /**
     * @param Nutgram $bot
     */
    public function __invoke(Nutgram $bot): void
    {
        $bot->sendMessage(__('telegram.bot.fallback'));
    }
}
