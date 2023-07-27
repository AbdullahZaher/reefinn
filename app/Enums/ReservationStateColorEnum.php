<?php

namespace App\Enums;

enum ReservationStateColorEnum: string
{
    case Pending = 'blue';
    case Checkin = 'yellow';
    case Checkout = 'green';
    case Canceled = 'red';

    public static function fromName(string $name)
    {
        return constant("self::$name");
    }
}
