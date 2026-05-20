<?php

namespace App\Imports\Sheets;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Supplier;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsSheetImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (!isset($row['nama']) || empty($row['nama'])) {
                continue;
            }

            // Get or create category
            $categoryId = null;
            if (!empty($row['kategori'])) {
                $category = ProductCategory::firstOrCreate(['name' => $row['kategori']]);
                $categoryId = $category->id;
            }

            // Get or create supplier
            $supplierId = null;
            if (!empty($row['supplier'])) {
                $supplier = Supplier::firstOrCreate(['name' => $row['supplier']]);
                $supplierId = $supplier->id;
            }

            // Update or create product
            Product::updateOrCreate(
                ['name' => $row['nama']],
                [
                    'category_id' => $categoryId,
                    'supplier_id' => $supplierId,
                    'price' => $row['harga'] ?? 0,
                    'modal_price' => $row['harga_modal'] ?? 0,
                    'is_active' => isset($row['is_active']) ? (bool)$row['is_active'] : true,
                ]
            );
        }
    }
}
