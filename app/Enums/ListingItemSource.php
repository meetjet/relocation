<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

class ListingItemSource extends Enum implements LocalizedEnum
{
    public const BOT = "bot";
    public const ADMIN = "admin";
}
