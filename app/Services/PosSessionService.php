<?php

namespace App\Services;

use App\Models\DailyRecap;
use App\Models\StockEntry;
use App\Models\Transaction;
use App\Models\User;
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
            if (DailyRecap::isSessionFinished($lastSessionDate, $activeJurusanId)) {
                return null;
            }

            return $lastSessionDate;
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

            $existingRecap = DailyRecap::withoutGlobalScope('active')
                ->where('date', $date)
                ->where('jurusan_id', $activeJurusanId)
                ->first();

            $newActualCash = 1;
            if ($existingRecap) {
                if ($existingRecap->actual_cash < 0) {
                    $newActualCash = abs($existingRecap->actual_cash);
                } elseif ($existingRecap->actual_cash > 1) {
                    $newActualCash = $existingRecap->actual_cash;
                }
            }

            DailyRecap::upsertForSession($date, $activeJurusanId, [
                'actual_cash' => $newActualCash,
                'cash_note' => $existingRecap && $existingRecap->actual_cash > 1 
                    ? ($existingRecap->cash_note ?? 'Auto-finished by system') 
                    : 'Auto-finished by system (Forgot to click finish)',
            ]);
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

            // Award opening session points
            $user = auth()->user();
            if ($user) {
                $user->increment('pending_points', 15);
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

            $existingRecap = DailyRecap::withoutGlobalScope('active')
                ->where('date', $today)
                ->where('jurusan_id', $activeJurusanId)
                ->first();

            $newActualCash = 1;
            if ($existingRecap) {
                if ($existingRecap->actual_cash < 0) {
                    $newActualCash = abs($existingRecap->actual_cash);
                } elseif ($existingRecap->actual_cash > 1) {
                    $newActualCash = $existingRecap->actual_cash;
                }
            }

            DailyRecap::upsertForSession($today, $activeJurusanId, [
                'actual_cash' => $newActualCash,
            ]);

            // Award closing session points and reset current streak
            $user = auth()->user();
            if ($user) {
                $user->update([
                    'streak' => 0,
                    'pending_points' => $user->pending_points + 15,
                ]);
            }
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
        string $docPrefix,
        string $paymentMethod = 'cash'
    ): string {
        $tDate = $transactionDate ?: now()->toDateString();
        $isBackdate = $tDate < now()->toDateString();
        $transactedAt = $tDate === now()->toDateString() ? now() : Carbon::parse($tDate.' '.now()->format('H:i:s'));

        $cleanName = preg_replace('/[^A-Za-z0-9]/', '', $buyerName ?: 'GUEST');
        $initials = strtoupper(substr($cleanName, 0, 2));
        $reference = $docPrefix.'-'.now()->format('Ymd').'-'.$initials.strtoupper(bin2hex(random_bytes(2)));

        DB::transaction(function () use ($cart, $change, $buyerName, $status, $note, $tDate, $isBackdate, $transactedAt, $activeJurusanId, $userId, $reference, $paymentMethod) {
            $first = true;
            foreach ($cart as $item) {
                // Hitung total harga topping yang dipilih
                $modifiersPrice = collect($item['selected_modifiers'] ?? [])->sum('price');
                $finalUnitPrice = $item['price'] + $modifiersPrice;
                $finalTotalPrice = $finalUnitPrice * $item['quantity'];

                $transaction = Transaction::create([
                    'jurusan_id' => $activeJurusanId,
                    'reference' => $reference,
                    'user_id' => $userId,
                    'product_id' => $item['id'],
                    'supplier_id' => $item['supplier_id'] ?? null,
                    'transacted_at' => $transactedAt,
                    'buyer_name' => $buyerName ?: null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $finalUnitPrice,
                    'unit_profit' => $item['profit'], // profit dasar tetap agar modal tidak salah, atau disesuaikan
                    'total_price' => $finalTotalPrice,
                    'debt_amount' => in_array($status, ['belum_menerima_uang', 'uang_dipinjam']) ? $finalTotalPrice : 0,
                    'change_due' => ($status === 'belum_kembalian' && $first) ? $change : 0,
                    'status' => $status,
                    'payment_method' => $paymentMethod,
                    'note' => $note ?: ($change > 0 && $first ? 'Kembalian: Rp'.number_format($change, 0, ',', '.') : null),
                ]);

                // Simpan item topping terpilih ke database pivot
                if (!empty($item['selected_modifiers'])) {
                    foreach ($item['selected_modifiers'] as $mod) {
                        $transaction->modifiers()->attach($mod['id'], [
                            'price' => $mod['price']
                        ]);
                    }
                }

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

            // Award transaction points and increment streak
            $user = User::find($userId);
            if ($user) {
                $user->increment('pending_points', 5);
                $user->increment('streak', 1);
            }
        });

        return $reference;
    }
}
