<?php

namespace App\Observers;

use App\Jobs\UploadIODeleteImageJob;
use App\Models\Picture;

class PictureObserver
{
    public bool $afterCommit = true;

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
