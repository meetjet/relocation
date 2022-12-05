<?php

namespace App\Jobs;

use App\UploadIO\UploadIO;
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
use RuntimeException;

class UploadIOUploadImageJob implements ShouldQueue
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
     * @return void
     */
    public function handle(UploadIO $uploadIO): void
    {
        try {
            $tmpFilepath = storage_path("app/{$this->model->tmp_image}");
            $uploadedFileData = $uploadIO->upload($tmpFilepath);
            File::delete($tmpFilepath);
            $this->model->forceFill([
                'content' => $uploadIO->getTransformationsCollection($uploadedFileData['fileUrl']),
                'uploadio_file_path' => $uploadedFileData['filePath'],
                'tmp_image' => null,
            ])->save();
        } catch (RuntimeException | FileNotFoundException | RequestException $e) {
            Log::error($e->getMessage());
        }
    }
}
