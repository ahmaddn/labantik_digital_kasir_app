<?php

namespace App\Exports;

use App\Models\DocumentationActivity;
use App\Models\DocumentationSchedule;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DocumentationScheduleExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles, WithCustomStartCell
{
    protected $activityId;
    protected $activity;
    protected $rowNo = 1;

    public function __construct($activityId)
    {
        $this->activityId = $activityId;
        $this->activity = DocumentationActivity::find($activityId);
    }

    public function collection()
    {
        if (!$this->activityId) {
            return collect();
        }

        return DocumentationSchedule::with(['user', 'jurusan'])
            ->where('activity_id', $this->activityId)
            ->orderBy('date')
            ->orderBy('shift')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Hari',
            'Tanggal',
            'Shift',
            'Nama Anggota Kasir',
            'Tingkatan',
            'Catatan / Keterangan Tugas',
        ];
    }

    public function map($schedule): array
    {
        return [
            $this->rowNo++,
            $schedule->date->translatedFormat('l'),
            $schedule->date->translatedFormat('d F Y'),
            'Shift ' . ($schedule->shift ?? 1),
            $schedule->user ? $schedule->user->name : '-',
            $schedule->user && $schedule->user->grade_level ? 'Tingkat ' . $schedule->user->grade_level : '-',
            $schedule->notes ?: 'Tugas Dokumentasi',
        ];
    }

    public function title(): string
    {
        return 'Jadwal Dokumentasi';
    }

    public function startCell(): string
    {
        return 'A6';
    }

    public function styles(Worksheet $sheet)
    {
        $title = $this->activity ? $this->activity->title : 'Jadwal Dokumentasi';
        $start = $this->activity ? $this->activity->start_date->translatedFormat('d F Y') : '-';
        $end = $this->activity ? $this->activity->end_date->translatedFormat('d F Y') : '-';
        $desc = $this->activity && $this->activity->description ? $this->activity->description : '-';

        // Document Title Banner
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'LAPORAN PENJADWALAN DOKUMENTASI LABANTIK');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->getColor()->setARGB('FF1E40AF');

        // Document Subtitle
        $sheet->mergeCells('A2:G2');
        $sheet->setCellValue('A2', 'Kegiatan: ' . $title . ' (Periode: ' . $start . ' - ' . $end . ')');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11)->getColor()->setARGB('FF0F172A');

        $sheet->mergeCells('A3:G3');
        $sheet->setCellValue('A3', 'Deskripsi Event: ' . $desc);
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(10)->getColor()->setARGB('FF475569');

        $sheet->mergeCells('A4:G4');
        $sheet->setCellValue('A4', 'Dicetak Pada: ' . now()->translatedFormat('d F Y H:i:s'));
        $sheet->getStyle('A4')->getFont()->setSize(9)->getColor()->setARGB('FF64748B');

        // Header style (Row 6)
        $sheet->getStyle('A6:G6')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle('A6:G6')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF059669'); // Emerald Green

        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(22);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(18);
        $sheet->getColumnDimension('G')->setWidth(35);

        // Center align No, Day, Date, Shift, and Grade columns
        $sheet->getStyle('A6:D100')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F6:F100')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Grid Borders and Alternate Zebra Stripe Colors
        $lastRow = $sheet->getHighestRow();
        if ($lastRow >= 6) {
            $sheet->getStyle('A6:G' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $sheet->getStyle('A6:G' . $lastRow)->getBorders()->getAllBorders()->getColor()->setARGB('FFCBD5E1');

            for ($row = 7; $row <= $lastRow; $row++) {
                if ($row % 2 === 0) {
                    $sheet->getStyle('A' . $row . ':G' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('FFF0FDF4'); // emerald-50 background for alternate rows
                }
            }
        }
    }
}
