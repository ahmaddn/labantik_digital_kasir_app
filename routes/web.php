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
use App\Livewire\Reports\CashierNotes;
use App\Livewire\Reports\CategoryDetail;
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
    Route::get('/late-report', \App\Livewire\Auth\LateReport::class)->name('late-report');
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
    Route::get('/category-detail/{categoryId}', CategoryDetail::class)->name('category-detail');
    Route::get('/inventory-report', InventoryReport::class)->name('inventory-report');
    Route::get('/supplier-report', SupplierReport::class)->name('supplier-report');
    Route::get('/cashier-notes', CashierNotes::class)->name('cashier-notes');
    Route::get('/debts', Debt::class)->name('debts');
    Route::get('/management/debt/{storeDebt}/print-deletion', function (\App\Models\StoreDebt $storeDebt) {
        if ($storeDebt->jurusan_id != session('active_jurusan_id')) {
            abort(403);
        }
        return view('print.debt-deletion', ['debt' => $storeDebt]);
    })->name('debts.print-deletion');
    Route::get('/reports/supplier-settlement/{supplierId}', function ($supplierId) {
        $supplier = \App\Models\Supplier::findOrFail($supplierId);
        $dateFrom = request('date_from', now()->startOfMonth()->toDateString());
        $dateTo = request('date_to', now()->toDateString());

        // Fetch products and their sales details
        $products = \App\Models\Product::where('supplier_id', $supplierId)
            ->where('jurusan_id', session('active_jurusan_id'))
            ->get()
            ->map(function ($product) use ($dateFrom, $dateTo) {
                // Get first stock entry in the period for opening stock (with positive stock fallback)
                $firstStock = \App\Models\StockEntry::where('product_id', $product->id)
                    ->whereBetween('date', [$dateFrom, $dateTo])
                    ->where('opening_stock', '>', 0)
                    ->orderBy('date', 'asc')
                    ->first() ?? \App\Models\StockEntry::where('product_id', $product->id)
                    ->whereBetween('date', [$dateFrom, $dateTo])
                    ->orderBy('date', 'asc')
                    ->first();

                // Get latest stock entry for closing stock context
                $latestStock = \App\Models\StockEntry::where('product_id', $product->id)
                    ->whereBetween('date', [$dateFrom, $dateTo])
                    ->orderBy('date', 'desc')
                    ->first();

                $openingStock = $firstStock ? $firstStock->opening_stock : 0;
                $closingStock = $latestStock ? max(0, $latestStock->closing_stock) : 0;

                if ($firstStock || $latestStock) {
                    $sold = max(0, $openingStock - $closingStock);
                } else {
                    $sold = \App\Models\Transaction::where('product_id', $product->id)
                        ->whereIn('status', ['uang_diterima', 'belum_kembalian'])
                        ->whereBetween('transacted_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                        ->sum('quantity');
                    $openingStock = $sold;
                }

                return (object) [
                    'name' => $product->name,
                    'price' => $product->price,
                    'modal_price' => $product->modal_price,
                    'opening_stock' => $openingStock,
                    'closing_stock' => $closingStock,
                    'sold_qty' => $sold,
                    'total_modal' => $sold * $product->modal_price,
                ];
            })
            ->filter(fn($p) => $p->sold_qty > 0 || $p->opening_stock > 0);

        return view('print.supplier-settlement', [
            'supplier' => $supplier,
            'products' => $products,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    })->name('supplier-settlement.print');
    Route::get('/profit-sharing', WeeklyProfit::class)->name('bagi-hasil');

    // Finance
    Route::get('/cash-management', CashManagement::class)->name('buku-kas');
    Route::get('/monthly-closing', \App\Livewire\Finance\MonthlyClosing::class)->name('monthly-closing');

    // User Management
    Route::get('/users', UserManagement::class)->name('users');
    Route::get('/management/security-logs', \App\Livewire\Management\SecurityLogs::class)->name('security-logs');
    Route::get('/jurusans', JurusanManagement::class)->name('jurusans');
    Route::get('/roles', RoleManagement::class)->name('roles');

    // Cashier Scheduling & Tasks & Attendance Reports
    Route::get('/management/schedules', \App\Livewire\Management\CashierScheduling::class)->name('schedules');
    Route::get('/management/tasks', \App\Livewire\Management\CashierTasks::class)->name('tasks');
    Route::get('/reports/attendances', \App\Livewire\Reports\AttendanceReport::class)->name('attendances');
    Route::get('/guide', \App\Livewire\Guide\CashierGuide::class)->name('guide');
    
    // Security Audit Log alerts
    Route::post('/log-security-alert', function (\Illuminate\Http\Request $request) {
        $user = auth()->user();
        $userName = $user ? $user->name : 'Guest';
        $userEmail = $user ? $user->email : 'N/A';
        $pageUrl = $request->input('url', 'Unknown Page');
        $type = $request->input('type', 'screenshot');
        
        \Illuminate\Support\Facades\Log::warning("SECURITY ALERT: User '{$userName}' ({$userEmail}) triggered a possible {$type} action on page: {$pageUrl}");
        
        return response()->json(['status' => 'logged']);
    })->name('security.log-alert');
});

require __DIR__.'/settings.php';
