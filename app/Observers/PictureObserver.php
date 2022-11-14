<?php

namespace App\Observers;

use App\Jobs\UploadIODeleteImageJob;
use App\Jobs\UploadIOUploadImageJob;
use App\Models\Picture;

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
            UploadIOUploadImageJob::dispatch($picture);
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
        }
    }
}
