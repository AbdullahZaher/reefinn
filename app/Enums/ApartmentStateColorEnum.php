<?php

namespace App\Enums;

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
}
