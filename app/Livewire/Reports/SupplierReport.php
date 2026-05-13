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

    public function render()
    {
        $query = Transaction::whereIn('status', ['uang_diterima', 'belum_kembalian'])
            ->whereBetween('transacted_at', [$this->dateFrom . ' 00:00:00', $this->dateTo . ' 23:59:59']);

        if ($this->supplierId) {
            $query->where('supplier_id', $this->supplierId);
        }

        $reports = $query->with('supplier', 'product')
            ->selectRaw('supplier_id, SUM(quantity) as total_qty, SUM(total_price) as total_sales, SUM(quantity * (unit_price - unit_profit)) as total_supplier_share, SUM(quantity * unit_profit) as total_shop_profit')
            ->groupBy('supplier_id')
            ->get()
            ->map(function($report) {
                if (!$report->supplier_id) {
                    $report->supplier_name = 'INTERNAL / TOKO';
                } else {
                    $report->supplier_name = $report->supplier->name ?? 'Unknown';
                }
                return $report;
            });

        return view('livewire.reports.supplier-report', [
            'reports' => $reports,
            'suppliers' => Supplier::all()
        ])->layout('layouts.app', ['title' => 'Laporan Supplier']);
    }
}
