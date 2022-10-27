<?php

namespace App\Telegram\Commands;

use App\Telegram\Conversations\ArmenianAskQuestionConversation;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;

class ArmenianStartCommand
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
        return __('telegram.armenian.start.welcome')
            . PHP_EOL
            . __('telegram.armenian.start.manual', [
                'command' => ArmenianAskQuestionConversation::getName(),
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
        return __('telegram.armenian.start.description');
    }
}
