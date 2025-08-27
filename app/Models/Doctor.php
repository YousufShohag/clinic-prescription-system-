<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'specialization',
        'degree',
        'bma_registration_number',
        'chamber',
        'email',
        'phone',
        'consultation_fee',
        'available_time',
        'notes',
        'image',
    ];

    public function patients()
        {
            return $this->hasMany(Patient::class);
        }

}
