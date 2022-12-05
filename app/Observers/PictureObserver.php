<?php

namespace App\Observers;

use App\Jobs\LocalStorageDeleteImageJob;
use App\Jobs\LocalStorageSaveImageJob;
use App\Jobs\UploadIODeleteImageJob;
use App\Jobs\UploadIOUploadImageJob;
use App\Models\Picture;
use Illuminate\Support\Facades\Log;

class PictureObserver
{
    public bool $afterCommit = true;

    /**
     * Handle the Picture "created" event.
     *
     * @param Picture $picture
     * @return void
     */
    public function created(Picture $picture): void
    {
        if ($picture->tmp_image) {
            if (config('filesystems.default') === "uploadio") {
                // Upload image to cloud.
                UploadIOUploadImageJob::dispatch($picture);
            } elseif (config('filesystems.default') === "local") {
                // Save image to local storage.
                LocalStorageSaveImageJob::dispatch($picture);
            } else {
                Log::error("An unsupported disk was specified for the file system. Check the FILESYSTEM_DISK setting.");
            }
        }
    }

    /**
     * Handle the Picture "deleted" event.
     *
     * @param Picture $picture
     * @return void
     */
    public function deleted(Picture $picture): void
    {
        if ($picture->uploadio_file_path) {
            UploadIODeleteImageJob::dispatch($picture->uploadio_file_path);
        } elseif ($picture->local_file_path) {
            LocalStorageDeleteImageJob::dispatch($picture->local_file_path);
        }
    }
}
