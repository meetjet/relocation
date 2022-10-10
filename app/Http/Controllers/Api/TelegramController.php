<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Telegram\Commands\StartCommand;
use App\Telegram\Conversations\AskQuestionConversation;
use App\Telegram\Handlers\ExceptionHandler;
use App\Telegram\Handlers\FallbackHandler;
use App\Telegram\Middleware\AuthMiddleware;
use App\Telegram\Middleware\LocaleMiddleware;
use Illuminate\Support\Facades\Log;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use SergiX44\Nutgram\Nutgram;

class TelegramController extends Controller
{
    /**
     * @param Nutgram $bot
     */
    public function __invoke(Nutgram $bot): void
    {
        try {
            $bot->middleware(AuthMiddleware::class);
            $bot->middleware(LocaleMiddleware::class);

            $bot->onCommand(StartCommand::getName(), StartCommand::class)
                ->description(StartCommand::getDescription());

            $bot->onCommand(AskQuestionConversation::getName(), AskQuestionConversation::class)
                ->description(AskQuestionConversation::getDescription());

            $bot->registerMyCommands();
            $bot->fallback(FallbackHandler::class);
            $bot->onException(ExceptionHandler::class);
            $bot->run();
        } catch (NotFoundExceptionInterface | ContainerExceptionInterface $e) {
            Log::error($e);
        }
    }
}
