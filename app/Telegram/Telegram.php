<?php

namespace App\Telegram;

use App\Enums\TelegramBotType;
use Illuminate\Contracts\Cache\Repository as Cache;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\RunningMode\Polling;
use SergiX44\Nutgram\RunningMode\Webhook;

class Telegram
{
    /**
     * Get a Telegram bot instance by the specified type.
     *
     * @param string $type
     * @return Nutgram
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getBotByType(string $type): Nutgram
    {
        return match ($type) {
            TelegramBotType::ARMENIAN => $this->getArmenianBot(),
            default => $this->getDefaultBot(),
        };
    }

    /**
     * Get the default Telegram bot instance.
     *
     * @return Nutgram
     */
    public function getDefaultBot(): Nutgram
    {
        return app(Nutgram::class);
    }

    /**
     * Get the Armenian Telegram bot instance.
     *
     * @return Nutgram
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getArmenianBot(): Nutgram
    {
        return $this->getBotInstance(config('nutgram.token_armenia'), config('nutgram.log_channel_armenia'));
    }

    /**
     * Get the Nutgram bot instance for the specified token and log channel.
     *
     * @param string $token
     * @param string $logChannel
     * @return Nutgram
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function getBotInstance(string $token, string $logChannel): Nutgram
    {
        $app = app();

        $bot = new Nutgram($token, array_merge([
            'cache' => $app->get(Cache::class),
            'logger' => $app->get(LoggerInterface::class)->channel($logChannel),
        ], config('nutgram.config', [])));

        if ($app->runningInConsole()) {
            $bot->setRunningMode(Polling::class);
        } else {
            $webhook = Webhook::class;

            if (config('nutgram.safe_mode', false)) {
                $webhook = new Webhook(fn() => $app->make('request')?->ip());
            }

            $bot->setRunningMode($webhook);
        }

        return $bot;
    }
}
