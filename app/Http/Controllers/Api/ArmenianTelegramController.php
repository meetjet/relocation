<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Telegram\Commands\ArmenianStartCommand;
use App\Telegram\Conversations\ArmenianAddListingConversation;
use App\Telegram\Conversations\ArmenianAskQuestionConversation;
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

class ArmenianTelegramController extends Controller
{
    public function __invoke(Telegram $telegram): void
    {
        try {
            $bot = $telegram->getArmenianBot();
            $bot->middleware(AuthMiddleware::class);
            $bot->middleware(LocaleMiddleware::class);

            $bot->onCommand(ArmenianStartCommand::getName(), ArmenianStartCommand::class)
                ->description(ArmenianStartCommand::getDescription());

            // TODO: works but temporarily hidden
//            $bot->onCommand(ArmenianAskQuestionConversation::getName(), ArmenianAskQuestionConversation::class)
//                ->description(ArmenianAskQuestionConversation::getDescription());

            $bot->onCommand(ArmenianAddListingConversation::getName(), ArmenianAddListingConversation::class)
                ->description(ArmenianAddListingConversation::getDescription());

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
