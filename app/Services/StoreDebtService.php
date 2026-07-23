<?php

namespace App\Services;

use App\Models\CashCategory;
use App\Models\CashTransaction;
use App\Models\StoreDebt;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class StoreDebtService
{
    public function createStoreDebt(
        string $creditorName,
        ?string $supplierId,
        float $amount,
        string $date,
        ?string $dueDate,
        ?string $note,
        ?string $activeJurusanId
    ): void {
        StoreDebt::create([
            'jurusan_id' => $activeJurusanId,
            'supplier_id' => $supplierId ?: null,
            'creditor_name' => $creditorName ?: 'Unknown Creditor',
            'amount' => $amount,
            'remaining_amount' => $amount,
            'status' => 'belum_lunas',
            'note' => $note,
            'due_date' => $dueDate ?: null,
            'date' => $date,
        ]);
    }

    public function settleStoreDebt(string $id, float $amount, string $method, ?string $activeJurusanId): void
    {
        DB::transaction(function () use ($id, $amount, $method, $activeJurusanId) {
            $debt = StoreDebt::find($id);
            if ($debt) {
                $newRemaining = $debt->remaining_amount - $amount;

                $note = $debt->note;
                $paymentNote = " (Pelunasan {$method} Rp".number_format($amount, 0, ',', '.').' pd '.now()->format('d/m/Y H:i').')';
                $note = $note ? $note.$paymentNote : ltrim($paymentNote);

                $debt->update([
                    'remaining_amount' => $newRemaining,
                    'status' => $newRemaining <= 0 ? 'lunas' : 'belum_lunas',
                    'note' => $note,
                ]);

                $category = CashCategory::where('name', 'Pelunasan Hutang')->first();
                if (! $category) {
                    $category = CashCategory::create([
                        'name' => 'Pelunasan Hutang',
                        'jurusan_id' => $activeJurusanId,
                    ]);
                }

                CashTransaction::create([
                    'jurusan_id' => $activeJurusanId,
                    'date' => now()->toDateString(),
                    'type' => 'expense',
                    'cash_type' => 'keuntungan',
                    'cash_category_id' => $category->id,
                    'amount' => $amount,
                    'description' => 'Pelunasan hutang toko kepada '.$debt->creditor_name,
                    'reference' => 'PAY-DEBT-'.now()->format('Ymd').'-'.strtoupper(bin2hex(random_bytes(2))),
                ]);
            }
        });
    }

    public function deleteStoreDebt(string $id, string $reason, string $userName): void
    {
        $debt = StoreDebt::find($id);
        if ($debt) {
            $deletionNote = ' [Dihapus pd '.now()->format('d/m/Y H:i').' oleh '.$userName.'. Alasan: '.$reason.']';
            $newNote = $debt->note ? $debt->note.$deletionNote : ltrim($deletionNote);

            $debt->update([
                'status' => 'dihapus',
                'remaining_amount' => 0,
                'note' => $newNote,
            ]);
        }
    }

    public function getStoreDebts(
        ?string $activeJurusanId,
        ?string $search,
        ?string $startDate,
        ?string $endDate,
        string $statusType,
        int $perPage = 5,
        string $pageName = 'page'
    ): LengthAwarePaginator {
        $query = StoreDebt::query()
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            });

        if ($search) {
            $query->where('creditor_name', 'like', '%'.$search.'%');
        }

        if ($startDate) {
            $query->whereDate('date', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('date', '<=', $endDate);
        }

        if ($statusType === 'active') {
            $query->where('status', 'belum_lunas');
        } else {
            $query->whereIn('status', ['lunas', 'dihapus']);
        }

        return $query->orderByDesc('date')
            ->orderByDesc('created_at')
            ->paginate($perPage, ['*'], $pageName);
    }
}
