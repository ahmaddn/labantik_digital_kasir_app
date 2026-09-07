<?php

namespace App\Livewire\Reports;

use App\Models\CashCategory;
use App\Models\CashTransaction;
use App\Models\Transaction;
use App\Models\WeeklyProfitShare;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class WeeklyProfit extends Component
{
    use WithPagination;

    public $startDate;

    public $endDate;

    public $viewMode = 'weekly';

    public $showDeleteModal = false;

    public $reportToDeleteId = null;

    public $selectedMonth = null;

    public $currentYear;

    // Mode pengecualian — aktifkan saat minggu tidak penuh (libur, ujian, dll)
    public bool $overrideCanProcess = false;

    // Detail breakdown bagi hasil per laporan
    public ?string $expandedReportId = null;
    public array $reportBreakdown = [];

    public function mount()
    {
        $activeRole = session('active_role_name');
        if ($activeRole === 'kasir') {
            $isScheduled = \App\Models\CashierSchedule::where('user_id', auth()->id())->exists();
            if ($isScheduled) {
                abort(403, 'Akses ditolak. Kasir terjadwal tidak diperkenankan mengakses menu Bagi Hasil Mingguan.');
            }
        }

        $this->startDate = now()->startOfWeek(Carbon::MONDAY)->toDateString();
        $this->endDate = now()->startOfWeek(Carbon::MONDAY)->addDays(4)->toDateString();
        $this->currentYear = now()->year;
        $this->selectedMonth = now()->month;

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
    }

    public function confirmDelete($id)
    {
        $this->reportToDeleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        if ($this->reportToDeleteId) {
            $report = WeeklyProfitShare::find($this->reportToDeleteId);
            if ($report) {
                $activeJurusanId = session('active_jurusan_id');
                $weekStart = Carbon::parse($report->week_start);
                $weekEnd = Carbon::parse($report->week_end);

                // Delete all cash transactions matching this period
                $descriptionPattern = 'Bagi Hasil Mingguan%Periode ' . $weekStart->format('d/m/Y') . ' s.d ' . $weekEnd->format('d/m/Y') . '%';
                CashTransaction::where('jurusan_id', $activeJurusanId)
                    ->where('description', 'like', $descriptionPattern)
                    ->delete();
                \App\Models\VirtualCashTransaction::where('jurusan_id', $activeJurusanId)
                    ->where('description', 'like', $descriptionPattern)
                    ->delete();

                $report->delete();
            }
            $this->reportToDeleteId = null;
            $this->showDeleteModal = false;
            $this->dispatch('toast', message: 'Laporan berhasil dihapus.');
        }
    }

    public function generateReport()
    {
        $activeJurusanId = session('active_jurusan_id');

        $weekStart = Carbon::parse($this->startDate);
        $weekEnd = Carbon::parse($this->endDate);

        $weekNumber = $weekEnd->weekOfMonth;
        $monthName = $weekEnd->translatedFormat('F Y');

        // Calculate total profit (Including shop's share from supplier products)
        $systemProfit = Transaction::whereBetween('transacted_at', [
            $weekStart->startOfDay()->toDateTimeString(),
            $weekEnd->endOfDay()->toDateTimeString(),
        ])
            ->where('jurusan_id', $activeJurusanId)
            ->whereIn('status', ['uang_diterima', 'belum_kembalian'])
            ->sum(DB::raw('unit_profit * quantity'));

        // Calculate total shortage from daily recaps
        $dailyRecaps = \App\Models\DailyRecap::whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->where('jurusan_id', $activeJurusanId)
            ->get();

        $totalShortage = 0;
        $totalSurplus = 0;
        foreach ($dailyRecaps as $recap) {
            if ((float) $recap->actual_cash <= 1) {
                continue;
            }

            $previousRecap = \App\Models\DailyRecap::forReporting()
                ->where('jurusan_id', $activeJurusanId)
                ->where('date', '<', $recap->date)
                ->orderBy('date', 'desc')
                ->first();
            $startingChangeCash = $previousRecap ? ($previousRecap->retained_change_cash ?? 0) : 0;

            $totalRevenueReal = Transaction::whereDate('transacted_at', $recap->date->toDateString())
                ->where('jurusan_id', $activeJurusanId)
                ->whereIn('status', ['uang_diterima', 'belum_kembalian'])
                ->sum('total_price');
            $diff = ((float) $recap->actual_cash - (float) $startingChangeCash) - $totalRevenueReal;
            if ($diff < 0) {
                $totalShortage += abs($diff);
            } else {
                $totalSurplus += $diff;
            }
        }

        $totalProfit = $systemProfit - $totalShortage;

        if ($totalProfit <= 0) {
            $this->dispatch('toast', message: 'Tidak ada keuntungan pada periode ini.', type: 'error');

            return;
        }

        $data = [
            'jurusan_id' => $activeJurusanId,
            'month_name' => $monthName,
            'week_number' => $weekNumber,
            'week_start' => $weekStart->toDateString(),
            'week_end' => $weekEnd->toDateString(),
            'total_profit' => $totalProfit,
            'kas_amount' => $totalProfit * 0.40, // 40%
            'shared_amount' => $totalProfit * 0.60, // 60% (Najmy 30% + Labantik 30%)
            'created_by' => auth()->id(),
        ];

        WeeklyProfitShare::updateOrCreate(
            [
                'week_start' => $weekStart->toDateString(),
                'week_end' => $weekEnd->toDateString(),
                'jurusan_id' => $activeJurusanId,
            ],
            $data
        );

        // Delete old postings for this period first to prevent duplicates on regeneration
        $descriptionPattern = 'Bagi Hasil Mingguan%Periode ' . $weekStart->format('d/m/Y') . ' s.d ' . $weekEnd->format('d/m/Y') . '%';
        CashTransaction::where('jurusan_id', $activeJurusanId)
            ->where('description', 'like', $descriptionPattern)
            ->delete();
        \App\Models\VirtualCashTransaction::where('jurusan_id', $activeJurusanId)
            ->where('description', 'like', $descriptionPattern)
            ->delete();

        // 1. Fetch transactions for category grouping
        $transactions = Transaction::with(['product.category', 'product.supplier'])
            ->whereBetween('transacted_at', [
                $weekStart->startOfDay()->toDateTimeString(),
                $weekEnd->endOfDay()->toDateTimeString(),
            ])
            ->where('jurusan_id', $activeJurusanId)
            ->whereIn('status', ['uang_diterima', 'belum_kembalian'])
            ->get();

        $grouped = $transactions->groupBy(fn($tx) => ($tx->product->supplier_id ?? $tx->supplier_id) ? 'supplier_' . ($tx->product->supplier_id ?? $tx->supplier_id) : 'category_' . ($tx->product->category_id ?? 'other'));

        $najmyUser = \App\Models\User::where('name', 'like', '%Najmy%')->first();
        $najmyName = $najmyUser ? $najmyUser->name : 'Najmy';

        $scaleFactor = ($systemProfit > 0) ? ($totalProfit / $systemProfit) : 1;

        foreach ($grouped as $key => $txs) {
            $firstTx = $txs->first();
            if (str_starts_with($key, 'supplier_')) {
                $supplierName = $firstTx->product->supplier->name ?? 'Supplier';
                $categoryNameClean = trim($supplierName);
                $cashCategoryName = 'Penjualan ' . $categoryNameClean;
            } else {
                $categoryName = $firstTx->product->category->name ?? 'Lainnya';
                $categoryNameClean = trim($categoryName);

                $categoryNameLower = strtolower($categoryNameClean);
                if ($categoryNameLower === 'makanan' || $categoryNameLower === 'minuman' || $categoryNameLower === 'makanan & minuman' || $categoryNameLower === 'makanan dan minuman' || $categoryNameLower === 'snack') {
                    $activeJurusan = \App\Models\Jurusan::find($activeJurusanId);
                    $activeJurusanNameLower = $activeJurusan ? strtolower($activeJurusan->name) : '';
                    $cashCategoryName = str_contains($activeJurusanNameLower, 'doku') ? 'Kas Doku' : 'Jurusan Snack & Minuman';
                } elseif ($categoryNameLower === 'umum' || $categoryNameLower === 'lainnya' || $categoryNameLower === 'lain-lain') {
                    $cashCategoryName = 'Keuntungan Jurusan';
                } else {
                    $cashCategoryName = 'Penjualan ' . $categoryNameClean;
                }
            }

            $catPenjualan = CashCategory::firstOrCreate(
                ['name' => $cashCategoryName, 'jurusan_id' => $activeJurusanId]
            );

            // Separate Cash vs Non-Cash transactions within group
            $cashGroupTxs = $txs->filter(fn($tx) => in_array($tx->payment_method ?? 'cash', ['cash', '', null]));
            $nonCashGroupTxs = $txs->filter(fn($tx) => in_array($tx->payment_method, ['transfer', 'qris']));

            // --- 1) Post Cash Profit Share to CashTransaction ---
            $cashGroupProfit = $cashGroupTxs->sum(fn($tx) => $tx->unit_profit * $tx->quantity);
            $adjustedCashGroupProfit = $cashGroupProfit * $scaleFactor;

            if ($adjustedCashGroupProfit > 0) {
                $najmyShare = $adjustedCashGroupProfit * 0.30;
                if ($najmyShare > 0) {
                    CashTransaction::create([
                        'jurusan_id' => $activeJurusanId,
                        'date' => now()->toDateString(),
                        'cash_type' => 'keuntungan',
                        'cash_category_id' => $catPenjualan->id,
                        'type' => 'expense',
                        'amount' => $najmyShare,
                        'description' => 'Bagi Hasil Mingguan dengan ' . $najmyName . ' (30% - Kategori: ' . $categoryNameClean . ') - Periode ' . $weekStart->format('d/m/Y') . ' s.d ' . $weekEnd->format('d/m/Y'),
                        'reference' => 'WD-PROFIT-NAJMY-' . now()->format('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2))),
                    ]);
                }

                $labantikShare = $adjustedCashGroupProfit * 0.30;
                if ($labantikShare > 0) {
                    CashTransaction::create([
                        'jurusan_id' => $activeJurusanId,
                        'date' => now()->toDateString(),
                        'cash_type' => 'keuntungan',
                        'cash_category_id' => $catPenjualan->id,
                        'type' => 'expense',
                        'amount' => $labantikShare,
                        'description' => 'Bagi Hasil Mingguan Labantik (30% - Kategori: ' . $categoryNameClean . ') - Periode ' . $weekStart->format('d/m/Y') . ' s.d ' . $weekEnd->format('d/m/Y'),
                        'reference' => 'WD-PROFIT-LABANTIK-' . now()->format('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2))),
                    ]);
                }

                $jurusanShare = $adjustedCashGroupProfit * 0.40;
                if ($jurusanShare > 0 && $catPenjualan->name !== 'Keuntungan Jurusan') {
                    CashTransaction::create([
                        'jurusan_id' => $activeJurusanId,
                        'date' => now()->toDateString(),
                        'cash_type' => 'keuntungan',
                        'cash_category_id' => $catPenjualan->id,
                        'type' => 'expense',
                        'amount' => $jurusanShare,
                        'description' => 'Bagi Hasil Mingguan Jurusan (40% - Kategori: ' . $categoryNameClean . ') - Periode ' . $weekStart->format('d/m/Y') . ' s.d ' . $weekEnd->format('d/m/Y'),
                        'reference' => 'WD-PROFIT-JURUSAN-OUT-' . now()->format('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2))),
                    ]);

                    $catKeuntunganJurusan = CashCategory::firstOrCreate(
                        ['name' => 'Keuntungan Jurusan', 'jurusan_id' => $activeJurusanId]
                    );

                    CashTransaction::create([
                        'jurusan_id' => $activeJurusanId,
                        'date' => now()->toDateString(),
                        'cash_type' => 'keuntungan',
                        'cash_category_id' => $catKeuntunganJurusan->id,
                        'type' => 'income',
                        'amount' => $jurusanShare,
                        'description' => 'Terima Bagi Hasil Mingguan Jurusan (40% - Dari: ' . $categoryNameClean . ') - Periode ' . $weekStart->format('d/m/Y') . ' s.d ' . $weekEnd->format('d/m/Y'),
                        'reference' => 'WD-PROFIT-JURUSAN-IN-' . now()->format('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2))),
                    ]);
                }
            }

            // --- 2) Post Non-Cash Profit Share to VirtualCashTransaction ---
            $byMethod = $nonCashGroupTxs->groupBy('payment_method');
            foreach ($byMethod as $method => $methodTxs) {
                $sourceMethod = in_array($method, ['transfer', 'qris']) ? $method : 'transfer';
                $methodGroupProfit = $methodTxs->sum(fn($tx) => $tx->unit_profit * $tx->quantity);
                $adjustedMethodGroupProfit = $methodGroupProfit * $scaleFactor;

                if ($adjustedMethodGroupProfit > 0) {
                    $najmyShare = $adjustedMethodGroupProfit * 0.30;
                    if ($najmyShare > 0) {
                        \App\Models\VirtualCashTransaction::create([
                            'jurusan_id' => $activeJurusanId,
                            'date' => now()->toDateString(),
                            'source_method' => $sourceMethod,
                            'cash_category_id' => $catPenjualan->id,
                            'type' => 'expense',
                            'amount' => $najmyShare,
                            'description' => 'Bagi Hasil Mingguan dengan ' . $najmyName . ' (30% - Kategori: ' . $categoryNameClean . ' - ' . strtoupper($sourceMethod) . ') - Periode ' . $weekStart->format('d/m/Y') . ' s.d ' . $weekEnd->format('d/m/Y'),
                            'reference' => 'WD-VIRTUAL-NAJMY-' . now()->format('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2))),
                        ]);
                    }

                    $labantikShare = $adjustedMethodGroupProfit * 0.30;
                    if ($labantikShare > 0) {
                        \App\Models\VirtualCashTransaction::create([
                            'jurusan_id' => $activeJurusanId,
                            'date' => now()->toDateString(),
                            'source_method' => $sourceMethod,
                            'cash_category_id' => $catPenjualan->id,
                            'type' => 'expense',
                            'amount' => $labantikShare,
                            'description' => 'Bagi Hasil Mingguan Labantik (30% - Kategori: ' . $categoryNameClean . ' - ' . strtoupper($sourceMethod) . ') - Periode ' . $weekStart->format('d/m/Y') . ' s.d ' . $weekEnd->format('d/m/Y'),
                            'reference' => 'WD-VIRTUAL-LABANTIK-' . now()->format('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2))),
                        ]);
                    }

                    $jurusanShare = $adjustedMethodGroupProfit * 0.40;
                    if ($jurusanShare > 0 && $catPenjualan->name !== 'Keuntungan Jurusan') {
                        \App\Models\VirtualCashTransaction::create([
                            'jurusan_id' => $activeJurusanId,
                            'date' => now()->toDateString(),
                            'source_method' => $sourceMethod,
                            'cash_category_id' => $catPenjualan->id,
                            'type' => 'expense',
                            'amount' => $jurusanShare,
                            'description' => 'Bagi Hasil Mingguan Jurusan (40% - Kategori: ' . $categoryNameClean . ' - ' . strtoupper($sourceMethod) . ') - Periode ' . $weekStart->format('d/m/Y') . ' s.d ' . $weekEnd->format('d/m/Y'),
                            'reference' => 'WD-VIRTUAL-JURUSAN-OUT-' . now()->format('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2))),
                        ]);

                        $catKeuntunganJurusan = CashCategory::firstOrCreate(
                            ['name' => 'Keuntungan Jurusan', 'jurusan_id' => $activeJurusanId]
                        );

                        \App\Models\VirtualCashTransaction::create([
                            'jurusan_id' => $activeJurusanId,
                            'date' => now()->toDateString(),
                            'source_method' => $sourceMethod,
                            'cash_category_id' => $catKeuntunganJurusan->id,
                            'type' => 'income',
                            'amount' => $jurusanShare,
                            'description' => 'Terima Bagi Hasil Mingguan Jurusan (40% - Dari: ' . $categoryNameClean . ' - ' . strtoupper($sourceMethod) . ') - Periode ' . $weekStart->format('d/m/Y') . ' s.d ' . $weekEnd->format('d/m/Y'),
                            'reference' => 'WD-VIRTUAL-JURUSAN-IN-' . now()->format('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2))),
                        ]);
                    }
                }
            }
        }

        $this->dispatch('toast', message: 'Laporan bagi hasil berhasil dibuat dan diposting ke Buku Kas & Kas Virtual!');
    }

    /**
     * Toggle accordion breakdown per laporan.
     * Load transaksi bagi hasil dari CashTransaction dan VirtualCashTransaction berdasarkan periode laporan.
     */
    public function toggleBreakdown(string $reportId): void
    {
        if ($this->expandedReportId === $reportId) {
            $this->expandedReportId = null;
            $this->reportBreakdown  = [];
            return;
        }

        $report          = \App\Models\WeeklyProfitShare::find($reportId);
        $activeJurusanId = session('active_jurusan_id');

        if (! $report) {
            return;
        }

        $weekStart = \Carbon\Carbon::parse($report->week_start)->format('d/m/Y');
        $weekEnd   = \Carbon\Carbon::parse($report->week_end)->format('d/m/Y');
        $pattern   = '%Periode ' . $weekStart . ' s.d ' . $weekEnd . '%';

        // Ambil transaksi kas tunai & virtual bagi hasil
        $txsCash = CashTransaction::where('jurusan_id', $activeJurusanId)
            ->where('description', 'like', 'Bagi Hasil Mingguan%')
            ->where('description', 'like', $pattern)
            ->where('type', 'expense')
            ->orderBy('description')
            ->get(['id', 'description', 'amount', 'cash_category_id']);

        $txsVirtual = \App\Models\VirtualCashTransaction::where('jurusan_id', $activeJurusanId)
            ->where('description', 'like', 'Bagi Hasil Mingguan%')
            ->where('description', 'like', $pattern)
            ->where('type', 'expense')
            ->orderBy('description')
            ->get(['id', 'description', 'amount', 'cash_category_id']);

        $transactions = $txsCash->concat($txsVirtual);

        // Kelompokkan per penerima
        $grouped = [];
        foreach ($transactions as $tx) {
            preg_match('/^Bagi Hasil Mingguan(?:\s+dengan)?\s+([^(]+)/i', $tx->description, $nameMatch);
            preg_match('/Kategori:\s*([^)]+)/i', $tx->description, $catMatch);
            preg_match('/(\d+)%/i', $tx->description, $pctMatch);

            $recipient = isset($nameMatch[1]) ? trim($nameMatch[1]) : 'Lainnya';
            $category  = isset($catMatch[1])  ? trim($catMatch[1])  : '-';
            $pct       = isset($pctMatch[1])  ? $pctMatch[1] . '%'  : '-';

            if (! isset($grouped[$recipient])) {
                $grouped[$recipient] = [
                    'name'        => $recipient,
                    'percentage'  => $pct,
                    'total'       => 0,
                    'categories'  => [],
                ];
            }

            $grouped[$recipient]['total']          += $tx->amount;
            $grouped[$recipient]['categories'][]    = [
                'name'   => $category,
                'amount' => $tx->amount,
            ];
        }

        $this->expandedReportId = $reportId;
        $this->reportBreakdown  = array_values($grouped);
    }

    public function render()
    {
        $activeJurusanId = session('active_jurusan_id');
        $reports = WeeklyProfitShare::with('creator')->where('jurusan_id', $activeJurusanId)->orderByDesc('week_end')->paginate(10);

        $weekStart = Carbon::parse($this->startDate);
        $weekEnd = Carbon::parse($this->endDate);

        $weeklyData = Transaction::whereBetween('transacted_at', [
            $weekStart->startOfDay()->toDateTimeString(),
            $weekEnd->endOfDay()->toDateTimeString(),
        ])
            ->where('jurusan_id', $activeJurusanId)
            ->whereIn('status', ['uang_diterima', 'belum_kembalian'])
            ->selectRaw('
                SUM(total_price) as total_revenue,
                SUM(CASE WHEN supplier_id IS NOT NULL THEN (unit_price - unit_profit) * quantity ELSE 0 END) as supplier_hak,
                SUM(unit_profit * quantity) as internal_profit
            ')
            ->first();

        $systemProfit = $weeklyData->internal_profit ?? 0;

        // Calculate Cash System Profit vs Non-Cash System Profit
        $cashSystemProfit = Transaction::whereBetween('transacted_at', [
            $weekStart->startOfDay()->toDateTimeString(),
            $weekEnd->endOfDay()->toDateTimeString(),
        ])
            ->where('jurusan_id', $activeJurusanId)
            ->whereIn('status', ['uang_diterima', 'belum_kembalian'])
            ->where(function ($q) {
                $q->where('payment_method', 'cash')
                    ->orWhereNull('payment_method')
                    ->orWhere('payment_method', '');
            })
            ->sum(DB::raw('unit_profit * quantity'));

        $nonCashSystemProfit = Transaction::whereBetween('transacted_at', [
            $weekStart->startOfDay()->toDateTimeString(),
            $weekEnd->endOfDay()->toDateTimeString(),
        ])
            ->where('jurusan_id', $activeJurusanId)
            ->whereIn('status', ['uang_diterima', 'belum_kembalian'])
            ->whereIn('payment_method', ['transfer', 'qris'])
            ->sum(DB::raw('unit_profit * quantity'));

        // Calculate total shortage from daily recaps for the current week
        $dailyRecaps = \App\Models\DailyRecap::whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->where('jurusan_id', $activeJurusanId)
            ->get();

        $totalShortage = 0;
        $totalSurplus = 0;
        foreach ($dailyRecaps as $recap) {
            if ((float) $recap->actual_cash <= 1) {
                continue;
            }

            $previousRecap = \App\Models\DailyRecap::forReporting()
                ->where('jurusan_id', $activeJurusanId)
                ->where('date', '<', $recap->date)
                ->orderBy('date', 'desc')
                ->first();
            $startingChangeCash = $previousRecap ? ($previousRecap->retained_change_cash ?? 0) : 0;

            $totalRevenueReal = Transaction::whereDate('transacted_at', $recap->date->toDateString())
                ->where('jurusan_id', $activeJurusanId)
                ->whereIn('status', ['uang_diterima', 'belum_kembalian'])
                ->sum('total_price');
            $diff = ((float) $recap->actual_cash - (float) $startingChangeCash) - $totalRevenueReal;
            if ($diff < 0) {
                $totalShortage += abs($diff);
            } else {
                $totalSurplus += $diff;
            }
        }

        $currentProfit = $systemProfit - $totalShortage;
        $cashProfit = max(0, $cashSystemProfit - $totalShortage);
        $nonCashProfit = $nonCashSystemProfit;

        $totalRevenue = $weeklyData->total_revenue ?? 0;
        $supplierHak = $weeklyData->supplier_hak ?? 0;

        $najmyUser = \App\Models\User::where('name', 'like', '%Najmy%')->first();
        $najmyName = $najmyUser ? $najmyUser->name : 'Najmy';

        $adminContributions = collect([
            (object) [
                'user' => (object) ['name' => $najmyName],
                'portion_name' => 'Bagi Hasil Najmy',
                'percentage' => '30%',
                'user_profit' => $currentProfit * 0.30,
                'cash_profit' => $cashProfit * 0.30,
                'non_cash_profit' => $nonCashProfit * 0.30,
            ],
            (object) [
                'user' => (object) ['name' => 'Labantik'],
                'portion_name' => 'Bagi Hasil Labantik (Kasir)',
                'percentage' => '30%',
                'user_profit' => $currentProfit * 0.30,
                'cash_profit' => $cashProfit * 0.30,
                'non_cash_profit' => $nonCashProfit * 0.30,
            ]
        ]);

        $dailyBreakdown = [];
        $tempDate = $weekStart->copy();
        while ($tempDate->lte($weekEnd)) {
            $dateStr = $tempDate->toDateString();

            $daySystemProfit = Transaction::whereDate('transacted_at', $dateStr)
                ->where('jurusan_id', $activeJurusanId)
                ->whereIn('status', ['uang_diterima', 'belum_kembalian'])
                ->sum(DB::raw('unit_profit * quantity'));

            $recap = \App\Models\DailyRecap::where('date', $dateStr)
                ->where('jurusan_id', $activeJurusanId)
                ->first();

            $dayShortage = 0;
            $daySurplus = 0;
            $dayDiff = 0;

            if ($recap && (float) $recap->actual_cash > 1) {
                $previousRecap = \App\Models\DailyRecap::forReporting()
                    ->where('jurusan_id', $activeJurusanId)
                    ->where('date', '<', $dateStr)
                    ->orderBy('date', 'desc')
                    ->first();
                $startingChangeCash = $previousRecap ? ($previousRecap->retained_change_cash ?? 0) : 0;

                $totalRevenueReal = Transaction::whereDate('transacted_at', $dateStr)
                    ->where('jurusan_id', $activeJurusanId)
                    ->whereIn('status', ['uang_diterima', 'belum_kembalian'])
                    ->sum('total_price');
                $dayDiff = ((float) $recap->actual_cash - (float) $startingChangeCash) - $totalRevenueReal;
                if ($dayDiff < 0) {
                    $dayShortage = abs($dayDiff);
                } else {
                    $daySurplus = $dayDiff;
                }
            }

            $dayNetProfit = $daySystemProfit - $dayShortage;

            $dailyBreakdown[] = [
                'date' => $tempDate->copy(),
                'system_profit' => $daySystemProfit,
                'shortage' => $dayShortage,
                'surplus' => $daySurplus,
                'diff' => $dayDiff,
                'net_profit' => $dayNetProfit,
                'has_audit' => $recap && (float) $recap->actual_cash > 1,
            ];

            $tempDate->addDay();
        }

        // Monthly Summary Logic - Filter by month and current year
        $monthName = Carbon::createFromDate($this->currentYear, $this->selectedMonth, 1)->translatedFormat('F Y');
        $monthlyReports = WeeklyProfitShare::select(
            'month_name',
            DB::raw('SUM(total_profit) as total_profit'),
            DB::raw('SUM(kas_amount) as total_kas'),
            DB::raw('SUM(shared_amount) as total_shared'),
            DB::raw('COUNT(*) as weeks_count'),
            DB::raw('MIN(created_at) as created_at')
        )
            ->where('month_name', 'like', '%' . $monthName . '%')
            ->where('jurusan_id', $activeJurusanId)
            ->groupBy('month_name')
            ->orderByDesc(DB::raw('MAX(week_end)'))
            ->get();

        // Yearly Summary Logic - Current year only
        $yearlyData = WeeklyProfitShare::select(
            DB::raw('SUM(total_profit) as total_profit'),
            DB::raw('SUM(kas_amount) as total_kas'),
            DB::raw('SUM(shared_amount) as total_shared'),
            DB::raw('COUNT(DISTINCT week_number) as total_weeks'),
            DB::raw('COUNT(DISTINCT month_name) as total_months')
        )
            ->whereYear('week_end', $this->currentYear)
            ->where('jurusan_id', $activeJurusanId)
            ->first();

        // Get list of available months with data for filter
        $availableMonths = WeeklyProfitShare::selectRaw('DISTINCT MONTH(week_end) as month, YEAR(week_end) as year')
            ->whereYear('week_end', $this->currentYear)
            ->where('jurusan_id', $activeJurusanId)
            ->orderBy('month', 'desc')
            ->get();

        // Get all monthly data for yearly breakdown
        $allMonthlyData = WeeklyProfitShare::select(
            'month_name',
            DB::raw('SUM(total_profit) as total_profit'),
            DB::raw('SUM(kas_amount) as total_kas'),
            DB::raw('SUM(shared_amount) as total_shared')
        )
            ->whereYear('week_end', $this->currentYear)
            ->where('jurusan_id', $activeJurusanId)
            ->groupBy('month_name')
            ->orderByDesc(DB::raw('MAX(week_end)'))
            ->get();

        return view('livewire.reports.weekly-profit', [
            'reports' => $reports,
            'monthlyReports' => $monthlyReports,
            'yearlyData' => $yearlyData,
            'allMonthlyData' => $allMonthlyData,
            'availableMonths' => $availableMonths,
            'currentWeek' => [
                'start' => $weekStart,
                'end' => $weekEnd,
                'profit' => $currentProfit,
                'cash_profit' => $cashProfit,
                'non_cash_profit' => $nonCashProfit,
                'total_revenue' => $totalRevenue,
                'supplier_hak' => $supplierHak,
                'adminContributions' => $adminContributions,
                'dailyBreakdown' => $dailyBreakdown,
            ],
            'canProcess' => $this->overrideCanProcess || in_array(now()->dayOfWeek, [Carbon::FRIDAY, Carbon::SATURDAY, Carbon::SUNDAY]),
        ])->layout('layouts.app', ['title' => 'Bagi Hasil Mingguan']);
    }
}
