<?php

namespace App\Livewire;

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class DebtManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $activeTab = 'debt'; // 'debt' or 'change'
    public $startDate;
    public $endDate;

    protected $queryString = [
        'search' => ['except' => ''],
        'activeTab' => ['except' => 'debt'],
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
    ];

    public function mount()
    {
        // No default dates to show all by default
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function settle($reference)
    {
        Transaction::where('reference', $reference)->update([
            'status' => 'uang_diterima',
            'debt_amount' => 0,
            'change_due' => 0,
            'transacted_at' => now(), // Move to today to count as today's profit
            'note' => DB::raw("CONCAT(COALESCE(note, ''), ' (Lunas pd ' , DATE_FORMAT(NOW(), '%d/%m/%Y %H:%i'), ')')")
        ]);

        $this->dispatch('toast', message: 'Seluruh nota berhasil diselesaikan.');
    }

    public function render()
    {
        $query = Transaction::query()
            ->when($this->activeTab === 'debt', function ($q) {
                return $q->whereIn('status', ['belum_menerima_uang', 'uang_dipinjam']);
            })
            ->when($this->activeTab === 'change', function ($q) {
                return $q->where('status', 'belum_kembalian');
            });

        if ($this->search) {
            $query->where('buyer_name', 'like', '%' . $this->search . '%');
        }

        if ($this->startDate) {
            $query->whereDate('transacted_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('transacted_at', '<=', $this->endDate);
        }

        $transactions = $query->selectRaw('reference, buyer_name, status, transacted_at, SUM(total_price) as total_price, SUM(debt_amount) as debt_amount, SUM(change_due) as change_due, COUNT(*) as items_count')
            ->groupBy('reference', 'buyer_name', 'status', 'transacted_at')
            ->orderByDesc('transacted_at')
            ->paginate(15);

        $summary = [
            'total_debt' => Transaction::whereIn('status', ['belum_menerima_uang', 'uang_dipinjam'])->sum('debt_amount'),
            'total_change' => Transaction::where('status', 'belum_kembalian')->sum('change_due'),
        ];

        return view('livewire.debt-management', [
            'transactions' => $transactions,
            'summary' => $summary
        ])->layout('layouts.app', ['title' => 'Manajemen Hutang & Kembalian']);
    }
}
