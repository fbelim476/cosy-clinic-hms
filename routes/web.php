<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\LabController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\MedicineController;
use App\Http\Controllers\Admin\SettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => auth()->check() ? redirect(auth()->user()->dashboardRoute()) : redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/queue/display', fn () => view('pages.queue.display'))->name('queue.display');

Route::middleware('auth')->group(function () {
    Route::get('/print/opd-slip/{visit}', [PrintController::class, 'opdSlip'])->name('print.opd-slip');
    Route::get('/print/prescription/{visit}', [PrintController::class, 'prescription'])->name('print.prescription');
    Route::get('/print/pharmacy-invoice/{order}', [PrintController::class, 'pharmacyInvoice'])->name('print.pharmacy-invoice');
    Route::get('/print/invoice/{invoice}', [PrintController::class, 'invoice'])->name('print.invoice');
    Route::get('/print/patient-card/{patient}', [PrintController::class, 'patientCard'])->name('print.patient-card');

    Route::middleware('role:super-admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', fn () => view('pages.admin.dashboard'))->name('dashboard');
        Route::get('/users', [UserController::class, 'index'])->name('users');
        Route::get('/medicines', [MedicineController::class, 'index'])->name('medicines');
        Route::post('/medicines', [MedicineController::class, 'store'])->name('medicines.store');
        Route::get('/medicines/export', [MedicineController::class, 'export'])->name('medicines.export');
        Route::get('/medicines/template', [MedicineController::class, 'template'])->name('medicines.template');
        Route::post('/medicines/import', [MedicineController::class, 'import'])->name('medicines.import');
        Route::get('/settings', [SettingController::class, 'index'])->name('settings');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    });

    Route::middleware('role:receptionist,nurse,super-admin')->prefix('reception')->name('reception.')->group(function () {
        Route::get('/dashboard', fn () => view('pages.reception.dashboard'))->name('dashboard');
        Route::get('/register', fn () => view('pages.reception.register'))->name('register');
        Route::get('/patients', [PatientController::class, 'index'])->name('patients');
        Route::get('/patients/{patient}', [PatientController::class, 'show'])->name('patients.show');
    });

    Route::middleware('role:doctor,super-admin')->prefix('doctor')->name('doctor.')->group(function () {
        Route::get('/dashboard', fn () => view('pages.doctor.dashboard'))->name('dashboard');
        Route::get('/consult/{visit}', fn (\App\Models\PatientVisit $visit) => view('pages.doctor.consult', compact('visit')))->name('consult');
    });

    Route::middleware('role:pharmacist,super-admin')->prefix('pharmacy')->name('pharmacy.')->group(function () {
        Route::get('/dashboard', fn () => view('pages.pharmacy.dashboard'))->name('dashboard');
    });

    Route::middleware('role:accountant,super-admin')->prefix('billing')->name('billing.')->group(function () {
        Route::get('/dashboard', [BillingController::class, 'index'])->name('dashboard');
        Route::post('/payment/{invoice}', [BillingController::class, 'payment'])->name('payment');
    });

    Route::middleware('role:lab-technician,super-admin')->prefix('lab')->name('lab.')->group(function () {
        Route::get('/dashboard', [LabController::class, 'index'])->name('dashboard');
        Route::post('/complete/{order}', [LabController::class, 'complete'])->name('complete');
    });

    Route::get('/patient/portal', fn () => view('pages.patient.portal'))->middleware('role:patient')->name('patient.portal');
});
