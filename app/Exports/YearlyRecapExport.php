<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class YearlyRecapExport implements WithMultipleSheets
{
    use Exportable;

    protected $recap;
    protected $categoryRecap;
    protected $monthlyBreakdown;
    protected $yearName;

    public function __construct($recap, $categoryRecap, $monthlyBreakdown, $yearName)
    {
        $this->recap = $recap;
        $this->categoryRecap = $categoryRecap;
        $this->monthlyBreakdown = $monthlyBreakdown;
        $this->yearName = $yearName;
    }

    public function sheets(): array
    {
        return [
            new YearlyRecapSummarySheet($this->recap, $this->categoryRecap, $this->yearName),
            new YearlyRecapMonthlySheet($this->monthlyBreakdown)
        ];
    }
}

class YearlyRecapSummarySheet implements FromCollection, WithTitle, WithStyles, WithCustomStartCell
{
    protected $recap;
    protected $categoryRecap;
    protected $yearName;

    public function __construct($recap, $categoryRecap, $yearName)
    {
        $this->recap = $recap;
        $this->categoryRecap = $categoryRecap;
        $this->yearName = $yearName;
    }

    public function collection()
    {
        $data = collect();

        $data->push(['RINGKASAN UTAMA', '', '', '', '']);
        $data->push(['Total Omzet Tahunan', $this->recap->total_revenue_real ?? 0, '', '', '']);
        $data->push(['Omzet Internal (Murni Jurusan)', $this->recap->total_internal_revenue ?? 0, '', '', '']);
        $data->push(['Total Omzet Kotor', $this->recap->total_revenue_all ?? 0, '', '', '']);
        $data->push(['Total Keuntungan', $this->recap->total_profit ?? 0, '', '', '']);
        $data->push(['Total Modal Berjalan', $this->recap->total_modal ?? 0, '', '', '']);
        $data->push(['Total Transaksi', $this->recap->total_transactions ?? 0, '', '', '']);
        $data->push(['', '', '', '', '']);
        
        $data->push(['Kategori', 'Volume', 'Modal', 'Keuntungan', 'Omzet']);
        foreach ($this->categoryRecap as $catName => $stats) {
            $data->push([$catName, $stats->qty, $stats->modal, $stats->profit, $stats->revenue]);
        }

        return $data;
    }

    public function title(): string
    {
        return 'Ringkasan';
    }

    public function startCell(): string
    {
        return 'A5';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:E1');
        $sheet->setCellValue('A1', 'LAPORAN REKAP TAHUNAN - LABANTIK');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->getColor()->setARGB('FF1E40AF');

        $sheet->mergeCells('A2:B2');
        $sheet->setCellValue('A2', 'Tahun: ' . $this->yearName);

        $sheet->mergeCells('A3:B3');
        $sheet->setCellValue('A3', 'Dicetak Pada: ' . now()->translatedFormat('d M Y H:i:s'));

        $sheet->getColumnDimension('A')->setWidth(35);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);

        // Styling for summary header
        $sheet->getStyle('A5')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle('A5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF1E40AF');
        
        // Format numbers for summary
        $sheet->getStyle('B6:B11')->getNumberFormat()->setFormatCode('#,##0');

        // Styling for category header
        $sheet->getStyle('A13:E13')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle('A13:E13')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFEF4444');
        $sheet->getStyle('A13:E13')->getAlignment()->setHorizontal('center');
        
        // Format numbers for category
        $lastRow = $sheet->getHighestRow();
        if ($lastRow >= 14) {
            $sheet->getStyle('B14:E' . $lastRow)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('A13:E' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        }
    }
}

class YearlyRecapMonthlySheet implements FromCollection, WithHeadings, WithTitle, WithStyles, WithMapping
{
    protected $monthlyBreakdown;

    public function __construct($monthlyBreakdown)
    {
        $this->monthlyBreakdown = $monthlyBreakdown;
    }

    public function collection()
    {
        return collect($this->monthlyBreakdown);
    }

    public function map($row): array
    {
        return [
            Carbon::create(null, $row->month)->translatedFormat('F'),
            $row->total_transactions,
            $row->total_revenue_real,
            $row->total_profit,
        ];
    }

    public function headings(): array
    {
        return ['Bulan', 'Transaksi', 'Pendapatan', 'Profit'];
    }

    public function title(): string
    {
        return 'Tren Bulanan';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(25);

        $sheet->getStyle('A1:D1')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle('A1:D1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF1E40AF');
        $sheet->getStyle('A1:D1')->getAlignment()->setHorizontal('center');

        $lastRow = $sheet->getHighestRow();
        if ($lastRow >= 2) {
            $sheet->getStyle('B2:D' . $lastRow)->getNumberFormat()->setFormatCode('#,##0');
        }
    }
}
