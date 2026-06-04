<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'complaint',
        'diagnosis',
        'prescription',
    ];

    public function medicines()
    {
        return $this->belongsToMany(Medicine::class)
            ->withPivot('quantity', 'instructions', 'price_at_time')
            ->withTimestamps();
    }

    // RELASI: Rekam medis ini milik satu Booking
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}