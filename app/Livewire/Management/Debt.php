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
    public $storeDebtSubTab = 'active'; // 'active' or 'history'
    public $changeSubTab = 'active'; // 'active' or 'history'
    public $debtSubTab = 'active'; // 'active' or 'history'
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

    public $showCancelSettleModal = false;
    public $cancelSettleReference = null;

    // 'Dijajankan' Feature Properties
    public $productSearch = '';
    public $spentItems = []; // [{product_id, name, price, quantity, total}]
    public $totalSpent = 0;

    // Store Debt Properties
    public $showCreateStoreDebtModal = false;
    public $newStoreDebtSupplierId = '';
    public $newStoreDebtCreditorName = '';
    public $newStoreDebtAmount = '';
    public $newStoreDebtDate = '';
    public $newStoreDebtDueDate = '';
    public $newStoreDebtNote = '';

    public $showSettleStoreDebtModal = false;
    public $selectedStoreDebtId = null;
    public $settleStoreDebtAmount = 0;
    public $settleStoreDebtMethod = 'dibayarkan'; // dibayarkan, dicicil
    public $maxStoreDebtAmount = 0;
    public $currentStoreDebtCreditor = '';

    // Delete Store Debt verification properties
    public $showDeleteStoreDebtModal = false;
    public $deletingStoreDebtId = null;
    public $deleteStoreDebtReason = '';
    public $deleteStoreDebtVerification = '';
    public $deleteStoreDebtStatement = false;
    public $deleteStoreDebtCreditorName = '';

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

    public function updatedStoreDebtSubTab()
    {
        $this->resetPage();
    }

    public function updatedChangeSubTab()
    {
        $this->resetPage();
    }

    public function updatedDebtSubTab()
    {
        $this->resetPage();
    }

    public function updatedSettleMethod($value)
    {
        if ($value === 'dijajankan') {
            $this->calculateTotalSpent();
        } elseif ($value === 'dibayarkan') {
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
                $updates['transacted_at'] = now();
            }

            $trx->update($updates);
        }

        // Check if the entire transaction is now fully settled
        $totalRemaining = Transaction::where('reference', $this->selectedReference)
            ->sum($this->activeTab === 'change' ? 'change_due' : 'debt_amount');
            
        if ($totalRemaining <= 0) {
            $allTrx = Transaction::where('reference', $this->selectedReference)->get();
            foreach ($allTrx as $t) {
                if ($t->status !== 'uang_diterima') {
                    $t->update([
                        'status' => 'uang_diterima',
                        'transacted_at' => now(),
                    ]);
                }
            }
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

    public function confirmCancelSettle($reference)
    {
        $this->cancelSettleReference = $reference;
        $this->showCancelSettleModal = true;
    }

    public function cancelSettle()
    {
        if (!$this->cancelSettleReference) {
            return;
        }

        $transactions = Transaction::where('reference', $this->cancelSettleReference)->get();
        
        $isChangeTrx = false;
        foreach ($transactions as $t) {
            if ($t->note && preg_match('/Kembalian:\s*Rp\s*([0-9.,]+)/i', $t->note)) {
                $isChangeTrx = true;
                break;
            }
        }

        foreach ($transactions as $trx) {
            if ($isChangeTrx) {
                $originalAmount = 0;
                if ($trx->note && preg_match('/Kembalian:\s*Rp\s*([0-9.,]+)/i', $trx->note, $matches)) {
                    $originalAmount = (int) str_replace(['.', ','], '', $matches[1]);
                }
                
                $trx->update([
                    'status' => 'belum_kembalian',
                    'change_due' => $originalAmount,
                    'note' => $originalAmount > 0 ? 'Kembalian: Rp' . number_format($originalAmount, 0, ',', '.') : null,
                    'transacted_at' => $trx->created_at,
                ]);
            } else {
                $trx->update([
                    'status' => 'belum_menerima_uang',
                    'debt_amount' => $trx->total_price,
                    'note' => null,
                    'transacted_at' => $trx->created_at,
                ]);
            }
        }
        
        $this->showCancelSettleModal = false;
        $this->cancelSettleReference = null;
        $this->dispatch('toast', message: 'Pelunasan berhasil dibatalkan dan status dikembalikan.');
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

    // Store Debt Actions
    public function openCreateStoreDebtModal()
    {
        $this->resetValidation();
        $this->newStoreDebtSupplierId = '';
        $this->newStoreDebtCreditorName = '';
        $this->newStoreDebtAmount = '';
        $this->newStoreDebtDate = now()->toDateString();
        $this->newStoreDebtDueDate = '';
        $this->newStoreDebtNote = '';
        $this->showCreateStoreDebtModal = true;
    }

    public function saveStoreDebt()
    {
        if (session('active_role_name') === 'superadmin') {
            abort(403, 'Superadmin tidak diizinkan membuat hutang baru.');
        }
        $this->validate([
            'newStoreDebtCreditorName' => 'required_without:newStoreDebtSupplierId|nullable|string|max:255',
            'newStoreDebtAmount' => 'required|numeric|min:1',
            'newStoreDebtDate' => 'required|date',
            'newStoreDebtDueDate' => 'nullable|date|after_or_equal:newStoreDebtDate',
            'newStoreDebtNote' => 'nullable|string',
        ]);

        $creditorName = $this->newStoreDebtCreditorName;
        if ($this->newStoreDebtSupplierId) {
            $supplier = \App\Models\Supplier::find($this->newStoreDebtSupplierId);
            if ($supplier) {
                $creditorName = $supplier->name;
            }
        }

        \App\Models\StoreDebt::create([
            'jurusan_id' => session('active_jurusan_id'),
            'supplier_id' => $this->newStoreDebtSupplierId ?: null,
            'creditor_name' => $creditorName ?: 'Unknown Creditor',
            'amount' => $this->newStoreDebtAmount,
            'remaining_amount' => $this->newStoreDebtAmount,
            'status' => 'belum_lunas',
            'note' => $this->newStoreDebtNote,
            'due_date' => $this->newStoreDebtDueDate ?: null,
            'date' => $this->newStoreDebtDate,
        ]);

        $this->showCreateStoreDebtModal = false;
        $this->dispatch('toast', message: 'Hutang toko berhasil ditambahkan.');
    }

    public function openSettleStoreDebtModal($id)
    {
        $this->resetValidation();
        $debt = \App\Models\StoreDebt::find($id);
        if ($debt) {
            $this->selectedStoreDebtId = $id;
            $this->currentStoreDebtCreditor = $debt->creditor_name;
            $this->maxStoreDebtAmount = $debt->remaining_amount;
            $this->settleStoreDebtAmount = $debt->remaining_amount;
            $this->settleStoreDebtMethod = 'dibayarkan';
            $this->showSettleStoreDebtModal = true;
        }
    }

    public function settleStoreDebt()
    {
        $this->validate([
            'settleStoreDebtAmount' => 'required|numeric|min:1|max:' . $this->maxStoreDebtAmount,
            'settleStoreDebtMethod' => 'required|in:dibayarkan,dicicil',
        ]);

        $debt = \App\Models\StoreDebt::find($this->selectedStoreDebtId);
        if ($debt) {
            $deduct = $this->settleStoreDebtAmount;
            $newRemaining = $debt->remaining_amount - $deduct;
            
            $note = $debt->note;
            $paymentNote = " (Pelunasan {$this->settleStoreDebtMethod} Rp" . number_format($deduct, 0, ',', '.') . " pd " . now()->format('d/m/Y H:i') . ")";
            $note = $note ? $note . $paymentNote : ltrim($paymentNote);

            $debt->update([
                'remaining_amount' => $newRemaining,
                'status' => $newRemaining <= 0 ? 'lunas' : 'belum_lunas',
                'note' => $note,
            ]);

            // Add to Cash Book / cash_transactions as Expense
            $category = \App\Models\CashCategory::where('name', 'Pelunasan Hutang')->first();
            if (!$category) {
                $category = \App\Models\CashCategory::create([
                    'name' => 'Pelunasan Hutang',
                    'jurusan_id' => session('active_jurusan_id'),
                ]);
            }

            \App\Models\CashTransaction::create([
                'jurusan_id' => session('active_jurusan_id'),
                'date' => now()->toDateString(),
                'type' => 'expense',
                'cash_type' => 'keuntungan',
                'cash_category_id' => $category->id,
                'amount' => $deduct,
                'description' => "Pelunasan hutang toko kepada " . $debt->creditor_name,
                'reference' => 'PAY-DEBT-' . now()->format('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2))),
            ]);
        }

        $this->showSettleStoreDebtModal = false;
        $this->dispatch('toast', message: 'Pelunasan hutang toko berhasil diproses.');
    }

    public function openDeleteStoreDebtModal($id)
    {
        $debt = \App\Models\StoreDebt::find($id);
        if (!$debt) {
            return;
        }

        if (session('active_role_name') === 'superadmin') {
            $debt->update([
                'status' => 'dihapus',
                'remaining_amount' => 0,
                'note' => $debt->note ? $debt->note . " [Dihapus oleh Superadmin]" : "[Dihapus oleh Superadmin]",
            ]);
            $this->dispatch('toast', message: 'Hutang toko berhasil dihapus oleh Superadmin.');
            return;
        }

        $this->resetValidation();
        $this->deletingStoreDebtId = $id;
        $this->deleteStoreDebtCreditorName = $debt->creditor_name;
        $this->deleteStoreDebtReason = '';
        $this->deleteStoreDebtVerification = '';
        $this->deleteStoreDebtStatement = false;
        $this->showDeleteStoreDebtModal = true;
    }

    public function deleteStoreDebt()
    {
        $this->validate([
            'deleteStoreDebtReason' => 'required|string|min:10',
            'deleteStoreDebtVerification' => 'required|string|in:' . $this->deleteStoreDebtCreditorName,
            'deleteStoreDebtStatement' => 'accepted',
        ], [
            'deleteStoreDebtReason.required' => 'Alasan penghapusan wajib diisi.',
            'deleteStoreDebtReason.min' => 'Alasan harus diisi minimal 10 karakter untuk pertanggungjawaban.',
            'deleteStoreDebtVerification.in' => 'Konfirmasi nama salah. Harus sesuai kreditor: "' . $this->deleteStoreDebtCreditorName . '".',
            'deleteStoreDebtStatement.accepted' => 'Anda harus menyetujui pernyataan pertanggungjawaban.',
        ]);

        $debt = \App\Models\StoreDebt::find($this->deletingStoreDebtId);
        if ($debt) {
            $user = auth()->user();
            $deletionNote = " [Dihapus pd " . now()->format('d/m/Y H:i') . " oleh " . $user->name . ". Alasan: " . $this->deleteStoreDebtReason . "]";
            $newNote = $debt->note ? $debt->note . $deletionNote : ltrim($deletionNote);

            $debt->update([
                'status' => 'dihapus',
                'remaining_amount' => 0,
                'note' => $newNote,
            ]);
        }

        $this->showDeleteStoreDebtModal = false;
        $this->dispatch('toast', message: 'Hutang toko berhasil dihapus (Status diubah menjadi dihapus).');
    }

    public function render()
    {
        $activeJurusanId = session('active_jurusan_id');
        $storeDebts = collect();
        
        if ($this->activeTab === 'store_debt') {
            $storeDebtQuery = \App\Models\StoreDebt::query()
                ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                    return $q->where('jurusan_id', $activeJurusanId);
                });

            if ($this->search) {
                $storeDebtQuery->where('creditor_name', 'like', '%' . $this->search . '%');
            }

            if ($this->startDate) {
                $storeDebtQuery->whereDate('date', '>=', $this->startDate);
            }

            if ($this->endDate) {
                $storeDebtQuery->whereDate('date', '<=', $this->endDate);
            }

            // Clone query and paginate separately
            $activeStoreDebts = (clone $storeDebtQuery)->where('status', 'belum_lunas')
                ->orderByDesc('date')
                ->orderByDesc('created_at')
                ->paginate(5, ['*'], 'activePage');

            $historyStoreDebts = (clone $storeDebtQuery)->whereIn('status', ['lunas', 'dihapus'])
                ->orderByDesc('date')
                ->orderByDesc('created_at')
                ->paginate(5, ['*'], 'historyPage');

            $storeDebts = collect();
            $transactions = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
        } else {
            $query = Transaction::query()
                ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                    return $q->where('jurusan_id', $activeJurusanId);
                })
                ->when($this->activeTab === 'debt', function ($q) {
                    if ($this->debtSubTab === 'history') {
                        return $q->where('status', 'uang_diterima')
                            ->whereIn('reference', function ($sub) {
                                $sub->select('reference')
                                    ->from('transactions')
                                    ->where('note', 'like', '%Pelunasan%')
                                    ->where('note', 'not like', '%Kembalian%');
                            });
                    } else {
                        return $q->whereIn('status', ['belum_menerima_uang', 'uang_dipinjam']);
                    }
                })
                ->when($this->activeTab === 'change', function ($q) {
                    if ($this->changeSubTab === 'history') {
                        return $q->where('status', 'uang_diterima')
                            ->whereIn('reference', function ($sub) {
                                $sub->select('reference')
                                    ->from('transactions')
                                    ->where('note', 'like', '%Pelunasan%')
                                    ->where('note', 'like', '%Kembalian%');
                            });
                    } else {
                        return $q->where('status', 'belum_kembalian');
                    }
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

            $transactions = $query->selectRaw('reference, buyer_name, status, transacted_at, MAX(note) as note, SUM(total_price) as total_price, SUM(debt_amount) as debt_amount, SUM(change_due) as change_due, COUNT(*) as items_count')
                ->groupBy('reference', 'buyer_name', 'status', 'transacted_at')
                ->orderByDesc('transacted_at')
                ->paginate(15);
        }

        $summary = [
            'total_debt' => Transaction::when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })->whereIn('status', ['belum_menerima_uang', 'uang_dipinjam'])->sum('debt_amount'),
            'total_change' => Transaction::when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })->where('status', 'belum_kembalian')->sum('change_due'),
            'total_store_debt' => \App\Models\StoreDebt::when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })->where('status', 'belum_lunas')->sum('remaining_amount'),
        ];

        return view('livewire.management.debt', [
            'transactions' => $transactions,
            'storeDebts' => $storeDebts,
            'activeStoreDebts' => $activeStoreDebts ?? collect(),
            'historyStoreDebts' => $historyStoreDebts ?? collect(),
            'summary' => $summary,
            'suppliers' => \App\Models\Supplier::orderBy('name')->get()
        ])->layout('layouts.app', ['title' => 'Manajemen Hutang & Kembalian']);
    }
}
