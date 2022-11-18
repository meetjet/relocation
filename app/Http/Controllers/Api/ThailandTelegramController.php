<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Telegram\Commands\ThailandStartCommand;
use App\Telegram\Conversations\ThailandAddListingConversation;
use App\Telegram\Conversations\ThailandAskQuestionConversation;
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

class ThailandTelegramController extends Controller
{
    public function __invoke(Telegram $telegram): void
    {
        try {
            $bot = $telegram->getThailandBot();
            $bot->middleware(AuthMiddleware::class);
//            $bot->middleware(LocaleMiddleware::class); // TODO: temporarily hidden

            $bot->onCommand(ThailandStartCommand::getName(), ThailandStartCommand::class)
                ->description(ThailandStartCommand::getDescription());

            // TODO: works but temporarily hidden
//            $bot->onCommand(ThailandAskQuestionConversation::getName(), ThailandAskQuestionConversation::class)
//                ->description(ThailandAskQuestionConversation::getDescription());

            $bot->onCommand(ThailandAddListingConversation::getName(), ThailandAddListingConversation::class)
                ->description(ThailandAddListingConversation::getDescription());

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
