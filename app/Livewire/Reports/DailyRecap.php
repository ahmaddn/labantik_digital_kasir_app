<?php

namespace App\Livewire\Reports;

use App\Exports\DailyDataExport;
use App\Models\CashTransaction;
use App\Models\DailyRecap as DailyRecapModel;
use App\Models\ProductCategory;
use App\Models\Transaction;
use App\Services\DailyRecapActionService;
use App\Services\DailyRecapQueryService;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class DailyRecap extends Component
{
    use WithFileUploads, WithPagination;

    #[Url]
    public string $selectedDate = '';

    public string $search = '';

    public string $filterStatus = '';

    public string $filterCategory = '';

    public $importFile;

    public bool $reopenSession = true;

    public bool $isPosted = false;

    // Cash Audit
    public $actualCash = 0;

    public $retainedChangeCash = 0;

    public $cashNote = '';

    public $startingChangeCash = 0;

    // Details Modal
    public bool $showDetailsModal = false;

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
        $activeJurusanId = session('active_jurusan_id');
        $recap = DailyRecapModel::where('date', $this->selectedDate)
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->first();
        if ($recap) {
            $this->actualCash = $recap->actual_cash;
            $this->retainedChangeCash = $recap->retained_change_cash ?? 0;
            $this->cashNote = $recap->cash_note ?? '';
        } else {
            $this->actualCash = 0;
            $this->retainedChangeCash = 0;
            $this->cashNote = '';
        }

        $previousRecap = DailyRecapModel::where('jurusan_id', $activeJurusanId)
            ->where('date', '<', $this->selectedDate)
            ->orderBy('date', 'desc')
            ->first();
        $this->startingChangeCash = $previousRecap ? ($previousRecap->retained_change_cash ?? 0) : 0;

        $this->isPosted = CashTransaction::where('date', $this->selectedDate)
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->where(function ($q) {
                $q->where('description', 'like', '%Penjualan Harian (Sistem)%')
                    ->orWhere('description', 'like', '%Bagi Hasil Supplier (Sistem)%')
                    ->orWhere('description', 'like', '%Modal Penjualan%')
                    ->orWhere('description', 'like', '%Keuntungan Penjualan%');
            })
            ->exists();
    }

    public function saveCashAudit(DailyRecapActionService $service): void
    {
        $activeJurusanId = session('active_jurusan_id');
        $service->saveCashAudit($this->selectedDate, $this->actualCash, $this->retainedChangeCash, $this->cashNote, $activeJurusanId);

        $this->dispatch('toast', message: 'Audit uang kas berhasil disimpan.');
    }

    public function postToCashBook(DailyRecapActionService $service): void
    {
        $activeJurusanId = session('active_jurusan_id');
        [$success, $msg] = $service->postToCashBook($this->selectedDate, $activeJurusanId);

        if (! $success) {
            $this->dispatch('toast', message: $msg, type: 'error');

            return;
        }

        $wasPosted = $this->isPosted;
        $this->isPosted = true;

        $message = $wasPosted
            ? 'Data kas harian berhasil diposting ulang (di-update) ke Buku Kas!'
            : 'Data kas harian per kategori (termasuk bagi hasil supplier) berhasil diposting ke Buku Kas!';

        $this->dispatch('toast', message: $message);
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatedFilterCategory(): void
    {
        $this->resetPage();
    }

    public function render(DailyRecapQueryService $service)
    {
        $activeJurusanId = session('active_jurusan_id');

        $query = Transaction::query()
            ->whereDate('transacted_at', $this->selectedDate)
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            });

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('reference', 'like', '%'.$this->search.'%')
                    ->orWhere('buyer_name', 'like', '%'.$this->search.'%')
                    ->orWhereHas('product', function ($pq) {
                        $pq->where('name', 'like', '%'.$this->search.'%');
                    });
            });
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterCategory) {
            $query->whereHas('product', function ($q) {
                $q->where('category_id', $this->filterCategory);
            });
        }

        $transactions = $query->selectRaw('reference, buyer_name, status, transacted_at, SUM(total_price) as total_amount, SUM(quantity) as total_qty, COUNT(*) as unique_items')
            ->groupBy('reference', 'buyer_name', 'status', 'transacted_at')
            ->orderByDesc('transacted_at')
            ->paginate(15);

        $recapData = $service->getRecapData($this->selectedDate, $activeJurusanId);

        return view('livewire.reports.daily-recap', [
            'recap' => $recapData['recap'],
            'categoryRecap' => $recapData['categoryRecap'],
            'transactions' => $transactions,
            'categories' => ProductCategory::orderBy('name')->get(),
        ])->layout('layouts.app', ['title' => 'Rekap Harian']);
    }

    public function viewDetails($reference): void
    {
        $this->detailReference = $reference;
        $this->showDetailsModal = true;
    }

    public function getDetailItemsProperty()
    {
        if (! $this->detailReference) {
            return collect();
        }

        return Transaction::with('product')->where('reference', $this->detailReference)->get();
    }

    public function exportCSV()
    {
        $this->dispatch('toast', message: 'Fitur ekspor CSV sedang dalam pengembangan.', type: 'info');
    }

    public function exportExcel()
    {
        $fileName = 'Rekap_Harian_'.$this->selectedDate.'.xlsx';

        return Excel::download(new DailyDataExport($this->selectedDate), $fileName);
    }

    public function importExcel(DailyRecapActionService $service)
    {
        $this->validate([
            'importFile' => 'required|mimes:xlsx,xls',
        ]);

        try {
            $service->importExcel($this->importFile, $this->selectedDate, $this->reopenSession);

            session()->flash('toast', $this->reopenSession
                ? 'Data berhasil diimpor dan Sesi Kasir telah dibuka kembali!'
                : 'Data berhasil diimpor!');

            return $this->redirect(route('daily-recap', ['date' => $this->selectedDate]), navigate: true);
        } catch (\Exception $e) {
            $this->dispatch('toast', message: 'Gagal mengimpor data: '.$e->getMessage(), type: 'error');
        }
    }
}
