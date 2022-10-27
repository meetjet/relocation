<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

class TelegramBotType extends Enum implements LocalizedEnum
{
    public const DEFAULT = "default";
    public const ARMENIAN = "armenian";
    public const GEORGIAN = "georgian";
    public const TURKISH = "turkish";
}
