<?php

namespace App\Telegram\Commands;

use App\Telegram\Conversations\ThailandAddListingConversation;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;

class ThailandStartCommand
{
    /**
     * @param Nutgram $bot
     */
    public function __invoke(Nutgram $bot): void
    {
        $bot->sendMessage($this->getWelcomeMessage(), [
            'parse_mode' => ParseMode::HTML,
            'disable_web_page_preview' => true,
        ]);
    }

    /**
     * @return string
     */
    private function getWelcomeMessage(): string
    {
        return __('telegram.thailand.start.welcome')
            . PHP_EOL
            . __('telegram.thailand.start.manual', [
                'command' => ThailandAddListingConversation::getName(),
            ]);
    }

    /**
     * @return string
     */
    public static function getName(): string
    {
        return "start";
    }

    /**
     * @return string
     */
    public static function getDescription(): string
    {
        return __('telegram.thailand.start.description');
    }
}
