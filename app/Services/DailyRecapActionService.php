<?php

namespace App\Services;

use App\Imports\DailyDataImport;
use App\Models\CashCategory;
use App\Models\CashTransaction;
use App\Models\DailyRecap as DailyRecapModel;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class DailyRecapActionService
{
    public function saveCashAudit(string $date, float $actualCash, float $retainedChangeCash, ?string $cashNote, ?string $activeJurusanId): void
    {
        DailyRecapModel::updateOrCreate(
            [
                'date' => $date,
                'jurusan_id' => $activeJurusanId,
            ],
            [
                'actual_cash' => $actualCash,
                'retained_change_cash' => $retainedChangeCash,
                'cash_note' => $cashNote,
            ]
        );
    }

    public function postToCashBook(string $date, ?string $activeJurusanId, ?string $userId = null): array
    {
        $recap = DailyRecapModel::where('date', $date)
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->first();

        if (! $recap || $recap->actual_cash <= 1) {
            return [false, 'Lakukan audit uang kas fisik terlebih dahulu sebelum melakukan posting!'];
        }

        $recap->update(['posted_by' => $userId]);

        $allTransactions = Transaction::with(['product.category', 'product.supplier'])
            ->whereDate('transacted_at', $date)
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->get();

        if ($allTransactions->isEmpty()) {
            return [false, 'Tidak ada transaksi pada tanggal ini.'];
        }

        $totalRevenueReal = $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum('total_price');

        $totalSupplierHak = $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])
            ->filter(fn ($tx) => ($tx->product->supplier_id ?? $tx->supplier_id) !== null)
            ->sum(fn ($tx) => ($tx->unit_price - $tx->unit_profit) * $tx->quantity);

        $totalProfit = $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum(fn ($tx) => $tx->unit_profit * $tx->quantity);

        $diff = ((float) $recap->actual_cash - (float) ($recap->retained_change_cash ?? 0)) - (float) $totalRevenueReal;

        $grouped = $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])
            ->groupBy(fn($tx) => ($tx->product->supplier_id ?? $tx->supplier_id) ? 'supplier_' . ($tx->product->supplier_id ?? $tx->supplier_id) : 'category_' . ($tx->product->category_id ?? 'other'));

        DB::transaction(function () use ($grouped, $totalSupplierHak, $diff, $activeJurusanId, $date, $recap, $totalProfit) {
            CashTransaction::where('date', $date)
                ->where('jurusan_id', $activeJurusanId)
                ->where(function ($q) {
                    $q->where('description', 'like', '%(Sistem)%')
                        ->orWhere('description', 'like', 'Penyesuaian Selisih%');
                })
                ->delete();

            foreach ($grouped as $key => $txs) {
                $firstTx = $txs->first();
                if (str_starts_with($key, 'supplier_')) {
                    $supplierName = $firstTx->product->supplier->name ?? 'Supplier';
                    $categoryNameClean = trim($supplierName);
                    $cashCategoryName = 'Penjualan '.$categoryNameClean;
                } else {
                    $categoryName = $firstTx->product->category->name ?? 'Lainnya';
                    $categoryNameClean = trim($categoryName);

                    $categoryNameLower = strtolower($categoryNameClean);
                    if ($categoryNameLower === 'makanan' || $categoryNameLower === 'minuman' || $categoryNameLower === 'makanan & minuman' || $categoryNameLower === 'makanan dan minuman' || $categoryNameLower === 'snack') {
                        $cashCategoryName = 'Jurusan Snack & Minuman';
                    } else {
                        $cashCategoryName = 'Penjualan '.$categoryNameClean;
                    }
                }

                $catPenjualan = CashCategory::firstOrCreate(
                    ['name' => $cashCategoryName, 'jurusan_id' => $activeJurusanId]
                );

                $catModalTotal = $txs->sum(fn ($tx) => ($tx->unit_price - $tx->unit_profit) * $tx->quantity);
                $catProfit = $txs->sum(fn ($tx) => $tx->unit_profit * $tx->quantity);

                if ($catModalTotal > 0) {
                    CashTransaction::updateOrCreate(
                        [
                            'date' => $date,
                            'jurusan_id' => $activeJurusanId,
                            'description' => 'Modal Penjualan '.$categoryNameClean.' (Sistem)',
                        ],
                        [
                            'cash_type' => 'modal',
                            'cash_category_id' => $catPenjualan->id,
                            'type' => 'income',
                            'amount' => $catModalTotal,
                        ]
                    );
                }

                if ($catProfit > 0) {
                    CashTransaction::updateOrCreate(
                        [
                            'date' => $date,
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

                if ($diff < 0 && $totalProfit > 0) {
                    $reduction = abs($diff) * ($catProfit / $totalProfit);
                    if ($reduction > 0) {
                        CashTransaction::updateOrCreate(
                            [
                                'date' => $date,
                                'jurusan_id' => $activeJurusanId,
                                'description' => 'Penyesuaian Selisih Kurang Keuntungan '.$categoryNameClean.' (Sistem)',
                            ],
                            [
                                'cash_type' => 'keuntungan',
                                'cash_category_id' => $catPenjualan->id,
                                'type' => 'expense',
                                'amount' => $reduction,
                            ]
                        );
                    }
                }
            }

            if ($diff > 0) {
                $catPenjualanUmum = CashCategory::firstOrCreate(
                    ['name' => 'Penjualan Umum', 'jurusan_id' => $activeJurusanId]
                );

                $baseDescription = 'Penyesuaian Selisih Lebih Uang Kas';
                $description = (! empty($recap->cash_note)) ? $baseDescription.' ('.$recap->cash_note.')' : $baseDescription;

                CashTransaction::updateOrCreate(
                    [
                        'date' => $date,
                        'jurusan_id' => $activeJurusanId,
                        'description' => $description,
                    ],
                    [
                        'cash_type' => 'keuntungan',
                        'cash_category_id' => $catPenjualanUmum->id,
                        'type' => 'income',
                        'amount' => $diff,
                    ]
                );
            }
        });

        return [true, 'Data kas harian berhasil diposting ke Buku Kas!'];
    }

    public function importExcel($importFile, string $selectedDate, bool $reopenSession): void
    {
        Excel::import(new DailyDataImport, $importFile);

        if ($reopenSession) {
            $recap = DailyRecapModel::where('date', $selectedDate)->first();
            if ($recap) {
                $recap->update([
                    'actual_cash' => 0,
                    'cash_note' => 'Sesi dibuka kembali setelah proses import data dari device lain.',
                ]);
            }
        }
    }
}
