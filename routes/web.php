<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartemenController;
use App\Http\Controllers\HandoverController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['id', 'en'])) {
        session(['locale' => $locale]);
        if (auth()->check()) {
            auth()->user()->update(['locale' => $locale]);
        }
    }
    return redirect()->back();
})->name('lang.switch');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ==========================================
    // ADMIN ONLY ROUTES (Super Admin & Admin IT)
    // ==========================================
    Route::middleware('admin')->group(function () {
        // Master Aset Management (Admin Operations)
        Route::get('/barang/generate-code', [BarangController::class, 'generateAssetCode'])->name('barang.generate-code');
        Route::get('/barang/template/download', [BarangController::class, 'downloadTemplate'])->name('barang.template');
        Route::post('/barang/import', [BarangController::class, 'importExcel'])->name('barang.import');
        Route::get('/barang/create', [BarangController::class, 'create'])->name('barang.create');
        Route::post('/barang', [BarangController::class, 'store'])->name('barang.store');
        Route::get('/barang/{barang}/edit', [BarangController::class, 'edit'])->name('barang.edit');
        Route::put('/barang/{barang}', [BarangController::class, 'update'])->name('barang.update');
        Route::patch('/barang/{barang}', [BarangController::class, 'update']);
        Route::delete('/barang/{barang}', [BarangController::class, 'destroy'])->name('barang.destroy');
        Route::get('/barang/{barang}/handover', [BarangController::class, 'handover'])->name('barang.handover');
        Route::post('/barang/{barang}/handover', [BarangController::class, 'storeHandover'])->name('barang.storeHandover');
        Route::get('/barang/barcode/batch', [BarangController::class, 'printBarcodeBatch'])->name('barang.barcode.batch');
        Route::get('/barang/qrcode/batch', [BarangController::class, 'printBarcodeBatch'])->name('barang.qrcode.batch');

        // Categories, Vendors, Departemens & Lokasi
        Route::resource('category', CategoryController::class)->except(['create', 'edit', 'show']); 
        Route::resource('vendor', VendorController::class)->except(['create', 'edit', 'show']);
        Route::resource('departemen', DepartemenController::class)->parameters(['departemen' => 'departemen'])->except(['create', 'edit', 'show']);
        Route::resource('lokasi', LokasiController::class)->except(['create', 'edit', 'show']);

        // Handover & Surat Tanda Terima (Receipt Admin Actions)
        Route::get('/handover/create', [HandoverController::class, 'create'])->name('handover.create');
        Route::post('/handover', [HandoverController::class, 'store'])->name('handover.store');

        // Maintenance & Servis Aset (Exports & Reports)
        Route::get('/maintenance/export/excel', [MaintenanceController::class, 'exportExcel'])->name('maintenance.export_excel');
        Route::get('/maintenance/report/print', [MaintenanceController::class, 'printReport'])->name('maintenance.report_print');
        Route::patch('/maintenance/{maintenance}/complete', [MaintenanceController::class, 'complete'])->name('maintenance.complete');
        Route::get('/maintenance/{maintenance}/print', [MaintenanceController::class, 'print'])->name('maintenance.print');
        Route::resource('maintenance', MaintenanceController::class);

        // IT Helpdesk Admin Actions & Exports
        Route::get('/ticket/export/excel', [TicketController::class, 'exportExcel'])->name('ticket.export_excel');
        Route::get('/ticket/report/print', [TicketController::class, 'printReport'])->name('ticket.report_print');
        Route::put('/ticket/{ticket}', [TicketController::class, 'update'])->name('ticket.update');
        Route::patch('/ticket/{ticket}', [TicketController::class, 'update']);
        Route::delete('/ticket/{ticket}', [TicketController::class, 'destroy'])->name('ticket.destroy');

        // User Management & Audit Activity Logs
        Route::resource('users', UserController::class)->except(['create', 'edit', 'show']);
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity_logs.index');
    });

    // ==========================================
    // SHARED / GENERAL ROUTES (Staff & Admin)
    // ==========================================
    
    // Master Aset (Read-only for Staff, scoped to assigned assets)
    Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');
    Route::get('/barang/{barang}', [BarangController::class, 'show'])->name('barang.show');
    Route::get('/barang/{barang}/barcode', [BarangController::class, 'printBarcode'])->name('barang.barcode');
    Route::get('/barang/{barang}/qrcode', [BarangController::class, 'printBarcode'])->name('barang.qrcode');

    // Handover & BAST (Shared: Staff views assigned handovers, Admin views all)
    Route::get('/handover-history', [HandoverController::class, 'index'])->name('handover.history');
    Route::get('/handover/receipt/{no_surat?}', [HandoverController::class, 'receipt'])->where('no_surat', '.*')->name('handover.receipt');
    Route::post('/handover/accept/{no_surat?}', [HandoverController::class, 'accept'])->where('no_surat', '.*')->name('handover.accept');

    // IT Helpdesk (Shared Routes: List, Create, Show, Response, Print)
    Route::get('/ticket', [TicketController::class, 'index'])->name('ticket.index');
    Route::post('/ticket', [TicketController::class, 'store'])->name('ticket.store');
    Route::get('/ticket/{ticket}', [TicketController::class, 'show'])->name('ticket.show');
    Route::post('/ticket/{ticket}/response', [TicketController::class, 'response'])->name('ticket.response');
    Route::get('/ticket/{ticket}/print', [TicketController::class, 'print'])->name('ticket.print');
});

require __DIR__.'/auth.php';