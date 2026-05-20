<?php

namespace App\Exports\Sheets;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class ProductsSheetExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    public function collection()
    {
        return Product::with(['category', 'supplier'])->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Kategori',
            'Supplier',
            'Nama',
            'Harga',
            'Harga Modal',
            'Profit',
            'Is Active',
        ];
    }

    /**
     * @param Product $product
     */
    public function map($product): array
    {
        return [
            $product->id,
            $product->category ? $product->category->name : '',
            $product->supplier ? $product->supplier->name : '',
            $product->name,
            $product->price,
            $product->modal_price,
            $product->profit,
            $product->is_active ? 1 : 0,
        ];
    }

    public function title(): string
    {
        return 'Products';
    }
}
