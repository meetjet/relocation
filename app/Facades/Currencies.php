<?php

namespace App\Facades;

use App\Services\CurrenciesService;
use Illuminate\Support\Facades\Facade;

/**
 * @package App\Facades
 * @method static array asSelectArray()
 * @method static array getValues()
 * @method static string getDescription(?string $key)
 * @method static string getSign(?string $key)
 * @method static string|null getCodeByCountry(?string $key)
 */
class Currencies extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return CurrenciesService::class;
    }
}
