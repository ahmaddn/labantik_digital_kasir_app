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

        // Fetch category summaries
        $categorySums = (clone $activeQuery)
            ->selectRaw("
                cash_category_id,
                SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as cat_income,
                SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as cat_expense
            ")
            ->groupBy('cash_category_id')
            ->get();

        $categoriesMap = CashCategory::where('jurusan_id', $activeJurusanId)->get()->keyBy('id');
        $categoryStats = [];
        foreach ($categorySums as $sum) {
            $cat = $categoriesMap[$sum->cash_category_id] ?? null;
            if ($cat) {
                $categoryStats[] = [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'income' => (float)$sum->cat_income,
                    'expense' => (float)$sum->cat_expense,
                    'balance' => (float)$sum->cat_income - (float)$sum->cat_expense,
                ];
            }
        }

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
            'startDate' => $startDate,
            'endDate' => $endDate,
            'categoryStats' => $categoryStats,
            'transactions' => $transactions,
            'categories' => $categories,
        ])->layout('layouts.app', ['title' => 'Buku Kas Virtual']);
    }
}
