<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;

Route::get('/', [ReportController::class, 'index'])->name('reports.index');

Route::get('/api/default-template', function () {
    $path = storage_path('app/default_template.json');
    if (file_exists($path)) {
        return response()->file($path, ['Content-Type' => 'application/json']);
    }
    return response()->json([]);
})->name('reports.defaultTemplate');

Route::prefix('reports')->name('reports.')->group(function () {
    Route::post('/', [ReportController::class, 'store'])->name('store');
    Route::post('/submit-and-download', [ReportController::class, 'submitAndDownload'])->name('submitAndDownload');
    Route::get('/{report}', [ReportController::class, 'show'])->name('show');
    Route::put('/{report}', [ReportController::class, 'update'])->name('update');
    Route::delete('/{report}', [ReportController::class, 'destroy'])->name('destroy');
    Route::post('/{report}/duplicate', [ReportController::class, 'duplicate'])->name('duplicate');
    Route::get('/{report}/export', [ReportController::class, 'export'])->name('export');
});
