<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Telegram\Commands\ArmeniaStartCommand;
use App\Telegram\Conversations\ArmeniaAddListingConversation;
use App\Telegram\Conversations\ArmeniaAskQuestionConversation;
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

class ArmeniaTelegramController extends Controller
{
    public function __invoke(Telegram $telegram): void
    {
        try {
            $bot = $telegram->getArmeniaBot();
            $bot->middleware(AuthMiddleware::class);
//            $bot->middleware(LocaleMiddleware::class); // TODO: temporarily hidden

            $bot->onCommand(ArmeniaStartCommand::getName(), ArmeniaStartCommand::class)
                ->description(ArmeniaStartCommand::getDescription());

            // TODO: works but temporarily hidden
//            $bot->onCommand(ArmeniaAskQuestionConversation::getName(), ArmeniaAskQuestionConversation::class)
//                ->description(ArmeniaAskQuestionConversation::getDescription());

            $bot->onCommand(ArmeniaAddListingConversation::getName(), ArmeniaAddListingConversation::class)
                ->description(ArmeniaAddListingConversation::getDescription());

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
