<?php

namespace App\Livewire\Finance;

use App\Models\CashTransaction;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class CashManagement extends Component
{
    use WithPagination;

    // Filters
    public string $filterMonth = '';

    // Form inputs
    public string $date = '';
    public string $cashType = 'modal';
    public $cashCategoryId = '';
    public string $newCategoryName = '';
    public string $type = 'expense';
    public $amount;
    public string $description = '';

    // Split details
    public $amountModal;
    public $amountProfit;
    public float $selectedCategoryModalBalance = 0;
    public float $selectedCategoryProfitBalance = 0;

    // Modal state
    public bool $showModal = false;
    public $editingId = null;
    public bool $showDeleteConfirmation = false;
    public $confirmingDeleteId = null;

    // Physical Cash Adjustment Modal properties
    public bool $showAdjustModal = false;
    public $adjustCategoryId = '';
    public $adjustCategoryName = '';
    public $adjustSystemBalance = 0;
    public $adjustPhysicalBalance;
    public string $adjustCashType = 'keuntungan';

    // Consolidation Modal properties
    public bool $showConsolidateModal = false;
    public $consolidateAmount;
    public string $consolidateCashType = 'keuntungan';

    public function mount()
    {
        $this->filterMonth = now()->format('Y-m');
        $this->date = now()->format('Y-m-d');
    }

    public function updatedFilterMonth()
    {
        $this->resetPage();
    }

    public function updatedCashCategoryId($value)
    {
        $this->calculateSelectedCategoryBalances($value);
    }

    public function updatedAmountModal($value)
    {
        if ($this->cashType === 'keduanya') {
            $this->amount = (float)($this->amountModal ?: 0) + (float)($this->amountProfit ?: 0);
        }
    }

    public function updatedAmountProfit($value)
    {
        if ($this->cashType === 'keduanya') {
            $this->amount = (float)($this->amountModal ?: 0) + (float)($this->amountProfit ?: 0);
        }
    }

    public function updatedCashType($value)
    {
        if ($value !== 'keduanya') {
            $this->amountModal = null;
            $this->amountProfit = null;
        } else {
            $this->amount = (float)($this->amountModal ?: 0) + (float)($this->amountProfit ?: 0);
        }
    }

    public function updatedType($value)
    {
        if ($value !== 'expense' && $this->cashType === 'keduanya') {
            $this->cashType = 'modal';
            $this->amountModal = null;
            $this->amountProfit = null;
        }
    }

    public function calculateSelectedCategoryBalances($categoryId)
    {
        if (!$categoryId) {
            $this->selectedCategoryModalBalance = 0;
            $this->selectedCategoryProfitBalance = 0;
            return;
        }

        $activeJurusanId = session('active_jurusan_id');

        $modalIncome = CashTransaction::where('jurusan_id', $activeJurusanId)->where('cash_category_id', $categoryId)->where('cash_type', 'modal')->where('type', 'income')->sum('amount');
        $modalExpense = CashTransaction::where('jurusan_id', $activeJurusanId)->where('cash_category_id', $categoryId)->where('cash_type', 'modal')->where('type', 'expense')->sum('amount');
        $this->selectedCategoryModalBalance = $modalIncome - $modalExpense;

        $profitIncome = CashTransaction::where('jurusan_id', $activeJurusanId)->where('cash_category_id', $categoryId)->where('cash_type', 'keuntungan')->where('type', 'income')->sum('amount');
        $profitExpense = CashTransaction::where('jurusan_id', $activeJurusanId)->where('cash_category_id', $categoryId)->where('cash_type', 'keuntungan')->where('type', 'expense')->sum('amount');

        // Handle Bagi Hasil Mingguan deduction if applicable
        $category = \App\Models\CashCategory::find($categoryId);
        $bagiHasilDeduction = 0;
        if ($category && $category->name !== 'Bagi Hasil Mingguan') {
            $catBagiHasil = \App\Models\CashCategory::where('jurusan_id', $activeJurusanId)->where('name', 'Bagi Hasil Mingguan')->first();
            if ($catBagiHasil) {
                $bagiHasilTransactions = CashTransaction::where('jurusan_id', $activeJurusanId)
                    ->where('cash_category_id', $catBagiHasil->id)
                    ->get();

                $prodCatName = str_replace('Penjualan ', '', $category->name);
                foreach ($bagiHasilTransactions as $tx) {
                    if (str_contains(strtolower($tx->description), 'kategori:')) {
                        if ($category->name === 'Jurusan Snack & Minuman') {
                            if (str_contains(strtolower($tx->description), 'makanan') || str_contains(strtolower($tx->description), 'minuman') || str_contains(strtolower($tx->description), 'snack')) {
                                $bagiHasilDeduction += $tx->amount;
                            }
                        } else {
                            if (str_contains(strtolower($tx->description), strtolower($prodCatName))) {
                                $bagiHasilDeduction += $tx->amount;
                            }
                        }
                    }
                }
            }
        }

        $profitBalance = $profitIncome - $profitExpense;
        if ($bagiHasilDeduction > 0) {
            $profitBalance -= $bagiHasilDeduction;
        }

        if ($category && $category->name === 'Bagi Hasil Mingguan') {
            $profitBalance = 0;
        }

        $this->selectedCategoryProfitBalance = $profitBalance;
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->reset(['type', 'cashType', 'cashCategoryId', 'newCategoryName', 'amount', 'description', 'editingId', 'amountModal', 'amountProfit', 'selectedCategoryModalBalance', 'selectedCategoryProfitBalance']);
        $this->date = now()->format('Y-m-d');
        $this->type = 'expense';
        $this->cashType = 'modal';
        $this->showModal = true;
    }

    public function saveTransaction()
    {
        $validationRules = [
            'date' => 'required|date',
            'cashType' => 'required|in:modal,keuntungan,keduanya',
            'cashCategoryId' => 'required|exists:cash_categories,id',
            'type' => 'required|in:income,expense',
            'description' => 'required|string|max:255',
        ];

        if ($this->cashType === 'keduanya') {
            $validationRules['amountModal'] = 'required|numeric|min:0';
            $validationRules['amountProfit'] = 'required|numeric|min:0';
        } else {
            $validationRules['amount'] = 'required|numeric|min:1';
        }

        $this->validate($validationRules);

        if ($this->cashType === 'keduanya') {
            $this->amount = (float)($this->amountModal ?: 0) + (float)($this->amountProfit ?: 0);
            if ($this->amount <= 0) {
                $this->addError('amount', 'Total nominal gabungan harus lebih dari 0.');
                return;
            }
        }

        $activeJurusanId = session('active_jurusan_id');

        \Illuminate\Support\Facades\DB::transaction(function () use ($activeJurusanId) {
            // If editing, delete original transaction(s) first
            if ($this->editingId) {
                $oldTx = CashTransaction::find($this->editingId);
                if ($oldTx) {
                    if ($oldTx->reference && str_starts_with($oldTx->reference, 'SPLIT-')) {
                        CashTransaction::where('reference', $oldTx->reference)->delete();
                    } else {
                        $oldTx->delete();
                    }
                }
            }

            // Create new transaction(s)
            if ($this->cashType === 'keduanya') {
                $splitRef = 'SPLIT-' . \Illuminate\Support\Str::uuid()->toString();

                if ($this->amountModal > 0) {
                    CashTransaction::create([
                        'jurusan_id' => $activeJurusanId,
                        'date' => $this->date,
                        'cash_type' => 'modal',
                        'cash_category_id' => $this->cashCategoryId,
                        'type' => $this->type,
                        'amount' => $this->amountModal,
                        'description' => $this->description,
                        'reference' => $splitRef,
                    ]);
                }

                if ($this->amountProfit > 0) {
                    CashTransaction::create([
                        'jurusan_id' => $activeJurusanId,
                        'date' => $this->date,
                        'cash_type' => 'keuntungan',
                        'cash_category_id' => $this->cashCategoryId,
                        'type' => $this->type,
                        'amount' => $this->amountProfit,
                        'description' => $this->description,
                        'reference' => $splitRef,
                    ]);
                }
            } else {
                CashTransaction::create([
                    'jurusan_id' => $activeJurusanId,
                    'date' => $this->date,
                    'cash_type' => $this->cashType,
                    'cash_category_id' => $this->cashCategoryId,
                    'type' => $this->type,
                    'amount' => $this->amount,
                    'description' => $this->description,
                ]);
            }
        });

        $message = $this->editingId ? 'Data kas berhasil diperbarui!' : 'Data kas berhasil ditambahkan!';
        $this->showModal = false;
        $this->dispatch('toast', message: $message);
    }

    public function editTransaction($id)
    {
        $transaction = CashTransaction::find($id);
        if ($transaction) {
            $this->resetValidation();
            $this->editingId = $transaction->id;
            $this->date = $transaction->date;
            $this->cashCategoryId = $transaction->cash_category_id;
            $this->type = $transaction->type;
            $this->description = $transaction->description;

            $this->calculateSelectedCategoryBalances($transaction->cash_category_id);

            if ($transaction->reference && str_starts_with($transaction->reference, 'SPLIT-')) {
                $this->cashType = 'keduanya';
                $siblings = CashTransaction::where('reference', $transaction->reference)->get();
                $modalTx = $siblings->where('cash_type', 'modal')->first();
                $profitTx = $siblings->where('cash_type', 'keuntungan')->first();

                $this->amountModal = $modalTx ? $modalTx->amount : 0;
                $this->amountProfit = $profitTx ? $profitTx->amount : 0;
                $this->amount = $this->amountModal + $this->amountProfit;
            } else {
                $this->cashType = $transaction->cash_type;
                $this->amount = $transaction->amount;
                $this->amountModal = null;
                $this->amountProfit = null;
            }
            $this->showModal = true;
        }
    }

    public function saveCategory()
    {
        $activeJurusanId = session('active_jurusan_id');
        $this->validate([
            'newCategoryName' => 'required|string|max:100|unique:cash_categories,name,NULL,id,jurusan_id,' . $activeJurusanId,
        ]);

        $category = \App\Models\CashCategory::create([
            'name' => $this->newCategoryName,
            'jurusan_id' => $activeJurusanId,
        ]);

        $this->cashCategoryId = $category->id;
        $this->newCategoryName = '';
        $this->calculateSelectedCategoryBalances($category->id);
        $this->dispatch('toast', message: 'Kategori berhasil ditambahkan!');
    }

    public function confirmDelete($id)
    {
        $this->confirmingDeleteId = $id;
        $this->showDeleteConfirmation = true;
    }

    public function deleteTransaction()
    {
        if ($this->confirmingDeleteId) {
            $transaction = CashTransaction::find($this->confirmingDeleteId);
            if ($transaction) {
                if ($transaction->reference && str_starts_with($transaction->reference, 'SPLIT-')) {
                    CashTransaction::where('reference', $transaction->reference)->delete();
                } else {
                    $transaction->delete();
                }
                $this->dispatch('toast', message: 'Catatan kas berhasil dihapus.');
            }
            $this->showDeleteConfirmation = false;
            $this->confirmingDeleteId = null;
        }
    }

    public function openAdjustModal($id, $name, $balance): void
    {
        $this->resetValidation();
        $this->adjustCategoryId = $id;
        $this->adjustCategoryName = $name;
        $this->adjustSystemBalance = (float) $balance;
        $this->adjustPhysicalBalance = '';
        $this->adjustCashType = 'keuntungan';
        $this->showAdjustModal = true;
    }

    public function submitAdjustment(): void
    {
        $this->validate([
            'adjustPhysicalBalance' => 'required|numeric|min:0',
            'adjustCashType' => 'required|in:modal,keuntungan',
        ]);

        $activeJurusanId = session('active_jurusan_id');
        $difference = (float) $this->adjustPhysicalBalance - $this->adjustSystemBalance;

        if (abs($difference) < 0.01) {
            $this->showAdjustModal = false;
            $this->dispatch('toast', message: 'Saldo fisik sudah sesuai dengan saldo sistem.');
            return;
        }

        $type = $difference > 0 ? 'income' : 'expense';
        $amount = abs($difference);
        $descType = $difference > 0 ? 'Lebih' : 'Kurang';

        CashTransaction::create([
            'jurusan_id' => $activeJurusanId,
            'date' => now()->format('Y-m-d'),
            'cash_type' => $this->adjustCashType,
            'cash_category_id' => $this->adjustCategoryId,
            'type' => $type,
            'amount' => $amount,
            'description' => "Penyesuaian Selisih {$descType} Kas {$this->adjustCategoryName} (Manual)",
        ]);

        // Invalidate cache
        \Illuminate\Support\Facades\Cache::forget('cash_balances_' . ($activeJurusanId ?: 'global'));

        $this->showAdjustModal = false;
        $this->dispatch('toast', message: 'Penyesuaian kas fisik berhasil disimpan!');
    }

    public function openConsolidateModal(): void
    {
        $this->consolidateAmount = '';
        $this->consolidateCashType = 'keuntungan';
        $this->showConsolidateModal = true;
    }

    public function consolidateToParent(): void
    {
        $activeJurusanId = session('active_jurusan_id');
        $jurusan = \App\Models\Jurusan::with('parent')->find($activeJurusanId);

        if (!$jurusan || !$jurusan->parent_id) {
            $this->dispatch('toast', message: 'Hanya sub-unit usaha yang dapat melakukan konsolidasi kas ke induk.', type: 'error');
            return;
        }

        $this->validate([
            'consolidateAmount' => 'required|numeric|min:1',
            'consolidateCashType' => 'required|in:modal,keuntungan',
        ]);

        // Calculate current balance of sub-unit
        $balances = CashTransaction::where('jurusan_id', $activeJurusanId)
            ->selectRaw("
                SUM(CASE WHEN cash_type = 'modal' AND type = 'income' THEN amount ELSE 0 END) as modal_income,
                SUM(CASE WHEN cash_type = 'modal' AND type = 'expense' THEN amount ELSE 0 END) as modal_expense,
                SUM(CASE WHEN cash_type = 'keuntungan' AND type = 'income' THEN amount ELSE 0 END) as profit_income,
                SUM(CASE WHEN cash_type = 'keuntungan' AND type = 'expense' THEN amount ELSE 0 END) as profit_expense
            ")
            ->first();

        $currentModalBalance = ($balances->modal_income ?? 0) - ($balances->modal_expense ?? 0);
        $currentProfitBalance = ($balances->profit_income ?? 0) - ($balances->profit_expense ?? 0);
        $availableBalance = $this->consolidateCashType === 'modal' ? $currentModalBalance : $currentProfitBalance;

        if ((float)$this->consolidateAmount > $availableBalance) {
            $this->dispatch('toast', message: 'Saldo tidak mencukupi untuk melakukan konsolidasi kas.', type: 'error');
            return;
        }

        // 1. Record expense in child
        CashTransaction::create([
            'jurusan_id' => $activeJurusanId,
            'date' => now()->format('Y-m-d'),
            'cash_type' => $this->consolidateCashType,
            'type' => 'expense',
            'amount' => (float)$this->consolidateAmount,
            'description' => "Konsolidasi Saldo ke Unit Induk (TEFA {$jurusan->parent->name})",
        ]);

        // 2. Find or create Category in Parent for "Konsolidasi Sub-Unit"
        $parentCategoryId = null;
        $parentCategory = \App\Models\CashCategory::where('jurusan_id', $jurusan->parent_id)
            ->where('name', 'Konsolidasi Sub-Unit')
            ->first();
        if (!$parentCategory) {
            $parentCategory = \App\Models\CashCategory::create([
                'jurusan_id' => $jurusan->parent_id,
                'name' => 'Konsolidasi Sub-Unit',
            ]);
        }
        $parentCategoryId = $parentCategory->id;

        // 3. Record income in parent
        CashTransaction::create([
            'jurusan_id' => $jurusan->parent_id,
            'date' => now()->format('Y-m-d'),
            'cash_type' => $this->consolidateCashType,
            'cash_category_id' => $parentCategoryId,
            'type' => 'income',
            'amount' => (float)$this->consolidateAmount,
            'description' => "Penerimaan Konsolidasi dari Sub-Unit: {$jurusan->name}",
        ]);

        // Invalidate caches
        \Illuminate\Support\Facades\Cache::forget('cash_balances_' . $activeJurusanId);
        \Illuminate\Support\Facades\Cache::forget('cash_balances_' . $jurusan->parent_id);

        $this->showConsolidateModal = false;
        $this->dispatch('toast', message: 'Konsolidasi kas ke induk berhasil dicatat!');
    }

    public function exportExcel()
    {
        $filename = 'Laporan_Kas_Internal_' . Carbon::parse($this->filterMonth)->translatedFormat('F_Y') . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\CashTransactionsExport($this->filterMonth), $filename);
    }

    public function render()
    {
        $activeJurusanId = session('active_jurusan_id');
        $jurusan = \App\Models\Jurusan::find($activeJurusanId);
        $isSubUnit = $jurusan && $jurusan->parent_id;
        $selectedMonth = $this->filterMonth ?: now()->format('Y-m');
        $selectedYear = Carbon::parse($selectedMonth . '-01')->year;
        $selectedMonthNumber = Carbon::parse($selectedMonth . '-01')->month;

        $query = CashTransaction::where('jurusan_id', $activeJurusanId)
            ->whereYear('date', $selectedYear)
            ->whereMonth('date', $selectedMonthNumber);

        // Overall cumulative balances (from the beginning of time up to the end of the filtered month)
        $endOfFilteredMonth = Carbon::parse($selectedMonth . '-01')->endOfMonth()->toDateString();
        $overallBalances = CashTransaction::where('jurusan_id', $activeJurusanId)
            ->where('date', '<=', $endOfFilteredMonth)
            ->selectRaw("
                SUM(CASE WHEN cash_type = 'modal' AND type = 'income' THEN amount ELSE 0 END) as modal_income,
                SUM(CASE WHEN cash_type = 'modal' AND type = 'expense' THEN amount ELSE 0 END) as modal_expense,
                SUM(CASE WHEN cash_type = 'keuntungan' AND type = 'income' THEN amount ELSE 0 END) as profit_income,
                SUM(CASE WHEN cash_type = 'keuntungan' AND type = 'expense' THEN amount ELSE 0 END) as profit_expense
            ")
            ->first();

        $currentModalBalance = ($overallBalances->modal_income ?? 0) - ($overallBalances->modal_expense ?? 0);
        $currentProfitBalance = ($overallBalances->profit_income ?? 0) - ($overallBalances->profit_expense ?? 0);

        $categorySums = (clone $query)
            ->selectRaw("
                cash_category_id,
                SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as cat_income,
                SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as cat_expense,
                SUM(CASE WHEN cash_type = 'modal' AND type = 'income' THEN amount ELSE 0 END) as modal_income,
                SUM(CASE WHEN cash_type = 'modal' AND type = 'expense' THEN amount ELSE 0 END) as modal_expense,
                SUM(CASE WHEN cash_type = 'keuntungan' AND type = 'income' THEN amount ELSE 0 END) as profit_income,
                SUM(CASE WHEN cash_type = 'keuntungan' AND type = 'expense' THEN amount ELSE 0 END) as profit_expense
            ")
            ->groupBy('cash_category_id')
            ->get()
            ->keyBy('cash_category_id');

        $catBagiHasil = \App\Models\CashCategory::where('jurusan_id', $activeJurusanId)->where('name', 'Bagi Hasil Mingguan')->first();
        $bagiHasilTransactions = [];
        if ($catBagiHasil) {
            $bagiHasilTransactions = (clone $query)
                ->where('cash_category_id', $catBagiHasil->id)
                ->get()
                ->toArray();
        }

        $monthlyStats = (clone $query)
            ->selectRaw("
                SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as income,
                SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expense
            ")
            ->first();

        $monthlyIncome = $monthlyStats->income ?? 0;
        $monthlyExpense = $monthlyStats->expense ?? 0;

        $categories = \App\Models\CashCategory::where('jurusan_id', $activeJurusanId)->get();
        if ($categories->isEmpty()) {
            $defaultCategories = [
                'Modal Awal',
                'Penjualan Toko / POS',
                'Pembelian Stok Barang',
                'Biaya Operasional',
                'Gaji & Insentif Kasir',
                'Bagi Hasil Mingguan',
                'Lain-lain / Dana Darurat'
            ];
            foreach ($defaultCategories as $catName) {
                \App\Models\CashCategory::create([
                    'jurusan_id' => $activeJurusanId,
                    'name' => $catName
                ]);
            }
            $categories = \App\Models\CashCategory::where('jurusan_id', $activeJurusanId)->get();
        }
        $categoryStats = [];

        foreach ($categories as $category) {
            $catSum = (object) ($categorySums->get($category->id) ?? []);

            $catIncome = $catSum->cat_income ?? 0;
            $catExpense = $catSum->cat_expense ?? 0;
            $modalBalance = ($catSum->modal_income ?? 0) - ($catSum->modal_expense ?? 0);
            $profitBalance = ($catSum->profit_income ?? 0) - ($catSum->profit_expense ?? 0);

            $prodCatName = str_replace('Penjualan ', '', $category->name);
            $bagiHasilDeduction = 0;
            if ($category->name !== 'Bagi Hasil Mingguan' && !empty($bagiHasilTransactions)) {
                foreach ($bagiHasilTransactions as $tx) {
                    if (str_contains(strtolower($tx['description']), 'kategori:')) {
                        if ($category->name === 'Jurusan Snack & Minuman') {
                            if (str_contains(strtolower($tx['description']), 'makanan') || str_contains(strtolower($tx['description']), 'minuman') || str_contains(strtolower($tx['description']), 'snack')) {
                                $bagiHasilDeduction += $tx['amount'];
                            }
                        } else {
                            if (str_contains(strtolower($tx['description']), strtolower($prodCatName))) {
                                $bagiHasilDeduction += $tx['amount'];
                            }
                        }
                    }
                }
            }

            if ($bagiHasilDeduction > 0) {
                $catExpense += $bagiHasilDeduction;
                $profitBalance -= $bagiHasilDeduction;
            }

            if ($category->name === 'Bagi Hasil Mingguan') {
                $profitBalance = 0;
            }

            $categoryStats[] = [
                'id' => $category->id,
                'name' => $category->name,
                'income' => $catIncome,
                'expense' => $catExpense,
                'balance' => $catIncome - $catExpense,
                'modal_balance' => $modalBalance,
                'profit_balance' => $profitBalance,
            ];
        }

        $transactions = (clone $query)
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('livewire.finance.cash-management', [
            'transactions' => $transactions,
            'currentModalBalance' => $currentModalBalance,
            'currentProfitBalance' => $currentProfitBalance,
            'monthlyIncome' => $monthlyIncome,
            'monthlyExpense' => $monthlyExpense,
            'categories' => $categories,
            'categoryStats' => collect($categoryStats),
            'isSubUnit' => $isSubUnit,
        ])->layout('layouts.app', ['title' => 'Buku Kas Internal']);
    }
}
