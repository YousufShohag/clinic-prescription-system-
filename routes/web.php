<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ProfileController,
    CategoryController,
    MedicineController,
    DashboardController,
    ReportController,      // (unused here but left for parity)
    DoctorController,
    TestController,
    PatientController,
    PrescriptionController,
    TestCategoryController,
    InvoiceController,
    CustomerController,
    AppointmentController,
    CalenderController,

};

Route::get('/', function () {
    return view('frontend.welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/* Profile (auth) */
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/* ========== App routes (all behind auth) ========== */
Route::middleware(['auth'])->group(function () {

    /* ---- AJAX endpoints (define BEFORE resources) ---- */
    Route::get('/medicines/search', [MedicineController::class, 'search'])->name('medicines.search');
    Route::get('/tests/search', [TestController::class, 'search'])->name('tests.search');
    Route::get('/patients/search', [PatientController::class, 'search'])->name('patients.search');

    // Patient history + list of prescriptions (JSON for UI)
    Route::get('/patients/{patient}/history', [PatientController::class, 'history'])->name('patients.history');
    Route::get('/patients/{patient}/prescriptions', [PrescriptionController::class, 'byPatient'])->name('patients.prescriptions');

    /* ---- Medicines ---- */
    Route::get('/medicines/history/{medicine}', [MedicineController::class, 'history'])->name('medicines.history');
    Route::post('/medicines/import', [MedicineController::class, 'importCsv'])->name('medicines.import');
    Route::resource('medicines', MedicineController::class)->except('show'); // no show page



Route::get('/patients/{patient}/documents', [PatientController::class, 'documents'])
     ->name('patients.documents');
    /* ---- Core Resources ---- */
    Route::resource('tests', TestController::class);
    Route::resource('doctors', DoctorController::class);
    Route::resource('patients', PatientController::class);

// ---------------------------------------
    // Calendar data: counts per day in a range
    Route::get('/calendars/calendar-data1', [CalenderController::class, 'calendarData'])
    ->name('appointments.calendar');

// Day detail list (history for a date)
    Route::get('/calendars/day1', [CalenderController::class, 'dayList'])
    ->name('appointments.day1');
// ------------------------------------------
    // Calendar data: counts per day in a range
    Route::get('/appointments/calendar-data', [AppointmentController::class, 'calendarData'])
    ->name('appointments.calendarData');

// Day detail list (history for a date)
    Route::get('/appointments/day', [AppointmentController::class, 'dayList'])
    ->name('appointments.day');

    Route::resource('appointments', AppointmentController::class);

    Route::resource('calendars', CalenderController::class);

    Route::resource('test-categories', TestCategoryController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('categories', CategoryController::class);

    /* ---- Invoices ---- */
    Route::get('invoices/due', [InvoiceController::class, 'dueInvoices'])->name('invoices.due');
    Route::get('invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');
    Route::put('invoices/{invoice}/quick-pay', [InvoiceController::class, 'quickPay'])->name('invoices.quickPay');
    Route::resource('invoices', InvoiceController::class);

    /* ---- Prescriptions ---- */
    //  Route::get('prescriptions/{prescription}/pdf', [PrescriptionController::class, 'pdf'])->name('prescriptions.pdf');
Route::get('/prescriptions/{prescription}/pdf-tcpdf', [PrescriptionController::class, 'pdfTcpdf'])
    ->name('prescriptions.pdf.tcpdf');

    //  Route::get('prescriptions/{prescription}/pdf', [PrescriptionController::class, 'pdf'])->name('prescriptions.pdf');
    Route::resource('prescriptions', PrescriptionController::class);
    // If you truly need a custom show, do:
    // Route::resource('prescriptions', PrescriptionController::class)->except('show');
    // Route::get('/prescriptions/{prescription}', [PrescriptionController::class, 'show'])->name('prescriptions.show');


    Route::get('/reports/doctor-patients', [ReportController::class, 'doctorPatients'])
    ->name('reports.doctorPatients');

    Route::get('/reports/doctor-patients/export', [ReportController::class, 'doctorPatientsExport'])
        ->name('reports.doctorPatients.export');

        // Detailed prescriptions “story” page
    Route::get('/reports/prescriptions', [ReportController::class, 'prescriptions'])
    ->name('reports.prescriptions');

// CSV export for the same view
    Route::get('/reports/prescriptions/export', [ReportController::class, 'prescriptionsExport'])
    ->name('reports.prescriptions.export');




});

require __DIR__ . '/auth.php';

Route::view('/portfolio', 'portfolio')->name('portfolio');
