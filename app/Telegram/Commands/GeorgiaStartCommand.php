<?php

namespace App\Telegram\Commands;

use App\Telegram\Conversations\GeorgiaAddListingConversation;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;

class GeorgiaStartCommand
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
        return __('telegram.georgia.start.welcome')
            . PHP_EOL
            . PHP_EOL
            . __('telegram.georgia.start.manual', [
                'command' => GeorgiaAddListingConversation::getName(),
                'link' => addSubdomainToUrl(route('listings.index'), "georgia"),
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
        return __('telegram.georgia.start.description');
    }
}
