<?php

namespace App\Imports;

use App\Imports\Sheets\ProductsSheetImport;
use App\Imports\Sheets\StockEntriesSheetImport;
use App\Imports\Sheets\TransactionsSheetImport;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DailyDataImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Products' => new ProductsSheetImport(),
            'StockEntries' => new StockEntriesSheetImport(),
            'Transactions' => new TransactionsSheetImport(),
        ];
    }
}
