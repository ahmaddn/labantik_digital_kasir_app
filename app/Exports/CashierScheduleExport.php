<?php

namespace App\Exports;

use App\Models\CashierSchedule;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CashierScheduleExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles, WithCustomStartCell
{
    protected $weekStart;
    protected $jurusanId;

    public function __construct($weekStart, $jurusanId)
    {
        $this->weekStart = Carbon::parse($weekStart)->startOfWeek();
        $this->jurusanId = $jurusanId;
    }

    public function collection()
    {
        $start = $this->weekStart->toDateString();
        $end = $this->weekStart->copy()->endOfWeek()->toDateString();

        return CashierSchedule::with(['user', 'jurusan'])
            ->whereBetween('date', [$start, $end])
            ->when($this->jurusanId, function($q) {
                $q->where('jurusan_id', $this->jurusanId);
            })
            ->orderBy('date')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Hari',
            'Tanggal',
            'Nama Kasir',
            'Unit TEFA / Jurusan',
            'Catatan / Keterangan',
        ];
    }

    public function map($schedule): array
    {
        return [
            $schedule->date->translatedFormat('l'),
            $schedule->date->translatedFormat('d F Y'), // Indonesian format: e.g. 20 Januari 2026
            $schedule->user->name,
            $schedule->jurusan ? $schedule->jurusan->name : 'Semua',
            $schedule->notes ?: '-',
        ];
    }

    public function title(): string
    {
        return 'Jadwal Kasir';
    }

    public function startCell(): string
    {
        return 'A5';
    }

    public function styles(Worksheet $sheet)
    {
        $start = $this->weekStart->translatedFormat('d F Y');
        $end = $this->weekStart->copy()->endOfWeek()->translatedFormat('d F Y');

        // Document Title Banner
        $sheet->mergeCells('A1:E1');
        $sheet->setCellValue('A1', 'LAPORAN JADWAL JAGA KASIR - LABANTIK');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->getColor()->setARGB('FF1E40AF');

        // Document Subtitle
        $sheet->mergeCells('A2:C2');
        $sheet->setCellValue('A2', 'Periode: ' . $start . ' - ' . $end);
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(11)->getColor()->setARGB('FF475569');

        $sheet->mergeCells('A3:C3');
        $sheet->setCellValue('A3', 'Dicetak Pada: ' . now()->translatedFormat('d F Y H:i:s'));
        $sheet->getStyle('A3')->getFont()->setSize(9)->getColor()->setARGB('FF64748B');

        // Header style (Row 5)
        $sheet->getStyle('A5:E5')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle('A5:E5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF2563EB'); // Primary Blue

        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(30);

        // Center align day and date columns
        $sheet->getStyle('A5:B100')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Grid Borders and Alternate Zebra Stripe Colors
        $lastRow = $sheet->getHighestRow();
        if ($lastRow >= 5) {
            $sheet->getStyle('A5:E' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $sheet->getStyle('A5:E' . $lastRow)->getBorders()->getAllBorders()->getColor()->setARGB('FFCBD5E1'); // Slate 300 border

            for ($row = 6; $row <= $lastRow; $row++) {
                if ($row % 2 === 0) {
                    $sheet->getStyle('A' . $row . ':E' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('FFF8FAFC'); // slate-50 background for alternate rows
                }
            }
        }
    }
}
