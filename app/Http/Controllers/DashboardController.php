<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Prescription;
use App\Models\Patient;
use App\Models\Medicine;
use App\Models\Customer;
use App\Models\Invoice;
use Carbon\Carbon;
use DB;

class DashboardController extends Controller
{
    public function index()
    {
        // KPI counts
        $totalCategories = Category::count();
        $totalMedicines  = Medicine::count();
        $totalCustomers  = Customer::count();
        $totalInvoices   = Invoice::count();
        $totalPrescription   = Prescription::count();
        $totalPatient   = Patient::count();

        // Sales summary
        $todaysSales    = Invoice::whereDate('invoice_date', Carbon::today())->sum('grand_total');
        $thisMonthSales = Invoice::whereMonth('invoice_date', Carbon::now()->month)
                                ->whereYear('invoice_date', Carbon::now()->year)
                                ->sum('grand_total');
        $lastMonthSales = Invoice::whereMonth('invoice_date', Carbon::now()->subMonth()->month)
                                ->whereYear('invoice_date', Carbon::now()->subMonth()->year)
                                ->sum('grand_total');

        // Top 5 medicines
        $topMedicines = DB::table('invoice_items')
            ->join('medicines', 'invoice_items.medicine_id', '=', 'medicines.id')
            ->select('medicines.name', DB::raw('SUM(invoice_items.quantity) as total_qty'))
            ->groupBy('medicines.name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // Low stock (less than 10)
        $lowStockMedicines = Medicine::where('stock', '<', 10)->get();

        // Expired medicines
        $expiredMedicines = Medicine::whereDate('expiry_date', '<', Carbon::today())->get();

        // Sales trend (last 7 days)
        $sales = Invoice::select(
                    DB::raw('DATE(invoice_date) as date'),
                    DB::raw('SUM(grand_total) as total')
                )
                ->where('invoice_date', '>=', Carbon::now()->subDays(7))
                ->groupBy('date')
                ->orderBy('date')
                ->get();

        $salesDates   = $sales->pluck('date')->toArray();
        $salesAmounts = $sales->pluck('total')->toArray();

        return view('dashboard', compact(
            'totalCategories',
            'totalMedicines',
            'totalCustomers',
            'totalInvoices',
            'todaysSales',
            'thisMonthSales',
            'lastMonthSales',
            'topMedicines',
            'lowStockMedicines',
            'expiredMedicines',
            'salesDates',
            'salesAmounts',
            'totalPrescription',
            'totalPatient'
        ));
    }
}
