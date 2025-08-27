<?php
// app/Models/Invoice.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    //protected $fillable = ['customer_id', 'invoice_date', 'total', 'notes', 'status'];
protected $fillable = [
    'customer_id',
    'invoice_date',
    'total',
    'discount',
    'tax',
    'grand_total',
    'payment_method',
    'paid_amount',
    'notes',
    'status',
];

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function items() {
        return $this->hasMany(InvoiceItem::class);
    }
}
