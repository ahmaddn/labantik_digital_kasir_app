<?php

namespace App\Livewire\Finance;

use App\Models\CashCategory;
use App\Models\Jurusan;
use App\Models\VirtualCashTransaction;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class VirtualCashManagement extends Component
{
    use WithPagination;

    // Filters
    public string $filterMonth = '';
    public string $filterType = 'weekly';
    public string $filterWeek = 'this_week';
    public string $filterSourceMethod = ''; // '', 'transfer', 'qris'

    // Form inputs
    public string $date = '';
    public string $sourceMethod = 'transfer'; // 'transfer', 'qris'
    public $cashCategoryId = '';
    public string $type = 'expense'; // 'income', 'expense'
    public $amount;
    public string $description = '';

    // Modal state
    public bool $showModal = false;
    public $editingId = null;
    public bool $showDeleteConfirmation = false;
    public $confirmingDeleteId = null;

    public array $chartData = [];

    public function mount()
    {
        $this->filterMonth = now()->format('Y-m');
        $this->date = now()->format('Y-m-d');
    }

    public function updatedFilterMonth()
    {
        $this->resetPage();
    }

    public function updatedFilterType()
    {
        $this->resetPage();
    }

    public function updatedFilterWeek()
    {
        $this->resetPage();
    }

    public function updatedFilterSourceMethod()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->reset(['type', 'sourceMethod', 'cashCategoryId', 'amount', 'description', 'editingId']);
        $this->date = now()->format('Y-m-d');
        $this->type = 'expense';
        $this->sourceMethod = 'transfer';
        $this->showModal = true;
    }

    public function saveTransaction()
    {
        $this->validate([
            'date' => 'required|date',
            'sourceMethod' => 'required|in:transfer,qris',
            'cashCategoryId' => 'required|exists:cash_categories,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string|max:255',
        ]);

        $activeJurusanId = session('active_jurusan_id');

        if ($this->editingId) {
            $tx = VirtualCashTransaction::find($this->editingId);
            if ($tx) {
                $tx->update([
                    'jurusan_id' => $activeJurusanId,
                    'date' => $this->date,
                    'source_method' => $this->sourceMethod,
                    'cash_category_id' => $this->cashCategoryId,
                    'type' => $this->type,
                    'amount' => $this->amount,
                    'description' => $this->description,
                ]);
            }
            $message = 'Transaksi kas virtual berhasil diperbarui!';
        } else {
            VirtualCashTransaction::create([
                'jurusan_id' => $activeJurusanId,
                'date' => $this->date,
                'source_method' => $this->sourceMethod,
                'cash_category_id' => $this->cashCategoryId,
                'type' => $this->type,
                'amount' => $this->amount,
                'description' => $this->description,
            ]);
            $message = 'Transaksi kas virtual berhasil ditambahkan!';
        }

        $this->showModal = false;
        $this->dispatch('toast', message: $message);
    }

    public function editTransaction($id)
    {
        $transaction = VirtualCashTransaction::find($id);
        if ($transaction) {
            $this->resetValidation();
            $this->editingId = $transaction->id;
            $this->date = $transaction->date ? $transaction->date->format('Y-m-d') : now()->format('Y-m-d');
            $this->sourceMethod = $transaction->source_method;
            $this->cashCategoryId = $transaction->cash_category_id;
            $this->type = $transaction->type;
            $this->amount = $transaction->amount;
            $this->description = $transaction->description;
            $this->showModal = true;
        }
    }

    // Delete category confirmation state
    public bool $showDeleteCategoryConfirmation = false;
    public $confirmingDeleteCategoryId = null;
    public string $confirmingDeleteCategoryName = '';
    public int $confirmingDeleteCategoryTxCount = 0;

    public function confirmDeleteCategory($id, $name)
    {
        $this->confirmingDeleteCategoryId = $id;
        $this->confirmingDeleteCategoryName = $name;
        $this->confirmingDeleteCategoryTxCount = VirtualCashTransaction::where('cash_category_id', $id)->count();
        $this->showDeleteCategoryConfirmation = true;
    }

    public function deleteCategory()
    {
        if ($this->confirmingDeleteCategoryId) {
            // Delete all virtual transactions associated with this category
            VirtualCashTransaction::where('cash_category_id', $this->confirmingDeleteCategoryId)->delete();

            $category = CashCategory::find($this->confirmingDeleteCategoryId);
            if ($category) {
                $category->delete();
                $this->dispatch('toast', message: 'Kategori dan seluruh transaksi kas virtual di dalamnya berhasil dihapus.');
            }
            $this->showDeleteCategoryConfirmation = false;
            $this->confirmingDeleteCategoryId = null;
            $this->confirmingDeleteCategoryName = '';
            $this->confirmingDeleteCategoryTxCount = 0;
        }
    }

    public function confirmDelete($id)
    {
        $this->confirmingDeleteId = $id;
        $this->showDeleteConfirmation = true;
    }

    public function deleteTransaction()
    {
        if ($this->confirmingDeleteId) {
            $transaction = VirtualCashTransaction::find($this->confirmingDeleteId);
            if ($transaction) {
                $transaction->delete();
                $this->dispatch('toast', message: 'Transaksi kas virtual berhasil dihapus.');
            }
            $this->showDeleteConfirmation = false;
            $this->confirmingDeleteId = null;
        }
    }

    public function render()
    {
        $activeJurusanId = session('active_jurusan_id');

        // Overall balances
        $overallQuery = VirtualCashTransaction::where('jurusan_id', $activeJurusanId);

        $totalBalance = (clone $overallQuery)
            ->selectRaw("SUM(CASE WHEN type = 'income' THEN amount ELSE -amount END) as total")
            ->value('total') ?? 0;

        $transferBalance = (clone $overallQuery)
            ->where('source_method', 'transfer')
            ->selectRaw("SUM(CASE WHEN type = 'income' THEN amount ELSE -amount END) as total")
            ->value('total') ?? 0;

        $qrisBalance = (clone $overallQuery)
            ->where('source_method', 'qris')
            ->selectRaw("SUM(CASE WHEN type = 'income' THEN amount ELSE -amount END) as total")
            ->value('total') ?? 0;

        // Define Start and End Dates based on period filters
        $startDate = null;
        $endDate = null;

        if ($this->filterType === 'weekly') {
            if ($this->filterWeek === 'this_week') {
                $startDate = now()->startOfWeek(Carbon::MONDAY)->format('Y-m-d');
                $endDate = now()->endOfWeek(Carbon::SUNDAY)->format('Y-m-d');
            } elseif ($this->filterWeek === 'last_week') {
                $startDate = now()->subWeek()->startOfWeek(Carbon::MONDAY)->format('Y-m-d');
                $endDate = now()->subWeek()->endOfWeek(Carbon::SUNDAY)->format('Y-m-d');
            } else {
                $monthDate = Carbon::createFromFormat('Y-m', $this->filterMonth);
                $weekNumber = (int) str_replace('week_', '', $this->filterWeek);

                if ($weekNumber === 1) {
                    $startDate = $monthDate->copy()->startOfMonth()->format('Y-m-d');
                    $endDate = $monthDate->copy()->startOfMonth()->addDays(6)->format('Y-m-d');
                } elseif ($weekNumber === 2) {
                    $startDate = $monthDate->copy()->startOfMonth()->addDays(7)->format('Y-m-d');
                    $endDate = $monthDate->copy()->startOfMonth()->addDays(13)->format('Y-m-d');
                } elseif ($weekNumber === 3) {
                    $startDate = $monthDate->copy()->startOfMonth()->addDays(14)->format('Y-m-d');
                    $endDate = $monthDate->copy()->startOfMonth()->addDays(20)->format('Y-m-d');
                } elseif ($weekNumber === 4) {
                    $startDate = $monthDate->copy()->startOfMonth()->addDays(21)->format('Y-m-d');
                    $endDate = $monthDate->copy()->startOfMonth()->addDays(27)->format('Y-m-d');
                } else {
                    $startDate = $monthDate->copy()->startOfMonth()->addDays(28)->format('Y-m-d');
                    $endDate = $monthDate->copy()->endOfMonth()->format('Y-m-d');
                }
            }
        } elseif ($this->filterType === 'monthly') {
            $startDate = Carbon::createFromFormat('Y-m', $this->filterMonth)->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::createFromFormat('Y-m', $this->filterMonth)->endOfMonth()->format('Y-m-d');
        }

        // Base query for transaction list & period stats
        $activeQuery = VirtualCashTransaction::with('cashCategory')
            ->where('jurusan_id', $activeJurusanId);

        if ($startDate && $endDate) {
            $activeQuery->whereBetween('date', [$startDate, $endDate]);
        }

        if ($this->filterSourceMethod) {
            $activeQuery->where('source_method', $this->filterSourceMethod);
        }

        $periodBalances = (clone $activeQuery)
            ->selectRaw("
                SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as total_income,
                SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as total_expense
            ")
            ->first();

        $displayIncome = (float)($periodBalances->total_income ?? 0);
        $displayExpense = (float)($periodBalances->total_expense ?? 0);

        // Calculate non-cash sales modal (HPP) and gross profit from Transaction table
        $salesQuery = \App\Models\Transaction::where('jurusan_id', $activeJurusanId)
            ->whereIn('status', ['uang_diterima', 'belum_kembalian']);

        if ($startDate && $endDate) {
            $salesQuery->whereBetween('transacted_at', [
                Carbon::parse($startDate)->startOfDay()->toDateTimeString(),
                Carbon::parse($endDate)->endOfDay()->toDateTimeString(),
            ]);
        }

        if ($this->filterSourceMethod) {
            $salesQuery->where('payment_method', $this->filterSourceMethod);
        } else {
            $salesQuery->whereIn('payment_method', ['transfer', 'qris']);
        }

        $salesStats = (clone $salesQuery)
            ->selectRaw("
                SUM((unit_price - unit_profit) * quantity) as total_modal,
                SUM(unit_profit * quantity) as total_sales_profit,
                SUM(total_price) as total_sales_revenue
            ")
            ->first();

        $virtualModal = (float)($salesStats->total_modal ?? 0);
        $salesProfit = (float)($salesStats->total_sales_profit ?? 0);

        // Virtual Profit Calculation:
        // Net Virtual Profit = Sales Profit + Other Income (VirtualCashTransaction income) - Virtual Expenses
        $virtualProfit = $salesProfit + $displayIncome - $displayExpense;

        // Fetch non-cash sales grouped by product category / supplier category using aggregated database query
        $salesStatsByCatName = [];

        $rawCategorySales = \App\Models\Transaction::query()
            ->leftJoin('products', 'transactions.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('suppliers', 'products.supplier_id', '=', 'suppliers.id')
            ->where('transactions.jurusan_id', $activeJurusanId)
            ->whereIn('transactions.status', ['uang_diterima', 'belum_kembalian'])
            ->when($startDate && $endDate, fn($q) => $q->whereBetween('transactions.transacted_at', [
                Carbon::parse($startDate)->startOfDay()->toDateTimeString(),
                Carbon::parse($endDate)->endOfDay()->toDateTimeString(),
            ]))
            ->when($this->filterSourceMethod, fn($q) => $q->where('transactions.payment_method', $this->filterSourceMethod), fn($q) => $q->whereIn('transactions.payment_method', ['transfer', 'qris']))
            ->selectRaw("
                suppliers.name as supplier_name,
                categories.name as category_name,
                SUM(transactions.total_price) as sales_income,
                SUM((transactions.unit_price - transactions.unit_profit) * transactions.quantity) as modal,
                SUM(transactions.unit_profit * transactions.quantity) as profit
            ")
            ->groupBy('suppliers.name', 'categories.name')
            ->get();

        $activeJurusan = Jurusan::find($activeJurusanId);
        $activeJurusanNameLower = $activeJurusan ? strtolower($activeJurusan->name) : '';

        foreach ($rawCategorySales as $row) {
            if ($row->supplier_name) {
                $cashCatName = 'Penjualan ' . trim($row->supplier_name);
            } else {
                $catNameClean = trim($row->category_name ?? 'Lainnya');
                $categoryNameLower = strtolower($catNameClean);
                if (in_array($categoryNameLower, ['makanan', 'minuman', 'makanan & minuman', 'makanan dan minuman', 'snack'])) {
                    $cashCatName = str_contains($activeJurusanNameLower, 'doku') ? 'Kas Doku' : 'Jurusan Snack & Minuman';
                } elseif (in_array($categoryNameLower, ['umum', 'lainnya', 'lain-lain'])) {
                    $cashCatName = 'Keuntungan Jurusan';
                } else {
                    $cashCatName = 'Penjualan ' . $catNameClean;
                }
            }

            if (!isset($salesStatsByCatName[$cashCatName])) {
                $salesStatsByCatName[$cashCatName] = ['sales_income' => 0, 'modal' => 0, 'profit' => 0];
            }
            $salesStatsByCatName[$cashCatName]['sales_income'] += (float)$row->sales_income;
            $salesStatsByCatName[$cashCatName]['modal'] += (float)$row->modal;
            $salesStatsByCatName[$cashCatName]['profit'] += (float)$row->profit;
        }

        // Aggregate category sums directly from VirtualCashTransaction
        $categoriesMap = CashCategory::where('jurusan_id', $activeJurusanId)->get()->keyBy('id');

        $categorySums = (clone $activeQuery)
            ->selectRaw("
                cash_category_id,
                SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as cat_income,
                SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as cat_expense
            ")
            ->groupBy('cash_category_id')
            ->get();

        $categoryStatsMap = [];

        foreach ($categorySums as $sum) {
            $catId = $sum->cash_category_id;
            $catName = $categoriesMap[$catId]->name ?? 'Penjualan Umum';

            $salesData = $salesStatsByCatName[$catName] ?? null;
            $catIncome = (float)$sum->cat_income;
            $catExpense = (float)$sum->cat_expense;

            $categoryStatsMap[$catId ?: 'null'] = [
                'id' => $catId,
                'name' => $catName,
                'income' => $catIncome,
                'expense' => $catExpense,
                'modal' => $salesData ? (float)$salesData['modal'] : 0,
                'profit' => $salesData ? (float)$salesData['profit'] : ($catIncome - $catExpense),
                'balance' => $catIncome - $catExpense,
            ];
        }

        // Filter out categories with zero activity
        $categoryStats = array_values(array_filter($categoryStatsMap, function ($stat) {
            return $stat['income'] > 0 || $stat['expense'] > 0 || $stat['modal'] > 0 || $stat['profit'] > 0 || $stat['balance'] != 0;
        }));

        $transactions = (clone $activeQuery)
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15);

        $categories = CashCategory::where('jurusan_id', $activeJurusanId)->get();

        return view('livewire.finance.virtual-cash-management', [
            'totalBalance' => $totalBalance,
            'transferBalance' => $transferBalance,
            'qrisBalance' => $qrisBalance,
            'displayIncome' => $displayIncome,
            'displayExpense' => $displayExpense,
            'virtualModal' => $virtualModal,
            'virtualProfit' => $virtualProfit,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'categoryStats' => $categoryStats,
            'transactions' => $transactions,
            'categories' => $categories,
        ])->layout('layouts.app', ['title' => 'Buku Kas Virtual']);
    }
}
