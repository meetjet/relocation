<?php

namespace App\Forms\Components;

use App\Jobs\TelegramResendEventToChannelJob;
use App\Jobs\TelegramSendEventToChannelJob;
use App\Models\Event;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;

class ResendEventToTelegramChannel extends Field
{
    protected string $view = 'forms.components.resend-event-to-telegram-channel';

    protected function setUp(): void
    {
        parent::setUp();

        $this->registerListeners([
            'sendEventToTelegramChannel' => [
                function (Component $component): void {
                    logger("Manually sending event to Telegram channel.");

                    $event = $component->getActionFormModel();

                    if (!($event instanceof Event)) {
                        logger("Failed to send event: no instance of Event class.");
                        return;
                    }

                    TelegramSendEventToChannelJob::dispatch($event);
                },
            ],
            'resendEventToTelegramChannel' => [
                function (Component $component): void {
                    logger("Manually resending event to Telegram channel.");

                    $event = $component->getActionFormModel();

                    if (!($event instanceof Event)) {
                        logger("Failed to resend event: no instance of Event class.");
                        return;
                    }

                    if (!$event->telegram_chat_id || !$event->telegram_message_id) {
                        logger("Failed to resend event: no 'telegram_chat_id' or 'telegram_message_id'.");
                        return;
                    }

                    TelegramResendEventToChannelJob::dispatch($event);
                },
            ],
        ]);
    }
}
