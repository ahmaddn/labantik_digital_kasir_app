<?php

namespace App\Livewire\Reports;

use App\Exports\SupplierReportExport;
use App\Models\CashCategory;
use App\Models\CashTransaction;
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
    public $activeTab = 'unsettled'; // 'unsettled' atau 'history'

    public function mount()
    {
        $this->dateFrom = now()->startOfMonth()->toDateString();
        $this->dateTo = now()->toDateString();
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function exportExcel()
    {
        $filename = 'Laporan_Supplier_' . Carbon::parse($this->dateFrom)->format('Ymd') . '-' . Carbon::parse($this->dateTo)->format('Ymd') . '.xlsx';

        return Excel::download(new SupplierReportExport($this->dateFrom, $this->dateTo, $this->supplierId), $filename);
    }

    public function render()
    {
        $activeJurusanId = session('active_jurusan_id');

        // 1. DATA TAB UNSETTLED (Tagihan Bagi Hasil Aktif / Belum Dilunasi)
        $suppliersQuery = Supplier::query();
        if ($this->supplierId) {
            $suppliersQuery->where('id', $this->supplierId);
        }

        $unsettledReports = $suppliersQuery->get()->map(function ($supplier) use ($activeJurusanId) {
            // Cari transaksi pelunasan terakhir untuk supplier ini
            $lastSettlement = CashTransaction::forReporting()
                ->where('reference', 'like', "SETTLE-SUPPLIER-{$supplier->id}-%")
                ->orderBy('created_at', 'desc')
                ->first();

            $lastSettledAt = $lastSettlement ? $lastSettlement->created_at : null;

            // Ambil hanya transaksi penjualan SETELAH pelunasan terakhir
            $trxQuery = Transaction::forReporting()
                ->join('products', 'transactions.product_id', '=', 'products.id')
                ->where('products.supplier_id', $supplier->id)
                ->where('transactions.jurusan_id', $activeJurusanId)
                ->whereIn('transactions.status', ['uang_diterima', 'belum_kembalian'])
                ->whereBetween('transactions.transacted_at', [$this->dateFrom . ' 00:00:00', $this->dateTo . ' 23:59:59']);

            if ($lastSettledAt) {
                // Hanya hitung penjualan yang terjadi setelah waktu pelunasan terakhir
                $trxQuery->where('transactions.created_at', '>', $lastSettledAt);
            }

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
                'last_settled_at' => $lastSettledAt,
            ];
        })->filter(fn($r) => $r->total_qty > 0);

        // 2. DATA TAB HISTORY (Riwayat Pelunasan Bagi Hasil Supplier)
        $historyQuery = CashTransaction::forReporting()
            ->where('reference', 'like', 'SETTLE-SUPPLIER-%')
            ->where('jurusan_id', $activeJurusanId)
            ->whereBetween('date', [$this->dateFrom, $this->dateTo])
            ->orderBy('created_at', 'desc');

        if ($this->supplierId) {
            $historyQuery->where('reference', 'like', "SETTLE-SUPPLIER-{$this->supplierId}-%");
        }

        $settlementHistory = $historyQuery->get()->map(function ($tx) {
            // Extract supplier id from reference
            $parts = explode('-', $tx->reference);
            $supplierId = $parts[2] ?? null;
            $supplier = $supplierId ? Supplier::find($supplierId) : null;

            return (object) [
                'id' => $tx->id,
                'reference' => $tx->reference,
                'date' => $tx->date,
                'created_at' => $tx->created_at,
                'supplier_id' => $supplierId,
                'supplier_name' => $supplier ? $supplier->name : 'Supplier',
                'amount' => $tx->amount,
                'description' => $tx->description,
            ];
        });

        return view('livewire.reports.supplier-report', [
            'unsettledReports' => $unsettledReports,
            'settlementHistory' => $settlementHistory,
            'suppliers' => Supplier::all(),
        ])->layout('layouts.app', ['title' => 'Laporan Bagi Hasil Supplier']);
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

        $timestamp = now()->format('YmdHis');
        $reference = "SETTLE-SUPPLIER-{$supplierId}-{$timestamp}";

        $finalAmount = $isNoCash ? 0 : $amount;
        $prefix = $isNoCash ? '[Tanpa Potong Kas] ' : '';

        CashTransaction::create([
            'jurusan_id' => $activeJurusanId,
            'date' => now()->toDateString(),
            'type' => 'expense',
            'cash_type' => 'modal',
            'cash_category_id' => $category->id,
            'amount' => $finalAmount,
            'description' => $prefix . "Pelunasan bagi hasil supplier {$supplierName} sebesar Rp" . number_format($amount, 0, ',', '.') . ' pada ' . now()->format('d/m/Y H:i'),
            'reference' => $reference,
        ]);

        $this->dispatch('toast', message: "Berhasil melunasi bagi hasil {$supplierName}. Data telah dipindahkan ke Riwayat Pelunasan.");
    }

    public function unsettleSupplier($historyId)
    {
        $tx = CashTransaction::forReporting()->find($historyId);

        if ($tx) {
            $tx->delete();
            $this->dispatch('toast', message: "Pelunasan berhasil dibatalkan. Transaksi dikembalikan ke Tagihan Aktif.");
        }
    }

    public function settleAndShare($supplierId, $supplierName, $amount, $isNoCash = false)
    {
        $this->settleSupplier($supplierId, $supplierName, $amount, $isNoCash);

        $msg = "📢 *LAPORAN BAGI HASIL SUPPLIER*\n";
        $msg .= "👤 *Supplier:* {$supplierName}\n";
        $msg .= '📅 *Tanggal Pelunasan:* ' . now()->translatedFormat('d M Y H:i') . "\n";
        $msg .= '💰 *Total Hak Supplier:* Rp' . number_format($amount, 0, ',', '.') . "\n\n";
        $msg .= "*Status:* ✅ LUNAS" . ($isNoCash ? " (Di luar sistem kas)" : " (Sudah dibayarkan)") . "\n\n";
        $msg .= '_Terima kasih atas kerjasamanya._';

        $waUrl = 'https://api.whatsapp.com/send?text=' . urlencode($msg);

        $this->dispatch('open-link', url: $waUrl);
    }
}
