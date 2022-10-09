<?php

namespace App\Telegram\Conversations;

use App\Models\Faq;
use Psr\SimpleCache\InvalidArgumentException;
use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;

class AskQuestionConversation extends Conversation
{
    /**
     * @param Nutgram $bot
     * @throws InvalidArgumentException
     */
    public function start(Nutgram $bot): void
    {
        $bot->sendMessage(__('telegram.bot.question.start'));
        $this->next('askQuestion');
    }

    /**
     * @param Nutgram $bot
     * @throws InvalidArgumentException
     */
    public function askQuestion(Nutgram $bot): void
    {
        $questionText = $bot->message()->text;

        if ($questionText) {
            Faq::create([
                'original' => $questionText,
            ]);
            $bot->sendMessage(__('telegram.bot.question.end'));
            $this->end();
            return;
        }

        $this->start($bot);
    }

    /**
     * @return string
     */
    public static function getName(): string
    {
        return "question";
    }

    /**
     * @return string
     */
    public static function getDescription(): string
    {
        return __('telegram.bot.question.description');
    }
}
