<?php

namespace App\Jobs;

use App\Models\Event;
use App\Telegram\Telegram;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Telegram send event to channel.
 *
 * @package App\Jobs
 */
class TelegramResendEventToChannelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Event $event;

    /**
     * Create a new job instance.
     *
     * @param Event $event
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    /**
     * Execute the job.
     *
     * @param Telegram $telegram
     * @return void
     */
    public function handle(Telegram $telegram): void
    {
        try {
            $bot = $telegram->getChannelBotByCountry($this->event->country);
            $chatId = $this->event->telegram_chat_id;

            logger("Trying to delete message from channel with chat ID = {$chatId} and message ID = {$this->event->telegram_message_id}");

            if ($bot->deleteMessage($chatId, $this->event->telegram_message_id)) {
                logger("Message was successfully deleted.");
            }

            TelegramSendEventToChannelJob::dispatch($this->event);
        } catch (Exception $e) {
            Log::error("Telegram resend event to channel: " . $e->getMessage());
        } catch (Throwable $e) {
            Log::error($e);
        }
    }
}
