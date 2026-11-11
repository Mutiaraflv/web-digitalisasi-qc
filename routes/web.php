<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PartController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\NcrController;
use App\Http\Controllers\CalibrationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes - Enterprise QCMS PT SSC Works
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:10,1')->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    // 1. Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/dashboard/live-data', [DashboardController::class, 'liveData'])->name('dashboard.liveData');

    // 2. Input Inspeksi Baru (Workspace Utama)
    Route::get('/inspection', [ReportController::class, 'create'])->name('inspection.create');
    Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');

    // 3. Riwayat & Operasional Laporan QC
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::post('/', [ReportController::class, 'store'])->name('store');
        Route::post('/submit-and-download', [ReportController::class, 'submitAndDownload'])->name('submitAndDownload');
        Route::post('/upload-photo', [ReportController::class, 'uploadPhoto'])->name('uploadPhoto');
        Route::get('/{report}', [ReportController::class, 'show'])->name('show');
        Route::get('/{report}/edit', [ReportController::class, 'edit'])->name('edit');
        Route::put('/{report}', [ReportController::class, 'update'])->name('update');
        Route::delete('/{report}', [ReportController::class, 'destroy'])->name('destroy');
        Route::get('/{report}/export', [ReportController::class, 'export'])->name('export');
        Route::get('/{report}/pdf', [ReportController::class, 'pdf'])->name('pdf');
        Route::get('/{report}/review', [ReportController::class, 'review'])->name('review');
        Route::post('/{report}/finish-review', [ReportController::class, 'finishReview'])->name('finishReview');
    });

    // 4. Manajemen Template & Katalog Komponen
    Route::get('/templates', [PartController::class, 'index'])->name('templates.index');
    Route::get('/parts', [PartController::class, 'index'])->name('parts.index');

    // 5. Modul Pendukung
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/ncrs', [NcrController::class, 'index'])->name('ncrs.index');
    Route::get('/calibration-tools', [CalibrationController::class, 'index'])->name('calibration.index');

    // 6. Pengaturan Sistem & Kelola Akun (Khusus Administrator)
    Route::middleware('admin')->prefix('settings')->name('settings.')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // API Endpoint Template Default (Blank)
    Route::get('/api/default-template', function () {
        $path = storage_path('app/default_template.json');
        if (file_exists($path)) {
            return response()->file($path, ['Content-Type' => 'application/json']);
        }
        return response()->json([]);
    })->name('reports.defaultTemplate');
});
