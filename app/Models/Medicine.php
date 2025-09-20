<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'generic',
        'strength',
        'manufacturer',
        'type',
        'stock',
        'price',
        'expiry_date',
        'description',
        'notes',
        'image',
        'status'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function stockHistories()
    {
        return $this->hasMany(StockHistory::class);
    }
    public function prescriptions()
    {
        return $this->belongsToMany(Prescription::class, 'prescription_medicines')
                    ->withPivot('duration', 'times_per_day','meal_time')
                    ->withTimestamps();
    }


    // optional: include it automatically in JSON
    protected $appends = ['type_prefix'];

    public function getTypePrefixAttribute(): string
    {
        $map = [
            'tablet'     => 'Tab.',
            'tab'        => 'Tab.',
            'capsule'    => 'Cap.',
            'cap'        => 'Cap.',
            'syrup'      => 'Syr.',
            'suspension' => 'Susp.',
            'drop'       => 'Drop',
            'injection'  => 'Inj.',
            'inj'        => 'Inj.',
            'cream'      => 'Cr.',
            'ointment'   => 'Oint.',
            'gel'        => 'Gel',
        ];
        return $map[strtolower($this->type ?? '')] ?? '';
    }


}
