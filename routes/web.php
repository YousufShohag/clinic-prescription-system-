<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\TestCategoryController;


// routes/web.php
use App\Http\Controllers\InvoiceController;

// routes/web.php
use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



Route::middleware(['auth'])->group(function () {
    Route::resource('customers', CustomerController::class);
});


Route::middleware(['auth'])->group(function () {
    Route::resource('categories', CategoryController::class);
});



Route::middleware(['auth'])->group(function () {
    // custom route
    Route::get('/medicines/history/{medicine}', [MedicineController::class, 'history'])
        ->name('medicines.history');

    // resource route
    Route::resource('medicines', MedicineController::class)->except('show');
});



// Route::middleware('auth')->group(function() {
//     // Route::get('/tests', [TestController::class, 'index'])->name('tests.index');
//     Route::get('/tests/{test}', [TestController::class, 'show'])->name('tests.show');

//     Route::resource('tests', TestController::class);
// });

Route::middleware('auth')->group(function() {
    Route::resource('tests', TestController::class);
});


Route::middleware('auth')->group(function() {
    Route::resource('doctors', DoctorController::class);
});








// Route::middleware(['auth'])->group(function () {
//     //  custom routes first
//     Route::get('invoices/due', [InvoiceController::class, 'dueInvoices'])->name('invoices.due');
//     Route::get('invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');

// Route::put('invoices/{invoice}/quick-pay', [InvoiceController::class, 'quickPay'])
//     ->name('invoices.quickPay');

//     // register resource
//     Route::resource('invoices', InvoiceController::class);
// });

Route::middleware(['auth'])->group(function () {
    Route::get('invoices/due', [InvoiceController::class, 'dueInvoices'])->name('invoices.due');
    Route::get('invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');

    // 👇 Quick Pay
    Route::put('invoices/{invoice}/quick-pay', [InvoiceController::class, 'quickPay'])->name('invoices.quickPay');

    Route::resource('invoices', InvoiceController::class);
});


// Route::get('/reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel');
// Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');

Route::middleware(['auth'])->group(function () {
    Route::resource('patients', PatientController::class);
});

// Route::middleware('auth')->group(function () {
//     Route::get('/medicines/search', [MedicineController::class, 'search'])->name('medicines.search');
// Route::get('/tests/search', [PrescriptionController::class, 'searchTests'])->name('tests.search');
//     Route::resource('prescriptions', PrescriptionController::class);

// });
Route::middleware('auth')->group(function() {
    // Prescription resource
    Route::resource('prescriptions', PrescriptionController::class);

    // AJAX search routes
    Route::get('/medicines/search', [MedicineController::class, 'search'])->name('medicines.search');
    Route::get('/tests/search', [PrescriptionController::class, 'searchTests'])->name('tests.search');
});


// Route::middleware('auth')->group(function() {

//     // Prescription routes
//     Route::get('prescriptions/create', [PrescriptionController::class, 'create'])->name('prescriptions.create');
//     Route::post('prescriptions', [PrescriptionController::class, 'store'])->name('prescriptions.store');

//     // Dynamic search
//     Route::get('medicines/search', [MedicineController::class, 'search'])->name('medicines.search');
//     Route::get('tests/search', [TestController::class, 'search'])->name('tests.search');
// });


Route::middleware('auth')->group(function() {
    Route::resource('test-categories', TestCategoryController::class);
});

// Route::middleware('auth')->group(function() {
//     Route::get('/patients/{patient}/prescriptions', [PrescriptionController::class, 'byPatient'])
//         ->name('patients.prescriptions'); // AJAX endpoint returning JSON
// });

Route::middleware(['auth'])->group(function () {

    // AJAX: list prescriptions for a patient (JSON)
    Route::get('/patients/{patient}/prescriptions', [PrescriptionController::class, 'byPatient'])
        ->name('patients.prescriptions');

    // Ensure you have a show route with this exact name:
    Route::get('/prescriptions/{prescription}', [PrescriptionController::class, 'show'])
        ->name('prescriptions.show'); // <<< used in the modal “View” link
});


require __DIR__.'/auth.php';


Route::view('/portfolio', 'portfolio')->name('portfolio');

