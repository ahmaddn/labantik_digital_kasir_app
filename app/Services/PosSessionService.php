<?php

namespace App\Services;

use App\Models\DailyRecap;
use App\Models\StockEntry;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PosSessionService
{
    public function detectUnfinishedSession(string $today, ?string $activeJurusanId): ?string
    {
        $lastSessionDate = StockEntry::where('date', '<', $today)
            ->whereHas('product', function ($q) use ($activeJurusanId) {
                if ($activeJurusanId) {
                    $q->where('jurusan_id', $activeJurusanId);
                }
            })
            ->orderBy('date', 'desc')
            ->value('date');

        if ($lastSessionDate) {
            $isFinished = DailyRecap::where('date', $lastSessionDate)
                ->where('jurusan_id', $activeJurusanId)
                ->where('actual_cash', '>', 0)
                ->exists();

            if (! $isFinished) {
                return $lastSessionDate;
            }
        }

        return null;
    }

    public function fixUnfinishedSession(string $date, Collection $allProducts, ?string $activeJurusanId): void
    {
        DB::transaction(function () use ($date, $allProducts, $activeJurusanId) {
            foreach ($allProducts as $p) {
                $entry = StockEntry::where('product_id', $p->id)->where('date', $date)->first();
                if ($entry) {
                    $sold = Transaction::where('product_id', $p->id)
                        ->whereDate('transacted_at', $date)
                        ->sum('quantity');

                    $entry->update([
                        'closing_stock' => max(0, $entry->opening_stock - $sold),
                    ]);
                }
            }

            DailyRecap::updateOrCreate(
                [
                    'date' => $date,
                    'jurusan_id' => $activeJurusanId,
                ],
                [
                    'actual_cash' => 1,
                    'cash_note' => 'Auto-finished by system (Forgot to click finish)',
                ]
            );
        });
    }

    public function getLastSessionStocks(string $today, ?string $activeJurusanId): array
    {
        $lastSessionDate = StockEntry::where('date', '<', $today)
            ->whereHas('product', function ($q) use ($activeJurusanId) {
                if ($activeJurusanId) {
                    $q->where('jurusan_id', $activeJurusanId);
                }
            })
            ->orderBy('date', 'desc')
            ->value('date');

        if ($lastSessionDate) {
            return StockEntry::where('date', $lastSessionDate)
                ->whereHas('product', function ($q) use ($activeJurusanId) {
                    if ($activeJurusanId) {
                        $q->where('jurusan_id', $activeJurusanId);
                    }
                })
                ->pluck('closing_stock', 'product_id')
                ->toArray();
        }

        return [];
    }

    public function saveOpeningStock(array $stockItems, string $today): void
    {
        DB::transaction(function () use ($stockItems, $today) {
            foreach ($stockItems as $productId => $qty) {
                $entry = StockEntry::updateOrCreate(
                    ['product_id' => $productId, 'date' => $today],
                    [
                        'opening_stock' => $qty ?? 0,
                        'user_id' => auth()->id(),
                    ]
                );

                $totalSold = Transaction::where('product_id', $productId)
                    ->whereDate('transacted_at', $today)
                    ->sum('quantity');

                $entry->update([
                    'closing_stock' => max(0, $entry->opening_stock - $totalSold),
                ]);
            }
        });
    }

    public function saveClosingStock(array $stockItems, string $today, ?string $activeJurusanId): void
    {
        DB::transaction(function () use ($stockItems, $today, $activeJurusanId) {
            foreach ($stockItems as $productId => $qty) {
                StockEntry::updateOrCreate(
                    ['product_id' => $productId, 'date' => $today],
                    [
                        'closing_stock' => $qty ?? 0,
                        'user_id' => auth()->id(),
                    ]
                );
            }

            DailyRecap::updateOrCreate(
                [
                    'date' => $today,
                    'jurusan_id' => $activeJurusanId,
                ],
                ['actual_cash' => 1]
            );
        });
    }

    public function checkout(
        array $cart,
        float $change,
        ?string $buyerName,
        string $status,
        ?string $note,
        ?string $transactionDate,
        string $userId,
        ?string $activeJurusanId,
        string $docPrefix
    ): string {
        $tDate = $transactionDate ?: now()->toDateString();
        $isBackdate = $tDate < now()->toDateString();
        $transactedAt = $tDate === now()->toDateString() ? now() : Carbon::parse($tDate.' '.now()->format('H:i:s'));

        $cleanName = preg_replace('/[^A-Za-z0-9]/', '', $buyerName ?: 'GUEST');
        $initials = strtoupper(substr($cleanName, 0, 2));
        $reference = $docPrefix.'-'.now()->format('Ymd').'-'.$initials.strtoupper(bin2hex(random_bytes(2)));

        DB::transaction(function () use ($cart, $change, $buyerName, $status, $note, $tDate, $isBackdate, $transactedAt, $activeJurusanId, $userId, $reference) {
            $first = true;
            foreach ($cart as $item) {
                Transaction::create([
                    'jurusan_id' => $activeJurusanId,
                    'reference' => $reference,
                    'user_id' => $userId,
                    'product_id' => $item['id'],
                    'supplier_id' => $item['supplier_id'] ?? null,
                    'transacted_at' => $transactedAt,
                    'buyer_name' => $buyerName ?: null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'unit_profit' => $item['profit'],
                    'total_price' => $item['price'] * $item['quantity'],
                    'debt_amount' => in_array($status, ['belum_menerima_uang', 'uang_dipinjam']) ? ($item['price'] * $item['quantity']) : 0,
                    'change_due' => ($status === 'belum_kembalian' && $first) ? $change : 0,
                    'status' => $status,
                    'note' => $note ?: ($change > 0 && $first ? 'Kembalian: Rp'.number_format($change, 0, ',', '.') : null),
                ]);
                $first = false;

                if ($isBackdate) {
                    $entries = StockEntry::where('product_id', $item['id'])
                        ->where('date', '>=', $tDate)
                        ->orderBy('date', 'asc')
                        ->get();

                    foreach ($entries as $ent) {
                        $soldLater = Transaction::where('product_id', $item['id'])
                            ->whereDate('transacted_at', $ent->date)
                            ->sum('quantity');

                        $ent->update([
                            'closing_stock' => max(0, $ent->opening_stock - $soldLater),
                        ]);
                    }
                }
            }
        });

        return $reference;
    }
}
