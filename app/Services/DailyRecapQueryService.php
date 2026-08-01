<?php

namespace App\Services;

use App\Models\Transaction;
use Carbon\Carbon;

class DailyRecapQueryService
{
    public function getRecapData(string $selectedDate, ?string $activeJurusanId): array
    {
        $allTransactions = Transaction::forReporting()->with(['product.category'])
            ->whereDate('transacted_at', $selectedDate)
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->get();

        if ($allTransactions->isEmpty()) {
            return [
                'recap' => null,
                'categoryRecap' => collect(),
            ];
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
            'month_name' => Carbon::parse($selectedDate)->translatedFormat('F Y'),
            'month_week' => Carbon::parse($selectedDate)->weekOfMonth,
            'generated_at' => now(),
        ];

        $categoryRecap = $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->groupBy(fn ($tx) => $tx->product->category_id ?? 'null')
            ->map(function ($group) {
                $first = $group->first();

                return (object) [
                    'id' => $first->product->category_id ?? 'null',
                    'name' => $first->product->category->name ?? 'Tanpa Kategori',
                    'revenue' => $group->sum('total_price'),
                    'profit' => $group->sum(fn ($tx) => $tx->unit_profit * $tx->quantity),
                    'modal' => $group->sum(fn ($tx) => ($tx->unit_price - $tx->unit_profit) * $tx->quantity),
                    'qty' => $group->sum('quantity'),
                ];
            })->sortByDesc('revenue');

        return [
            'recap' => $recap,
            'categoryRecap' => $categoryRecap,
        ];
    }
}
