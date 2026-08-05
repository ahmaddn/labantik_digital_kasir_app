<?php

namespace App\Exports;

use App\Models\LabantikRegistration;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LabantikCandidatesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $candidates;
    private $index = 0;

    public function __construct($candidates)
    {
        $this->candidates = $candidates;
    }

    public function collection()
    {
        return $this->candidates;
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal Daftar',
            'Nama Lengkap',
            'Kelas',
            'Jurusan Tujuan',
            'No HP / WhatsApp',
            'No HP Orang Tua',
            'Alamat Rumah',
            'Alasan & Motivasi Masuk',
            'Riwayat Penyakit Bawaan',
        ];
    }

    public function map($candidate): array
    {
        $this->index++;
        return [
            $this->index,
            $candidate->created_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i'),
            $candidate->full_name,
            $candidate->class_name,
            $candidate->jurusan ? $candidate->jurusan->name : 'Global',
            $candidate->phone_number,
            $candidate->parent_phone_number,
            $candidate->address,
            $candidate->reason,
            $candidate->illness_history ?: 'Tidak ada',
        ];
    }
}
