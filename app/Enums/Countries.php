<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * @see http://actravel.ru/country_codes.html
 * @package App\Enums
 */
class Countries extends Enum implements LocalizedEnum
{
    public const ARM = "ARM";
    public const GEO = "GEO";
    public const TUR = "TUR";
}
