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
                    ->withPivot('duration', 'times_per_day')
                    ->withTimestamps();
    }


}
