<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// ─── Owner Routes ────────────────────────────────────────────────
Route::middleware(['auth', 'role:owner'])->prefix('owner')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'owner'])->name('owner.dashboard');

    Route::resource('users', UserController::class)->except(['show', 'destroy']);
    Route::resource('branches', \App\Http\Controllers\BranchController::class)->except(['show', 'destroy']);

    Route::get('/categories', [\App\Http\Controllers\Owner\ViewController::class, 'categories'])->name('categories.index');
    Route::get('/suppliers', [\App\Http\Controllers\Owner\ViewController::class, 'suppliers'])->name('suppliers.index');
    Route::get('/units', [\App\Http\Controllers\Owner\ViewController::class, 'units'])->name('units.index');
    Route::get('/products', [\App\Http\Controllers\Owner\ViewController::class, 'products'])->name('products.index');
    Route::get('/stock-ins', [\App\Http\Controllers\Owner\ViewController::class, 'stockIns'])->name('incoming-stocks.index');
    Route::get('/stock-outs', [\App\Http\Controllers\Owner\ViewController::class, 'stockOuts'])->name('outgoing-stocks.index');
    Route::get('/stock-transfers', [\App\Http\Controllers\Owner\ViewController::class, 'stockTransfers'])->name('stock-transfers.index');
    Route::get('/transactions', [\App\Http\Controllers\Owner\ViewController::class, 'sales'])->name('transactions.index');
    Route::get('/transactions/{sale}', [\App\Http\Controllers\Owner\ViewController::class, 'showTransaction'])->name('transactions.show');
    Route::get('/returns', [\App\Http\Controllers\Owner\ViewController::class, 'returnItems'])->name('returns.index');

    Route::get('/discounts', [\App\Http\Controllers\Owner\DiscountApprovalController::class, 'index'])->name('owner.discounts.index');
    Route::post('/discounts/{discount}/approve', [\App\Http\Controllers\Owner\DiscountApprovalController::class, 'approve'])->name('owner.discounts.approve');
    Route::post('/discounts/{discount}/reject', [\App\Http\Controllers\Owner\DiscountApprovalController::class, 'reject'])->name('owner.discounts.reject');

    Route::get('/shifts', [\App\Http\Controllers\Owner\ViewController::class, 'shifts'])->name('shifts.index');
    Route::get('/reports/sales', [\App\Http\Controllers\Owner\ViewController::class, 'reportSales'])->name('reports.sales');
    Route::get('/reports/stocks', [\App\Http\Controllers\Owner\ViewController::class, 'reportStocks'])->name('reports.stocks');
    
    // Advanced Reports
    Route::get('/reports/best-sellers', [\App\Http\Controllers\Owner\ViewController::class, 'bestSellers'])->name('reports.best-sellers');
    Route::get('/reports/stock-card/{branch_id}/{product_id}', [\App\Http\Controllers\Owner\ViewController::class, 'stockCard'])->name('reports.stock-card');
});

// ─── Kepala Cabang Routes ────────────────────────────────────────
Route::middleware(['auth', 'role:kepala_cabang'])->prefix('kepala-cabang')->name('kepala-cabang.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'kepalaCabang'])->name('dashboard');

    Route::resource('categories', \App\Http\Controllers\KepalaCabang\CategoryController::class)->except(['show', 'destroy']);
    Route::resource('units', \App\Http\Controllers\KepalaCabang\UnitController::class)->except(['show', 'destroy']);
    Route::resource('suppliers', \App\Http\Controllers\KepalaCabang\SupplierController::class)->except(['show', 'destroy']);
    Route::resource('products', \App\Http\Controllers\KepalaCabang\ProductController::class)->except(['show', 'destroy']);

    Route::resource('stock-ins', \App\Http\Controllers\KepalaCabang\StockInController::class)->except(['show', 'destroy']);
    Route::resource('stock-outs', \App\Http\Controllers\KepalaCabang\StockOutController::class)->except(['show', 'destroy']);
    Route::resource('stock-transfers', \App\Http\Controllers\KepalaCabang\StockTransferController::class)->except(['show', 'destroy']);
    Route::post('/stock-transfers/{stock_transfer}/approve', [\App\Http\Controllers\KepalaCabang\StockTransferController::class, 'approve'])->name('stock-transfers.approve');
    Route::post('/stock-transfers/{stock_transfer}/reject', [\App\Http\Controllers\KepalaCabang\StockTransferController::class, 'reject'])->name('stock-transfers.reject');
    Route::post('/stock-transfers/{stock_transfer}/receive', [\App\Http\Controllers\KepalaCabang\StockTransferController::class, 'receive'])->name('stock-transfers.receive');
    Route::resource('returns', \App\Http\Controllers\KepalaCabang\ReturnController::class)->except(['edit', 'destroy']);
    Route::post('/returns/{returnItem}/approve', [\App\Http\Controllers\KepalaCabang\ReturnController::class, 'approve'])->name('returns.approve');
    Route::post('/returns/{returnItem}/reject', [\App\Http\Controllers\KepalaCabang\ReturnController::class, 'reject'])->name('returns.reject');

    Route::resource('discounts', \App\Http\Controllers\KepalaCabang\DiscountController::class)->only(['index', 'create', 'store', 'destroy']);

    Route::get('/shifts', [\App\Http\Controllers\KepalaCabang\ShiftController::class, 'index'])->name('shifts.index');

    Route::get('/transactions', [\App\Http\Controllers\KepalaCabang\ReportController::class, 'transactions'])->name('transactions.index');
    Route::get('/transactions/{sale}', [\App\Http\Controllers\KepalaCabang\ReportController::class, 'showTransaction'])->name('transactions.show');
    Route::get('/reports/sales', [\App\Http\Controllers\KepalaCabang\ReportController::class, 'sales'])->name('reports.sales');
    Route::get('/reports/stocks', [\App\Http\Controllers\KepalaCabang\ReportController::class, 'stocks'])->name('reports.stocks');
    
    // Advanced Reports
    Route::get('/reports/best-sellers', [\App\Http\Controllers\KepalaCabang\ReportController::class, 'bestSellers'])->name('reports.best-sellers');
    Route::get('/reports/stock-card/{product_id}', [\App\Http\Controllers\KepalaCabang\ReportController::class, 'stockCard'])->name('reports.stock-card');
});

// ─── Kasir Routes ────────────────────────────────────────────────
Route::middleware(['auth', 'role:kasir'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'kasir'])->name('dashboard');
    
    // Shift Routes
    Route::get('/shifts/create', [\App\Http\Controllers\Kasir\ShiftController::class, 'create'])->name('shifts.create');
    Route::post('/shifts/store', [\App\Http\Controllers\Kasir\ShiftController::class, 'store'])->name('shifts.store');
    Route::get('/shifts/close', [\App\Http\Controllers\Kasir\ShiftController::class, 'edit'])->name('shifts.close');
    Route::put('/shifts/update', [\App\Http\Controllers\Kasir\ShiftController::class, 'update'])->name('shifts.update');
    
    Route::resource('transactions', \App\Http\Controllers\Kasir\TransactionController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('transactions/{transaction}/void', [\App\Http\Controllers\Kasir\TransactionController::class, 'voidTransaction'])->name('transactions.void');
    Route::resource('returns', \App\Http\Controllers\Kasir\ReturnController::class)->only(['index', 'create', 'store']);
    
    Route::get('/categories', [\App\Http\Controllers\Kasir\MasterDataController::class, 'categories'])->name('categories.index');
    Route::get('/units', [\App\Http\Controllers\Kasir\MasterDataController::class, 'units'])->name('units.index');
    Route::get('/suppliers', [\App\Http\Controllers\Kasir\MasterDataController::class, 'suppliers'])->name('suppliers.index');
    Route::get('/products', [\App\Http\Controllers\Kasir\MasterDataController::class, 'products'])->name('products.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/photo', [\App\Http\Controllers\ProfileController::class, 'updatePhoto'])->name('profile.updatePhoto');
    Route::put('/profile/security', [\App\Http\Controllers\ProfileController::class, 'updateSecurity'])->name('profile.updateSecurity');
});

require __DIR__.'/auth.php';
