<?php

namespace App\Jobs;

use App\UploadIO\UploadIO;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\RequestException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class UploadIODeleteImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $filepath;

    /**
     * Create a new job instance.
     *
     * @param string $filepath
     */
    public function __construct(string $filepath)
    {
        $this->filepath = $filepath;
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
            $uploadIO->delete($this->filepath);
        } catch (RuntimeException | RequestException $e) {
            Log::error($e->getMessage());
        } catch (Exception $e) {
            Log::error($e);
        }
    }
}
