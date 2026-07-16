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

class MonthlyRecapExport implements WithMultipleSheets
{
    use Exportable;

    protected $recap;
    protected $categoryRecap;
    protected $dailyBreakdown;
    protected $monthName;

    public function __construct($recap, $categoryRecap, $dailyBreakdown, $monthName)
    {
        $this->recap = $recap;
        $this->categoryRecap = $categoryRecap;
        $this->dailyBreakdown = $dailyBreakdown;
        $this->monthName = $monthName;
    }

    public function sheets(): array
    {
        return [
            new MonthlyRecapSummarySheet($this->recap, $this->categoryRecap, $this->monthName),
            new MonthlyRecapDailySheet($this->dailyBreakdown)
        ];
    }
}

class MonthlyRecapSummarySheet implements FromCollection, WithTitle, WithStyles, WithCustomStartCell
{
    protected $recap;
    protected $categoryRecap;
    protected $monthName;

    public function __construct($recap, $categoryRecap, $monthName)
    {
        $this->recap = $recap;
        $this->categoryRecap = $categoryRecap;
        $this->monthName = $monthName;
    }

    public function collection()
    {
        $data = collect();

        $data->push(['RINGKASAN UTAMA', '', '', '', '']);
        $data->push(['Total Omzet Tunai', $this->recap->total_revenue_real ?? 0, '', '', '']);
        $data->push(['Omzet Internal (Murni Jurusan)', $this->recap->total_internal_revenue ?? 0, '', '', '']);
        $data->push(['Total Omzet Kotor', $this->recap->total_revenue_all ?? 0, '', '', '']);
        $data->push(['Keuntungan Estimasi', $this->recap->total_profit ?? 0, '', '', '']);
        $data->push(['Total Modal Terputar', $this->recap->total_modal ?? 0, '', '', '']);
        $data->push(['Volume Transaksi', $this->recap->total_transactions ?? 0, '', '', '']);
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
        $sheet->setCellValue('A1', 'LAPORAN REKAP BULANAN - LABANTIK');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->getColor()->setARGB('FF1E40AF');

        $sheet->mergeCells('A2:B2');
        $sheet->setCellValue('A2', 'Bulan: ' . $this->monthName);

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

        // Styling for category header (which is at row 13)
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

class MonthlyRecapDailySheet implements FromCollection, WithHeadings, WithTitle, WithStyles, WithMapping
{
    protected $dailyBreakdown;

    public function __construct($dailyBreakdown)
    {
        $this->dailyBreakdown = $dailyBreakdown;
    }

    public function collection()
    {
        return $this->dailyBreakdown;
    }

    public function map($row): array
    {
        $kasFisik = $row->actual_cash !== null ? (float)$row->actual_cash - (float)$row->retained_change_cash : null;
        $selisih = $row->actual_cash !== null ? $kasFisik - (float)$row->total_revenue_real : null;

        return [
            $row->date,
            $row->total_transactions,
            $row->total_revenue_real,
            $row->actual_cash !== null ? $kasFisik : 'Belum Audit',
            $row->actual_cash !== null ? $selisih : '-',
            $row->retained_change_cash ?? 0,
            $row->total_profit,
        ];
    }

    public function headings(): array
    {
        return ['Tanggal', 'Transaksi', 'Pendapatan (Sistem)', 'Kas Fisik (Riil)', 'Selisih', 'Uang Kembalian Ditahan', 'Profit'];
    }

    public function title(): string
    {
        return 'Breakdown Harian';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(25);
        $sheet->getColumnDimension('G')->setWidth(25);

        $sheet->getStyle('A1:G1')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle('A1:G1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF1E40AF');
        $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal('center');

        $lastRow = $sheet->getHighestRow();
        if ($lastRow >= 2) {
            $sheet->getStyle('B2:G' . $lastRow)->getNumberFormat()->setFormatCode('#,##0');
        }
    }
}
