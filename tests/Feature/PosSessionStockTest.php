<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\StockEntry;
use App\Models\User;
use App\Services\PosSessionService;
use App\Livewire\Pos\Kasir;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PosSessionStockTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_last_session_stocks_pulls_most_recent_closing_stock_per_product(): void
    {
        $category = ProductCategory::create(['name' => 'Snack']);

        $productA = Product::create([
            'name' => 'Produk A',
            'price' => 5000,
            'modal_price' => 3000,
            'category_id' => $category->id,
            'is_active' => true,
        ]);

        $productB = Product::create([
            'name' => 'Produk B',
            'price' => 10000,
            'modal_price' => 7000,
            'category_id' => $category->id,
            'is_active' => true,
        ]);

        $productC = Product::create([
            'name' => 'Produk C (Barang Masuk Rabu)',
            'price' => 15000,
            'modal_price' => 10000,
            'category_id' => $category->id,
            'is_active' => true,
        ]);

        // Tuesday entries (2026-09-01)
        StockEntry::create([
            'product_id' => $productA->id,
            'date' => '2026-09-01',
            'opening_stock' => 20,
            'closing_stock' => 20,
        ]);

        StockEntry::create([
            'product_id' => $productB->id,
            'date' => '2026-09-01',
            'opening_stock' => 20,
            'closing_stock' => 15,
        ]);

        // Wednesday entry (2026-09-02) — Only Product C restocked/adjusted
        StockEntry::create([
            'product_id' => $productC->id,
            'date' => '2026-09-02',
            'opening_stock' => 10,
            'closing_stock' => 10,
        ]);

        $service = app(PosSessionService::class);
        $lastStocks = $service->getLastSessionStocks('2026-09-03', null);
        $yesterdayStocks = $service->getYesterdayStocks('2026-09-03', null);
        $lastDetails = $service->getLastSessionDetails('2026-09-03', null);

        // Verify Thursday (2026-09-03) gets Product A & B from Tuesday and Product C from Wednesday
        $this->assertEquals(20, $lastStocks[$productA->id]);
        $this->assertEquals(15, $lastStocks[$productB->id]);
        $this->assertEquals(10, $lastStocks[$productC->id]);

        // Yesterday (2026-09-02) strictly has only Product C
        $this->assertArrayNotHasKey($productA->id, $yesterdayStocks);
        $this->assertEquals(10, $yesterdayStocks[$productC->id]);

        // Last session details show exact dates per product
        $this->assertEquals(20, $lastDetails[$productA->id]['stock']);
        $this->assertEquals(10, $lastDetails[$productC->id]['stock']);
    }
}
