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

    // Edit Transaction
    public $showEditModal = false;
    public $editingTransaction = null;
    public $editQty = 0;
    public $editStatus = '';
    public $editNote = '';
    public $editBuyer = '';
    public $editChangeDue = 0;

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
