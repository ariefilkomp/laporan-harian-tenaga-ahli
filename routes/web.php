<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanHarianController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/laporan-harian', [LaporanHarianController::class, 'index'])->name('laporanHarian');
    Route::get('/laporan-harian-docx', [LaporanHarianController::class, 'docx'])->name('docx');
    Route::post('/lh-delete', [LaporanHarianController::class, 'delete'])->name('lh.delete');
    Route::post('/lh-save', [LaporanHarianController::class, 'save'])->name('lh.save');
    Route::post('/upload-bukti', [LaporanHarianController::class, 'uploadBukti'])->name('uploadBukti');
    Route::post('/hapus-bukti', [LaporanHarianController::class, 'hapusBukti'])->name('hapusBukti');
});

require __DIR__.'/auth.php';
