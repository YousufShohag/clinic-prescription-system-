<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

      protected $fillable = [
          'doctor_id',
          'name',
          'age',
          'sex',
          'dob',
          'blood_group',
          'guardian_name',
          'address',
          'images',
          'documents',
          'status',
          'phone',
        'email',
        'next_return_date',
        'notes'
    ];

protected $casts = [
    'images' => 'array',
    'documents' => 'array',  // <= IMPORTANT
];
     


 public function prescriptions()
    {
        // Make sure prescriptions table has a patient_id column that points to patients.id
        return $this->hasMany(Prescription::class, 'patient_id', 'id');
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

     public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }



}
