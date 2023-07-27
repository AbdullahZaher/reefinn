<?php

namespace App\Models;

use App\Models\Apartment;
use App\Models\Reservation;
use Illuminate\Support\Carbon;
use Alkoumi\LaravelHijriDate\Hijri;
use Illuminate\Database\Eloquent\Model;
use JamesMills\LaravelTimezone\Facades\Timezone;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Record extends Model
{
    use HasFactory;

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

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}
