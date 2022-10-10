<?php

namespace App\Telegram\Middleware;

use App\Telegram\Exceptions\AuthRequiredException;
use Illuminate\Support\Facades\Request;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use SergiX44\Nutgram\Middleware\Link;
use SergiX44\Nutgram\Nutgram;

class AuthMiddleware
{
    /**
     * @param Nutgram $bot
     * @param Link $next
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(Nutgram $bot, Link $next): void
    {
        $secret = Request::server('HTTP_X_TELEGRAM_BOT_API_SECRET_TOKEN');

        if (!$secret || $secret !== config('nutgram.secret')) {
            throw new AuthRequiredException();
        }

        $next($bot);
    }
}
