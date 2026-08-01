<?php

namespace App\Livewire\Finance;

use App\Models\CashTransaction;
use App\Models\DailyRecap;
use App\Models\MonthlyClosingRecord;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class MonthlyClosing extends Component
{
    public $selectedMonth;

    public $availableMonths = [];

    public $carryForwardModal = 0;

    public $carryForwardProfit = 0;

    public $isClosed = false;

    public $canClose = false;

    public $canCancel = false;

    public $closeBlockedReason = null;

    public $cancelBlockedReason = null;

    public $showCancelConfirmation = false;

    public $monthStats = [];

    public function mount(): void
    {
        $this->selectedMonth = now()->subMonth()->format('Y-m');
        $this->calculateMonths();
        $this->loadMonthStats();
    }

    public function updatedSelectedMonth(): void
    {
        $this->loadMonthStats();
    }

    protected function calculateMonths(): void
    {
        $activeJurusanId = session('active_jurusan_id');

        $dates = Transaction::withoutGlobalScope('active')
            ->where('jurusan_id', $activeJurusanId)
            ->selectRaw("DATE_FORMAT(transacted_at, '%Y-%m') as month")
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->pluck('month');

        $this->availableMonths = $dates->toArray();
        if (empty($this->availableMonths)) {
            $this->availableMonths = [now()->format('Y-m'), now()->subMonth()->format('Y-m')];
        }
    }

    public function loadMonthStats(): void
    {
        $activeJurusanId = session('active_jurusan_id');
        $year = Carbon::parse($this->selectedMonth)->year;
        $month = Carbon::parse($this->selectedMonth)->month;

        $this->isClosed = Transaction::withoutGlobalScope('active')
            ->where('jurusan_id', $activeJurusanId)
            ->whereYear('transacted_at', $year)
            ->whereMonth('transacted_at', $month)
            ->where('is_archived', true)
            ->exists();

        $balances = CashTransaction::withoutGlobalScope('active')
            ->where('jurusan_id', $activeJurusanId)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->selectRaw("
                SUM(CASE WHEN cash_type = 'modal' AND type = 'income' THEN amount ELSE 0 END) as modal_income,
                SUM(CASE WHEN cash_type = 'modal' AND type = 'expense' THEN amount ELSE 0 END) as modal_expense,
                SUM(CASE WHEN cash_type = 'keuntungan' AND type = 'income' THEN amount ELSE 0 END) as profit_income,
                SUM(CASE WHEN cash_type = 'keuntungan' AND type = 'expense' THEN amount ELSE 0 END) as profit_expense
            ")
            ->first();

        $modalBalance = ($balances->modal_income ?? 0) - ($balances->modal_expense ?? 0);
        $profitBalance = ($balances->profit_income ?? 0) - ($balances->profit_expense ?? 0);

        $this->monthStats = [
            'modal' => $modalBalance,
            'profit' => $profitBalance,
            'total' => $modalBalance + $profitBalance,
        ];

        if (! $this->isClosed) {
            $this->carryForwardModal = $modalBalance;
            $this->carryForwardProfit = $profitBalance;
        }

        $this->closeBlockedReason = $this->resolveCloseBlockedReason();
        $this->canClose = $this->closeBlockedReason === null;
        $this->cancelBlockedReason = $this->resolveCancelBlockedReason();
        $this->canCancel = $this->isClosed && $this->cancelBlockedReason === null;
    }

    protected function isInLastWeekOfMonth(CarbonInterface $date): bool
    {
        return $date->day >= ($date->daysInMonth - 6);
    }

    protected function canCloseSelectedMonth(): bool
    {
        return $this->resolveCloseBlockedReason() === null;
    }

    protected function resolveCloseBlockedReason(): ?string
    {
        $selected = Carbon::parse($this->selectedMonth.'-01');
        $now = now();

        if ($selected->format('Y-m') > $now->format('Y-m')) {
            return 'Bulan ini belum dapat ditutup buku.';
        }

        if ($selected->format('Y-m') === $now->format('Y-m') && ! $this->isInLastWeekOfMonth($now)) {
            return 'Tutup buku bulan berjalan hanya dapat dilakukan pada minggu terakhir bulan (7 hari terakhir).';
        }

        return null;
    }

    protected function resolveCancelBlockedReason(): ?string
    {
        if (! $this->isClosed) {
            return null;
        }

        $activeJurusanId = session('active_jurusan_id');
        $year = Carbon::parse($this->selectedMonth)->year;
        $month = Carbon::parse($this->selectedMonth)->month;

        if ($this->nextMonthHasNonCarryForwardActivity($year, $month, $activeJurusanId)) {
            return 'Batalkan tutup buku tidak dapat dilakukan karena bulan berikutnya sudah memiliki transaksi atau rekap harian.';
        }

        return null;
    }

    protected function nextMonthHasNonCarryForwardActivity(int $year, int $month, string $jurusanId): bool
    {
        $nextMonth = Carbon::create($year, $month, 1)->addMonth();

        if (Transaction::withoutGlobalScope('active')
            ->where('jurusan_id', $jurusanId)
            ->whereYear('transacted_at', $nextMonth->year)
            ->whereMonth('transacted_at', $nextMonth->month)
            ->exists()) {
            return true;
        }

        if (DailyRecap::withoutGlobalScope('active')
            ->where('jurusan_id', $jurusanId)
            ->whereYear('date', $nextMonth->year)
            ->whereMonth('date', $nextMonth->month)
            ->exists()) {
            return true;
        }

        return CashTransaction::withoutGlobalScope('active')
            ->where('jurusan_id', $jurusanId)
            ->whereYear('date', $nextMonth->year)
            ->whereMonth('date', $nextMonth->month)
            ->excludeCarryForward()
            ->exists();
    }

    protected function deleteCarryForwardTransactions(string $jurusanId, string $nextMonthFirstDay, ?MonthlyClosingRecord $closingRecord): void
    {
        if ($closingRecord && ! empty($closingRecord->carry_forward_transaction_ids)) {
            CashTransaction::withoutGlobalScope('active')
                ->where('jurusan_id', $jurusanId)
                ->whereIn('id', $closingRecord->carry_forward_transaction_ids)
                ->delete();
        }

        CashTransaction::withoutGlobalScope('active')
            ->where('jurusan_id', $jurusanId)
            ->whereDate('date', $nextMonthFirstDay)
            ->carryForwardOnly()
            ->delete();
    }

    protected function getClosingPeriodLabel(int $year, int $month): string
    {
        return Carbon::create($year, $month, 1)->translatedFormat('F Y');
    }

    public function closeMonth(): void
    {
        $activeJurusanId = session('active_jurusan_id');
        $year = Carbon::parse($this->selectedMonth)->year;
        $month = Carbon::parse($this->selectedMonth)->month;

        if ($this->isClosed) {
            $this->dispatch('toast', message: 'Bulan ini sudah ditutup buku sebelumnya!', type: 'error');

            return;
        }

        if (! $this->canCloseSelectedMonth()) {
            $this->dispatch('toast', message: $this->closeBlockedReason ?? 'Tutup buku tidak dapat dilakukan saat ini.', type: 'error');

            return;
        }

        $pendingPointsSnapshot = [];

        DB::transaction(function () use ($year, $month, $activeJurusanId, &$pendingPointsSnapshot) {
            Transaction::withoutGlobalScope('active')
                ->where('jurusan_id', $activeJurusanId)
                ->whereYear('transacted_at', $year)
                ->whereMonth('transacted_at', $month)
                ->update(['is_archived' => true]);

            CashTransaction::withoutGlobalScope('active')
                ->where('jurusan_id', $activeJurusanId)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->update(['is_archived' => true]);

            DailyRecap::withoutGlobalScope('active')
                ->where('jurusan_id', $activeJurusanId)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->update(['is_archived' => true]);

            $userIds = Transaction::withoutGlobalScope('active')
                ->where('jurusan_id', $activeJurusanId)
                ->whereYear('transacted_at', $year)
                ->whereMonth('transacted_at', $month)
                ->distinct()
                ->pluck('user_id')
                ->filter()
                ->toArray();

            if (! empty($userIds)) {
                $users = User::query()->whereIn('id', $userIds)->get(['id', 'pending_points']);

                foreach ($users as $user) {
                    if ($user->pending_points > 0) {
                        $pendingPointsSnapshot[(string) $user->id] = $user->pending_points;
                    }
                }

                DB::table('users')->whereIn('id', $userIds)->update([
                    'points' => DB::raw('points + pending_points'),
                    'pending_points' => 0,
                ]);
            }

            $nextMonthFirstDay = Carbon::create($year, $month, 1)->addMonth()->startOfMonth()->toDateString();
            $closingLabel = $this->getClosingPeriodLabel($year, $month);
            $carryForwardTransactionIds = [];

            if ($this->carryForwardModal != 0) {
                $type = $this->carryForwardModal > 0 ? 'income' : 'expense';
                $carryForwardTransactionIds[] = CashTransaction::create([
                    'jurusan_id' => $activeJurusanId,
                    'date' => $nextMonthFirstDay,
                    'cash_type' => 'modal',
                    'type' => $type,
                    'amount' => abs($this->carryForwardModal),
                    'description' => 'Saldo Awal Modal Bawaan (Tutup Buku '.$closingLabel.')',
                ])->id;
            }

            if ($this->carryForwardProfit != 0) {
                $type = $this->carryForwardProfit > 0 ? 'income' : 'expense';
                $carryForwardTransactionIds[] = CashTransaction::create([
                    'jurusan_id' => $activeJurusanId,
                    'date' => $nextMonthFirstDay,
                    'cash_type' => 'keuntungan',
                    'type' => $type,
                    'amount' => abs($this->carryForwardProfit),
                    'description' => 'Saldo Awal Keuntungan Bawaan (Tutup Buku '.$closingLabel.')',
                ])->id;
            }

            MonthlyClosingRecord::query()->updateOrCreate(
                [
                    'jurusan_id' => $activeJurusanId,
                    'period' => $this->selectedMonth,
                ],
                [
                    'pending_points_snapshot' => $pendingPointsSnapshot,
                    'carry_forward_modal' => $this->carryForwardModal,
                    'carry_forward_profit' => $this->carryForwardProfit,
                    'carry_forward_transaction_ids' => $carryForwardTransactionIds,
                    'closed_at' => now(),
                ]
            );
        });

        Cache::flush();

        $this->loadMonthStats();
        $this->dispatch('toast', message: 'Tutup buku berhasil! Saldo baru telah dibawa ke bulan berikutnya.');
    }

    public function confirmCancelClosing(): void
    {
        if (! $this->canCancel) {
            $this->dispatch('toast', message: $this->cancelBlockedReason ?? 'Batalkan tutup buku tidak dapat dilakukan.', type: 'error');

            return;
        }

        $this->showCancelConfirmation = true;
    }

    public function cancelClosing(): void
    {
        $this->showCancelConfirmation = false;

        if (! $this->isClosed) {
            $this->dispatch('toast', message: 'Bulan ini belum ditutup buku.', type: 'error');

            return;
        }

        if ($this->cancelBlockedReason !== null) {
            $this->dispatch('toast', message: $this->cancelBlockedReason, type: 'error');

            return;
        }

        $activeJurusanId = session('active_jurusan_id');
        $year = Carbon::parse($this->selectedMonth)->year;
        $month = Carbon::parse($this->selectedMonth)->month;
        $nextMonthFirstDay = Carbon::create($year, $month, 1)->addMonth()->startOfMonth()->toDateString();
        $closingRecord = MonthlyClosingRecord::query()
            ->where('jurusan_id', $activeJurusanId)
            ->where('period', $this->selectedMonth)
            ->first();

        DB::transaction(function () use ($year, $month, $activeJurusanId, $nextMonthFirstDay, $closingRecord) {
            Transaction::withoutGlobalScope('active')
                ->where('jurusan_id', $activeJurusanId)
                ->whereYear('transacted_at', $year)
                ->whereMonth('transacted_at', $month)
                ->update(['is_archived' => false]);

            CashTransaction::withoutGlobalScope('active')
                ->where('jurusan_id', $activeJurusanId)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->update(['is_archived' => false]);

            DailyRecap::withoutGlobalScope('active')
                ->where('jurusan_id', $activeJurusanId)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->update(['is_archived' => false]);

            $this->deleteCarryForwardTransactions($activeJurusanId, $nextMonthFirstDay, $closingRecord);

            if ($closingRecord && ! empty($closingRecord->pending_points_snapshot)) {
                foreach ($closingRecord->pending_points_snapshot as $userId => $pendingPoints) {
                    $pendingPoints = (int) $pendingPoints;

                    if ($pendingPoints <= 0) {
                        continue;
                    }

                    User::query()
                        ->where('id', $userId)
                        ->update([
                            'points' => DB::raw('GREATEST(points - '.$pendingPoints.', 0)'),
                            'pending_points' => DB::raw('pending_points + '.$pendingPoints),
                        ]);
                }
            }

            $closingRecord?->delete();
        });

        Cache::flush();

        $this->loadMonthStats();
        $this->dispatch('toast', message: 'Tutup buku berhasil dibatalkan. Transaksi bulan ini kembali aktif.');
    }

    public function render()
    {
        return view('livewire.finance.monthly-closing')
            ->layout('layouts.app', ['title' => 'Tutup Buku Bulanan']);
    }
}
