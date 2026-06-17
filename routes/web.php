<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController; // TAMBAHKAN INI

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    // Route untuk generate kode otomatis via AJAX
    Route::get('/barang/generate-code', [\App\Http\Controllers\BarangController::class, 'generateAssetCode'])->name('barang.generate-code');
    
    Route::get('barang/{barang}/handover', [BarangController::class, 'handover'])->name('barang.handover');
    Route::post('barang/{barang}/handover', [BarangController::class, 'storeHandover'])->name('barang.storeHandover');

    Route::resource('barang', BarangController::class);
    Route::post('/barang/import', [App\Http\Controllers\BarangController::class, 'importExcel'])->name('barang.import');
    Route::resource('category', CategoryController::class); 
    
    
    // Route untuk halaman Handover History
    Route::get('/handover-history', [App\Http\Controllers\HandoverController::class, 'index'])->name('handover.history');

    Route::resource('users', UserController::class); // TAMBAHKAN INI

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';