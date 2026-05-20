<?php

namespace App\Imports\Sheets;

use App\Models\Product;
use App\Models\StockEntry;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StockEntriesSheetImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (!isset($row['nama_produk']) || empty($row['nama_produk'])) {
                continue;
            }

            $product = Product::where('name', $row['nama_produk'])->first();
            
            if ($product) {
                StockEntry::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'date' => $row['date'] ?? today()->toDateString(),
                    ],
                    [
                        'opening_stock' => $row['opening_stock'] ?? 0,
                        'closing_stock' => $row['closing_stock'] ?? 0,
                    ]
                );
            }
        }
    }
}
