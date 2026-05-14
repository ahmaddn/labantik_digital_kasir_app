<?php

namespace App\Livewire\Management;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;

class Debt extends Component
{
    use WithPagination;

    public $search = '';
    public $activeTab = 'debt'; // 'debt' or 'change'
    public $startDate;
    public $endDate;

    // Modal State
    public $showSettleModal = false;
    public $selectedReference = null;
    public $settleAmount = 0;
    public $settleMethod = 'dibayarkan'; // dibayarkan, dicicil, dijajankan
    public $maxAmount = 0;
    public $currentBuyerName = '';

    // Details Modal
    public $showDetailsModal = false;
    public $detailReference = null;

    // 'Dijajankan' Feature Properties
    public $productSearch = '';
    public $spentItems = []; // [{product_id, name, price, quantity, total}]
    public $totalSpent = 0;

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

    public function updatedSettleMethod($value)
    {
        if ($value === 'dijajankan') {
            $this->calculateTotalSpent();
        } else {
            $this->settleAmount = $this->maxAmount;
        }
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function openSettleModal($reference)
    {
        $this->selectedReference = $reference;
        $trx = Transaction::where('reference', $reference)->first();
        
        if ($trx) {
            $this->currentBuyerName = $trx->buyer_name;
            $this->maxAmount = Transaction::where('reference', $reference)
                ->sum($this->activeTab === 'change' ? 'change_due' : 'debt_amount');
                
            $this->settleAmount = $this->maxAmount;
            $this->settleMethod = 'dibayarkan';
            $this->showSettleModal = true;
        }
    }

    public function settle()
    {
        $this->validate([
            'settleAmount' => 'required|numeric|min:1|max:' . $this->maxAmount,
            'settleMethod' => 'required|in:dibayarkan,dicicil,dijajankan',
        ]);

        $transactions = Transaction::where('reference', $this->selectedReference)->get();
        $remainingToSettle = $this->settleAmount;

        foreach ($transactions as $trx) {
            if ($remainingToSettle <= 0) break;

            $currentField = $this->activeTab === 'change' ? 'change_due' : 'debt_amount';
            $currentAmount = $trx->$currentField;
            
            if ($currentAmount <= 0) continue;

            $deduct = min($currentAmount, $remainingToSettle);
            $remainingToSettle -= $deduct;

            $newAmount = $currentAmount - $deduct;
            $updates = [
                $currentField => $newAmount,
                'note' => $trx->note . " (Pelunasan {$this->settleMethod} Rp" . number_format($deduct, 0, ',', '.') . " pd " . now()->format('d/m/Y H:i') . ")"
            ];

            if ($newAmount <= 0) {
                // If this specific row is now fully settled
                if ($this->activeTab === 'change') {
                    // It was belum_kembalian, now it's fully received/returned
                    $updates['status'] = 'uang_diterima';
                } else {
                    // It was belum_menerima_uang or uang_dipinjam, now it's received
                    $updates['status'] = 'uang_diterima';
                }
                $updates['transacted_at'] = now(); // Move to today to count profit/cash today
            }

            $trx->update($updates);
        }

        // Create new transactions if 'dijajankan'
        if ($this->settleMethod === 'dijajankan' && !empty($this->spentItems)) {
            $reference = 'SPNT-' . now()->format('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2)));
            foreach ($this->spentItems as $item) {
                Transaction::create([
                    'reference' => $reference,
                    'user_id' => auth()->id(),
                    'product_id' => $item['product_id'],
                    'supplier_id' => $item['supplier_id'],
                    'transacted_at' => now(),
                    'buyer_name' => $transactions->first()->buyer_name, // Carry over buyer name
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'unit_profit' => $item['profit'],
                    'total_price' => $item['total'],
                    'status' => 'uang_diterima',
                    'note' => "Pembelian dari tukar kembalian nota " . $this->selectedReference
                ]);
            }
        }

        $this->showSettleModal = false;
        $this->showDetailsModal = false;
        $this->spentItems = [];
        $this->totalSpent = 0;
        $this->dispatch('toast', message: 'Pelunasan berhasil diproses.');
    }

    public function viewDetails($reference)
    {
        $this->detailReference = $reference;
        $this->showDetailsModal = true;
    }

    #[Computed]
    public function detailItems()
    {
        if (!$this->detailReference) return collect();
        return Transaction::with('product')->where('reference', $this->detailReference)->get();
    }

    #[Computed]
    public function searchResults()
    {
        if (strlen($this->productSearch) < 2) return collect();
        return Product::where('is_active', true)
            ->where('name', 'like', '%' . $this->productSearch . '%')
            ->limit(5)
            ->get();
    }

    public function addSpentItem($productId)
    {
        $product = Product::find($productId);
        if (!$product) return;

        $existing = collect($this->spentItems)->firstWhere('product_id', $productId);
        if ($existing) {
            $this->spentItems = collect($this->spentItems)->map(function($item) use ($productId) {
                if ($item['product_id'] == $productId) {
                    $item['quantity']++;
                    $item['total'] = $item['quantity'] * $item['price'];
                }
                return $item;
            })->toArray();
        } else {
            $this->spentItems[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'profit' => $product->profit,
                'supplier_id' => $product->supplier_id,
                'quantity' => 1,
                'total' => $product->price
            ];
        }
        $this->calculateTotalSpent();
        $this->productSearch = '';
    }

    public function updateSpentItemQty($index, $qty)
    {
        $qty = max(1, (int)$qty);
        $this->spentItems[$index]['quantity'] = $qty;
        $this->spentItems[$index]['total'] = $qty * $this->spentItems[$index]['price'];
        $this->calculateTotalSpent();
    }

    public function removeSpentItem($index)
    {
        unset($this->spentItems[$index]);
        $this->spentItems = array_values($this->spentItems);
        $this->calculateTotalSpent();
    }

    public function calculateTotalSpent()
    {
        $this->totalSpent = collect($this->spentItems)->sum('total');
        if ($this->settleMethod === 'dijajankan') {
            $this->settleAmount = $this->totalSpent;
        }
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

        return view('livewire.management.debt', [
            'transactions' => $transactions,
            'summary' => $summary
        ])->layout('layouts.app', ['title' => 'Manajemen Hutang & Kembalian']);
    }
}
