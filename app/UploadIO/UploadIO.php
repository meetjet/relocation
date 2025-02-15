<?php

namespace App\UploadIO;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class UploadIO
{
    /**
     * Upload file to UploadIO from local storage.
     *
     * @param string $filepath The full path to the file in local storage.
     * @return array
     * @throws FileNotFoundException
     * @throws RequestException
     */
    public function upload(string $filepath): array
    {
        if (!config('uploadio.public_key') || !config('uploadio.account_id')) {
            throw new RuntimeException("UploadIO configuration error. Check the UPLOAD_IO_PUBLIC_KEY and UPLOAD_IO_ACCOUNT_ID settings.");
        }

        if (!File::exists($filepath)) {
            throw new FileNotFoundException(sprintf('The file "%s" does not exist.', $filepath));
        }

        $response = Http::withBody(File::get($filepath), File::mimeType($filepath))
            ->withToken(config('uploadio.public_key'))
            ->post(sprintf("https://api.upload.io/v2/accounts/%s/uploads/binary", config('uploadio.account_id')))
            ->throw();

        return (array)$response->json();
    }

    /**
     * @param string $filepath
     * @return string
     * @throws RequestException
     */
    public function download(string $filepath): string
    {
        if (!config('uploadio.public_key') || !config('uploadio.account_id')) {
            throw new RuntimeException("UploadIO configuration error. Check the UPLOAD_IO_PUBLIC_KEY and UPLOAD_IO_ACCOUNT_ID settings.");
        }

        $response = Http::withBasicAuth('apikey', config('uploadio.secret_key'))
            ->get(sprintf("https://upcdn.io/%s/raw%s", config('uploadio.account_id'), $filepath))
            ->throw();

        return $response->body();
    }

    /**
     * Delete file from UploadIO.
     *
     * @param string $filepath UploadIO file path (see "uploadio_file_path" field in "Picture" model).
     * @throws RequestException
     */
    public function delete(string $filepath): void
    {
        if (!config('uploadio.secret_key') || !config('uploadio.account_id')) {
            throw new RuntimeException("UploadIO configuration error. Check the UPLOAD_IO_SECRET_KEY and UPLOAD_IO_ACCOUNT_ID settings.");
        }

        Http::withBasicAuth('apikey', config('uploadio.secret_key'))
            ->delete(sprintf("https://api.upload.io/v2/accounts/%s/files?filePath=%s", config('uploadio.account_id'), $filepath))
            ->throw();
    }

    /**
     * @see https://upload.io/dashboard/transformations
     * @param string $rawUrl
     * @return Collection
     */
    public function getTransformationsCollection(string $rawUrl): Collection
    {
        $collection = collect();
        $transformations = config('uploadio.transformations');

        foreach ($transformations as $_transformation) {
            $collection->put($_transformation, Str::replace("raw", $_transformation, $rawUrl));
        }

        return $collection;
    }
}
