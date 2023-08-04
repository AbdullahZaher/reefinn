<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Alkoumi\LaravelHijriDate\Hijri;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model
{
    use HasFactory;

    protected function date(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => json_decode($value)->{auth()->user()->calendar},
            set: fn (string $value) => json_encode(['gregorian' => $value, 'hijri' => Hijri::Date('Y-m-d', Carbon::parse($value)),]),
        );
    }
}