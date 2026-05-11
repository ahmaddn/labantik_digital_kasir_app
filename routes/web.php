<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;
use App\Livewire\TransactionForm;
use App\Livewire\ProductManager;
use App\Livewire\CategoryManager;
use App\Livewire\DailyRecapView;
use App\Livewire\MonthlyRecapView;
use App\Livewire\YearlyRecapView;
use App\Livewire\KasirMode;

Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/transactions', TransactionForm::class)->name('transactions');
    Route::get('/products', ProductManager::class)->name('products');
    Route::get('/categories', CategoryManager::class)->name('categories');
    Route::get('/suppliers', \App\Livewire\SupplierManager::class)->name('suppliers');
    Route::get('/kasir', KasirMode::class)->name('kasir');

    // Recaps
    Route::get('/daily-recap', DailyRecapView::class)->name('daily-recap');
    Route::get('/monthly-recap', MonthlyRecapView::class)->name('monthly-recap');
    Route::get('/yearly-recap', YearlyRecapView::class)->name('yearly-recap');
    Route::get('/inventory-report', \App\Livewire\InventoryReportView::class)->name('inventory-report');
    Route::get('/supplier-report', \App\Livewire\SupplierReportView::class)->name('supplier-report');
});


require __DIR__ . '/settings.php';
