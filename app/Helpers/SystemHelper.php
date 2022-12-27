<?php

use App\Services\CountriesService;
use App\Services\CurrenciesService;
use App\Services\LocationsService;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\Pure;

if (!function_exists('includeFilesInFolder')) {
    /**
     * Loops through a folder and requires all PHP files
     * Searches sub-directories as well.
     *
     * @param $folder
     */
    function includeFilesInFolder($folder)
    {
        try {
            $rdi = new RecursiveDirectoryIterator($folder);
            $it = new RecursiveIteratorIterator($rdi);

            while ($it->valid()) {
                if (!$it->isDot() && $it->isFile() && $it->isReadable() && $it->current()->getExtension() === 'php') {
                    require $it->key();
                }

                $it->next();
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}

if (!function_exists('includeRouteFiles')) {

    /**
     * @param $folder
     */
    function includeRouteFiles($folder)
    {
        includeFilesInFolder($folder);
    }
}

if (!function_exists('getFullRussianPhoneNumber')) {

    /**
     * Returns the full Russian mobile phone number (11 digits) in international format. For example:
     *  "+7 (999) 888-77-66" => "+79998887766"
     *  or "9998887766" => "+79998887766"
     *  or "89998887766" => "+79998887766"
     *  or "999888776" => "999888776" (at least 10 digits required)
     *
     * @param string|null $number
     *
     * @return string|null
     */
    function getFullRussianPhoneNumber(?string $number): ?string
    {
        if ($number) {
            $number = preg_replace("/\D+/", '', $number);
            if (Str::length($number) === 11 && Str::startsWith($number, "8")) {
                $number = Str::substr($number, 1);
            }
            if (Str::length($number) === 10 && !Str::startsWith($number, "7")) {
                $number = '7' . $number;
            }
            if (Str::length($number) === 11 && Str::startsWith($number, "7")) {
                $number = '+' . $number;
            }
        }

        return $number;
    }
}

if (!function_exists('formatRussianPhoneNumber')) {
    /**
     * "+79998887766" => "+7 999 888-77-66"
     *
     * @see https://snipp.ru/php/phone-format
     *
     * @param string|null $value
     *
     * @return string|null
     */
    function formatRussianPhoneNumber(?string $value): ?string
    {
        if (is_null($value)) {
            return null;
        }

        return preg_replace(
            [
                '/[\+]?([7|8])[-|\s]?\([-|\s]?(\d{3})[-|\s]?\)[-|\s]?(\d{3})[-|\s]?(\d{2})[-|\s]?(\d{2})/',
                '/[\+]?([7|8])[-|\s]?(\d{3})[-|\s]?(\d{3})[-|\s]?(\d{2})[-|\s]?(\d{2})/',
                '/[\+]?([7|8])[-|\s]?\([-|\s]?(\d{4})[-|\s]?\)[-|\s]?(\d{2})[-|\s]?(\d{2})[-|\s]?(\d{2})/',
                '/[\+]?([7|8])[-|\s]?(\d{4})[-|\s]?(\d{2})[-|\s]?(\d{2})[-|\s]?(\d{2})/',
                '/[\+]?([7|8])[-|\s]?\([-|\s]?(\d{4})[-|\s]?\)[-|\s]?(\d{3})[-|\s]?(\d{3})/',
                '/[\+]?([7|8])[-|\s]?(\d{4})[-|\s]?(\d{3})[-|\s]?(\d{3})/',
            ],
            [
                '+7 $2 $3-$4-$5',
                '+7 $2 $3-$4-$5',
                '+7 $2 $3-$4-$5',
                '+7 $2 $3-$4-$5',
                '+7 $2 $3-$4',
                '+7 $2 $3-$4',
            ],
            trim($value)
        );
    }
}

if (!function_exists('generatePhoneNumber')) {

    /**
     * Returns the full Russian mobile phone number (11 digits) in international format. For example: "+79998887766".
     *
     * @return string
     */
    #[Pure] function generatePhoneNumber(): string
    {
        $cod_arr = array('920', '938', '964', '909', '916', '911', '914', '978', '962', '950', '906', '919', '952', '922', '960', '968', '961', '913', '983', '917', '912', '921', '937', '965', '900', '927', '951', '904', '903', '999', '953', '924', '702', '777', '966', '905', '910', '984', '981', '963', '701', '929', '925', '707', '908', '918', '915');
        $num_arr = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');

        return '+7'
            . $cod_arr[array_rand($cod_arr)]
            . $num_arr[array_rand($num_arr)]
            . $num_arr[array_rand($num_arr)]
            . $num_arr[array_rand($num_arr)]
            . $num_arr[array_rand($num_arr)]
            . $num_arr[array_rand($num_arr)]
            . $num_arr[array_rand($num_arr)]
            . $num_arr[array_rand($num_arr)];
    }
}

if (!function_exists('parsePhoneNumber')) {

    /**
     * Returns the full mobile phone number in international format. For example:
     *  "+7 (999) 888-77-66" => "79998887766"
     *
     * @param string|null $value
     * @param string $country
     * @return string|null
     */
    function parsePhoneNumber(?string $value, string $country): ?string
    {
        if ($value) {
            $value = preg_replace("/\D+/", '', $value);
        }

        return $value;
    }
}

if (!function_exists('formatPhoneNumber')) {
    /**
     * "+79998887766" => "+7 999 888-77-66"
     *
     * @param string|null $value
     * @param string $country
     * @return string|null
     */
    function formatPhoneNumber(?string $value, string $country): ?string
    {
        return $value;
    }
}

if (!function_exists('addSubdomainToUrl')) {
    /**
     * Add a subdomain to the URL. For example: "http://relocation.test/listings" => "http://armenia.relocation.test/listings".
     *
     * @param string $url
     * @param string|null $subdomain
     * @return string
     */
    function addSubdomainToUrl(string $url, ?string $subdomain): string
    {
        if ($subdomain) {
            $domain = config('app.domain');

            return Str::replace("//{$domain}", "//{$subdomain}.{$domain}", $url);
        }

        return $url;
    }
}

if (!function_exists('countries')) {
    /**
     * @return CountriesService
     */
    function countries(): CountriesService
    {
        return app(CountriesService::class);
    }
}

if (!function_exists('currencies')) {
    /**
     * @return CurrenciesService
     */
    function currencies(): CurrenciesService
    {
        return app(CurrenciesService::class);
    }
}

if (!function_exists('locations')) {
    /**
     * @return LocationsService
     */
    function locations(): LocationsService
    {
        return app(LocationsService::class);
    }
}

if (!function_exists('getCurrentCountry')) {
    /**
     * @return string|null
     */
    function getCurrentCountry(): ?string
    {
        $request = request();

        if ($request) {
            $currentCountry = str($request->getHost())
                ->replace(('.' . config('app.domain')), "")
                ->lower()
                ->value();

            return in_array($currentCountry, countries()->getValues(), true)
                ? $currentCountry
                : null;
        }

        return null;
    }
}

if (!function_exists('isUrlWithCountry')) {
    /**
     * @param string|null $url
     * @return bool
     */
    function isUrlWithCountry(?string $url): bool
    {
        if ($url) {
            $subdomain = str(parse_url($url, PHP_URL_HOST))
                ->replace(('.' . config('app.domain')), "")
                ->lower()
                ->value();

            return in_array($subdomain, countries()->getValues(), true);
        }

        return false;
    }
}

if (!function_exists('getFakeTransformCollection')) {
    /**
     * @return Collection
     */
    function getFakeTransformCollection(): Collection
    {
        return collect([
            'raw' => "https://picsum.photos/1280/960",
            'large' => "https://picsum.photos/1280/960",
            'medium' => "https://picsum.photos/640/480",
            'thumbnail' => "https://picsum.photos/400/300",
            'thumbnail-square' => "https://picsum.photos/400/400",
        ]);
    }
}
