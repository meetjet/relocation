<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\File;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class LocalStorageSaveImageJob implements ShouldQueue
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
     * @return void
     */
    public function handle(): void
    {
        try {
            $tmpFilepath = storage_path("app/{$this->model->tmp_image}");
            $path = Storage::disk('public')->putFile('photos', new File($tmpFilepath));
            Storage::delete($this->model->tmp_image);

            $transformations = config('uploadio.transformations');
            $transformCollection = collect();

            foreach ($transformations as $_transformation) {
                $transformCollection->put($_transformation, Storage::url($path));
            }

            $this->model->forceFill([
                'content' => $transformCollection,
                'local_file_path' => $path,
                'tmp_image' => null,
            ])->save();
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
