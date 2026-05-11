<?php

namespace App\Livewire;

use App\Models\Transaction;
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

    public function settle($transactionId)
    {
        $transaction = Transaction::findOrFail($transactionId);
        
        // If it was debt, mark as paid. If it was unreturned change, mark as paid.
        // Both cases result in 'uang_diterima' status in this context
        $transaction->update([
            'status' => 'uang_diterima',
            'debt_amount' => 0,
            'change_due' => 0,
            'note' => $transaction->note . ' (Selesai pada ' . now()->format('d/m/Y H:i') . ')'
        ]);

        $this->dispatch('toast', message: 'Transaksi berhasil diselesaikan.');
    }

    public function render()
    {
        $query = Transaction::with(['product', 'supplier'])
            ->when($this->activeTab === 'debt', function ($q) {
                return $q->debt();
            })
            ->when($this->activeTab === 'change', function ($q) {
                return $q->unreturnedChange();
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

        $summary = [
            'total_debt' => Transaction::debt()->sum('debt_amount'),
            'total_change' => Transaction::unreturnedChange()->sum('change_due'),
        ];

        return view('livewire.debt-management', [
            'transactions' => $query->latest('transacted_at')->paginate(15),
            'summary' => $summary
        ])->layout('layouts.app', ['title' => 'Manajemen Hutang & Kembalian']);
    }
}
