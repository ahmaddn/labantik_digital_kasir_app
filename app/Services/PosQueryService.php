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
        $products = Product::where('is_active', true)
            ->whereHas('stockEntries', function ($q) use ($today) {
                $q->where('date', $today)->where('opening_stock', '>', 0);
            })
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->with(['stockEntries' => fn ($q) => $q->where('date', $today)])
            ->get();

        if ($products->isEmpty()) {
            return collect();
        }

        $productIds = $products->pluck('id');
        $soldMap = Transaction::whereIn('product_id', $productIds)
            ->whereDate('transacted_at', $today)
            ->groupBy('product_id')
            ->selectRaw('product_id, SUM(quantity) as total_sold')
            ->pluck('total_sold', 'product_id');

        $activeProductCategoryIds = $products->filter(function ($p) use ($soldMap) {
            $entry = $p->stockEntries->first();
            $sold = (int) ($soldMap[$p->id] ?? 0);

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
        $products = Product::where('is_active', true)
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->whereHas('stockEntries', function ($q) use ($today) {
                $q->where('date', $today)->where('opening_stock', '>', 0);
            })
            ->with(['category', 'stockEntries' => fn ($q) => $q->where('date', $today)])
            ->get();

        if ($products->isEmpty()) {
            return collect();
        }

        $productIds = $products->pluck('id');
        $soldMap = Transaction::whereIn('product_id', $productIds)
            ->whereDate('transacted_at', $today)
            ->groupBy('product_id')
            ->selectRaw('product_id, SUM(quantity) as total_sold')
            ->pluck('total_sold', 'product_id');

        return $products->map(function ($p) use ($soldMap) {
            $entry = $p->stockEntries->first();
            $sold = (int) ($soldMap[$p->id] ?? 0);

            return [
                'id' => $p->id,
                'name' => $p->name,
                'category' => $p->category->name ?? 'Uncategorized',
                'modal_price' => (float) $p->modal_price,
                'opening' => $entry ? $entry->opening_stock : 0,
                'sold' => $sold,
                'expected' => ($entry ? $entry->opening_stock : 0) - $sold,
                'actual' => $entry ? $entry->closing_stock : 0,
            ];
        });
    }

    public function getProductsForAlpine(string $today, ?string $activeJurusanId): Collection
    {
        $products = Product::with(['category', 'modifierGroups.modifiers', 'stockEntries' => fn ($q) => $q->where('date', $today)])
            ->where('is_active', true)
            ->whereHas('stockEntries', function ($q) use ($today) {
                $q->where('date', $today)->where('opening_stock', '>', 0);
            })
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->orderBy('name')
            ->get();

        if ($products->isEmpty()) {
            return collect();
        }

        $productIds = $products->pluck('id');
        $soldMap = Transaction::whereIn('product_id', $productIds)
            ->whereDate('transacted_at', $today)
            ->groupBy('product_id')
            ->selectRaw('product_id, SUM(quantity) as total_sold')
            ->pluck('total_sold', 'product_id');

        return $products->map(function ($p) use ($soldMap) {
            $entry = $p->stockEntries->first();
            $sold = (int) ($soldMap[$p->id] ?? 0);
            $available_stock = ($entry ? $entry->opening_stock : 0) - $sold;

            // Siapkan data modifier groups & modifiers
            $modifiersData = [];
            foreach ($p->modifierGroups as $group) {
                $mods = [];
                foreach ($group->modifiers as $mod) {
                    $mods[] = [
                        'id' => $mod->id,
                        'name' => $mod->name,
                        'price' => (int) $mod->price,
                    ];
                }
                $modifiersData[] = [
                    'id' => $group->id,
                    'name' => $group->name,
                    'min' => (int) $group->min_selection,
                    'max' => (int) $group->max_selection,
                    'options' => $mods,
                ];
            }

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
                'modifier_groups' => $modifiersData,
            ];
        });
    }
}
