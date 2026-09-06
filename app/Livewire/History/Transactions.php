<?php

namespace App\Livewire\History;

use App\Models\Jurusan;
use App\Models\StockEntry;
use App\Models\Transaction;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class Transactions extends Component
{
    use WithPagination;

    #[Livewire\Attributes\Url]
    public $highlight = '';

    public $search = '';

    public $filterStatus = '';

    public $filterDate = '';

    public $filterMonth = '';

    public $filterJurusan = '';

    public $filterPaymentMethod = '';

    public $successMessage = '';

    // Details Modal
    public $showDetailsModal = false;

    public $detailReference = null;

    // Edit Modal
    public $showEditModal = false;

    public $editingReference = null;

    public $editBuyerName = '';

    public $editStatus = '';

    public $editPaymentMethod = 'cash';

    public $editItems = []; // Array of transaction items for editing

    protected $queryString = ['search', 'filterStatus', 'filterDate', 'filterMonth', 'filterJurusan', 'filterPaymentMethod'];

    public function updatedFilterMonth()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function updatedFilterDate()
    {
        $this->resetPage();
    }

    public function updatedFilterJurusan()
    {
        $this->resetPage();
    }

    public function updatedFilterPaymentMethod()
    {
        $this->resetPage();
    }

    private function cascadeStockUpdate($productId, $date, $quantityDiff)
    {
        $tDate = Carbon::parse($date)->toDateString();
        $isBackdate = $tDate < now()->toDateString();

        if ($isBackdate) {
            $entries = StockEntry::where('product_id', $productId)
                ->where('date', '>=', $tDate)
                ->orderBy('date', 'asc')
                ->get();

            foreach ($entries as $entry) {
                if ($entry->date === $tDate) {
                    $entry->closing_stock -= $quantityDiff;
                } else {
                    $entry->opening_stock -= $quantityDiff;
                    $entry->closing_stock -= $quantityDiff;
                }
                $entry->save();
            }
        }
    }

    public function delete($reference)
    {
        $transactions = Transaction::forReporting()->where('reference', $reference)->get();

        foreach ($transactions as $tx) {
            $this->cascadeStockUpdate($tx->product_id, $tx->transacted_at, -$tx->quantity);
        }

        Transaction::forReporting()->where('reference', $reference)->delete();
        $this->dispatch('toast', message: 'Seluruh transaksi berhasil dihapus.');
    }

    public function viewDetails($reference): void
    {
        $this->detailReference = $reference;
        $this->showDetailsModal = true;
    }

    public function getDetailItemsProperty()
    {
        if (! $this->detailReference) {
            return collect();
        }

        return Transaction::forReporting()->with('product')->where('reference', $this->detailReference)->get();
    }

    public function edit($reference)
    {
        $this->editingReference = $reference;
        $transactions = Transaction::forReporting()->where('reference', $reference)->get();

        if ($transactions->isEmpty()) {
            return;
        }

        $first = $transactions->first();
        $this->editBuyerName = $first->buyer_name;
        $this->editStatus = $first->status;
        $this->editPaymentMethod = $first->payment_method ?? 'cash';

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
            'editPaymentMethod' => 'required|in:cash,transfer,qris',
            'editItems.*.quantity' => 'required|numeric|min:1',
        ]);

        foreach ($this->editItems as $item) {
            $tx = Transaction::forReporting()->find($item['id']);
            if ($tx) {
                $qtyDiff = $item['quantity'] - $tx->quantity;
                if ($qtyDiff != 0) {
                    $this->cascadeStockUpdate($tx->product_id, $tx->transacted_at, $qtyDiff);
                }

                $tx->update([
                    'buyer_name' => $this->editBuyerName,
                    'status' => $this->editStatus,
                    'payment_method' => $this->editPaymentMethod,
                    'quantity' => $item['quantity'],
                    'total_price' => $tx->unit_price * $item['quantity'],
                ]);
            }
        }

        $this->showEditModal = false;
        $this->dispatch('toast', message: 'Transaksi berhasil diperbarui.');
    }

    public function render()
    {
        $activeJurusanId = session('active_jurusan_id');
        $query = Transaction::query();

        $query->when($activeJurusanId, function ($q) use ($activeJurusanId) {
            return $q->where('jurusan_id', $activeJurusanId);
        })
            ->when(! $activeJurusanId && $this->filterJurusan, function ($q) {
                return $q->where('jurusan_id', $this->filterJurusan);
            });

        if ($this->highlight && ! $this->search) {
            $query->where('reference', $this->highlight);
        } elseif ($this->search) {
            $query->whereIn('reference', Transaction::query()
                ->select('reference')
                ->where('reference', 'like', '%' . $this->search . '%')
                ->orWhere('buyer_name', 'like', '%' . $this->search . '%')
                ->orWhereHas('product', function ($pq) {
                    $pq->where('name', 'like', '%' . $this->search . '%');
                })
            );
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterMonth) {
            $date = Carbon::parse($this->filterMonth . '-01');
            $query->whereYear('transacted_at', $date->year)
                ->whereMonth('transacted_at', $date->month);
        } elseif ($this->filterDate) {
            $query->whereDate('transacted_at', $this->filterDate);
        }

        // Calculate tab metrics (count & total) prior to payment_method filter
        $groupedSub = (clone $query)
            ->selectRaw("reference, MAX(COALESCE(payment_method, 'cash')) as p_method, SUM(total_price) as sum_total")
            ->groupBy('reference')
            ->get();

        $methodStats = [
            'all' => ['count' => $groupedSub->count(), 'total' => $groupedSub->sum('sum_total')],
            'cash' => [
                'count' => $groupedSub->whereIn('p_method', ['cash', '', null])->count(),
                'total' => $groupedSub->whereIn('p_method', ['cash', '', null])->sum('sum_total')
            ],
            'transfer' => [
                'count' => $groupedSub->where('p_method', 'transfer')->count(),
                'total' => $groupedSub->where('p_method', 'transfer')->sum('sum_total')
            ],
            'qris' => [
                'count' => $groupedSub->where('p_method', 'qris')->count(),
                'total' => $groupedSub->where('p_method', 'qris')->sum('sum_total')
            ],
        ];

        if ($this->filterPaymentMethod) {
            if ($this->filterPaymentMethod === 'cash') {
                $query->where(function ($q) {
                    $q->where('payment_method', 'cash')
                        ->orWhereNull('payment_method')
                        ->orWhere('payment_method', '');
                });
            } else {
                $query->where('payment_method', $this->filterPaymentMethod);
            }
        }

        $transactions = $query->selectRaw("reference, MAX(buyer_name) as buyer_name, MAX(status) as status, MAX(transacted_at) as transacted_at, MAX(COALESCE(payment_method, 'cash')) as payment_method, SUM(total_price) as total_amount, SUM(quantity) as total_qty, COUNT(*) as unique_items, MAX(jurusan_id) as jurusan_id, MAX(user_id) as user_id")
            ->groupBy('reference')
            ->orderByDesc('transacted_at')
            ->paginate(15);

        return view('livewire.history.transactions', [
            'transactions' => $transactions,
            'jurusans' => Jurusan::all(),
            'methodStats' => $methodStats,
        ])->layout('layouts.app', ['title' => 'Riwayat Transaksi']);
    }
}
