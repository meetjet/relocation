<?php

namespace App\UploadIO;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class UploadIO
{
    /**
     * @param string $filepath
     * @return string
     * @throws FileNotFoundException
     * @throws RequestException
     */
    public function upload(string $filepath): string
    {
        if (!File::exists($filepath)) {
            throw new FileNotFoundException(sprintf('The file "%s" does not exist.', $filepath));
        }

        $response = Http::withBody(File::get($filepath), File::mimeType($filepath))
            ->withToken(config('uploadio.public_key'))
            ->post(sprintf("https://api.upload.io/v2/accounts/%s/uploads/binary", config('uploadio.account_id')))
            ->throw();

        $responseBody = (array)$response->object();

        return $responseBody['fileUrl'];
    }
}
