<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

class Countries extends Enum implements LocalizedEnum
{
    public const ARM = "ARM";
    public const GEO = "GEO";
    public const TUR = "TUR";
}
