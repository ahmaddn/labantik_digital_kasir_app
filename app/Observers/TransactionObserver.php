<?php

namespace App\Observers;

use App\Models\DailyRecap;
use App\Models\StockEntry;
use App\Models\Transaction;
use Carbon\Carbon;

class TransactionObserver
{
    public function saved(Transaction $transaction): void
    {
        $this->recalculateDailyRecap($transaction->transacted_at->toDateString(), $transaction->jurusan_id);
        $this->updateStock($transaction->product_id, $transaction->transacted_at->toDateString());
    }

    public function deleted(Transaction $transaction): void
    {
        $this->recalculateDailyRecap($transaction->transacted_at->toDateString(), $transaction->jurusan_id);
        $this->updateStock($transaction->product_id, $transaction->transacted_at->toDateString());
    }

    protected function recalculateDailyRecap(string $date, ?string $jurusanId): void
    {
        $carbonDate = Carbon::parse($date);

        $stats = Transaction::whereDate('transacted_at', $date)
            ->where('jurusan_id', $jurusanId)
            ->selectRaw("
                SUM(CASE WHEN status = 'uang_diterima' THEN total_price ELSE 0 END) as revenue_real,
                SUM(total_price) as revenue_all,
                SUM(unit_profit * quantity) as profit,
                SUM((unit_price - unit_profit) * quantity) as modal,
                COUNT(CASE WHEN status = 'uang_diterima' THEN 1 END) as count_received,
                COUNT(CASE WHEN status = 'belum_kembalian' THEN 1 END) as count_unpaid_change,
                COUNT(CASE WHEN status = 'belum_menerima_uang' THEN 1 END) as count_no_payment,
                COUNT(CASE WHEN status = 'uang_dipinjam' THEN 1 END) as count_borrowed
            ")
            ->first();

        DailyRecap::updateOrCreate(
            [
                'date' => $date,
                'jurusan_id' => $jurusanId,
            ],
            [
                'month_week' => ceil($carbonDate->day / 7),
                'month_name' => $carbonDate->translatedFormat('F'),
                'total_revenue_real' => $stats->revenue_real ?? 0,
                'total_revenue_all' => $stats->revenue_all ?? 0,
                'total_profit' => $stats->profit ?? 0,
                'total_modal' => $stats->modal ?? 0,
                'count_received' => $stats->count_received ?? 0,
                'count_unpaid_change' => $stats->count_unpaid_change ?? 0,
                'count_no_payment' => $stats->count_no_payment ?? 0,
                'count_borrowed' => $stats->count_borrowed ?? 0,
                'generated_at' => now(),
            ]
        );
    }

    protected function updateStock(string $productId, string $date): void
    {
        $totalSold = Transaction::where('product_id', $productId)
            ->whereDate('transacted_at', $date)
            ->sum('quantity');

        $stockEntry = StockEntry::where('product_id', $productId)
            ->whereDate('date', $date)
            ->first();

        if ($stockEntry) {
            $stockEntry->update([
                'closing_stock' => $stockEntry->opening_stock - $totalSold,
            ]);
        }
    }
}
