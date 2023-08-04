<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationRecord extends Model
{
    use HasFactory;

    protected $casts = [
        'old_data' => 'json',
        'new_data' => 'json',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class)->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
