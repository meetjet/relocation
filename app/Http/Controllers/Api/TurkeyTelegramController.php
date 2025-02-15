<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Telegram\Commands\TurkeyStartCommand;
use App\Telegram\Conversations\TurkeyAddListingConversation;
use App\Telegram\Conversations\TurkeyAskQuestionConversation;
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

class TurkeyTelegramController extends Controller
{
    public function __invoke(Telegram $telegram): void
    {
        try {
            $bot = $telegram->getTurkeyBot();
            $bot->middleware(AuthMiddleware::class);
//            $bot->middleware(LocaleMiddleware::class); // TODO: temporarily hidden

            $bot->onCommand(TurkeyStartCommand::getName(), TurkeyStartCommand::class)
                ->description(TurkeyStartCommand::getDescription());

            // TODO: works but temporarily hidden
//            $bot->onCommand(TurkeyAskQuestionConversation::getName(), TurkeyAskQuestionConversation::class)
//                ->description(TurkeyAskQuestionConversation::getDescription());

            $bot->onCommand(TurkeyAddListingConversation::getName(), TurkeyAddListingConversation::class)
                ->description(TurkeyAddListingConversation::getDescription());

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
