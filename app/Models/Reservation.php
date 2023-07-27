<?php

namespace App\Models;

use App\Models\Apartment;
use Illuminate\Support\Carbon;
use Alkoumi\LaravelHijriDate\Hijri;
use App\Enums\ReservationStateEnum;
use Illuminate\Database\Eloquent\Model;
use JamesMills\LaravelTimezone\Facades\Timezone;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reservation extends Model
{
    use HasFactory;

    protected $casts = [
        'real_checkin' => 'datetime',
        'real_checkout' => 'datetime',
    ];

    protected function guestBirthday(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => json_decode($value)->{auth()->user()->calendar},
            set: fn (string $value) => json_encode(['gregorian' => $value, 'hijri' => Hijri::Date('Y-m-d', Carbon::parse($value)),]),
        );
    }

    protected function checkin(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => json_decode($value)->{auth()->user()->calendar},
            set: fn (string $value) => json_encode(['gregorian' => $value, 'hijri' => Hijri::Date('Y-m-d', Carbon::parse($value)),]),
        );
    }

    protected function checkout(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => json_decode($value)->{auth()->user()->calendar},
            set: fn (string $value) => json_encode(['gregorian' => $value, 'hijri' => Hijri::Date('Y-m-d', Carbon::parse($value)),]),
        );
    }

    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: function (string $value) {
                $value = Carbon::parse($value);
                return auth()->user()->calendar == 'hijri' ? Hijri::Date('j F Y h:i:s A', Timezone::convertToLocal($value, 'Y-m-d h:i:s A')) : Carbon::parse(Timezone::convertToLocal($value, 'Y-m-d h:i:s A'))->translatedFormat('d F Y h:i:s A');
            },
        );
    }

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    public function scopePending($query)
    {
        return $query->where('state', ReservationStateEnum::Pending->value);
    }
}
