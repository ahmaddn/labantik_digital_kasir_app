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
        $query = Transaction::join('products', 'transactions.product_id', '=', 'products.id')
            ->whereIn('transactions.status', ['uang_diterima', 'belum_kembalian'])
            ->whereBetween('transactions.transacted_at', [$this->dateFrom . ' 00:00:00', $this->dateTo . ' 23:59:59']);

        if ($this->supplierId) {
            $query->where('products.supplier_id', $this->supplierId);
        }

        $suppliersList = \App\Models\Supplier::pluck('name', 'id');

        $reports = $query->selectRaw('products.supplier_id as supplier_id, SUM(transactions.quantity) as total_qty, SUM(transactions.total_price) as total_sales, SUM(transactions.quantity * (transactions.unit_price - transactions.unit_profit)) as total_supplier_share, SUM(transactions.quantity * transactions.unit_profit) as total_shop_profit')
            ->groupBy('products.supplier_id')
            ->get()
            ->map(function($report) use ($suppliersList) {
                if (!$report->supplier_id) {
                    $report->supplier_name = 'INTERNAL / TOKO';
                } else {
                    $report->supplier_name = $suppliersList[$report->supplier_id] ?? 'Unknown';
                }
                return $report;
            });

        return view('livewire.reports.supplier-report', [
            'reports' => $reports,
            'suppliers' => Supplier::all()
        ])->layout('layouts.app', ['title' => 'Laporan Supplier']);
    }
}
