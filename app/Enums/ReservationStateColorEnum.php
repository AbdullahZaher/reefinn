<?php

namespace App\Enums;

use Illuminate\Support\Str;

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

    public static function toArrayAsc(): array
    {
        $values = [];
        foreach (self::cases() as $case) {
            $values[Str::lower($case->name)] = $case->value;
        }

        return $values;
    }
}