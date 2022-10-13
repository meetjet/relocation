<?php

namespace App\Telegram\Conversations;

use App\Models\Faq;
use App\Telegram\Actions\CreateUserAction;
use Psr\SimpleCache\InvalidArgumentException;
use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;
use Throwable;

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
     * @throws Throwable
     */
    public function askQuestion(Nutgram $bot): void
    {
        $questionText = $bot->message()->text;

        if ($questionText) {
            Faq::forceCreate([
                'user_id' => app(CreateUserAction::class)->execute($bot->user()),
                'original' => $questionText,
            ]);
            $bot->sendMessage(__('telegram.bot.question.end', [
                'command' => self::getName(),
                'link' => route("faq"),
            ]));
            $this->end();
            return;
        }

        $bot->sendMessage(__('telegram.bot.question.unsupported'));
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
