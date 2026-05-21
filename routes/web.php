<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;
use App\Livewire\History\Transactions;
use App\Livewire\Management\Product;
use App\Livewire\Management\Category;
use App\Livewire\Management\Supplier;
use App\Livewire\Management\Debt;
use App\Livewire\Pos\Kasir;
use App\Livewire\Reports\DailyRecap;
use App\Livewire\Reports\MonthlyRecap;
use App\Livewire\Reports\YearlyRecap;
use App\Livewire\Reports\InventoryReport;
use App\Livewire\Reports\SupplierReport;
use App\Livewire\Reports\WeeklyProfit;
use App\Livewire\Finance\CashManagement;

Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/transactions', Transactions::class)->name('transactions');
    Route::get('/products', Product::class)->name('products');
    Route::get('/categories', Category::class)->name('categories');
    Route::get('/suppliers', Supplier::class)->name('suppliers');
    Route::get('/cashier', Kasir::class)->name('kasir');

    // Recaps
    Route::get('/daily-recap/{date?}', DailyRecap::class)->name('daily-recap');
    Route::get('/monthly-recap', MonthlyRecap::class)->name('monthly-recap');
    Route::get('/yearly-recap', YearlyRecap::class)->name('yearly-recap');
    Route::get('/inventory-report', InventoryReport::class)->name('inventory-report');
    Route::get('/supplier-report', SupplierReport::class)->name('supplier-report');
    Route::get('/debts', Debt::class)->name('debts');
    Route::get('/profit-sharing', WeeklyProfit::class)->name('bagi-hasil');
    
    // Finance
    Route::get('/cash-management', CashManagement::class)->name('buku-kas');
});


require __DIR__ . '/settings.php';
