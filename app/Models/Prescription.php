<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id','patient_id',
        'problem_description','doctor_advice','return_date',
        'oe','bp','pulse','temperature_c','spo2','respiratory_rate',
        'weight_kg','height_cm','bmi',
    ];

    protected $casts = [
        'temperature_c'    => 'decimal:1',
        'weight_kg'        => 'decimal:2',
        'height_cm'        => 'decimal:2',
        'bmi'              => 'decimal:1',
        'pulse'            => 'integer',
        'spo2'             => 'integer',
        'respiratory_rate' => 'integer',
    ];

    public function doctor()  { return $this->belongsTo(Doctor::class); }
    public function patient() { return $this->belongsTo(Patient::class); }

    

    // ✅ Matches your migration/table: prescription_medicines (plural)
    public function medicines()
    {
        return $this->belongsToMany(
            Medicine::class,
            'prescription_medicines',     // table name in DB
            'prescription_id',
            'medicine_id'
        )->withPivot(['duration','times_per_day'])
         ->withTimestamps();
    }

    // 🔧 FIX: make this match your actual DB table name.
    // If your table is 'prescription_test' (singular), use this:
    public function tests()
    {
        return $this->belongsToMany(
            Test::class,
            'prescription_tests',          // << change to 'prescription_tests' ONLY if your DB table is plural
            'prescription_id',
            'test_id'
        )->withTimestamps();
    }



}
