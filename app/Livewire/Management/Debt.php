<?php

namespace App\Livewire\Management;

use App\Models\Product;
use App\Models\StoreDebt;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Services\CustomerDebtService;
use App\Services\StoreDebtService;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Debt extends Component
{
    use WithPagination;

    public $search = '';

    public $activeTab = 'debt'; // 'debt' or 'change' or 'store_debt'

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

    public function mount() {}

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

    public function settle(CustomerDebtService $debtService)
    {
        $this->validate([
            'settleAmount' => 'required|numeric|min:1|max:'.$this->maxAmount,
            'settleMethod' => 'required|in:dibayarkan,dicicil,dijajankan',
        ]);

        $debtService->settleDebt(
            $this->selectedReference,
            $this->settleAmount,
            $this->settleMethod,
            $this->activeTab,
            $this->spentItems,
            auth()->id()
        );

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

    public function cancelSettle(CustomerDebtService $debtService)
    {
        if (! $this->cancelSettleReference) {
            return;
        }

        $debtService->cancelSettle($this->cancelSettleReference);

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
        if (! $this->detailReference) {
            return collect();
        }

        return Transaction::with('product')->where('reference', $this->detailReference)->get();
    }

    #[Computed]
    public function searchResults()
    {
        if (strlen($this->productSearch) < 2) {
            return collect();
        }

        return Product::where('is_active', true)
            ->where('name', 'like', '%'.$this->productSearch.'%')
            ->limit(5)
            ->get();
    }

    public function addSpentItem($productId)
    {
        $product = Product::find($productId);
        if (! $product) {
            return;
        }

        $existing = collect($this->spentItems)->firstWhere('product_id', $productId);
        if ($existing) {
            $this->spentItems = collect($this->spentItems)->map(function ($item) use ($productId) {
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
                'total' => $product->price,
            ];
        }
        $this->calculateTotalSpent();
        $this->productSearch = '';
    }

    public function updateSpentItemQty($index, $qty)
    {
        $qty = max(1, (int) $qty);
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

    public function saveStoreDebt(StoreDebtService $debtService)
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
            $supplier = Supplier::find($this->newStoreDebtSupplierId);
            if ($supplier) {
                $creditorName = $supplier->name;
            }
        }

        $debtService->createStoreDebt(
            $creditorName ?: 'Unknown Creditor',
            $this->newStoreDebtSupplierId ?: null,
            $this->newStoreDebtAmount,
            $this->newStoreDebtDate,
            $this->newStoreDebtDueDate ?: null,
            $this->newStoreDebtNote,
            session('active_jurusan_id')
        );

        $this->showCreateStoreDebtModal = false;
        $this->dispatch('toast', message: 'Hutang toko berhasil ditambahkan.');
    }

    public function openSettleStoreDebtModal($id)
    {
        $this->resetValidation();
        $debt = StoreDebt::find($id);
        if ($debt) {
            $this->selectedStoreDebtId = $id;
            $this->currentStoreDebtCreditor = $debt->creditor_name;
            $this->maxStoreDebtAmount = $debt->remaining_amount;
            $this->settleStoreDebtAmount = $debt->remaining_amount;
            $this->settleStoreDebtMethod = 'dibayarkan';
            $this->showSettleStoreDebtModal = true;
        }
    }

    public function settleStoreDebt(StoreDebtService $debtService)
    {
        $this->validate([
            'settleStoreDebtAmount' => 'required|numeric|min:1|max:'.$this->maxStoreDebtAmount,
            'settleStoreDebtMethod' => 'required|in:dibayarkan,dicicil',
        ]);

        $debtService->settleStoreDebt(
            $this->selectedStoreDebtId,
            $this->settleStoreDebtAmount,
            $this->settleStoreDebtMethod,
            session('active_jurusan_id')
        );

        $this->showSettleStoreDebtModal = false;
        $this->dispatch('toast', message: 'Pelunasan hutang toko berhasil diproses.');
    }

    public function openDeleteStoreDebtModal($id)
    {
        $debt = StoreDebt::find($id);
        if (! $debt) {
            return;
        }

        if (session('active_role_name') === 'superadmin') {
            $debt->update([
                'status' => 'dihapus',
                'remaining_amount' => 0,
                'note' => $debt->note ? $debt->note.' [Dihapus oleh Superadmin]' : '[Dihapus oleh Superadmin]',
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

    public function deleteStoreDebt(StoreDebtService $debtService)
    {
        $this->validate([
            'deleteStoreDebtReason' => 'required|string|min:10',
            'deleteStoreDebtVerification' => 'required|string|in:'.$this->deleteStoreDebtCreditorName,
            'deleteStoreDebtStatement' => 'accepted',
        ], [
            'deleteStoreDebtReason.required' => 'Alasan penghapusan wajib diisi.',
            'deleteStoreDebtReason.min' => 'Alasan harus diisi minimal 10 karakter untuk pertanggungjawaban.',
            'deleteStoreDebtVerification.in' => 'Konfirmasi nama salah. Harus sesuai kreditor: "'.$this->deleteStoreDebtCreditorName.'".',
            'deleteStoreDebtStatement.accepted' => 'Anda harus menyetujui pernyataan pertanggungjawaban.',
        ]);

        $user = auth()->user();
        $debtService->deleteStoreDebt(
            $this->deletingStoreDebtId,
            $this->deleteStoreDebtReason,
            $user->name
        );

        $this->showDeleteStoreDebtModal = false;
        $this->dispatch('toast', message: 'Hutang toko berhasil dihapus (Status diubah menjadi dihapus).');
    }

    public function render(CustomerDebtService $custService, StoreDebtService $storeService)
    {
        $activeJurusanId = session('active_jurusan_id');
        $storeDebts = collect();

        if ($this->activeTab === 'store_debt') {
            $activeStoreDebts = $storeService->getStoreDebts(
                $activeJurusanId,
                $this->search,
                $this->startDate,
                $this->endDate,
                'active',
                5,
                'activePage'
            );

            $historyStoreDebts = $storeService->getStoreDebts(
                $activeJurusanId,
                $this->search,
                $this->startDate,
                $this->endDate,
                'history',
                5,
                'historyPage'
            );

            $transactions = new LengthAwarePaginator([], 0, 15);
        } else {
            $transactions = $custService->getTransactions(
                $activeJurusanId,
                $this->activeTab,
                $this->activeTab === 'debt' ? $this->debtSubTab : $this->changeSubTab,
                $this->search,
                $this->startDate,
                $this->endDate,
                15
            );
        }

        $summary = $custService->getSummary($activeJurusanId);

        return view('livewire.management.debt', [
            'transactions' => $transactions,
            'storeDebts' => $storeDebts,
            'activeStoreDebts' => $activeStoreDebts ?? collect(),
            'historyStoreDebts' => $historyStoreDebts ?? collect(),
            'summary' => $summary,
            'suppliers' => Supplier::orderBy('name')->get(),
        ])->layout('layouts.app', ['title' => 'Manajemen Hutang & Kembalian']);
    }
}
