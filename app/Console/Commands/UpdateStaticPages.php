<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class UpdateStaticPages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pages:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update static pages';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $countries = config('pages.countries');
        $zipManager = new ZipArchive;
        foreach ($countries as $country) {
            $response = Http::withToken(config('services.github.token'))
                ->get('https://api.github.com/repos/relocation-digital/' . $country . '/zipball/main');
            Storage::put('temp/' . $country . '.zip', $response->body());
            $zipManager->open(storage_path('app/temp/' . $country . '.zip'));
            $zipRootFolder = $zipManager->getNameIndex(0);
            $zipManager->extractTo(storage_path('app/collections/pages'));
            $zipManager->close();
            File::deleteDirectory(storage_path('app/collections/pages/' . $country));
            File::moveDirectory(storage_path('app/collections/pages/' . $zipRootFolder), storage_path('app/collections/pages/' . $country));
        }
        return Command::SUCCESS;
    }
}
