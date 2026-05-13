<?php

namespace App\Livewire\Reports;

use App\Models\DailyRecap as DailyRecapModel;
use App\Models\Transaction;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class DailyRecap extends Component
{
    use WithPagination;

    #[Url]
    public string $selectedDate = '';
    public string $search = '';
    public string $filterStatus = '';
    public string $filterCategory = '';
    
    // Cash Audit
    public $actualCash = 0;
    public $cashNote = '';

    // Details Modal
    public $showDetailsModal = false;
    public $detailReference = null;

    public function mount($date = null): void
    {
        $this->selectedDate = $date ?? today()->toDateString();
        $this->loadCashAudit();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedDate(): void
    {
        $this->loadCashAudit();
        $this->resetPage();
    }

    protected function loadCashAudit(): void
    {
        $recap = DailyRecapModel::where('date', $this->selectedDate)->first();
        if ($recap) {
            $this->actualCash = $recap->actual_cash;
            $this->cashNote = $recap->cash_note ?? '';
        } else {
            $this->actualCash = 0;
            $this->cashNote = '';
        }
    }

    public function saveCashAudit(): void
    {
        DailyRecapModel::updateOrCreate(
            ['date' => $this->selectedDate],
            [
                'actual_cash' => $this->actualCash,
                'cash_note' => $this->cashNote,
            ]
        );

        $this->dispatch('toast', message: 'Audit uang kas berhasil disimpan.');
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatedFilterCategory(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $allTransactions = Transaction::with(['product.category'])
            ->whereDate('transacted_at', $this->selectedDate)
            ->get();

        $query = Transaction::query()
            ->whereDate('transacted_at', $this->selectedDate);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('reference', 'like', '%' . $this->search . '%')
                  ->orWhere('buyer_name', 'like', '%' . $this->search . '%')
                  ->orWhereHas('product', function($pq) {
                      $pq->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterCategory) {
            $query->whereHas('product', function($q) {
                $q->where('category_id', $this->filterCategory);
            });
        }

        $transactions = $query->selectRaw('reference, buyer_name, status, transacted_at, SUM(total_price) as total_amount, SUM(quantity) as total_qty, COUNT(*) as unique_items')
            ->groupBy('reference', 'buyer_name', 'status', 'transacted_at')
            ->orderByDesc('transacted_at')
            ->paginate(15);


        if ($allTransactions->isEmpty()) {
            return view('livewire.reports.daily-recap', [
                'recap' => null,
                'categoryRecap' => collect(),
                'transactions' => $transactions,
                'categories' => \App\Models\ProductCategory::orderBy('name')->get()
            ])->layout('layouts.app', ['title' => 'Rekap Harian']);
        }

        $totalRevenueAll = $allTransactions->sum('total_price');
        $totalRevenueReal = $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum('total_price');
        
        $totalSupplierHak = $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])
            ->whereNotNull('supplier_id')
            ->sum(fn($tx) => ($tx->unit_price - $tx->unit_profit) * $tx->quantity);
            
        $totalInternalRevenue = $totalRevenueReal - $totalSupplierHak;

        $totalProfit = $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum(fn($tx) => $tx->unit_profit * $tx->quantity);
        $totalModal = $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum(fn($tx) => ($tx->unit_price - $tx->unit_profit) * $tx->quantity);

        $recap = (object) [
            'total_revenue_all' => $totalRevenueAll,
            'total_revenue_real' => $totalRevenueReal,
            'total_internal_revenue' => $totalInternalRevenue,
            'total_profit' => $totalProfit,
            'total_modal' => $totalModal,
            'count_received' => $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->count(),
            'count_unpaid_change' => $allTransactions->where('status', 'belum_kembalian')->count(),
            'count_no_payment' => $allTransactions->whereIn('status', ['belum_menerima_uang', 'uang_dipinjam'])->count(),
            'month_name' => Carbon::parse($this->selectedDate)->translatedFormat('F Y'),
            'month_week' => Carbon::parse($this->selectedDate)->weekOfMonth,
            'generated_at' => now(),
        ];

        $categoryRecap = $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->groupBy(fn($tx) => $tx->product->category->name ?? 'Tanpa Kategori')
            ->map(function($group) {
                return (object) [
                    'revenue' => $group->sum('total_price'),
                    'profit' => $group->sum(fn($tx) => $tx->unit_profit * $tx->quantity),
                    'modal' => $group->sum(fn($tx) => ($tx->unit_price - $tx->unit_profit) * $tx->quantity),
                    'qty' => $group->sum('quantity'),
                ];
            })->sortByDesc('revenue');

        return view('livewire.reports.daily-recap', [
            'recap' => $recap,
            'categoryRecap' => $categoryRecap,
            'transactions' => $transactions,
            'categories' => \App\Models\ProductCategory::orderBy('name')->get()
        ])->layout('layouts.app', ['title' => 'Rekap Harian']);

    }

    public function viewDetails($reference): void
    {
        $this->detailReference = $reference;
        $this->showDetailsModal = true;
    }

    public function getDetailItemsProperty()
    {
        if (!$this->detailReference) return collect();
        return Transaction::with('product')->where('reference', $this->detailReference)->get();
    }

    public function exportCSV()
    {
        $this->dispatch('toast', message: 'Fitur ekspor CSV sedang dalam pengembangan.', type: 'info');
    }
}
