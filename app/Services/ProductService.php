<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockEntry;
use Illuminate\Support\Facades\DB;

class ProductService
{
    /**
     * Delete multiple products.
     */
    public function bulkDelete(array $productIds): void
    {
        Product::whereIn('id', $productIds)->delete();
    }

    /**
     * Toggle active status for multiple products.
     *
     * @return bool New status
     */
    public function bulkToggleStatus(array $productIds): bool
    {
        return DB::transaction(function () use ($productIds) {
            $products = Product::whereIn('id', $productIds)->get();
            $allActive = $products->every(fn ($p) => $p->is_active);
            $newStatus = ! $allActive;

            Product::whereIn('id', $productIds)->update(['is_active' => $newStatus]);

            return $newStatus;
        });
    }

    /**
     * Create or update a product.
     */
    public function saveProduct(?string $editingId, array $data): void
    {
        if ($editingId) {
            $product = Product::find($editingId);
            if ($product) {
                $product->update($data);
            }
        } else {
            Product::create($data);
        }
    }

    /**
     * Save daily stock entry for a product.
     */
    public function saveStock(string $productId, string $date, int $openingStock): void
    {
        StockEntry::updateOrCreate(
            [
                'product_id' => $productId,
                'date' => $date,
            ],
            [
                'opening_stock' => $openingStock,
                'closing_stock' => $openingStock,
            ]
        );
    }
}
