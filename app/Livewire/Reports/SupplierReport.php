<?php

namespace App\Livewire\Reports;

use App\Exports\SupplierReportExport;
use App\Models\CashCategory;
use App\Models\CashTransaction;
use App\Models\Product;
use App\Models\StockEntry;
use App\Models\Supplier;
use App\Models\Transaction;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

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
        $filename = 'Laporan_Supplier_' . Carbon::parse($this->dateFrom)->format('Ymd') . '-' . Carbon::parse($this->dateTo)->format('Ymd') . '.xlsx';

        return Excel::download(new SupplierReportExport($this->dateFrom, $this->dateTo, $this->supplierId), $filename);
    }

    public function render()
    {
        $activeJurusanId = session('active_jurusan_id');
        $suppliersList = Supplier::pluck('name', 'id');

        $suppliersQuery = Supplier::query();
        if ($this->supplierId) {
            $suppliersQuery->where('id', $this->supplierId);
        }

        $reports = $suppliersQuery->get()->map(function ($supplier) use ($activeJurusanId) {
            $products = Product::where('supplier_id', $supplier->id)
                ->where('jurusan_id', $activeJurusanId)
                ->get();

            $totalQty = 0;
            $totalSales = 0;
            $totalSupplierShare = 0;
            $totalShopProfit = 0;

            foreach ($products as $product) {
                $sold = Transaction::forReporting()->where('product_id', $product->id)
                    ->whereIn('status', ['uang_diterima', 'belum_kembalian'])
                    ->whereBetween('transacted_at', [$this->dateFrom . ' 00:00:00', $this->dateTo . ' 23:59:59'])
                    ->sum('quantity');

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
                'is_settled' => CashTransaction::forReporting()->where('reference', "SETTLE-SUPPLIER-{$supplier->id}-{$this->dateFrom}-{$this->dateTo}")->exists(),
            ];
        })->filter(fn($r) => $r->total_qty > 0 && !$r->is_settled);

        return view('livewire.reports.supplier-report', [
            'reports' => $reports,
            'suppliers' => Supplier::all(),
        ])->layout('layouts.app', ['title' => 'Laporan Supplier']);
    }

    public function settleSupplier($supplierId, $supplierName, $amount, $isNoCash = false)
    {
        $activeJurusanId = session('active_jurusan_id');

        $category = CashCategory::where('jurusan_id', $activeJurusanId)
            ->where('name', 'Bagi Hasil Supplier')
            ->first();

        if (! $category) {
            $category = CashCategory::create([
                'jurusan_id' => $activeJurusanId,
                'name' => 'Bagi Hasil Supplier',
            ]);
        }

        $reference = "SETTLE-SUPPLIER-{$supplierId}-{$this->dateFrom}-{$this->dateTo}";

        $exists = CashTransaction::forReporting()->where('reference', $reference)->exists();
        if ($exists) {
            $this->dispatch('toast', message: 'Bagi hasil supplier ini sudah dilunasi sebelumnya.');

            return;
        }

        $finalAmount = $isNoCash ? 0 : $amount;
        $prefix = $isNoCash ? '[Tanpa Potong Kas] ' : '';

        CashTransaction::create([
            'jurusan_id' => $activeJurusanId,
            'date' => now()->toDateString(),
            'type' => 'expense',
            'cash_type' => 'modal',
            'cash_category_id' => $category->id,
            'amount' => $finalAmount,
            'description' => $prefix . "Pelunasan bagi hasil supplier {$supplierName} periode " . Carbon::parse($this->dateFrom)->translatedFormat('d M Y') . ' s/d ' . Carbon::parse($this->dateTo)->translatedFormat('d M Y'),
            'reference' => $reference,
        ]);

        $this->dispatch('toast', message: "Berhasil melunasi bagi hasil {$supplierName}.");
    }

    public function settleAndShare($supplierId, $supplierName, $amount, $isNoCash = false)
    {
        $this->settleSupplier($supplierId, $supplierName, $amount, $isNoCash);

        $msg = "📢 *LAPORAN BAGI HASIL SUPPLIER*\n";
        $msg .= "👤 *Supplier:* {$supplierName}\n";
        $msg .= '📅 *Periode:* ' . Carbon::parse($this->dateFrom)->translatedFormat('d M Y') . ' s/d ' . Carbon::parse($this->dateTo)->translatedFormat('d M Y') . "\n";
        $msg .= '💰 *Total Hak Supplier:* Rp' . number_format($amount, 0, ',', '.') . "\n\n";
        $msg .= "*Status:* ✅ LUNAS" . ($isNoCash ? " (Di luar sistem kas)" : " (Sudah dibayarkan)") . "\n\n";
        $msg .= '_Terima kasih atas kerjasamanya._';

        $waUrl = 'https://api.whatsapp.com/send?text=' . urlencode($msg);

        $this->dispatch('open-link', url: $waUrl);
    }
}
