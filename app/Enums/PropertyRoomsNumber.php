<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

class PropertyRoomsNumber extends Enum implements LocalizedEnum
{
    public const ONE_ROOM = "one_room";
    public const TWO_ROOMS = "two_rooms";
    public const THREE_ROOMS = "three_rooms";
    public const FOUR_ROOMS = "four_rooms";
    public const FIVE_ROOMS = "five_rooms";
}
