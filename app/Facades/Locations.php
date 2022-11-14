<?php

namespace App\Facades;

use App\Services\LocationsService;
use Illuminate\Support\Facades\Facade;

/**
 * @package App\Facades
 * @method static array asSelectArray(?string $country)
 * @method static array getValues(?string $country)
 * @method static string getDescription(?string $country, ?string $key)
 */
class Locations extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return LocationsService::class;
    }
}
