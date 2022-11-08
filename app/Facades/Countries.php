<?php

namespace App\Facades;

use App\Services\CountriesService;
use Illuminate\Support\Facades\Facade;

/**
 * @package App\Facades
 * @method static array asSelectArray()
 * @method static array getValues()
 * @method static string getDescription(?string $key)
 */
class Countries extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return CountriesService::class;
    }
}
