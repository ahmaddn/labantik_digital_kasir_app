<?php

namespace Tests\Feature\Services;

use App\Models\DailyRecap;
use App\Models\Jurusan;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\StockEntry;
use App\Services\PosSessionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosSessionServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array{jurusan: Jurusan, product: Product}
     */
    private function createContext(): array
    {
        $jurusan = Jurusan::query()->create(['name' => 'Jurusan Test']);

        $category = ProductCategory::query()->create([
            'jurusan_id' => $jurusan->id,
            'name' => 'Makanan',
        ]);

        $product = Product::query()->create([
            'jurusan_id' => $jurusan->id,
            'category_id' => $category->id,
            'name' => 'Kerupuk',
            'label' => 'Kerupuk - Rp1000',
            'price' => 1000,
            'modal_price' => 200,
            'is_active' => true,
        ]);

        return compact('jurusan', 'product');
    }

    public function test_detect_unfinished_session_ignores_archived_finished_recap(): void
    {
        ['jurusan' => $jurusan, 'product' => $product] = $this->createContext();
        $sessionDate = '2026-07-31';

        StockEntry::query()->create([
            'product_id' => $product->id,
            'date' => $sessionDate,
            'opening_stock' => 10,
            'closing_stock' => 5,
        ]);

        $recap = DailyRecap::query()->create([
            'jurusan_id' => $jurusan->id,
            'date' => $sessionDate,
            'month_week' => 5,
            'month_name' => 'Juli',
            'actual_cash' => 50000,
        ]);
        $recap->forceFill(['is_archived' => true])->save();

        $service = app(PosSessionService::class);

        $this->assertNull($service->detectUnfinishedSession('2026-08-01', $jurusan->id));
    }

    public function test_fix_unfinished_session_updates_archived_recap_instead_of_inserting_duplicate(): void
    {
        ['jurusan' => $jurusan, 'product' => $product] = $this->createContext();
        $sessionDate = '2026-07-31';

        StockEntry::query()->create([
            'product_id' => $product->id,
            'date' => $sessionDate,
            'opening_stock' => 10,
            'closing_stock' => 10,
        ]);

        $recap = DailyRecap::query()->create([
            'jurusan_id' => $jurusan->id,
            'date' => $sessionDate,
            'month_week' => 5,
            'month_name' => 'Juli',
            'actual_cash' => 0,
        ]);
        $recap->forceFill(['is_archived' => true])->save();

        $service = app(PosSessionService::class);
        $service->fixUnfinishedSession($sessionDate, collect([$product]), $jurusan->id);

        $this->assertSame(1, DailyRecap::withoutGlobalScope('active')->count());

        $recap = DailyRecap::withoutGlobalScope('active')->first();
        $this->assertSame(1, $recap->actual_cash);
        $this->assertSame('Auto-finished by system (Forgot to click finish)', $recap->cash_note);
    }
}
