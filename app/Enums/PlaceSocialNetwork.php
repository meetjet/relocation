<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

class PlaceSocialNetwork extends Enum implements LocalizedEnum
{
    public const INSTAGRAM = 'instagram';
    public const TELEGRAM = 'telegram';
    public const FACEBOOK = 'facebook';
}
