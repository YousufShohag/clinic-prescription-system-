<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Customer;
use App\Models\Medicine;
use App\Models\StockHistory;
use Illuminate\Http\Request;
use DB;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    /** List invoices */
    public function index() {
        $invoices = Invoice::with('customer')->latest()->paginate(10);
        return view('admin.invoices.index', compact('invoices'));
    }

    /** Create invoice */
    public function create() {
        $customers = Customer::where('status', 1)->get();
        $medicines = Medicine::all();
        return view('admin.invoices.create', compact('customers', 'medicines'));
    }

    /** Store invoice */
    public function store(Request $request)
    {
        DB::transaction(function () use ($request) {
            $invoice = Invoice::create([
                'customer_id'    => $request->customer_id,
                'invoice_date'   => now(),
                'notes'          => $request->notes,
                'payment_method' => $request->payment_method,
                'paid_amount'    => $request->paid_amount ?? 0,
                'discount'       => $request->discount ?? 0,
                'tax'            => $request->tax ?? 0,
                'status'         => $request->payment_method === 'Due' ? 0 : 1,
                'total'          => 0,
                'grand_total'    => 0,
            ]);

            $total = 0;

            foreach ($request->items as $item) {
                $medicine = Medicine::find($item['medicine_id']);
                $subtotal = $medicine->price * $item['quantity'];

                InvoiceItem::create([
                    'invoice_id'  => $invoice->id,
                    'medicine_id' => $medicine->id,
                    'quantity'    => $item['quantity'],
                    'price'       => $medicine->price,
                    'subtotal'    => $subtotal,
                ]);

                $total += $subtotal;

                // stock adjustment
                $medicine->decrement('stock', $item['quantity']);

                StockHistory::create([
                    'medicine_id' => $medicine->id,
                    'invoice_id'  => $invoice->id,
                    'type'        => 'remove',
                    'quantity'    => $item['quantity'],
                    'stock_after' => $medicine->stock,
                ]);
            }

            $discountAmount = ($total * $invoice->discount) / 100;
            $taxAmount = (($total - $discountAmount) * $invoice->tax) / 100;
            $grandTotal = $total - $discountAmount + $taxAmount;

            $invoice->update([
                'total'       => $total,
                'grand_total' => $grandTotal,
                'status'      => ($invoice->paid_amount >= $grandTotal) ? 1 : 0
            ]);
        });

        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully.');
    }

    /** Show invoice */
    public function show(Invoice $invoice) {
        $invoice->load('items.medicine', 'customer');
        return view('admin.invoices.show', compact('invoice'));
    }

    /** Edit invoice */
    public function edit(Invoice $invoice)
    {
        $customers = Customer::all();
        $invoice->load('items.medicine');

        $invoiceItems = $invoice->items->map(function ($i) {
            return [
                'medicine_id' => $i->medicine_id,
                'name'        => $i->medicine->name,
                'quantity'    => $i->quantity,
                'price'       => $i->price,
                'subtotal'    => $i->subtotal,
            ];
        })->values()->toArray();

        return view('admin.invoices.edit', compact('invoice', 'customers', 'invoiceItems'));
    }

    /** Download invoice PDF */
    public function download(Invoice $invoice)
    {
        $invoice->load('items.medicine', 'customer');
        $pdf = Pdf::loadView('admin.invoices.pdf', compact('invoice'));
        return $pdf->download('invoice_'.$invoice->id.'.pdf');
    }

    /** Due invoices */
    public function dueInvoices()
    {
        $invoices = Invoice::with('customer')
            ->whereColumn('paid_amount', '<', 'grand_total')
            ->latest()
            ->paginate(10);

        return view('admin.invoices.due', compact('invoices'));
    }

    /** Full Update Invoice (edit page) */
    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'payment_method' => 'required|string',
            'paid_amount' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.medicine_id' => 'required|exists:medicines,id',
            'items.*.quantity' => 'required|numeric|min:1',
        ]);

        // rollback stock for old items
        foreach ($invoice->items as $oldItem) {
            $medicine = Medicine::find($oldItem->medicine_id);
            if ($medicine) {
                $medicine->stock += $oldItem->quantity;
                $medicine->save();

                StockHistory::create([
                    'medicine_id' => $medicine->id,
                    'invoice_id'  => $invoice->id,
                    'type'        => 'rollback',
                    'quantity'    => $oldItem->quantity,
                    'stock_after' => $medicine->stock,
                ]);
            }
        }

        $invoice->items()->delete();

        // update invoice main info
        $invoice->update([
            'customer_id'   => $request->customer_id,
            'notes'         => $request->notes,
            'payment_method'=> $request->payment_method,
            'paid_amount'   => $request->paid_amount ?? $invoice->grand_total,
            'discount'      => $request->discount,
            'tax'           => $request->tax,
        ]);

        $total = 0;

        // add new items
        foreach ($request->items as $item) {
            $medicine = Medicine::find($item['medicine_id']);
            if ($medicine) {
                $subtotal = $medicine->price * $item['quantity'];
                $total += $subtotal;

                $invoice->items()->create([
                    'medicine_id' => $medicine->id,
                    'quantity'    => $item['quantity'],
                    'price'       => $medicine->price,
                    'subtotal'    => $subtotal,
                ]);

                $medicine->stock -= $item['quantity'];
                $medicine->save();

                StockHistory::create([
                    'medicine_id' => $medicine->id,
                    'invoice_id'  => $invoice->id,
                    'type'        => 'remove',
                    'quantity'    => $item['quantity'],
                    'stock_after' => $medicine->stock,
                ]);
            }
        }

        // recalc totals
        $discountAmount = ($total * $invoice->discount) / 100;
        $taxAmount = (($total - $discountAmount) * $invoice->tax) / 100;
        $grandTotal = $total - $discountAmount + $taxAmount;

        $invoice->update([
            'total'       => $total,
            'grand_total' => $grandTotal,
            'status'      => $invoice->paid_amount >= $grandTotal ? 1 : 0,
        ]);

        return redirect()->route('invoices.index')->with('success', 'Invoice updated successfully!');
    }

    /** Quick Pay for modal */
    public function quickPay(Request $request, Invoice $invoice)
{
    $request->validate([
        'paid_amount'    => 'required|numeric|min:0',
        'payment_method' => 'required|string',
    ]);

    // Update paid amount
    $invoice->paid_amount += $request->paid_amount;
    $invoice->payment_method = $request->payment_method;

    // Update status if fully paid
    if ($invoice->paid_amount >= $invoice->grand_total) {
        $invoice->status = 1; // Paid
    }

    $invoice->save();

    return redirect()->route('invoices.due')->with('success', '✅ Payment recorded successfully!');
}


}
