<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

class TelegramBotType extends Enum implements LocalizedEnum
{
    public const DEFAULT = "default";
    public const ARMENIA = "armenia";
    public const GEORGIA = "georgia";
    public const THAILAND = "thailand";
    public const TURKEY = "turkey";
}
