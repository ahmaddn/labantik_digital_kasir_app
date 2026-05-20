<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\ProductsSheetExport;
use App\Exports\Sheets\StockEntriesSheetExport;
use App\Exports\Sheets\TransactionsSheetExport;

class DailyDataExport implements WithMultipleSheets
{
    use Exportable;

    protected $date;

    public function __construct(string $date)
    {
        $this->date = $date;
    }

    public function sheets(): array
    {
        return [
            new ProductsSheetExport(),
            new StockEntriesSheetExport($this->date),
            new TransactionsSheetExport($this->date),
        ];
    }
}
