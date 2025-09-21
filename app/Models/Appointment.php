<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'scheduled_at',
        'notes',
        'appointment_number',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    // If you still want old-style fields available to views:
    protected $appends = [
        'date',        // virtual (from scheduled_at)
        'start_time',  // virtual (from scheduled_at)
        // 'end_at',    // optional virtual end datetime
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    /* ----------------- Virtual accessors (do NOT override scheduled_at) ----------------- */

    public function getDateAttribute(): ?string
    {
        return $this->scheduled_at?->format('Y-m-d');
    }

    public function getStartTimeAttribute(): ?string
    {
        return $this->scheduled_at?->format('H:i');
    }

    // Optional helper: computed end datetime based on duration_min (defaults to 30)
    public function getEndAtAttribute(): ?Carbon
    {
        if (!$this->scheduled_at) return null;
        $minutes = (int) ($this->duration_min ?? 30);
        return (clone $this->scheduled_at)->addMinutes($minutes);
    }

    public function getAppointmentCodeAttribute(): ?string
{
    if (!$this->appointment_number || !$this->scheduled_at) return null;
    return 'APT-' . $this->scheduled_at->format('Ymd') . '-' .
           str_pad((string)$this->appointment_number, 3, '0', STR_PAD_LEFT);
}

}
