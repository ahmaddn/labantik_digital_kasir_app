<?php

namespace App\Livewire\Reports;

use App\Models\ProductCategory;
use App\Models\Transaction;
use Carbon\Carbon;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryDetail extends Component
{
    use WithPagination;

    public string $categoryId;
    public ?string $categoryName = '';

    #[Url]
    public string $type = 'daily'; // 'daily', 'monthly', 'yearly'

    #[Url]
    public string $date = '';

    #[Url]
    public string $month = '';

    #[Url]
    public string $year = '';

    #[Url]
    public string $search = '';

    #[Url]
    public string $sortBy = 'total_revenue'; // 'total_revenue', 'total_qty', 'total_profit', 'product_name'

    #[Url]
    public string $sortDirection = 'desc';

    public function mount(string $categoryId): void
    {
        $this->categoryId = $categoryId;
        
        if ($categoryId === 'null' || $categoryId === 'none') {
            $this->categoryName = 'Tanpa Kategori';
        } else {
            $category = ProductCategory::find($categoryId);
            $this->categoryName = $category ? $category->name : 'Kategori Tidak Ditemukan';
        }

        // Set defaults if empty
        if (empty($this->date)) {
            $this->date = today()->toDateString();
        }
        if (empty($this->month)) {
            $this->month = (string) now()->month;
        }
        if (empty($this->year)) {
            $this->year = (string) now()->year;
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function sort(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'desc';
        }
        $this->resetPage();
    }

    public function getFormattedPeriodProperty(): string
    {
        if ($this->type === 'daily') {
            return Carbon::parse($this->date)->translatedFormat('d F Y');
        } elseif ($this->type === 'monthly') {
            return Carbon::create($this->year, $this->month, 1)->translatedFormat('F Y');
        } else {
            return $this->year;
        }
    }

    public function getBackUrlProperty(): string
    {
        if ($this->type === 'daily') {
            return route('daily-recap', ['date' => $this->date]);
        } elseif ($this->type === 'monthly') {
            return route('monthly-recap');
        } else {
            return route('yearly-recap');
        }
    }

    public function render()
    {
        $activeJurusanId = session('active_jurusan_id');

        $query = Transaction::query()
            ->join('products', 'transactions.product_id', '=', 'products.id')
            ->leftJoin('suppliers', 'products.supplier_id', '=', 'suppliers.id')
            ->whereIn('transactions.status', ['uang_diterima', 'belum_kembalian'])
            ->where('transactions.jurusan_id', $activeJurusanId);

        if ($this->categoryId === 'null' || $this->categoryId === 'none') {
            $query->whereNull('products.category_id');
        } else {
            $query->where('products.category_id', $this->categoryId);
        }

        // Apply period filter
        if ($this->type === 'daily') {
            $query->whereDate('transactions.transacted_at', $this->date);
        } elseif ($this->type === 'monthly') {
            $query->whereMonth('transactions.transacted_at', $this->month)
                  ->whereYear('transactions.transacted_at', $this->year);
        } elseif ($this->type === 'yearly') {
            $query->whereYear('transactions.transacted_at', $this->year);
        }

        // Apply search filter
        if ($this->search) {
            $query->where('products.name', 'like', '%' . $this->search . '%');
        }

        // Select aggregate data grouped by product ID
        $query->selectRaw('
            products.id as product_id,
            products.name as product_name,
            suppliers.name as supplier_name,
            MAX(transactions.unit_price) as unit_price,
            MAX(transactions.unit_profit) as unit_profit,
            SUM(transactions.quantity) as total_qty,
            SUM(transactions.total_price) as total_revenue,
            SUM(transactions.unit_profit * transactions.quantity) as total_profit,
            SUM((transactions.unit_price - transactions.unit_profit) * transactions.quantity) as total_modal
        ')
        ->groupBy('products.id', 'products.name', 'suppliers.name');

        // Apply sorting
        if (in_array($this->sortBy, ['total_revenue', 'total_qty', 'total_profit', 'total_modal', 'product_name'])) {
            $query->orderBy($this->sortBy, $this->sortDirection);
        } else {
            $query->orderBy('total_revenue', 'desc');
        }

        $products = $query->paginate(15);

        // Calculate summary for this category in selected period
        $summaryQuery = Transaction::query()
            ->join('products', 'transactions.product_id', '=', 'products.id')
            ->whereIn('transactions.status', ['uang_diterima', 'belum_kembalian'])
            ->where('transactions.jurusan_id', $activeJurusanId);

        if ($this->categoryId === 'null' || $this->categoryId === 'none') {
            $summaryQuery->whereNull('products.category_id');
        } else {
            $summaryQuery->where('products.category_id', $this->categoryId);
        }

        if ($this->type === 'daily') {
            $summaryQuery->whereDate('transactions.transacted_at', $this->date);
        } elseif ($this->type === 'monthly') {
            $summaryQuery->whereMonth('transactions.transacted_at', $this->month)
                         ->whereYear('transactions.transacted_at', $this->year);
        } elseif ($this->type === 'yearly') {
            $summaryQuery->whereYear('transactions.transacted_at', $this->year);
        }

        $summaryStats = $summaryQuery->selectRaw('
            SUM(transactions.quantity) as total_qty,
            SUM(transactions.total_price) as total_revenue,
            SUM(transactions.unit_profit * transactions.quantity) as total_profit,
            SUM((transactions.unit_price - transactions.unit_profit) * transactions.quantity) as total_modal
        ')->first();

        return view('livewire.reports.category-detail', [
            'products' => $products,
            'summary' => $summaryStats,
        ])->layout('layouts.app', ['title' => 'Detail Performa Kategori']);
    }
}
