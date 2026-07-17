<?php

use App\Http\Middleware\EnsureRoleSelected;
use App\Livewire\Auth\SelectRole;
use App\Livewire\Dashboard;
use App\Livewire\Finance\CashManagement;
use App\Livewire\History\Transactions;
use App\Livewire\Management\Category;
use App\Livewire\Management\Debt;
use App\Livewire\Management\JurusanManagement;
use App\Livewire\Management\Product;
use App\Livewire\Management\RoleManagement;
use App\Livewire\Management\Supplier;
use App\Livewire\Management\ThemeCustomizer;
use App\Livewire\Management\UserManagement;
use App\Livewire\Pos\Kasir;
use App\Livewire\Reports\DailyRecap;
use App\Livewire\Reports\InventoryReport;
use App\Livewire\Reports\MonthlyRecap;
use App\Livewire\Reports\SupplierReport;
use App\Livewire\Reports\WeeklyProfit;
use App\Livewire\Reports\YearlyRecap;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/select-role', SelectRole::class)->name('select-role');
});

Route::middleware(['auth', 'verified', EnsureRoleSelected::class])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/transactions', Transactions::class)->name('transactions');
    Route::get('/products', Product::class)->name('products');
    Route::get('/categories', Category::class)->name('categories');
    Route::get('/suppliers', Supplier::class)->name('suppliers');
    Route::get('/cashier', Kasir::class)->name('kasir');

    // Theme Settings
    Route::get('/settings/theme', ThemeCustomizer::class)->name('theme-customizer');

    // Recaps
    Route::get('/daily-recap/{date?}', DailyRecap::class)->name('daily-recap');
    Route::get('/monthly-recap', MonthlyRecap::class)->name('monthly-recap');
    Route::get('/yearly-recap', YearlyRecap::class)->name('yearly-recap');
    Route::get('/inventory-report', InventoryReport::class)->name('inventory-report');
    Route::get('/supplier-report', SupplierReport::class)->name('supplier-report');
    Route::get('/debts', Debt::class)->name('debts');
    Route::get('/management/debt/{storeDebt}/print-deletion', function (\App\Models\StoreDebt $storeDebt) {
        if ($storeDebt->jurusan_id != session('active_jurusan_id')) {
            abort(403);
        }
        return view('print.debt-deletion', ['debt' => $storeDebt]);
    })->name('debts.print-deletion');
    Route::get('/profit-sharing', WeeklyProfit::class)->name('bagi-hasil');

    // Finance
    Route::get('/cash-management', CashManagement::class)->name('buku-kas');

    // User Management
    Route::get('/users', UserManagement::class)->name('users');
    Route::get('/jurusans', JurusanManagement::class)->name('jurusans');
    Route::get('/roles', RoleManagement::class)->name('roles');
});

require __DIR__.'/settings.php';
