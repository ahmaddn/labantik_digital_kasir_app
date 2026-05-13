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

    protected $queryString = [
        'search' => ['except' => ''],
        'filterCategory' => ['except' => ''],
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
                'closing_stock' => $this->newClosingStock
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

    public function updatingSelectedDate()
    {
        $this->resetPage();
    }


    public function render()
    {
        $query = Product::with('category')->where('is_active', true);

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        if ($this->filterCategory) {
            $query->where('category_id', $this->filterCategory);
        }

        $products = $query->orderBy('name')->paginate(15);

        $reportData = [];

        foreach ($products as $product) {
            $stockEntry = StockEntry::where('product_id', $product->id)
                ->where('date', $this->selectedDate)
                ->first();

            $sold = Transaction::where('product_id', $product->id)
                ->whereDate('transacted_at', $this->selectedDate)
                ->sum('quantity');

            $opening = $stockEntry ? $stockEntry->opening_stock : 0;
            $closing = $stockEntry ? $stockEntry->closing_stock : 0;

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
