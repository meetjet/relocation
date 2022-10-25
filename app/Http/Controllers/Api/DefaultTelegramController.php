<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Telegram\Commands\DefaultStartCommand;
use App\Telegram\Conversations\DefaultAskQuestionConversation;
use App\Telegram\Handlers\ApiErrorHandler;
use App\Telegram\Handlers\ExceptionHandler;
use App\Telegram\Handlers\FallbackHandler;
use App\Telegram\Middleware\AuthMiddleware;
use App\Telegram\Middleware\LocaleMiddleware;
use App\Telegram\Telegram;
use Illuminate\Support\Facades\Log;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use SergiX44\Hydrator\Exception\HydrationException;

class DefaultTelegramController extends Controller
{
    /**
     * @param Telegram $telegram
     */
    public function __invoke(Telegram $telegram): void
    {
        try {
            $bot = $telegram->getDefaultBot();
            $bot->middleware(AuthMiddleware::class);
            $bot->middleware(LocaleMiddleware::class);

            $bot->onCommand(DefaultStartCommand::getName(), DefaultStartCommand::class)
                ->description(DefaultStartCommand::getDescription());

            $bot->onCommand(DefaultAskQuestionConversation::getName(), DefaultAskQuestionConversation::class)
                ->description(DefaultAskQuestionConversation::getDescription());

            $bot->registerMyCommands();
            $bot->fallback(FallbackHandler::class);
            $bot->onException(ExceptionHandler::class);
            $bot->onApiError(ApiErrorHandler::class);
            $bot->run();
        } catch (HydrationException $e) {
            // If the request contains the wrong JSON object.
//            Log::error($e->getMessage());
        } catch (NotFoundExceptionInterface | ContainerExceptionInterface $e) {
            Log::error($e);
        }
    }
}
