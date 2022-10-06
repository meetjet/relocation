<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

class FaqStatus extends Enum implements LocalizedEnum
{
    public const CREATED = "created";
    public const PUBLISHED = "published";
    public const REJECTED = "rejected";
}
