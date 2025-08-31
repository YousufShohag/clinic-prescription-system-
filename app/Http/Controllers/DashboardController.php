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
use App\Models\Appointment;
use DB;

class DashboardController extends Controller
{
    // public function index()
    // {
    //     // KPI counts
    //     $totalCategories = Category::count();
    //     $totalMedicines  = Medicine::count();
    //     $totalCustomers  = Customer::count();
    //     $totalInvoices   = Invoice::count();
    //     $totalPrescription   = Prescription::count();
    //     $totalPatient   = Patient::count();

    //     // Sales summary
    //     $todaysSales    = Invoice::whereDate('invoice_date', Carbon::today())->sum('grand_total');
    //     $thisMonthSales = Invoice::whereMonth('invoice_date', Carbon::now()->month)
    //                             ->whereYear('invoice_date', Carbon::now()->year)
    //                             ->sum('grand_total');
    //     $lastMonthSales = Invoice::whereMonth('invoice_date', Carbon::now()->subMonth()->month)
    //                             ->whereYear('invoice_date', Carbon::now()->subMonth()->year)
    //                             ->sum('grand_total');

    //     // Top 5 medicines
    //     $topMedicines = DB::table('invoice_items')
    //         ->join('medicines', 'invoice_items.medicine_id', '=', 'medicines.id')
    //         ->select('medicines.name', DB::raw('SUM(invoice_items.quantity) as total_qty'))
    //         ->groupBy('medicines.name')
    //         ->orderByDesc('total_qty')
    //         ->limit(5)
    //         ->get();

    //     // Low stock (less than 10)
    //     $lowStockMedicines = Medicine::where('stock', '<', 10)->get();

    //     // Expired medicines
    //     $expiredMedicines = Medicine::whereDate('expiry_date', '<', Carbon::today())->get();

    //     // Sales trend (last 7 days)
    //     $sales = Invoice::select(
    //                 DB::raw('DATE(invoice_date) as date'),
    //                 DB::raw('SUM(grand_total) as total')
    //             )
    //             ->where('invoice_date', '>=', Carbon::now()->subDays(7))
    //             ->groupBy('date')
    //             ->orderBy('date')
    //             ->get();

    //     $salesDates   = $sales->pluck('date')->toArray();
    //     $salesAmounts = $sales->pluck('total')->toArray();

    //     return view('dashboard', compact(
    //         'totalCategories',
    //         'totalMedicines',
    //         'totalCustomers',
    //         'totalInvoices',
    //         'todaysSales',
    //         'thisMonthSales',
    //         'lastMonthSales',
    //         'topMedicines',
    //         'lowStockMedicines',
    //         'expiredMedicines',
    //         'salesDates',
    //         'salesAmounts',
    //         'totalPrescription',
    //         'totalPatients'
    //     ));
    // }

    public function index()
    {
        $today    = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        // Totals
        $totalPatient       = Patient::count();
        $totalPrescription  = Prescription::count();

        // If your appointments have a single DATETIME column "scheduled_at":
        $appointmentsTodayCount    = Appointment::whereDate('scheduled_at', $today)->count();
        $appointmentsTomorrowCount = Appointment::whereDate('scheduled_at', $tomorrow)->count();

        $todayAppointments = Appointment::with(['patient:id,name,phone', 'doctor:id,name'])
            ->whereDate('scheduled_at', $today)
            ->orderBy('scheduled_at')
            ->limit(20)
            ->get();

        $tomorrowAppointments = Appointment::with(['patient:id,name,phone', 'doctor:id,name'])
            ->whereDate('scheduled_at', $tomorrow)
            ->orderBy('scheduled_at')
            ->limit(20)
            ->get();

        /* -------------------------------------------------------------
         * If instead you store separate columns (date, start_time),
         * replace the four queries above with:
         *
         * $appointmentsTodayCount    = Appointment::whereDate('date', $today)->count();
         * $appointmentsTomorrowCount = Appointment::whereDate('date', $tomorrow)->count();
         *
         * $todayAppointments = Appointment::with(['patient:id,name,phone', 'doctor:id,name'])
         *     ->whereDate('date', $today)
         *     ->orderBy('date')->orderBy('start_time')
         *     ->limit(20)
         *     ->get();
         *
         * $tomorrowAppointments = Appointment::with(['patient:id,name,phone', 'doctor:id,name'])
         *     ->whereDate('date', $tomorrow)
         *     ->orderBy('date')->orderBy('start_time')
         *     ->limit(20)
         *     ->get();
         * ------------------------------------------------------------- */

        return view('dashboard', [
            'totalPatient'              => $totalPatient,
            'totalPrescription'         => $totalPrescription,
            'appointmentsTodayCount'    => $appointmentsTodayCount,
            'appointmentsTomorrowCount' => $appointmentsTomorrowCount,
            'todayAppointments'         => $todayAppointments,
            'tomorrowAppointments'      => $tomorrowAppointments,
        ]);
    }
}
