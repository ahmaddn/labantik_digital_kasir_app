<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockEntry;
use Illuminate\Support\Facades\DB;

class ProductService
{
    /**
     * Delete a single product.
     * @throws \Exception
     */
    public function deleteProduct(string $productId): void
    {
        $product = Product::findOrFail($productId);
        
        if ($product->transactions()->exists()) {
            throw new \Exception("Produk '{$product->name}' tidak bisa dihapus karena memiliki riwayat transaksi. Silakan nonaktifkan status produk saja.");
        }
        
        DB::transaction(function () use ($product) {
            $product->stockEntries()->delete();
            $product->delete();
        });
    }

    /**
     * Delete multiple products.
     * @throws \Exception
     */
    public function bulkDelete(array $productIds): void
    {
        $products = Product::whereIn('id', $productIds)->get();
        
        foreach ($products as $product) {
            if ($product->transactions()->exists()) {
                throw new \Exception("Produk '{$product->name}' tidak bisa dihapus karena memiliki riwayat transaksi. Silakan nonaktifkan status produk saja.");
            }
        }
        
        DB::transaction(function () use ($productIds) {
            StockEntry::whereIn('product_id', $productIds)->delete();
            Product::whereIn('id', $productIds)->delete();
        });
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

    public function saveProduct(?string $editingId, array $data, bool $updateHistory = false): Product
    {
        if ($editingId) {
            $product = Product::find($editingId);
            if ($product) {
                $product->update($data);

                if ($updateHistory) {
                    $newPrice = $data['price'];
                    $newModal = $data['modal_price'];
                    $newProfit = $newPrice - $newModal;

                    \Illuminate\Support\Facades\DB::transaction(function () use ($product, $newPrice, $newProfit) {
                        $transactions = \App\Models\Transaction::where('product_id', $product->id)->get();
                        foreach ($transactions as $tx) {
                            $tx->update([
                                'unit_price' => $newPrice,
                                'unit_profit' => $newProfit,
                                'total_price' => $newPrice * $tx->quantity,
                                'debt_amount' => in_array($tx->status, ['belum_menerima_uang', 'uang_dipinjam']) ? ($newPrice * $tx->quantity) : 0,
                            ]);
                        }
                    });
                }

                return $product;
            }
        }
        return Product::create($data);
    }

    /**
     * Save daily stock entry for a product.
     */
    public function saveStock(string $productId, string $date, int $openingStock): void
    {
        // Defensive cast — pastikan tidak pernah null/string masuk ke kolom integer
        $openingStock = max(0, (int) $openingStock);

        StockEntry::updateOrCreate(
            [
                'product_id' => $productId,
                'date'       => $date,
            ],
            [
                'opening_stock' => $openingStock,
                'closing_stock' => $openingStock,
            ]
        );
    }
}
