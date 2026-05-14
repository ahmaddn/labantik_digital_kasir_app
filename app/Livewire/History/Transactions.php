<?php

namespace App\Livewire\History;

use App\Models\Product;
use App\Models\Transaction;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class Transactions extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = '';
    public $successMessage = '';

    // Details Modal
    public $showDetailsModal = false;
    public $detailReference = null;

    // Edit Modal
    public $showEditModal = false;
    public $editingReference = null;
    public $editBuyerName = '';
    public $editStatus = '';
    public $editItems = []; // Array of transaction items for editing


    protected $queryString = ['search', 'filterStatus'];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function delete($reference)
    {
        Transaction::where('reference', $reference)->delete();
        $this->dispatch('toast', message: 'Seluruh transaksi berhasil dihapus.');
    }

    public function viewDetails($reference): void
    {
        $this->detailReference = $reference;
        $this->showDetailsModal = true;
    }

    public function getDetailItemsProperty()
    {
        if (!$this->detailReference) return collect();
        return Transaction::with('product')->where('reference', $this->detailReference)->get();
    }

    public function edit($reference)
    {
        $this->editingReference = $reference;
        $transactions = Transaction::where('reference', $reference)->get();
        
        if ($transactions->isEmpty()) return;

        $first = $transactions->first();
        $this->editBuyerName = $first->buyer_name;
        $this->editStatus = $first->status;
        
        $this->editItems = [];
        foreach ($transactions as $tx) {
            $this->editItems[] = [
                'id' => $tx->id,
                'name' => $tx->product->name ?? 'Unknown',
                'quantity' => $tx->quantity,
                'unit_price' => $tx->unit_price,
            ];
        }

        $this->showEditModal = true;
    }

    public function update()
    {
        $this->validate([
            'editStatus' => 'required',
            'editItems.*.quantity' => 'required|numeric|min:1'
        ]);

        foreach ($this->editItems as $item) {
            $tx = Transaction::find($item['id']);
            if ($tx) {
                $tx->update([
                    'buyer_name' => $this->editBuyerName,
                    'status' => $this->editStatus,
                    'quantity' => $item['quantity'],
                    'total_price' => $tx->unit_price * $item['quantity'],
                    // Note: debt_amount and change_due might need adjustment if they were set,
                    // but for simplicity we keep original logic or reset based on status.
                ]);
            }
        }

        $this->showEditModal = false;
        $this->dispatch('toast', message: 'Transaksi berhasil diperbarui.');
    }

    public function render()
    {
        $query = Transaction::query();

        if ($this->search) {
            $query->where(function($q) {
                $q->where('reference', 'like', '%' . $this->search . '%')
                  ->orWhere('buyer_name', 'like', '%' . $this->search . '%')
                  ->orWhereHas('product', function($pq) {
                      $pq->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $transactions = $query->selectRaw('reference, MAX(buyer_name) as buyer_name, MAX(status) as status, MAX(transacted_at) as transacted_at, SUM(total_price) as total_amount, SUM(quantity) as total_qty, COUNT(*) as unique_items')
            ->groupBy('reference')
            ->orderByDesc('transacted_at')
            ->paginate(15);

        return view('livewire.history.transactions', [
            'transactions' => $transactions
        ])->layout('layouts.app', ['title' => 'Riwayat Transaksi']);
    }
}
