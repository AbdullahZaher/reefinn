<?php

namespace App\Models;

use App\Models\User;
use App\Models\Apartment;
use Illuminate\Support\Carbon;
use App\Settings\GeneralSettings;
use Alkoumi\LaravelHijriDate\Hijri;
use App\Enums\ReservationStateEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use JamesMills\LaravelTimezone\Facades\Timezone;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reservation extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'real_checkin' => 'datetime',
        'real_checkout' => 'datetime',
    ];

    public $appends = [
        'reservationNumber',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->r_id = rand(100, 999);
        });
    }

    protected function reservationNumber(): Attribute
    {
        return Attribute::make(
            get: fn () => app(GeneralSettings::class)->branch_no . $this->id . $this->r_id,
        );
    }

    protected function guestBirthday(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => json_decode($value)->{auth()->user()?->calendar ?? 'gregorian'},
            set: fn (string $value) => json_encode(['gregorian' => $value, 'hijri' => Hijri::Date('Y-m-d', Carbon::parse($value)),]),
        );
    }

    protected function checkin(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => json_decode($value)->{auth()->user()?->calendar ?? 'gregorian'},
            set: fn (string $value) => json_encode(['gregorian' => $value, 'hijri' => Hijri::Date('Y-m-d', Carbon::parse($value)),]),
        );
    }

    protected function checkout(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => json_decode($value)->{auth()->user()?->calendar ?? 'gregorian'},
            set: fn (string $value) => json_encode(['gregorian' => $value, 'hijri' => Hijri::Date('Y-m-d', Carbon::parse($value)),]),
        );
    }

    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: function (string $value) {
                $value = Carbon::parse($value);
                return auth()->user()?->calendar == 'hijri' ? Hijri::Date('j F Y h:i:s A', Timezone::convertToLocal($value, 'Y-m-d h:i:s A')) : Carbon::parse(Timezone::convertToLocal($value, 'Y-m-d h:i:s A'))->translatedFormat('d F Y h:i:s A');
            },
        );
    }

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    public function admin() {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function scopePending($query)
    {
        return $query->where('state', ReservationStateEnum::Pending->value);
    }
}