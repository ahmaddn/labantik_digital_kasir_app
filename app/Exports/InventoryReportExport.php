<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\StockEntry;
use App\Models\Transaction;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventoryReportExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, WithCustomStartCell
{
    protected $date;
    protected $search;
    protected $category;

    public function __construct($date, $search, $category)
    {
        $this->date = $date;
        $this->search = $search;
        $this->category = $category;
    }

    public function collection()
    {
        $query = Product::with('category')->where('is_active', true);

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        if ($this->category) {
            $query->where('category_id', $this->category);
        }

        $products = $query->orderBy('name')->get();

        $reportData = collect();

        foreach ($products as $product) {
            $stockEntry = StockEntry::where('product_id', $product->id)
                ->where('date', $this->date)
                ->first();

            $sold = Transaction::where('product_id', $product->id)
                ->whereDate('transacted_at', $this->date)
                ->sum('quantity');

            $opening = $stockEntry ? $stockEntry->opening_stock : 0;
            $closing = $stockEntry ? $stockEntry->closing_stock : 0;

            $expected = $opening - $sold;
            $discrepancy = $closing - $expected;

            $reportData->push((object)[
                'name' => $product->name,
                'category' => $product->category->name ?? '-',
                'opening' => $opening,
                'sold' => $sold,
                'expected' => $expected,
                'closing' => $closing,
                'discrepancy' => $discrepancy,
            ]);
        }

        return $reportData;
    }

    public function map($row): array
    {
        return [
            $row->name,
            $row->category,
            $row->opening,
            $row->sold,
            $row->expected,
            $row->closing,
            $row->discrepancy,
        ];
    }

    public function headings(): array
    {
        return [
            'Nama Produk',
            'Kategori',
            'Stok Awal',
            'Terjual',
            'Sisa Sistem (Teori)',
            'Stok Fisik Akhir',
            'Selisih',
        ];
    }

    public function title(): string
    {
        return 'Laporan Stok';
    }

    public function startCell(): string
    {
        return 'A5';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'LAPORAN SELISIH STOK (INVENTORY) - LABANTIK');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->getColor()->setARGB('FF1E40AF');

        $sheet->mergeCells('A2:B2');
        $sheet->setCellValue('A2', 'Tanggal: ' . Carbon::parse($this->date)->translatedFormat('d F Y'));

        $sheet->mergeCells('A3:B3');
        $sheet->setCellValue('A3', 'Dicetak Pada: ' . now()->translatedFormat('d M Y H:i:s'));

        // Header style
        $sheet->getStyle('A5:G5')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle('A5:G5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF2563EB');
        
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(25);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(15);

        // Borders
        $lastRow = $sheet->getHighestRow();
        if ($lastRow >= 5) {
            $sheet->getStyle('A5:G' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        }
    }
}
