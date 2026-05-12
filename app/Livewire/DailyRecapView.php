<?php

namespace App\Livewire;

use App\Models\DailyRecap;
use App\Models\Transaction;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class DailyRecapView extends Component
{
    use WithPagination;

    public string $selectedDate = '';
    public string $search = '';
    public string $filterStatus = '';
    public string $filterCategory = '';
    
    // Cash Audit
    public $actualCash = 0;
    public $cashNote = '';

    // Edit Transaction
    public $showEditModal = false;
    public $editingTransaction = null;
    public $editQty = 0;
    public $editStatus = '';
    public $editNote = '';
    public $editBuyer = '';
    public $editChangeDue = 0;


    public function mount($date = null): void
    {
        $this->selectedDate = $date ?? today()->toDateString();
        $this->loadCashAudit();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedDate(): void
    {
        $this->loadCashAudit();
        $this->resetPage();
    }

    protected function loadCashAudit(): void
    {
        $recap = DailyRecap::where('date', $this->selectedDate)->first();
        if ($recap) {
            $this->actualCash = $recap->actual_cash;
            $this->cashNote = $recap->cash_note ?? '';
        } else {
            $this->actualCash = 0;
            $this->cashNote = '';
        }
    }

    public function saveCashAudit(): void
    {
        DailyRecap::updateOrCreate(
            ['date' => $this->selectedDate],
            [
                'actual_cash' => $this->actualCash,
                'cash_note' => $this->cashNote,
            ]
        );

        $this->dispatch('toast', message: 'Audit uang kas berhasil disimpan.');
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatedFilterCategory(): void
    {
        $this->resetPage();
    }


    public function render()
    {
        $allTransactions = Transaction::with(['product.category'])
            ->whereDate('transacted_at', $this->selectedDate)
            ->get();

        $query = Transaction::with(['product.category'])
            ->whereDate('transacted_at', $this->selectedDate)
            ->orderByDesc('transacted_at');

        if ($this->search) {
            $query->whereHas('product', function($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterCategory) {
            $query->whereHas('product', function($q) {
                $q->where('category_id', $this->filterCategory);
            });
        }


        if ($allTransactions->isEmpty()) {
            return view('livewire.daily-recap-view', [
                'recap' => null,
                'transactions' => $query->paginate(15)
            ])->layout('layouts.app', ['title' => 'Rekap Harian']);
        }

        $totalRevenueAll = $allTransactions->sum('total_price');
        $totalRevenueReal = $allTransactions->where('status', 'uang_diterima')->sum('total_price');
        $totalProfit = $allTransactions->sum(fn($tx) => $tx->unit_profit * $tx->quantity);
        $totalModal = $allTransactions->sum(fn($tx) => ($tx->unit_price - $tx->unit_profit) * $tx->quantity);

        $recap = (object) [
            'total_revenue_all' => $totalRevenueAll,
            'total_revenue_real' => $totalRevenueReal,
            'total_profit' => $totalProfit,
            'total_modal' => $totalModal,
            'count_received' => $allTransactions->where('status', 'uang_diterima')->count(),
            'count_unpaid_change' => $allTransactions->where('status', 'belum_kembalian')->count(),
            'count_no_payment' => $allTransactions->where('status', 'belum_menerima_uang')->count(),
            'month_name' => Carbon::parse($this->selectedDate)->translatedFormat('F Y'),
            'month_week' => Carbon::parse($this->selectedDate)->weekOfMonth,
            'generated_at' => now(),
        ];

        $categoryRecap = $allTransactions->groupBy(fn($tx) => $tx->product->category->name ?? 'Tanpa Kategori')
            ->map(function($group) {
                return (object) [
                    'revenue' => $group->sum('total_price'),
                    'profit' => $group->sum(fn($tx) => $tx->unit_profit * $tx->quantity),
                    'modal' => $group->sum(fn($tx) => ($tx->unit_price - $tx->unit_profit) * $tx->quantity),
                    'qty' => $group->sum('quantity'),
                ];
            })->sortByDesc('revenue');

        return view('livewire.daily-recap-view', [
            'recap' => $recap,
            'categoryRecap' => $categoryRecap,
            'transactions' => $query->paginate(15),
            'categories' => \App\Models\ProductCategory::orderBy('name')->get()
        ])->layout('layouts.app', ['title' => 'Rekap Harian']);

    }

    public function exportCSV()
    {
        $this->dispatch('toast', message: 'Fitur ekspor CSV sedang dalam pengembangan.', type: 'info');
    }

    public function editTransaction($id): void
    {
        $this->editingTransaction = Transaction::find($id);
        if (!$this->editingTransaction) return;

        $this->editQty = $this->editingTransaction->quantity;
        $this->editStatus = $this->editingTransaction->status;
        $this->editNote = $this->editingTransaction->note ?? '';
        $this->editBuyer = $this->editingTransaction->buyer_name ?? '';
        $this->editChangeDue = $this->editingTransaction->change_due ?? 0;
        $this->showEditModal = true;
    }

    public function updateTransaction(): void
    {
        if (!$this->editingTransaction) return;

        $totalPrice = $this->editingTransaction->unit_price * $this->editQty;
        $debt = in_array($this->editStatus, ['belum_menerima_uang', 'uang_dipinjam']) ? $totalPrice : 0;
        $changeDue = ($this->editStatus === 'belum_kembalian') ? $this->editChangeDue : 0;

        $this->editingTransaction->update([
            'quantity' => $this->editQty,
            'total_price' => $totalPrice,
            'status' => $this->editStatus,
            'note' => $this->editNote,
            'buyer_name' => $this->editBuyer ?: null,
            'debt_amount' => $debt,
            'change_due' => $changeDue
        ]);

        $this->showEditModal = false;
        $this->dispatch('toast', message: 'Transaksi berhasil diperbarui!');
    }
}
