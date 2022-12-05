<?php

namespace App\Jobs;

use App\Telegram\Telegram;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class TelegramAttachImagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Model $model;

    /**
     * Create a new job instance.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
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
            $tmpFolder = config('nutgram.tmp_folder');
            $storagePath = storage_path("app/{$tmpFolder}");
            File::ensureDirectoryExists($storagePath);
            $bot = $telegram->getBotByType($this->model->telegram_bot_type);

            foreach ($this->model->telegram_attached_images as $_image) {
                $file = $bot->getFile($_image['file_id']);

                if ($file) {
                    $file->save($storagePath);
                    $this->model->pictures()->forceCreate(array_merge($_image, [
                        'tmp_image' => "{$tmpFolder}/" . pathinfo($file->file_path, PATHINFO_BASENAME),
                    ]));
                } else {
                    Log::error("Telegram get file, file not found: {$_image['file_id']}");
                }
            }

            $this->model->forceFill([
                'telegram_attached_images' => null,
            ])->save();
        } catch (GuzzleException | NotFoundExceptionInterface | ContainerExceptionInterface $e) {
            Log::error($e);
        }
    }
}
