<?php

namespace App\Livewire\Finance;

use App\Models\CashTransaction;
use App\Models\DailyRecap;
use App\Models\Transaction;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class MonthlyClosing extends Component
{
    public $selectedMonth;
    public $availableMonths = [];
    
    // Carry Forward Inputs (editable)
    public $carryForwardModal = 0;
    public $carryForwardProfit = 0;
    
    public $isClosed = false;
    public $monthStats = [];

    public function mount()
    {
        $this->selectedMonth = now()->subMonth()->format('Y-m');
        $this->calculateMonths();
        $this->loadMonthStats();
    }

    public function updatedSelectedMonth()
    {
        $this->loadMonthStats();
    }

    protected function calculateMonths()
    {
        $activeJurusanId = session('active_jurusan_id');
        
        // Find distinct months from transactions
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

    public function loadMonthStats()
    {
        $activeJurusanId = session('active_jurusan_id');
        $year = Carbon::parse($this->selectedMonth)->year;
        $month = Carbon::parse($this->selectedMonth)->month;

        // Check if already closed
        $this->isClosed = Transaction::withoutGlobalScope('active')
            ->where('jurusan_id', $activeJurusanId)
            ->whereYear('transacted_at', $year)
            ->whereMonth('transacted_at', $month)
            ->where('is_archived', true)
            ->exists();

        // Calculate stats for this month
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
            'total' => $modalBalance + $profitBalance
        ];

        if (!$this->isClosed) {
            $this->carryForwardModal = $modalBalance;
            $this->carryForwardProfit = $profitBalance;
        }
    }

    public function closeMonth()
    {
        $activeJurusanId = session('active_jurusan_id');
        $year = Carbon::parse($this->selectedMonth)->year;
        $month = Carbon::parse($this->selectedMonth)->month;

        if ($this->isClosed) {
            $this->dispatch('toast', message: 'Bulan ini sudah ditutup buku sebelumnya!', type: 'error');
            return;
        }

        DB::transaction(function () use ($year, $month, $activeJurusanId) {
            // 1. Mark transactions as archived
            Transaction::withoutGlobalScope('active')
                ->where('jurusan_id', $activeJurusanId)
                ->whereYear('transacted_at', $year)
                ->whereMonth('transacted_at', $month)
                ->update(['is_archived' => true]);

            // 2. Mark cash transactions as archived
            CashTransaction::withoutGlobalScope('active')
                ->where('jurusan_id', $activeJurusanId)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->update(['is_archived' => true]);

            // 3. Mark daily recaps as archived
            DailyRecap::withoutGlobalScope('active')
                ->where('jurusan_id', $activeJurusanId)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->update(['is_archived' => true]);

            // 4. Consolidate pending points of active cashiers in this month/jurusan
            $userIds = Transaction::withoutGlobalScope('active')
                ->where('jurusan_id', $activeJurusanId)
                ->whereYear('transacted_at', $year)
                ->whereMonth('transacted_at', $month)
                ->distinct()
                ->pluck('user_id')
                ->filter()
                ->toArray();

            if (!empty($userIds)) {
                DB::table('users')->whereIn('id', $userIds)->update([
                    'points' => DB::raw('points + pending_points'),
                    'pending_points' => 0
                ]);
            }

            // 5. Create carry forward starting transactions for the next month
            $nextMonthFirstDay = Carbon::create($year, $month, 1)->addMonth()->startOfMonth()->toDateString();
            
            // Modal Carry Forward
            if ($this->carryForwardModal != 0) {
                $type = $this->carryForwardModal > 0 ? 'income' : 'expense';
                CashTransaction::create([
                    'jurusan_id' => $activeJurusanId,
                    'date' => $nextMonthFirstDay,
                    'cash_type' => 'modal',
                    'type' => $type,
                    'amount' => abs($this->carryForwardModal),
                    'description' => "Saldo Awal Modal Bawaan (Tutup Buku " . Carbon::create($year, $month, 1)->translatedFormat('F Y') . ")",
                ]);
            }

            // Profit Carry Forward
            if ($this->carryForwardProfit != 0) {
                $type = $this->carryForwardProfit > 0 ? 'income' : 'expense';
                CashTransaction::create([
                    'jurusan_id' => $activeJurusanId,
                    'date' => $nextMonthFirstDay,
                    'cash_type' => 'keuntungan',
                    'type' => $type,
                    'amount' => abs($this->carryForwardProfit),
                    'description' => "Saldo Awal Keuntungan Bawaan (Tutup Buku " . Carbon::create($year, $month, 1)->translatedFormat('F Y') . ")",
                ]);
            }
        });

        // Clear all cache
        Cache::flush();

        $this->loadMonthStats();
        $this->dispatch('toast', message: 'Tutup buku berhasil! Saldo baru telah dibawa ke bulan berikutnya.');
    }

    public function render()
    {
        return view('livewire.finance.monthly-closing')
            ->layout('layouts.app', ['title' => 'Tutup Buku Bulanan']);
    }
}
