<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

class TelegramBotImageMimeType extends Enum implements LocalizedEnum
{
    public const JPEG = "image/jpeg";
    public const PNG = "image/png";
}
