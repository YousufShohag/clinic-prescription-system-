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
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }


   protected $appends = ['scheduled_at'];

public function getScheduledAtAttribute()
{
    // If DB already has scheduled_at column, use it.
    if (array_key_exists('scheduled_at', $this->attributes) && !is_null($this->attributes['scheduled_at'])) {
        return Carbon::parse($this->attributes['scheduled_at']);
    }

    // Otherwise synthesize from date + start_time (if present)
    $d = $this->date ? ($this->date instanceof Carbon ? $this->date->toDateString() : (string)$this->date) : null;
    $t = $this->start_time ?? null;

    if ($d && $t) return Carbon::parse("$d $t");
    if ($d) return Carbon::parse("$d 00:00:00");

    return null;
}
}