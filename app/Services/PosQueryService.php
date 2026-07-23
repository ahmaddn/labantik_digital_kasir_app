<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\StockEntry;
use App\Models\Transaction;
use Illuminate\Support\Collection;

class PosQueryService
{
    public function getCategories(string $today, ?string $activeJurusanId): Collection
    {
        $activeProductCategoryIds = Product::where('is_active', true)
            ->whereHas('stockEntries', function ($q) use ($today) {
                $q->where('date', $today)->where('opening_stock', '>', 0);
            })
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->get()
            ->filter(function ($p) use ($today) {
                $entry = StockEntry::where('product_id', $p->id)->where('date', $today)->first();
                $sold = Transaction::where('product_id', $p->id)
                    ->whereDate('transacted_at', $today)
                    ->sum('quantity');

                return (($entry ? $entry->opening_stock : 0) - $sold) > 0;
            })
            ->pluck('category_id')
            ->filter()
            ->unique();

        return ProductCategory::whereIn('id', $activeProductCategoryIds)
            ->orderBy('name')
            ->get();
    }

    public function getStockComparison(string $today, ?string $activeJurusanId): Collection
    {
        return Product::where('is_active', true)
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->whereHas('stockEntries', function ($q) use ($today) {
                $q->where('date', $today)->where('opening_stock', '>', 0);
            })
            ->with(['stockEntries' => fn ($q) => $q->where('date', $today)])
            ->get()
            ->map(function ($p) use ($today) {
                $entry = $p->stockEntries->first();
                $sold = Transaction::where('product_id', $p->id)
                    ->whereDate('transacted_at', $today)
                    ->sum('quantity');

                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'category' => $p->category->name ?? 'Uncategorized',
                    'opening' => $entry ? $entry->opening_stock : 0,
                    'sold' => $sold,
                    'expected' => ($entry ? $entry->opening_stock : 0) - $sold,
                    'actual' => $entry ? $entry->closing_stock : 0,
                ];
            });
    }

    public function getProductsForAlpine(string $today, ?string $activeJurusanId): Collection
    {
        return Product::with(['category', 'stockEntries' => fn ($q) => $q->where('date', $today)])
            ->where('is_active', true)
            ->whereHas('stockEntries', function ($q) use ($today) {
                $q->where('date', $today)->where('opening_stock', '>', 0);
            })
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->orderBy('name')
            ->get()
            ->map(function ($p) use ($today) {
                $entry = $p->stockEntries->first();
                $sold = Transaction::where('product_id', $p->id)
                    ->whereDate('transacted_at', $today)
                    ->sum('quantity');
                $available_stock = ($entry ? $entry->opening_stock : 0) - $sold;

                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'price' => (int) $p->price,
                    'profit' => (int) $p->profit,
                    'supplier_id' => $p->supplier_id,
                    'category_id' => $p->category_id,
                    'category_name' => $p->category->name ?? 'Uncategorized',
                    'initial' => substr($p->name, 0, 1),
                    'available_stock' => $available_stock,
                ];
            });
    }
}
