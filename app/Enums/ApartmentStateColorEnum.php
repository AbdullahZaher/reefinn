<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum ApartmentStateColorEnum: string
{
    case Empty = 'green';
    case Reserved = 'orange';
    case Cleaning = 'yellow';
    case Inhabited = 'red';
    case Maintenance = 'gray';

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