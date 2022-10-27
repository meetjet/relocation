<?php

namespace App\UploadIO;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

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
     * Delete file from UploadIO.
     *
     * @param string $filepath UploadIO file path (see "uploadio_file_path" field in "Picture" model).
     * @throws RequestException
     */
    public function delete(string $filepath): void
    {
        Http::withBasicAuth('apikey', config('uploadio.secret_key'))
            ->delete(sprintf("https://api.upload.io/v2/accounts/%s/files?filePath=%s", config('uploadio.account_id'), $filepath))
            ->throw();
    }
}
