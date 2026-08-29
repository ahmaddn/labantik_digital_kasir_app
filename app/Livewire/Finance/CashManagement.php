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
    public string $filterType = 'weekly';
    public string $filterWeek = 'this_week';
    public string $activeTab = 'cumulative';

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
    public bool $showDeleteCategoryConfirmation = false;
    public $confirmingDeleteCategoryId = null;
    public string $confirmingDeleteCategoryName = '';
    public int $confirmingDeleteCategoryTxCount = 0;

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
    public array $chartData = [];

    public function mount()
    {
        $this->filterMonth = now()->format('Y-m');
        $this->date = now()->format('Y-m-d');

        // Automatically rename existing "Penjualan Umum" to "Keuntungan Jurusan"
        $activeJurusanId = session('active_jurusan_id');
        $existingPenjualanUmum = \App\Models\CashCategory::where('jurusan_id', $activeJurusanId)
            ->where('name', 'Penjualan Umum')
            ->first();
        if ($existingPenjualanUmum) {
            $keuntunganJurusan = \App\Models\CashCategory::where('jurusan_id', $activeJurusanId)
                ->where('name', 'Keuntungan Jurusan')
                ->first();
            if ($keuntunganJurusan) {
                // Merge transactions to existing Keuntungan Jurusan, then delete Penjualan Umum
                \App\Models\CashTransaction::where('jurusan_id', $activeJurusanId)
                    ->where('cash_category_id', $existingPenjualanUmum->id)
                    ->update(['cash_category_id' => $keuntunganJurusan->id]);
                $existingPenjualanUmum->delete();
            } else {
                // Simply rename
                $existingPenjualanUmum->update(['name' => 'Keuntungan Jurusan']);
            }
        }

        // Clean up and rename categories dynamically for Doku
        $activeJurusan = \App\Models\Jurusan::find($activeJurusanId);
        $activeJurusanNameLower = $activeJurusan ? strtolower($activeJurusan->name) : '';
        if ($activeJurusan && str_contains($activeJurusanNameLower, 'doku')) {
            // Rename "Jurusan Snack & Minuman" to "Kas Doku"
            $oldCat = \App\Models\CashCategory::where('jurusan_id', $activeJurusanId)
                ->where('name', 'Jurusan Snack & Minuman')
                ->first();
            if ($oldCat) {
                $newCat = \App\Models\CashCategory::where('jurusan_id', $activeJurusanId)
                    ->where('name', 'Kas Doku')
                    ->first();
                if ($newCat) {
                    \App\Models\CashTransaction::where('jurusan_id', $activeJurusanId)
                        ->where('cash_category_id', $oldCat->id)
                        ->update(['cash_category_id' => $newCat->id]);
                    $oldCat->delete();
                } else {
                    $oldCat->update(['name' => 'Kas Doku']);
                }
            }

            // Remove "Penjualan Cinuy" and "Penjualan Eskrim" categories and their transactions from Doku
            $buggyCats = \App\Models\CashCategory::where('jurusan_id', $activeJurusanId)
                ->whereIn('name', ['Penjualan Cinuy', 'Penjualan Eskrim'])
                ->get();
            foreach ($buggyCats as $buggyCat) {
                \App\Models\CashTransaction::where('jurusan_id', $activeJurusanId)
                    ->where('cash_category_id', $buggyCat->id)
                    ->delete();
                $buggyCat->delete();
            }
        }
    }

    public function updatedFilterMonth()
    {
        $this->resetPage();
    }

    public function updatedFilterType()
    {
        $this->resetPage();
    }

    public function updatedFilterWeek()
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

    public function confirmDeleteCategory($id, $name)
    {
        $this->confirmingDeleteCategoryId = $id;
        $this->confirmingDeleteCategoryName = $name;
        $this->confirmingDeleteCategoryTxCount = CashTransaction::where('cash_category_id', $id)->count();
        $this->showDeleteCategoryConfirmation = true;
    }

    public function deleteCategory()
    {
        if ($this->confirmingDeleteCategoryId) {
            // Delete all transactions associated with this category
            CashTransaction::where('cash_category_id', $this->confirmingDeleteCategoryId)->delete();

            $category = \App\Models\CashCategory::find($this->confirmingDeleteCategoryId);
            if ($category) {
                $category->delete();
                $this->dispatch('toast', message: 'Kategori dan seluruh transaksi di dalamnya berhasil dihapus.');
            }
            $this->showDeleteCategoryConfirmation = false;
            $this->confirmingDeleteCategoryId = null;
            $this->confirmingDeleteCategoryName = '';
            $this->confirmingDeleteCategoryTxCount = 0;

            // Invalidate cache
            $activeJurusanId = session('active_jurusan_id');
            \Illuminate\Support\Facades\Cache::forget('cash_balances_' . ($activeJurusanId ?: 'global'));
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

        $catKeuntunganJurusan = \App\Models\CashCategory::where('jurusan_id', $activeJurusanId)
            ->where('name', 'Keuntungan Jurusan')
            ->first();
        $keuntunganJurusanId = $catKeuntunganJurusan ? $catKeuntunganJurusan->id : 0;

        // Calculate current balance of sub-unit
        $balances = CashTransaction::where('jurusan_id', $activeJurusanId)
            ->selectRaw("
                SUM(CASE WHEN cash_type = 'modal' AND type = 'income' THEN amount ELSE 0 END) as modal_income,
                SUM(CASE WHEN cash_type = 'modal' AND type = 'expense' THEN amount ELSE 0 END) as modal_expense,
                SUM(CASE WHEN cash_type = 'keuntungan' AND cash_category_id = ? AND type = 'income' THEN amount ELSE 0 END) as profit_income,
                SUM(CASE WHEN cash_type = 'keuntungan' AND cash_category_id = ? AND type = 'expense' THEN amount ELSE 0 END) as profit_expense
            ", [$keuntunganJurusanId, $keuntunganJurusanId])
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
        $filename = 'Laporan_Buku_Kas_Internal_Kumulatif.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\CashTransactionsExport(null), $filename);
    }

    public function render()
    {
        $activeJurusanId = session('active_jurusan_id');
        $jurusan = \App\Models\Jurusan::find($activeJurusanId);
        $isSubUnit = $jurusan && $jurusan->parent_id;

        // Get Keuntungan Jurusan Category ID
        $catKeuntunganJurusan = \App\Models\CashCategory::where('jurusan_id', $activeJurusanId)
            ->where('name', 'Keuntungan Jurusan')
            ->first();
        $keuntunganJurusanId = $catKeuntunganJurusan ? $catKeuntunganJurusan->id : 0;

        // Overall cumulative query (all transactions)
        $cumulativeQuery = CashTransaction::where('jurusan_id', $activeJurusanId);

        // Calculate overall cumulative balances (for context)
        $cumulativeBalances = (clone $cumulativeQuery)
            ->selectRaw("
                SUM(CASE WHEN cash_type = 'modal' AND type = 'income' THEN amount ELSE 0 END) as modal_income,
                SUM(CASE WHEN cash_type = 'modal' AND type = 'expense' THEN amount ELSE 0 END) as modal_expense,
                SUM(CASE WHEN cash_type = 'keuntungan' AND type = 'income' THEN amount ELSE 0 END) as profit_income,
                SUM(CASE WHEN cash_type = 'keuntungan' AND type = 'expense' THEN amount ELSE 0 END) as profit_expense
            ")
            ->first();
        $cumulativeModalBalance = ($cumulativeBalances->modal_income ?? 0) - ($cumulativeBalances->modal_expense ?? 0);
        $cumulativeProfitBalance = ($cumulativeBalances->profit_income ?? 0) - ($cumulativeBalances->profit_expense ?? 0);

        // Define Start and End Dates based on period filters
        $startDate = null;
        $endDate = null;

        if ($this->filterType === 'weekly') {
            if ($this->filterWeek === 'this_week') {
                $startDate = now()->startOfWeek(Carbon::MONDAY)->format('Y-m-d');
                $endDate = now()->endOfWeek(Carbon::SUNDAY)->format('Y-m-d');
            } elseif ($this->filterWeek === 'last_week') {
                $startDate = now()->subWeek()->startOfWeek(Carbon::MONDAY)->format('Y-m-d');
                $endDate = now()->subWeek()->endOfWeek(Carbon::SUNDAY)->format('Y-m-d');
            } else {
                $monthDate = Carbon::createFromFormat('Y-m', $this->filterMonth);
                $weekNumber = (int) str_replace('week_', '', $this->filterWeek);
                
                if ($weekNumber === 1) {
                    $startDate = $monthDate->copy()->startOfMonth()->format('Y-m-d');
                    $endDate = $monthDate->copy()->startOfMonth()->addDays(6)->format('Y-m-d');
                } elseif ($weekNumber === 2) {
                    $startDate = $monthDate->copy()->startOfMonth()->addDays(7)->format('Y-m-d');
                    $endDate = $monthDate->copy()->startOfMonth()->addDays(13)->format('Y-m-d');
                } elseif ($weekNumber === 3) {
                    $startDate = $monthDate->copy()->startOfMonth()->addDays(14)->format('Y-m-d');
                    $endDate = $monthDate->copy()->startOfMonth()->addDays(20)->format('Y-m-d');
                } elseif ($weekNumber === 4) {
                    $startDate = $monthDate->copy()->startOfMonth()->addDays(21)->format('Y-m-d');
                    $endDate = $monthDate->copy()->startOfMonth()->addDays(27)->format('Y-m-d');
                } else {
                    $startDate = $monthDate->copy()->startOfMonth()->addDays(28)->format('Y-m-d');
                    $endDate = $monthDate->copy()->endOfMonth()->format('Y-m-d');
                }
            }
        } elseif ($this->filterType === 'monthly') {
            $startDate = Carbon::createFromFormat('Y-m', $this->filterMonth)->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::createFromFormat('Y-m', $this->filterMonth)->endOfMonth()->format('Y-m-d');
        }

        // Calculate Starting Balances (Saldo Awal)
        $startingModal = 0;
        $startingProfit = 0;
        $startingBalance = 0;

        if ($startDate) {
            $startingSums = CashTransaction::where('jurusan_id', $activeJurusanId)
                ->where('date', '<', $startDate)
                ->selectRaw("
                    SUM(CASE WHEN type = 'income' AND cash_type = 'modal' THEN amount ELSE 0 END) - SUM(CASE WHEN type = 'expense' AND cash_type = 'modal' THEN amount ELSE 0 END) as modal_bal,
                    SUM(CASE WHEN type = 'income' AND cash_type = 'keuntungan' THEN amount ELSE 0 END) - SUM(CASE WHEN type = 'expense' AND cash_type = 'keuntungan' THEN amount ELSE 0 END) as profit_bal
                ")
                ->first();
            $startingModal = (float)($startingSums->modal_bal ?? 0);
            $startingProfit = (float)($startingSums->profit_bal ?? 0);
            $startingBalance = $startingModal + $startingProfit;
        }

        // Set Active Query based on range
        $activeQuery = CashTransaction::where('jurusan_id', $activeJurusanId);
        if ($startDate && $endDate) {
            $activeQuery = $activeQuery->whereBetween('date', [$startDate, $endDate]);
        }

        // Fetch category sums for active query
        $categorySums = (clone $activeQuery)
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

        // Fetch stats for active query
        $periodBalances = (clone $activeQuery)
            ->selectRaw("
                SUM(CASE WHEN cash_type = 'modal' AND type = 'income' THEN amount ELSE 0 END) as modal_income,
                SUM(CASE WHEN cash_type = 'modal' AND type = 'expense' THEN amount ELSE 0 END) as modal_expense,
                SUM(CASE WHEN cash_type = 'keuntungan' AND type = 'income' THEN amount ELSE 0 END) as profit_income,
                SUM(CASE WHEN cash_type = 'keuntungan' AND type = 'expense' THEN amount ELSE 0 END) as profit_expense
            ")
            ->first();

        $periodModalIn = (float)($periodBalances->modal_income ?? 0);
        $periodModalOut = (float)($periodBalances->modal_expense ?? 0);
        $periodProfitIn = (float)($periodBalances->profit_income ?? 0);
        $periodProfitOut = (float)($periodBalances->profit_expense ?? 0);

        $displayIncome = $periodModalIn + $periodProfitIn;
        $displayExpense = $periodModalOut + $periodProfitOut;

        if ($startDate && $endDate) {
            $currentModalBalance = $startingModal + ($periodModalIn - $periodModalOut);
            $currentProfitBalance = $startingProfit + ($periodProfitIn - $periodProfitOut);
        } else {
            $currentModalBalance = $cumulativeModalBalance;
            $currentProfitBalance = $cumulativeProfitBalance;
        }

        // Calculate Audit Adjustments (deficits/surpluses from physical cash checks)
        $adjustments = (clone $activeQuery)
            ->where('description', 'like', 'Penyesuaian Selisih%')
            ->get();
        $totalDeficit = 0;
        $totalSurplus = 0;
        foreach ($adjustments as $adj) {
            if ($adj->type === 'expense') {
                $totalDeficit += $adj->amount;
            } else {
                $totalSurplus += $adj->amount;
            }
        }

        // Build Chart.js data
        $chartData = [
            'inflowLabels' => [],
            'inflowValues' => [],
            'outflowLabels' => [],
            'outflowValues' => [],
        ];
        
        $categoriesMap = \App\Models\CashCategory::where('jurusan_id', $activeJurusanId)->get()->keyBy('id');
        foreach ($categorySums as $catId => $sum) {
            $catName = isset($categoriesMap[$catId]) ? $categoriesMap[$catId]->name : 'Tanpa Kategori';
            if ($sum->cat_income > 0) {
                $chartData['inflowLabels'][] = $catName;
                $chartData['inflowValues'][] = (float)$sum->cat_income;
            }
            if ($sum->cat_expense > 0) {
                $chartData['outflowLabels'][] = $catName;
                $chartData['outflowValues'][] = (float)$sum->cat_expense;
            }
        }

        $this->chartData = $chartData;

        $catBagiHasil = \App\Models\CashCategory::where('jurusan_id', $activeJurusanId)->where('name', 'Bagi Hasil Mingguan')->first();
        $bagiHasilTransactions = [];
        if ($catBagiHasil) {
            $bagiHasilTransactions = (clone $activeQuery)
                ->where('cash_category_id', $catBagiHasil->id)
                ->get()
                ->toArray();
        }

        $categories = \App\Models\CashCategory::where('jurusan_id', $activeJurusanId)
            ->whereNotIn('name', [
                'Bagi Hasil Mingguan', 
                'Bagi Hasil Supplier', 
                'Bagi Hasil Pengelola', 
                'Bagi Hasil Labantik'
            ])
            ->get();

        if (\App\Models\CashCategory::where('jurusan_id', $activeJurusanId)->count() === 0) {
            $defaultCategories = [
                'Modal Awal',
                'Penjualan Toko / POS',
                'Pembelian Stok Barang',
                'Biaya Operasional',
                'Gaji & Insentif Kasir',
                'Lain-lain / Dana Darurat'
            ];
            foreach ($defaultCategories as $catName) {
                \App\Models\CashCategory::create([
                    'jurusan_id' => $activeJurusanId,
                    'name' => $catName
                ]);
            }
            $categories = \App\Models\CashCategory::where('jurusan_id', $activeJurusanId)
                ->whereNotIn('name', [
                    'Bagi Hasil Mingguan', 
                    'Bagi Hasil Supplier', 
                    'Bagi Hasil Pengelola', 
                    'Bagi Hasil Labantik'
                ])
                ->get();
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

        $transactions = (clone $activeQuery)
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('livewire.finance.cash-management', [
            'transactions' => $transactions,
            'currentModalBalance' => $currentModalBalance,
            'currentProfitBalance' => $currentProfitBalance,
            'displayIncome' => $displayIncome,
            'displayExpense' => $displayExpense,
            'categories' => $categories,
            'categoryStats' => collect($categoryStats),
            'isSubUnit' => $isSubUnit,
            'startingBalance' => $startingBalance,
            'chartData' => $chartData,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalDeficit' => $totalDeficit,
            'totalSurplus' => $totalSurplus,
            'adjustments' => $adjustments,
        ])->layout('layouts.app', ['title' => 'Buku Kas Internal']);
    }
}
