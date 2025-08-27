<?php

// app/Models/InvoiceItem.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = ['invoice_id', 'medicine_id', 'quantity', 'price', 'subtotal'];

    public function invoice() {
        return $this->belongsTo(Invoice::class);
    }

    public function medicine() {
        return $this->belongsTo(Medicine::class);
    }
}
