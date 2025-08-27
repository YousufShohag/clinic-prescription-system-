<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price', 'note', 'status','test_category_id'];


    protected $casts = ['price' => 'decimal:2'];

    public function category()
    {
        return $this->belongsTo(TestCategory::class, 'test_category_id');
    }

    public function prescriptions()
{
    return $this->belongsToMany(Prescription::class, 'prescription_tests')
                ->withTimestamps();
}

}


