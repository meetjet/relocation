<?php

namespace App\Facades;

use App\Services\CitiesService;
use Illuminate\Support\Facades\Facade;

/**
 * @package App\Facades
 * @method static array asSelectArray(?string $country)
 * @method static array getValues(?string $country)
 * @method static string getDescription(?string $country, ?string $key)
 */
class Cities extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return CitiesService::class;
    }
}
