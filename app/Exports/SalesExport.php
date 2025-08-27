<?php 

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;

class SalesExport implements FromCollection
{
    protected $month;

    public function __construct($month) {
        $this->month = $month;
    }

    public function collection()
    {
        return Invoice::whereMonth('invoice_date', $this->month)->get([
            'id', 'customer_id', 'grand_total', 'payment_method', 'status'
        ]);
    }
}
