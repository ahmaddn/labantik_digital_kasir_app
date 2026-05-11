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


    public function mount($date = null): void
    {
        $this->selectedDate = $date ?? today()->toDateString();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedDate(): void
    {
        $this->resetPage();
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
        $allTransactions = Transaction::with('product')
            ->whereDate('transacted_at', $this->selectedDate)
            ->get();

        $query = Transaction::with('product')
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

        return view('livewire.daily-recap-view', [
            'recap' => $recap,
            'transactions' => $query->paginate(15),
            'categories' => \App\Models\ProductCategory::orderBy('name')->get()

        ])->layout('layouts.app', ['title' => 'Rekap Harian']);

    }

    public function exportCSV()
    {
        $this->dispatch('toast', message: 'Fitur ekspor CSV sedang dalam pengembangan.', type: 'info');
    }
}
