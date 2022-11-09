<?php

namespace App\Jobs;

use App\Telegram\Telegram;
use App\UploadIO\UploadIO;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\RequestException;
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
     * @param UploadIO $uploadIO
     * @param Telegram $telegram
     * @return void
     */
    public function handle(UploadIO $uploadIO, Telegram $telegram): void
    {
        try {
            foreach ($this->model->telegram_attached_images as $_image) {
                $storagePath = storage_path(config('nutgram.tmp_folder'));
                File::ensureDirectoryExists($storagePath);
                $bot = $telegram->getBotByType($this->model->telegram_bot_type);
                $file = $bot->getFile($_image['file_id']);

                if ($file) {
                    $file->save($storagePath);
                    $tmpFilepath = $storagePath . '/' . pathinfo($file->file_path, PATHINFO_BASENAME);
                    $uploadedFileData = $uploadIO->upload($tmpFilepath);
                    File::delete($tmpFilepath);
                    $this->model->pictures()->forceCreate(array_merge($_image, [
                        'content' => $uploadIO->getTransformationsCollection($uploadedFileData['fileUrl']),
                        'uploadio_file_path' => $uploadedFileData['filePath'],
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
        } catch (FileNotFoundException | RequestException $e) {
            Log::error($e->getMessage());
        }
    }
}
