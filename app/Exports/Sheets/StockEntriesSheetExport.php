<?php

namespace App\Exports\Sheets;

use App\Models\StockEntry;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class StockEntriesSheetExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $date;

    public function __construct(string $date)
    {
        $this->date = $date;
    }

    public function collection()
    {
        return StockEntry::with('product')->where('date', $this->date)->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Produk',
            'Date',
            'Opening Stock',
            'Closing Stock',
        ];
    }

    /**
     * @param StockEntry $entry
     */
    public function map($entry): array
    {
        return [
            $entry->id,
            $entry->product ? $entry->product->name : '',
            $entry->date,
            $entry->opening_stock,
            $entry->closing_stock,
        ];
    }

    public function title(): string
    {
        return 'StockEntries';
    }
}
