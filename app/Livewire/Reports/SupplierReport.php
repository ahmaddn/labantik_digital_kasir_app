<?php

namespace App\Livewire\Reports;

use App\Models\Supplier;
use App\Models\Transaction;
use Livewire\Component;
use Livewire\WithPagination;

class SupplierReport extends Component
{
    use WithPagination;

    public $dateFrom;
    public $dateTo;
    public $supplierId = '';

    public function mount()
    {
        $this->dateFrom = now()->startOfMonth()->toDateString();
        $this->dateTo = now()->toDateString();
    }

    public function exportExcel()
    {
        $filename = 'Laporan_Supplier_' . \Carbon\Carbon::parse($this->dateFrom)->format('Ymd') . '-' . \Carbon\Carbon::parse($this->dateTo)->format('Ymd') . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\SupplierReportExport($this->dateFrom, $this->dateTo, $this->supplierId), $filename);
    }

    public function render()
    {
        $activeJurusanId = session('active_jurusan_id');
        $suppliersList = \App\Models\Supplier::pluck('name', 'id');

        $suppliersQuery = \App\Models\Supplier::query();
        if ($this->supplierId) {
            $suppliersQuery->where('id', $this->supplierId);
        }

        $reports = $suppliersQuery->get()->map(function ($supplier) use ($activeJurusanId) {
            $products = \App\Models\Product::where('supplier_id', $supplier->id)
                ->where('jurusan_id', $activeJurusanId)
                ->get();
                
            $totalQty = 0;
            $totalSales = 0;
            $totalSupplierShare = 0;
            $totalShopProfit = 0;
            
            foreach ($products as $product) {
                $firstStock = \App\Models\StockEntry::where('product_id', $product->id)
                    ->whereBetween('date', [$this->dateFrom, $this->dateTo])
                    ->where('opening_stock', '>', 0)
                    ->orderBy('date', 'asc')
                    ->first() ?? \App\Models\StockEntry::where('product_id', $product->id)
                    ->whereBetween('date', [$this->dateFrom, $this->dateTo])
                    ->orderBy('date', 'asc')
                    ->first();

                $latestStock = \App\Models\StockEntry::where('product_id', $product->id)
                    ->whereBetween('date', [$this->dateFrom, $this->dateTo])
                    ->orderBy('date', 'desc')
                    ->first();

                $openingStock = $firstStock ? $firstStock->opening_stock : 0;
                $closingStock = $latestStock ? max(0, $latestStock->closing_stock) : 0;
                
                if ($firstStock || $latestStock) {
                    $sold = max(0, $openingStock - $closingStock);
                } else {
                    $sold = \App\Models\Transaction::where('product_id', $product->id)
                        ->whereIn('status', ['uang_diterima', 'belum_kembalian'])
                        ->whereBetween('transacted_at', [$this->dateFrom . ' 00:00:00', $this->dateTo . ' 23:59:59'])
                        ->sum('quantity');
                }
                
                if ($sold > 0) {
                    $totalQty += $sold;
                    $totalSales += $sold * $product->price;
                    $totalSupplierShare += $sold * $product->modal_price;
                    $totalShopProfit += $sold * ($product->price - $product->modal_price);
                }
            }
            
            return (object) [
                'supplier_id' => $supplier->id,
                'supplier_name' => $supplier->name,
                'total_qty' => $totalQty,
                'total_sales' => $totalSales,
                'total_supplier_share' => $totalSupplierShare,
                'total_shop_profit' => $totalShopProfit,
            ];
        })->filter(fn($r) => $r->total_qty > 0);

        return view('livewire.reports.supplier-report', [
            'reports' => $reports,
            'suppliers' => Supplier::all()
        ])->layout('layouts.app', ['title' => 'Laporan Supplier']);
    }
}
