<?php

namespace App\Telegram\Conversations;

use App\Enums\TelegramBotType;
use App\Models\Faq;
use App\Telegram\Actions\CreateUserAction;
use Illuminate\Support\Str;
use Psr\SimpleCache\InvalidArgumentException;
use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;
use Throwable;

class ArmeniaAskQuestionConversation extends Conversation
{
    /**
     * @param Nutgram $bot
     * @throws InvalidArgumentException
     */
    public function start(Nutgram $bot): void
    {
        $bot->sendMessage(__('telegram.armenia.question.start'));
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
                'country' => "armenia",
                'telegram_bot_type' => TelegramBotType::ARMENIA,
                'telegram_user_id' => $bot->userId(),
                'telegram_user_language_code' => $bot->user()->language_code,
                'telegram_chat_id' => $bot->chatId(),
                'telegram_message_id' => $bot->messageId(),
            ]);

            $bot->sendMessage(__('telegram.armenia.question.end', [
                'command' => self::getName(),
                'link' => addSubdomainToUrl(route('faqs.index'), "armenia"),
            ]));
            $this->end();
            return;
        }

        $bot->sendMessage(__('telegram.armenia.question.unsupported'));
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
        return __('telegram.armenia.question.description');
    }
}
