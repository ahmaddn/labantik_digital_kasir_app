<?php

namespace App\Livewire\Reports;

use App\Exports\DailyDataExport;
use App\Imports\DailyDataImport;
use App\Models\CashCategory;
use App\Models\CashTransaction;
use App\Models\DailyRecap as DailyRecapModel;
use App\Models\ProductCategory;
use App\Models\Transaction;
use Carbon\Carbon;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class DailyRecap extends Component
{
    use WithFileUploads, WithPagination;

    #[Url]
    public string $selectedDate = '';

    public string $search = '';

    public string $filterStatus = '';

    public string $filterCategory = '';

    public $importFile;

    public bool $reopenSession = true;

    public bool $isPosted = false;

    // Cash Audit
    public $actualCash = 0;

    public $retainedChangeCash = 0;

    public $cashNote = '';

    // Details Modal
    public bool $showDetailsModal = false;

    public $detailReference = null;

    public function mount($date = null): void
    {
        $this->selectedDate = $date ?? today()->toDateString();
        $this->loadCashAudit();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedDate(): void
    {
        $this->loadCashAudit();
        $this->resetPage();
    }

    protected function loadCashAudit(): void
    {
        $activeJurusanId = session('active_jurusan_id');
        $recap = DailyRecapModel::where('date', $this->selectedDate)
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->first();
        if ($recap) {
            $this->actualCash = $recap->actual_cash;
            $this->retainedChangeCash = $recap->retained_change_cash ?? 0;
            $this->cashNote = $recap->cash_note ?? '';
        } else {
            $this->actualCash = 0;
            $this->retainedChangeCash = 0;
            $this->cashNote = '';
        }

        $this->isPosted = CashTransaction::where('date', $this->selectedDate)
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->where(function ($q) {
                $q->where('description', 'like', '%Penjualan Harian (Sistem)%')
                    ->orWhere('description', 'like', '%Bagi Hasil Supplier (Sistem)%');
            })
            ->exists();
    }

    public function saveCashAudit(): void
    {
        $activeJurusanId = session('active_jurusan_id');
        DailyRecapModel::updateOrCreate(
            [
                'date' => $this->selectedDate,
                'jurusan_id' => $activeJurusanId,
            ],
            [
                'actual_cash' => $this->actualCash,
                'retained_change_cash' => $this->retainedChangeCash,
                'cash_note' => $this->cashNote,
            ]
        );

        $this->dispatch('toast', message: 'Audit uang kas berhasil disimpan.');
    }

    public function postToCashBook(): void
    {
        $activeJurusanId = session('active_jurusan_id');
        $recap = DailyRecapModel::where('date', $this->selectedDate)
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->first();

        if (! $recap || $recap->actual_cash <= 1) {
            $this->dispatch('toast', message: 'Lakukan audit uang kas fisik terlebih dahulu sebelum melakukan posting!', type: 'error');

            return;
        }

        // Ambil data transaksi hari ini
        $allTransactions = Transaction::with(['product.category'])
            ->whereDate('transacted_at', $this->selectedDate)
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->get();

        if ($allTransactions->isEmpty()) {
            $this->dispatch('toast', message: 'Tidak ada transaksi pada tanggal ini.', type: 'error');

            return;
        }

        $totalRevenueReal = $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum('total_price');

        // Hak Supplier / Bagi Hasil (Modal dari barang titipan supplier)
        $totalSupplierHak = $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])
            ->whereNotNull('supplier_id')
            ->sum(fn ($tx) => ($tx->unit_price - $tx->unit_profit) * $tx->quantity);

        // Hitung Selisih
        $diff = ((float) $recap->actual_cash - (float) ($recap->retained_change_cash ?? 0)) - (float) $totalRevenueReal;

        // Kelompokkan transaksi berdasarkan Kategori Produk
        $grouped = $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->groupBy('product.category_id');

        \DB::transaction(function () use ($grouped, $totalSupplierHak, $diff, $activeJurusanId) {
            foreach ($grouped as $categoryId => $txs) {
                $firstTx = $txs->first();
                $categoryName = $firstTx->product->category->name ?? 'Lainnya';
                $categoryNameClean = trim($categoryName);

                // Dapatkan atau buat Kategori Kas per Kategori Produk
                $catPenjualan = CashCategory::firstOrCreate(
                    ['name' => 'Penjualan '.$categoryNameClean, 'jurusan_id' => $activeJurusanId]
                );

                // Hitung modal internal untuk kategori ini (bukan barang supplier)
                $catModalInternal = $txs->whereNull('supplier_id')
                    ->sum(fn ($tx) => ($tx->unit_price - $tx->unit_profit) * $tx->quantity);

                // Hitung keuntungan bersih untuk kategori ini (semua untung internal + komisi titipan)
                $catProfit = $txs->sum(fn ($tx) => $tx->unit_profit * $tx->quantity);

                // 1. Post Modal Terpakai Kategori
                if ($catModalInternal > 0) {
                    CashTransaction::updateOrCreate(
                        [
                            'date' => $this->selectedDate,
                            'jurusan_id' => $activeJurusanId,
                            'description' => 'Modal Penjualan '.$categoryNameClean.' (Sistem)',
                        ],
                        [
                            'cash_type' => 'modal',
                            'cash_category_id' => $catPenjualan->id,
                            'type' => 'income',
                            'amount' => $catModalInternal,
                        ]
                    );
                }

                // 2. Post Keuntungan Kategori
                if ($catProfit > 0) {
                    CashTransaction::updateOrCreate(
                        [
                            'date' => $this->selectedDate,
                            'jurusan_id' => $activeJurusanId,
                            'description' => 'Keuntungan Penjualan '.$categoryNameClean.' (Sistem)',
                        ],
                        [
                            'cash_type' => 'keuntungan',
                            'cash_category_id' => $catPenjualan->id,
                            'type' => 'income',
                            'amount' => $catProfit,
                        ]
                    );
                }
            }

            // 3. Post Hak Supplier / Bagi Hasil Supplier
            if ($totalSupplierHak > 0) {
                $catSupplier = CashCategory::firstOrCreate(
                    ['name' => 'Bagi Hasil Supplier', 'jurusan_id' => $activeJurusanId]
                );

                CashTransaction::updateOrCreate(
                    [
                        'date' => $this->selectedDate,
                        'jurusan_id' => $activeJurusanId,
                        'description' => 'Bagi Hasil Supplier (Sistem)',
                    ],
                    [
                        'cash_type' => 'modal',
                        'cash_category_id' => $catSupplier->id,
                        'type' => 'income',
                        'amount' => $totalSupplierHak,
                    ]
                );
            }

            // 4. Post Selisih sebagai Adjustment (di Kategori Penjualan Umum)
            if ($diff !== 0) {
                $catPenjualanUmum = CashCategory::firstOrCreate(
                    ['name' => 'Penjualan Umum', 'jurusan_id' => $activeJurusanId]
                );

                $type = $diff < 0 ? 'expense' : 'income';
                $description = $diff < 0 ? 'Penyesuaian Selisih Kurang Uang Kas' : 'Penyesuaian Selisih Lebih Uang Kas';

                CashTransaction::updateOrCreate(
                    [
                        'date' => $this->selectedDate,
                        'jurusan_id' => $activeJurusanId,
                        'description' => $description,
                    ],
                    [
                        'cash_type' => 'keuntungan',
                        'cash_category_id' => $catPenjualanUmum->id,
                        'type' => $type,
                        'amount' => abs($diff),
                    ]
                );
            }

            // 5. Uang Cadangan Kembalian tidak diposting sebagai pengeluaran karena uangnya masih ada di laci kasir (bukan pengeluaran riil) dan akan masuk kembali ke kas.
        });

        $this->isPosted = true;
        $this->dispatch('toast', message: 'Data kas harian per kategori (termasuk bagi hasil supplier) berhasil diposting ke Buku Kas!');
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatedFilterCategory(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $activeJurusanId = session('active_jurusan_id');

        $allTransactions = Transaction::with(['product.category'])
            ->whereDate('transacted_at', $this->selectedDate)
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->get();

        $query = Transaction::query()
            ->whereDate('transacted_at', $this->selectedDate)
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            });

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('reference', 'like', '%'.$this->search.'%')
                    ->orWhere('buyer_name', 'like', '%'.$this->search.'%')
                    ->orWhereHas('product', function ($pq) {
                        $pq->where('name', 'like', '%'.$this->search.'%');
                    });
            });
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterCategory) {
            $query->whereHas('product', function ($q) {
                $q->where('category_id', $this->filterCategory);
            });
        }

        $transactions = $query->selectRaw('reference, buyer_name, status, transacted_at, SUM(total_price) as total_amount, SUM(quantity) as total_qty, COUNT(*) as unique_items')
            ->groupBy('reference', 'buyer_name', 'status', 'transacted_at')
            ->orderByDesc('transacted_at')
            ->paginate(15);

        if ($allTransactions->isEmpty()) {
            return view('livewire.reports.daily-recap', [
                'recap' => null,
                'categoryRecap' => collect(),
                'transactions' => $transactions,
                'categories' => ProductCategory::orderBy('name')->get(),
            ])->layout('layouts.app', ['title' => 'Rekap Harian']);
        }

        $totalRevenueAll = $allTransactions->sum('total_price');
        $totalRevenueReal = $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum('total_price');

        $totalSupplierHak = $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])
            ->whereNotNull('supplier_id')
            ->sum(fn ($tx) => ($tx->unit_price - $tx->unit_profit) * $tx->quantity);

        $totalInternalRevenue = $totalRevenueReal - $totalSupplierHak;

        $totalProfit = $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum(fn ($tx) => $tx->unit_profit * $tx->quantity);
        $totalModal = $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum(fn ($tx) => ($tx->unit_price - $tx->unit_profit) * $tx->quantity);

        $recap = (object) [
            'total_revenue_all' => $totalRevenueAll,
            'total_revenue_real' => $totalRevenueReal,
            'total_internal_revenue' => $totalInternalRevenue,
            'total_profit' => $totalProfit,
            'total_modal' => $totalModal,
            'count_received' => $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->count(),
            'count_unpaid_change' => $allTransactions->where('status', 'belum_kembalian')->count(),
            'count_no_payment' => $allTransactions->whereIn('status', ['belum_menerima_uang', 'uang_dipinjam'])->count(),
            'month_name' => Carbon::parse($this->selectedDate)->translatedFormat('F Y'),
            'month_week' => Carbon::parse($this->selectedDate)->weekOfMonth,
            'generated_at' => now(),
        ];

        $categoryRecap = $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->groupBy(fn ($tx) => $tx->product->category->name ?? 'Tanpa Kategori')
            ->map(function ($group) {
                return (object) [
                    'revenue' => $group->sum('total_price'),
                    'profit' => $group->sum(fn ($tx) => $tx->unit_profit * $tx->quantity),
                    'modal' => $group->sum(fn ($tx) => ($tx->unit_price - $tx->unit_profit) * $tx->quantity),
                    'qty' => $group->sum('quantity'),
                ];
            })->sortByDesc('revenue');

        return view('livewire.reports.daily-recap', [
            'recap' => $recap,
            'categoryRecap' => $categoryRecap,
            'transactions' => $transactions,
            'categories' => ProductCategory::orderBy('name')->get(),
        ])->layout('layouts.app', ['title' => 'Rekap Harian']);

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

        return Transaction::with('product')->where('reference', $this->detailReference)->get();
    }

    public function exportCSV()
    {
        $this->dispatch('toast', message: 'Fitur ekspor CSV sedang dalam pengembangan.', type: 'info');
    }

    public function exportExcel()
    {
        $fileName = 'Rekap_Harian_'.$this->selectedDate.'.xlsx';

        return Excel::download(new DailyDataExport($this->selectedDate), $fileName);
    }

    public function importExcel()
    {
        $this->validate([
            'importFile' => 'required|mimes:xlsx,xls',
        ]);

        try {
            Excel::import(new DailyDataImport, $this->importFile);

            // Reopen the cashier session for the imported date by resetting actual_cash
            if ($this->reopenSession) {
                $recap = DailyRecapModel::where('date', $this->selectedDate)->first();
                if ($recap) {
                    $recap->update(['actual_cash' => 0, 'cash_note' => 'Sesi dibuka kembali setelah proses import data dari device lain.']);
                }
            }

            session()->flash('toast', $this->reopenSession
                ? 'Data berhasil diimpor dan Sesi Kasir telah dibuka kembali!'
                : 'Data berhasil diimpor!');

            return $this->redirect(route('daily-recap', ['date' => $this->selectedDate]), navigate: true);
        } catch (\Exception $e) {
            $this->dispatch('toast', message: 'Gagal mengimpor data: '.$e->getMessage(), type: 'error');
        }
    }
}
