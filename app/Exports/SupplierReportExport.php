<?php

namespace App\Exports;

use App\Models\Transaction;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SupplierReportExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, WithCustomStartCell
{
    protected $dateFrom;
    protected $dateTo;
    protected $supplierId;

    public function __construct($dateFrom, $dateTo, $supplierId)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->supplierId = $supplierId;
    }

    public function collection()
    {
        $query = Transaction::join('products', 'transactions.product_id', '=', 'products.id')
            ->whereIn('transactions.status', ['uang_diterima', 'belum_kembalian'])
            ->whereBetween('transactions.transacted_at', [$this->dateFrom . ' 00:00:00', $this->dateTo . ' 23:59:59']);

        if ($this->supplierId) {
            $query->where('products.supplier_id', $this->supplierId);
        }

        $suppliersList = \App\Models\Supplier::pluck('name', 'id');

        return $query->selectRaw('products.supplier_id as supplier_id, SUM(transactions.quantity) as total_qty, SUM(transactions.total_price) as total_sales, SUM(transactions.quantity * (transactions.unit_price - transactions.unit_profit)) as total_supplier_share, SUM(transactions.quantity * transactions.unit_profit) as total_shop_profit')
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
    }

    public function map($row): array
    {
        return [
            $row->supplier_name,
            $row->total_qty,
            $row->total_sales,
            $row->total_supplier_share,
            $row->total_shop_profit,
        ];
    }

    public function headings(): array
    {
        return [
            'Nama Supplier / Pemilik',
            'Total Qty (Pcs)',
            'Omzet Penjualan (Rp)',
            'Bagi Hasil Supplier (Rp)',
            'Profit Toko (Rp)',
        ];
    }

    public function title(): string
    {
        return 'Laporan Supplier';
    }

    public function startCell(): string
    {
        return 'A5';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:E1');
        $sheet->setCellValue('A1', 'LAPORAN BAGI HASIL SUPPLIER - LABANTIK');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->getColor()->setARGB('FF1E40AF');

        $period = Carbon::parse($this->dateFrom)->translatedFormat('d M Y') . ' - ' . Carbon::parse($this->dateTo)->translatedFormat('d M Y');
        $sheet->mergeCells('A2:B2');
        $sheet->setCellValue('A2', 'Periode: ' . $period);

        $sheet->mergeCells('A3:B3');
        $sheet->setCellValue('A3', 'Dicetak Pada: ' . now()->translatedFormat('d M Y H:i:s'));

        // Header style
        $sheet->getStyle('A5:E5')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle('A5:E5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF2563EB');
        
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(20);

        // Number format
        $lastRow = $sheet->getHighestRow();
        if ($lastRow >= 6) {
            $sheet->getStyle('B6:B' . $lastRow)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('C6:E' . $lastRow)->getNumberFormat()->setFormatCode('#,##0');
        }

        // Borders
        if ($lastRow >= 5) {
            $sheet->getStyle('A5:E' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        }
    }
}
