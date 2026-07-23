<?php

namespace App\Services;

use App\Models\StoreDebt;
use App\Models\Transaction;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CustomerDebtService
{
    public function settleDebt(
        string $reference,
        float $settleAmount,
        string $settleMethod,
        string $activeTab,
        array $spentItems,
        string $userId
    ): void {
        DB::transaction(function () use ($reference, $settleAmount, $settleMethod, $activeTab, $spentItems, $userId) {
            $transactions = Transaction::where('reference', $reference)->get();
            $remainingToSettle = $settleAmount;

            foreach ($transactions as $trx) {
                if ($remainingToSettle <= 0) {
                    break;
                }

                $currentField = $activeTab === 'change' ? 'change_due' : 'debt_amount';
                $currentAmount = $trx->$currentField;

                if ($currentAmount <= 0) {
                    continue;
                }

                $deduct = min($currentAmount, $remainingToSettle);
                $remainingToSettle -= $deduct;

                $newAmount = $currentAmount - $deduct;
                $updates = [
                    $currentField => $newAmount,
                    'note' => $trx->note." (Pelunasan {$settleMethod} Rp".number_format($deduct, 0, ',', '.').' pd '.now()->format('d/m/Y H:i').')',
                ];

                if ($newAmount <= 0) {
                    $updates['transacted_at'] = now();
                }

                $trx->update($updates);
            }

            $totalRemaining = Transaction::where('reference', $reference)
                ->sum($activeTab === 'change' ? 'change_due' : 'debt_amount');

            if ($totalRemaining <= 0) {
                $allTrx = Transaction::where('reference', $reference)->get();
                foreach ($allTrx as $t) {
                    if ($t->status !== 'uang_diterima') {
                        $t->update([
                            'status' => 'uang_diterima',
                            'transacted_at' => now(),
                        ]);
                    }
                }
            }

            if ($settleMethod === 'dijajankan' && ! empty($spentItems)) {
                $newRef = 'SPNT-'.now()->format('Ymd').'-'.strtoupper(bin2hex(random_bytes(2)));
                foreach ($spentItems as $item) {
                    Transaction::create([
                        'reference' => $newRef,
                        'user_id' => $userId,
                        'product_id' => $item['product_id'],
                        'supplier_id' => $item['supplier_id'],
                        'transacted_at' => now(),
                        'buyer_name' => $transactions->first()->buyer_name ?? null,
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['price'],
                        'unit_profit' => $item['profit'],
                        'total_price' => $item['total'],
                        'status' => 'uang_diterima',
                        'note' => 'Pembelian dari tukar kembalian nota '.$reference,
                    ]);
                }
            }
        });
    }

    public function cancelSettle(string $reference): void
    {
        DB::transaction(function () use ($reference) {
            $transactions = Transaction::where('reference', $reference)->get();

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
                        'note' => $originalAmount > 0 ? 'Kembalian: Rp'.number_format($originalAmount, 0, ',', '.') : null,
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
        });
    }

    public function getSummary(?string $activeJurusanId): array
    {
        return [
            'total_debt' => Transaction::when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })->whereIn('status', ['belum_menerima_uang', 'uang_dipinjam'])->sum('debt_amount'),
            'total_change' => Transaction::when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })->where('status', 'belum_kembalian')->sum('change_due'),
            'total_store_debt' => StoreDebt::when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })->where('status', 'belum_lunas')->sum('remaining_amount'),
        ];
    }

    public function getTransactions(
        ?string $activeJurusanId,
        string $activeTab,
        string $subTab,
        ?string $search,
        ?string $startDate,
        ?string $endDate,
        int $perPage = 15
    ): LengthAwarePaginator {
        $query = Transaction::query()
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->when($activeTab === 'debt', function ($q) use ($subTab) {
                if ($subTab === 'history') {
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
            ->when($activeTab === 'change', function ($q) use ($subTab) {
                if ($subTab === 'history') {
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

        if ($search) {
            $query->where('buyer_name', 'like', '%'.$search.'%');
        }

        if ($startDate) {
            $query->whereDate('transacted_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('transacted_at', '<=', $endDate);
        }

        return $query->selectRaw('reference, buyer_name, status, transacted_at, MAX(note) as note, SUM(total_price) as total_price, SUM(debt_amount) as debt_amount, SUM(change_due) as change_due, COUNT(*) as items_count')
            ->groupBy('reference', 'buyer_name', 'status', 'transacted_at')
            ->orderByDesc('transacted_at')
            ->paginate($perPage);
    }
}
