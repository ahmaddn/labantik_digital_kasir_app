<?php

namespace App\Livewire\Reports;

use App\Models\Product;
use App\Models\StockEntry;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class InventoryReport extends Component
{
    use WithPagination;

    public $selectedDate;
    public $search = '';
    public $filterCategory = '';
    public $filterStatus = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterCategory' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'selectedDate' => ['except' => ''],
    ];


    public $editingProductId = null;
    public $newOpeningStock;
    public $newClosingStock;

    public function mount($date = null)
    {
        $this->selectedDate = $date ?? now()->toDateString();
    }

    public function editStock($productId, $opening, $closing)
    {
        $this->editingProductId = $productId;
        $this->newOpeningStock = $opening;
        $this->newClosingStock = $closing;
    }

    public function cancelEdit()
    {
        $this->editingProductId = null;
    }

    public function updateStock()
    {
        $this->validate([
            'newOpeningStock' => 'required|numeric|min:0',
            'newClosingStock' => 'required|numeric|min:0',
        ]);

        StockEntry::updateOrCreate(
            ['product_id' => $this->editingProductId, 'date' => $this->selectedDate],
            [
                'opening_stock' => $this->newOpeningStock,
                'closing_stock' => $this->newClosingStock,
                'user_id' => auth()->id(),
            ]
        );

        $this->editingProductId = null;
        session()->flash('success', 'Data stok berhasil diperbarui.');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterCategory()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingSelectedDate()
    {
        $this->resetPage();
    }


    public function exportExcel()
    {
        $filename = 'Laporan_Stok_' . Carbon::parse($this->selectedDate)->format('Ymd') . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\InventoryReportExport($this->selectedDate, $this->search, $this->filterCategory), $filename);
    }

    public function render()
    {
        $selectedDate = $this->selectedDate;
        $activeJurusanId = session('active_jurusan_id');

        $query = Product::with('category')
            ->where('is_active', true)
            ->when($activeJurusanId, function($q) use ($activeJurusanId) {
                $q->where('jurusan_id', $activeJurusanId);
            })
            ->select('products.*')
            ->selectSub(function($q) use ($selectedDate) {
                $q->from('stock_entries')
                    ->select('opening_stock')
                    ->whereColumn('product_id', 'products.id')
                    ->where('date', $selectedDate);
            }, 'opening_stock')
            ->selectSub(function($q) use ($selectedDate) {
                $q->from('stock_entries')
                    ->select('closing_stock')
                    ->whereColumn('product_id', 'products.id')
                    ->where('date', $selectedDate);
            }, 'closing_stock')
            ->selectSub(function($q) use ($selectedDate) {
                $q->from('transactions')
                    ->selectRaw('COALESCE(SUM(quantity), 0)')
                    ->whereColumn('product_id', 'products.id')
                    ->whereDate('transacted_at', $selectedDate);
            }, 'total_sold');

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        if ($this->filterCategory) {
            $query->where('category_id', $this->filterCategory);
        }

        if ($this->filterStatus === 'bermasalah') {
            $query->havingRaw('(COALESCE(closing_stock, 0) - (COALESCE(opening_stock, 0) - COALESCE(total_sold, 0))) != 0');
        } elseif ($this->filterStatus === 'normal') {
            $query->havingRaw('(COALESCE(closing_stock, 0) - (COALESCE(opening_stock, 0) - COALESCE(total_sold, 0))) = 0');
        }

        $products = $query->orderBy('name')->paginate(15);

        $reportData = [];
        $productIds = $products->pluck('id');

        $stockEntries = StockEntry::with('user')
            ->whereIn('product_id', $productIds)
            ->where('date', $this->selectedDate)
            ->get()
            ->keyBy('product_id');

        foreach ($products as $product) {
            $stockEntry = $stockEntries->get($product->id);
            $opening = (int) ($product->opening_stock ?? 0);
            $closing = (int) ($product->closing_stock ?? 0);
            $sold = (int) ($product->total_sold ?? 0);

            $expected = $opening - $sold;
            $discrepancy = $closing - $expected;

            $reportData[] = (object) [
                'id' => $product->id,
                'name' => $product->name,
                'category' => $product->category->name ?? '-',
                'opening' => $opening,
                'sold' => $sold,
                'expected' => $expected,
                'closing' => $closing,
                'discrepancy' => $discrepancy,
                'cashier_name' => $stockEntry && $stockEntry->user ? $stockEntry->user->name : null,
            ];
        }

        // Global Totals (for all products of the day)
        $allProducts = Product::where('is_active', true)->get();
        $totalSoldGlobal = Transaction::whereDate('transacted_at', $this->selectedDate)->sum('quantity');

        $totalDiscrepancyGlobal = 0;
        $itemsWithIssueGlobal = 0;

        // This might be expensive if many products, but we need it for correct summary.
        // Optimization: Use a query to join StockEntry and calculate.

        $discrepancies = DB::table('products')
            ->leftJoin('stock_entries', function ($join) {
                $join->on('products.id', '=', 'stock_entries.product_id')
                    ->where('stock_entries.date', '=', $this->selectedDate);
            })
            ->leftJoin(DB::raw('(SELECT product_id, SUM(quantity) as total_sold FROM transactions WHERE DATE(transacted_at) = "' . $this->selectedDate . '" GROUP BY product_id) as daily_sales'), 'products.id', '=', 'daily_sales.product_id')
            ->where('products.is_active', true)
            ->selectRaw('
                COALESCE(stock_entries.opening_stock, 0) as opening,
                COALESCE(daily_sales.total_sold, 0) as sold,
                COALESCE(stock_entries.closing_stock, 0) as closing
            ')
            ->get();

        foreach ($discrepancies as $d) {
            $expected = $d->opening - $d->sold;
            $diff = $d->closing - $expected;
            $totalDiscrepancyGlobal += $diff;
            if ($diff != 0) $itemsWithIssueGlobal++;
        }

        return view('livewire.reports.inventory-report', [
            'reportData' => $reportData,
            'products' => $products,
            'categories' => \App\Models\ProductCategory::orderBy('name')->get(),
            'totalSold' => $totalSoldGlobal,

            'totalDiscrepancy' => $totalDiscrepancyGlobal,
            'itemsWithIssue' => $itemsWithIssueGlobal,
            'dateFormatted' => Carbon::parse($this->selectedDate)->translatedFormat('d F Y')
        ])->layout('layouts.app', ['title' => 'Laporan Selisih Stok']);
    }
}
