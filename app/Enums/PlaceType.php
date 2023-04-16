<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * @deprecated
 */
class PlaceType extends Enum implements LocalizedEnum
{
    public const BAR = "bar";
    public const RESTAURANT = "restaurant";
    public const CAFE = "cafe";
}
