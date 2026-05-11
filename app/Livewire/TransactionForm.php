<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Transaction;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class TransactionForm extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = '';
    public $successMessage = '';

    protected $queryString = ['search', 'filterStatus'];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        $transaction = Transaction::find($id);
        if ($transaction) {
            $transaction->delete();
            $this->dispatch('toast', message: 'Transaksi berhasil dihapus.');
        }
    }

    public function edit($id)
    {
        // For now, redirect to kasir or just a placeholder for editing
        // Usually, in a POS, editing is rare, but we can implement it if needed
        $this->dispatch('toast', message: 'Fitur edit transaksi sedang dalam pengembangan.', type: 'info');
    }

    public function render()
    {
        $query = Transaction::with('product')
            ->orderByDesc('transacted_at');

        if ($this->search) {
            $query->whereHas('product', function($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })->orWhere('buyer_name', 'like', '%' . $this->search . '%');
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        return view('livewire.transaction-form', [
            'transactions' => $query->paginate(15)
        ])->layout('layouts.app', ['title' => 'Riwayat Transaksi']);
    }
}
