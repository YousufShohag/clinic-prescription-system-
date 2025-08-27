<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockHistory extends Model
{
    protected $fillable = [
        'medicine_id', 'invoice_id', 'type', 'quantity', 'stock_after'
    ];

    public function medicine() {
        return $this->belongsTo(Medicine::class);
    }

    public function invoice() {
        return $this->belongsTo(Invoice::class);
    }
}
