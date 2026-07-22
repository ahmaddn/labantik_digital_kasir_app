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
    public function saveCashAudit(string $date, float $actualCash, float $retainedChangeCash, ?string $cashNote, ?int $activeJurusanId): void
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

    public function postToCashBook(string $date, ?int $activeJurusanId): array
    {
        $recap = DailyRecapModel::where('date', $date)
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->first();

        if (! $recap || $recap->actual_cash <= 1) {
            return [false, 'Lakukan audit uang kas fisik terlebih dahulu sebelum melakukan posting!'];
        }

        $allTransactions = Transaction::with(['product.category'])
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
            ->whereNotNull('supplier_id')
            ->sum(fn ($tx) => ($tx->unit_price - $tx->unit_profit) * $tx->quantity);

        $diff = ((float) $recap->actual_cash - (float) ($recap->retained_change_cash ?? 0)) - (float) $totalRevenueReal;

        $grouped = $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->groupBy('product.category_id');

        DB::transaction(function () use ($grouped, $totalSupplierHak, $diff, $activeJurusanId, $date, $recap) {
            CashTransaction::where('date', $date)
                ->where('jurusan_id', $activeJurusanId)
                ->where(function ($q) {
                    $q->where('description', 'like', '%(Sistem)%')
                        ->orWhere('description', 'like', 'Penyesuaian Selisih%');
                })
                ->delete();

            foreach ($grouped as $categoryId => $txs) {
                $firstTx = $txs->first();
                $categoryName = $firstTx->product->category->name ?? 'Lainnya';
                $categoryNameClean = trim($categoryName);

                $catPenjualan = CashCategory::firstOrCreate(
                    ['name' => 'Penjualan '.$categoryNameClean, 'jurusan_id' => $activeJurusanId]
                );

                $catModalInternal = $txs->whereNull('supplier_id')
                    ->sum(fn ($tx) => ($tx->unit_price - $tx->unit_profit) * $tx->quantity);

                $catProfit = $txs->sum(fn ($tx) => $tx->unit_profit * $tx->quantity);

                if ($catModalInternal > 0) {
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
                            'amount' => $catModalInternal,
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
            }

            if ($totalSupplierHak > 0) {
                $catSupplier = CashCategory::firstOrCreate(
                    ['name' => 'Bagi Hasil Supplier', 'jurusan_id' => $activeJurusanId]
                );

                CashTransaction::updateOrCreate(
                    [
                        'date' => $date,
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

            if ($diff !== 0) {
                $catPenjualanUmum = CashCategory::firstOrCreate(
                    ['name' => 'Penjualan Umum', 'jurusan_id' => $activeJurusanId]
                );

                $type = $diff < 0 ? 'expense' : 'income';
                $baseDescription = $diff < 0 ? 'Penyesuaian Selisih Kurang Uang Kas' : 'Penyesuaian Selisih Lebih Uang Kas';
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
                        'type' => $type,
                        'amount' => abs($diff),
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
