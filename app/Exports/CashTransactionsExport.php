<?php

namespace App\Exports;

use App\Models\CashTransaction;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CashTransactionsExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, WithCustomStartCell
{
    protected $month;

    public function __construct($month)
    {
        $this->month = $month;
    }

    public function collection()
    {
        return CashTransaction::whereYear('date', Carbon::parse($this->month)->year)
            ->whereMonth('date', Carbon::parse($this->month)->month)
            ->orderBy('date', 'asc')
            ->orderBy('id', 'asc')
            ->get();
    }

    public function map($tx): array
    {
        return [
            Carbon::parse($tx->date)->translatedFormat('d M Y'),
            $tx->name ?? '-',
            $tx->cash_type === 'modal' ? 'Kas Modal' : 'Kas Keuntungan',
            $tx->description,
            $tx->type === 'income' ? 'Pemasukan' : 'Pengeluaran',
            $tx->amount
        ];
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Kas / Rekening',
            'Kategori',
            'Keterangan',
            'Jenis',
            'Nominal (Rp)',
        ];
    }

    public function title(): string
    {
        return 'Buku Kas';
    }

    public function startCell(): string
    {
        return 'A5';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', 'LAPORAN BUKU KAS INTERNAL - LABANTIK');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->getColor()->setARGB('FF1E40AF');

        $sheet->mergeCells('A2:B2');
        $sheet->setCellValue('A2', 'Bulan: ' . Carbon::parse($this->month)->translatedFormat('F Y'));

        $sheet->mergeCells('A3:B3');
        $sheet->setCellValue('A3', 'Dicetak Pada: ' . now()->translatedFormat('d M Y H:i:s'));

        // Header style
        $sheet->getStyle('A5:F5')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle('A5:F5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF2563EB');
        
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(40);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(20);

        // Number format for Nominal column
        $lastRow = $sheet->getHighestRow();
        if ($lastRow >= 6) {
            $sheet->getStyle('F6:F' . $lastRow)->getNumberFormat()->setFormatCode('#,##0');
        }

        // Borders
        $sheet->getStyle('A5:F' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    }
}
