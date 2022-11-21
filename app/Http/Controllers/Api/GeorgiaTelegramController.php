<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Telegram\Commands\GeorgiaStartCommand;
use App\Telegram\Conversations\GeorgiaAddListingConversation;
use App\Telegram\Conversations\GeorgiaAskQuestionConversation;
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

class GeorgiaTelegramController extends Controller
{
    public function __invoke(Telegram $telegram): void
    {
        try {
            $bot = $telegram->getGeorgiaBot();
            $bot->middleware(AuthMiddleware::class);
//            $bot->middleware(LocaleMiddleware::class); // TODO: temporarily hidden

            $bot->onCommand(GeorgiaStartCommand::getName(), GeorgiaStartCommand::class)
                ->description(GeorgiaStartCommand::getDescription());

            // TODO: works but temporarily hidden
//            $bot->onCommand(GeorgiaAskQuestionConversation::getName(), GeorgiaAskQuestionConversation::class)
//                ->description(GeorgiaAskQuestionConversation::getDescription());

            $bot->onCommand(GeorgiaAddListingConversation::getName(), GeorgiaAddListingConversation::class)
                ->description(GeorgiaAddListingConversation::getDescription());

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
