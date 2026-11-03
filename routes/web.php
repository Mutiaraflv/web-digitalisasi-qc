<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PartController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\NcrController;
use App\Http\Controllers\CalibrationController;

/*
|--------------------------------------------------------------------------
| Web Routes - Enterprise QCMS
|--------------------------------------------------------------------------
*/

// Dashboard Eksekutif Mutu
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index']);

// Operasional QC & Job Inspeksi
Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
Route::prefix('reports')->name('reports.')->group(function () {
    Route::post('/', [ReportController::class, 'store'])->name('store');
    Route::post('/submit-and-download', [ReportController::class, 'submitAndDownload'])->name('submitAndDownload');
    Route::get('/{report}', [ReportController::class, 'show'])->name('show');
    Route::put('/{report}', [ReportController::class, 'update'])->name('update');
    Route::delete('/{report}', [ReportController::class, 'destroy'])->name('destroy');
    Route::post('/{report}/duplicate', [ReportController::class, 'duplicate'])->name('duplicate');
    Route::get('/{report}/export', [ReportController::class, 'export'])->name('export');
});

// Master Data Mutu
Route::get('/parts', [PartController::class, 'index'])->name('parts.index');
Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');

// Manajemen Non-Conformance (NCR)
Route::get('/ncrs', [NcrController::class, 'index'])->name('ncrs.index');

// Manajemen Alat Ukur & Kalibrasi
Route::get('/calibration-tools', [CalibrationController::class, 'index'])->name('calibration.index');

// API Endpoint Template Default
Route::get('/api/default-template', function () {
    $path = storage_path('app/default_template.json');
    if (file_exists($path)) {
        return response()->file($path, ['Content-Type' => 'application/json']);
    }
    return response()->json([]);
})->name('reports.defaultTemplate');
