<?php

namespace App\Enums;

enum ReservationStateEnum: int
{
    case Pending = 0;
    case Canceled = 1;
    case Checkin = 2;
    case Checkout = 3;

    public static function toArray(): array
    {
        return array_map(
            fn (self $enum) => [
                'key' => $enum->value,
                'value' => $enum->name,
            ],
            self::cases()
        );
    }
}
