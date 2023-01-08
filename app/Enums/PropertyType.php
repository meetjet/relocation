<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

class PropertyType extends Enum implements LocalizedEnum
{
    public const APARTMENT = "apartment";
    public const HOME = "home";
}
