<?php

namespace App\Enums;

enum ApartmentStateEnum: int
{
    case Empty = 0;
    case Reserved = 1;
    case Inhabited = 2;
    case Cleaning = 3;
    case Maintenance = 4;

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
