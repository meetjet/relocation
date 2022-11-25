<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

class EventPaymentType extends Enum implements LocalizedEnum
{
    public const FREE = "free";
    public const PAID = "paid";
    public const DONATION = "donation";
}
