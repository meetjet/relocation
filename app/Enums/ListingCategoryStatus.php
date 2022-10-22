<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

class ListingCategoryStatus extends Enum implements LocalizedEnum
{
    public const ACTIVE = "active";
    public const INACTIVE = "inactive";
}
