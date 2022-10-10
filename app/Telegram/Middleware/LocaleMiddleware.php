<?php

namespace App\Telegram\Middleware;

use Illuminate\Support\Facades\App;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use SergiX44\Nutgram\Middleware\Link;
use SergiX44\Nutgram\Nutgram;

class LocaleMiddleware
{
    /**
     * @param Nutgram $bot
     * @param Link $next
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(Nutgram $bot, Link $next): void
    {
        $user = $bot->user();

        if ($user) {
            App::setLocale($user->language_code);
        }

        $next($bot);
    }
}
