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
            // Cari pelunasan terakhir untuk supplier ini
            $lastSettlement = CashTransaction::forReporting()
                ->where('reference', 'like', "SETTLE-SUPPLIER-{$supplier->id}-%")
                ->orderBy('created_at', 'desc')
                ->first();
            
            $lastSettledDate = $lastSettlement ? $lastSettlement->date : null;
            $lastSettledAt = $lastSettlement ? $lastSettlement->created_at : null;

            $trxQuery = Transaction::forReporting()
                ->join('products', 'transactions.product_id', '=', 'products.id')
                ->where('products.supplier_id', $supplier->id)
                ->where('transactions.jurusan_id', $activeJurusanId)
                ->whereIn('transactions.status', ['uang_diterima', 'belum_kembalian']);

            // Jika pernah ada pelunasan sebelumnya, hanya hitung transaksi SETELAH waktu pelunasan terakhir
            if ($lastSettledAt) {
                $trxQuery->where('transactions.transacted_at', '>', $lastSettledAt);
            } else {
                $trxQuery->whereBetween('transactions.transacted_at', [$this->dateFrom . ' 00:00:00', $this->dateTo . ' 23:59:59']);
            }

            // Filter batas akhir sesuai input dateTo
            $trxQuery->where('transactions.transacted_at', '<=', $this->dateTo . ' 23:59:59');

            $trxSummary = $trxQuery->selectRaw('
                    SUM(transactions.quantity) as total_qty,
                    SUM(transactions.total_price) as total_sales,
                    SUM(transactions.quantity * (transactions.unit_price - transactions.unit_profit)) as total_supplier_share,
                    SUM(transactions.quantity * transactions.unit_profit) as total_shop_profit
                ')
                ->first();

            $totalQty = (int) ($trxSummary->total_qty ?? 0);
            $totalSales = (float) ($trxSummary->total_sales ?? 0);
            $totalSupplierShare = (float) ($trxSummary->total_supplier_share ?? 0);
            $totalShopProfit = (float) ($trxSummary->total_shop_profit ?? 0);

            return (object) [
                'supplier_id' => $supplier->id,
                'supplier_name' => $supplier->name,
                'total_qty' => $totalQty,
                'total_sales' => $totalSales,
                'total_supplier_share' => $totalSupplierShare,
                'total_shop_profit' => $totalShopProfit,
                'is_settled' => false,
                'last_settled_date' => $lastSettledDate,
            ];
        })->filter(fn($r) => $r->total_qty > 0);

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
