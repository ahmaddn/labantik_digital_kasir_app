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
                'is_settled' => \App\Models\CashTransaction::where('reference', "SETTLE-SUPPLIER-{$supplier->id}-{$this->dateFrom}-{$this->dateTo}")->exists(),
            ];
        })->filter(fn($r) => $r->total_qty > 0);

        return view('livewire.reports.supplier-report', [
            'reports' => $reports,
            'suppliers' => Supplier::all()
        ])->layout('layouts.app', ['title' => 'Laporan Supplier']);
    }

    public function settleSupplier($supplierId, $supplierName, $amount)
    {
        $activeJurusanId = session('active_jurusan_id');
        
        $category = \App\Models\CashCategory::where('jurusan_id', $activeJurusanId)
            ->where('name', 'Bagi Hasil Supplier')
            ->first();
            
        if (!$category) {
            $category = \App\Models\CashCategory::create([
                'jurusan_id' => $activeJurusanId,
                'name' => 'Bagi Hasil Supplier'
            ]);
        }

        $reference = "SETTLE-SUPPLIER-{$supplierId}-{$this->dateFrom}-{$this->dateTo}";

        $exists = \App\Models\CashTransaction::where('reference', $reference)->exists();
        if ($exists) {
            $this->dispatch('toast', message: 'Bagi hasil supplier ini sudah dilunasi sebelumnya.');
            return;
        }

        \App\Models\CashTransaction::create([
            'jurusan_id' => $activeJurusanId,
            'date' => now()->toDateString(),
            'type' => 'expense',
            'cash_type' => 'modal',
            'cash_category_id' => $category->id,
            'amount' => $amount,
            'description' => "Pelunasan bagi hasil supplier {$supplierName} periode " . \Carbon\Carbon::parse($this->dateFrom)->translatedFormat('d M Y') . " s/d " . \Carbon\Carbon::parse($this->dateTo)->translatedFormat('d M Y'),
            'reference' => $reference,
        ]);

        $this->dispatch('toast', message: "Berhasil melunasi bagi hasil {$supplierName}.");
    }

    public function settleAndShare($supplierId, $supplierName, $amount)
    {
        $this->settleSupplier($supplierId, $supplierName, $amount);

        $msg = "📢 *LAPORAN BAGI HASIL SUPPLIER*\n";
        $msg .= "👤 *Supplier:* {$supplierName}\n";
        $msg .= "📅 *Periode:* " . \Carbon\Carbon::parse($this->dateFrom)->translatedFormat('d M Y') . " s/d " . \Carbon\Carbon::parse($this->dateTo)->translatedFormat('d M Y') . "\n";
        $msg .= "💰 *Total Hak Supplier:* Rp" . number_format($amount, 0, ',', '.') . "\n\n";
        $msg .= "*Status:* ✅ LUNAS (Sudah dibayarkan)\n\n";
        $msg .= "_Terima kasih atas kerjasamanya._";

        $waUrl = "https://api.whatsapp.com/send?text=" . urlencode($msg);
        
        $this->dispatch('open-link', url: $waUrl);
    }
}
