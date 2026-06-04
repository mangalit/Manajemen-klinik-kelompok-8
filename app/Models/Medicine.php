<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'stock',
        'unit',
        'price',
    ];

    public function medicalRecords()
    {
        return $this->belongsToMany(MedicalRecord::class)
            ->withPivot('quantity', 'instructions', 'price_at_time')
            ->withTimestamps();
    }
}
