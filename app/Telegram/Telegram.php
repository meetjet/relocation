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
            TelegramBotType::ARMENIA => $this->getArmeniaBot(),
            TelegramBotType::GEORGIA => $this->getGeorgiaBot(),
            TelegramBotType::THAILAND => $this->getThailandBot(),
            TelegramBotType::TURKEY => $this->getTurkeyBot(),
            default => $this->getDefaultBot(),
        };
    }

    /**
     * Get the default Telegram bot instance.
     *
     * @param string $country
     * @return Nutgram
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getChannelBotByCountry(string $country): Nutgram
    {
        return $this->getBotInstance(
            (string)config("nutgram.channel_token_{$country}"),
            (string)config("nutgram.channel_log_channel_{$country}")
        );
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
     * Get the Armenia Telegram bot instance.
     *
     * @return Nutgram
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getArmeniaBot(): Nutgram
    {
        return $this->getBotInstance(config('nutgram.token_armenia'), config('nutgram.log_channel_armenia'));
    }

    /**
     * Get the Georgia Telegram bot instance.
     *
     * @return Nutgram
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getGeorgiaBot(): Nutgram
    {
        return $this->getBotInstance(config('nutgram.token_georgia'), config('nutgram.log_channel_georgia'));
    }

    /**
     * Get the Thailand Telegram bot instance.
     *
     * @return Nutgram
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getThailandBot(): Nutgram
    {
        return $this->getBotInstance(config('nutgram.token_thailand'), config('nutgram.log_channel_thailand'));
    }

    /**
     * Get the Turkey Telegram bot instance.
     *
     * @return Nutgram
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getTurkeyBot(): Nutgram
    {
        return $this->getBotInstance(config('nutgram.token_turkey'), config('nutgram.log_channel_turkey'));
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
            'logger' => $app->get(LoggerInterface::class)->channel($logChannel ?: 'null'),
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
