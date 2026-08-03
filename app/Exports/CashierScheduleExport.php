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
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CashierScheduleExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
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
            'Email',
            'Unit TEFA / Jurusan',
            'Catatan / Keterangan',
        ];
    }

    public function map($schedule): array
    {
        return [
            $schedule->date->translatedFormat('l'),
            $schedule->date->format('d-m-Y'),
            $schedule->user->name,
            $schedule->user->email,
            $schedule->jurusan ? $schedule->jurusan->name : 'Semua',
            $schedule->notes ?: '-',
        ];
    }

    public function title(): string
    {
        return 'Jadwal Kasir';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
