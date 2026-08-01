<?php

namespace Tests\Feature\Finance;

use App\Livewire\Finance\MonthlyClosing;
use App\Models\CashTransaction;
use App\Models\Jurusan;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Role;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class MonthlyClosingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array{jurusan: Jurusan, user: User, product: Product}
     */
    private function createFinanceContext(): array
    {
        $jurusan = Jurusan::query()->create(['name' => 'Jurusan Test']);
        $role = Role::query()->create([
            'name' => 'pengelola_jurusan',
            'label' => 'Pengelola Jurusan',
        ]);

        $user = User::factory()->create([
            'points' => 5,
            'pending_points' => 10,
        ]);

        \DB::table('role_user')->insert([
            'id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'role_id' => $role->id,
            'jurusan_id' => $jurusan->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        session([
            'active_role_id' => $role->id,
            'active_role_name' => 'pengelola_jurusan',
            'active_role_label' => 'Pengelola Jurusan',
            'active_jurusan_id' => $jurusan->id,
        ]);

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

        return compact('jurusan', 'user', 'product');
    }

    private function createTransaction(Jurusan $jurusan, Product $product, User $user, Carbon $date): Transaction
    {
        return Transaction::query()->create([
            'jurusan_id' => $jurusan->id,
            'user_id' => $user->id,
            'product_id' => $product->id,
            'transacted_at' => $date,
            'quantity' => 1,
            'unit_price' => 1000,
            'unit_profit' => 800,
            'total_price' => 1000,
            'status' => 'uang_diterima',
        ]);
    }

    public function test_cannot_close_current_month_before_last_week(): void
    {
        Carbon::setTestNow('2026-08-10 10:00:00');

        ['jurusan' => $jurusan, 'user' => $user, 'product' => $product] = $this->createFinanceContext();
        $this->createTransaction($jurusan, $product, $user, now());

        Livewire::actingAs($user)
            ->test(MonthlyClosing::class)
            ->set('selectedMonth', '2026-08')
            ->assertSet('canClose', false)
            ->call('closeMonth')
            ->assertSet('isClosed', false);
    }

    public function test_can_close_current_month_during_last_week(): void
    {
        Carbon::setTestNow('2026-08-28 10:00:00');

        ['jurusan' => $jurusan, 'user' => $user, 'product' => $product] = $this->createFinanceContext();
        $this->createTransaction($jurusan, $product, $user, now());

        Livewire::actingAs($user)
            ->test(MonthlyClosing::class)
            ->set('selectedMonth', '2026-08')
            ->assertSet('canClose', true)
            ->set('carryForwardModal', 0)
            ->set('carryForwardProfit', 0)
            ->call('closeMonth')
            ->assertSet('isClosed', true);

        $this->assertTrue(
            Transaction::withoutGlobalScope('active')
                ->where('jurusan_id', $jurusan->id)
                ->whereYear('transacted_at', 2026)
                ->whereMonth('transacted_at', 8)
                ->where('is_archived', true)
                ->exists()
        );

        Carbon::setTestNow();
    }

    public function test_can_close_past_month_anytime(): void
    {
        Carbon::setTestNow('2026-08-10 10:00:00');

        ['jurusan' => $jurusan, 'user' => $user, 'product' => $product] = $this->createFinanceContext();
        $this->createTransaction($jurusan, $product, $user, Carbon::parse('2026-07-15'));

        Livewire::actingAs($user)
            ->test(MonthlyClosing::class)
            ->set('selectedMonth', '2026-07')
            ->assertSet('canClose', true)
            ->set('carryForwardModal', 0)
            ->set('carryForwardProfit', 0)
            ->call('closeMonth')
            ->assertSet('isClosed', true);

        Carbon::setTestNow();
    }

    public function test_can_cancel_closing_and_restore_transactions(): void
    {
        Carbon::setTestNow('2026-08-28 10:00:00');

        ['jurusan' => $jurusan, 'user' => $user, 'product' => $product] = $this->createFinanceContext();
        $this->createTransaction($jurusan, $product, $user, now());

        Livewire::actingAs($user)
            ->test(MonthlyClosing::class)
            ->set('selectedMonth', '2026-08')
            ->set('carryForwardModal', 5000)
            ->set('carryForwardProfit', 2000)
            ->call('closeMonth')
            ->assertSet('isClosed', true)
            ->call('cancelClosing')
            ->assertSet('isClosed', false);

        $this->assertFalse(
            Transaction::withoutGlobalScope('active')
                ->where('jurusan_id', $jurusan->id)
                ->whereYear('transacted_at', 2026)
                ->whereMonth('transacted_at', 8)
                ->where('is_archived', true)
                ->exists()
        );

        $this->assertDatabaseMissing('monthly_closing_records', [
            'jurusan_id' => $jurusan->id,
            'period' => '2026-08',
        ]);

        $this->assertFalse(
            CashTransaction::withoutGlobalScope('active')
                ->where('jurusan_id', $jurusan->id)
                ->whereDate('date', '2026-09-01')
                ->exists()
        );

        $user->refresh();
        $this->assertSame(5, $user->points);
        $this->assertSame(10, $user->pending_points);

        Carbon::setTestNow();
    }

    public function test_cannot_cancel_when_next_month_has_transactions(): void
    {
        Carbon::setTestNow('2026-08-28 10:00:00');

        ['jurusan' => $jurusan, 'user' => $user, 'product' => $product] = $this->createFinanceContext();
        $this->createTransaction($jurusan, $product, $user, Carbon::parse('2026-08-15'));

        Livewire::actingAs($user)
            ->test(MonthlyClosing::class)
            ->set('selectedMonth', '2026-08')
            ->set('carryForwardModal', 0)
            ->set('carryForwardProfit', 0)
            ->call('closeMonth')
            ->assertSet('isClosed', true);

        $this->createTransaction($jurusan, $product, $user, Carbon::parse('2026-09-02'));

        Livewire::actingAs($user)
            ->test(MonthlyClosing::class)
            ->set('selectedMonth', '2026-08')
            ->assertSet('canCancel', false)
            ->call('cancelClosing')
            ->assertSet('isClosed', true);

        Carbon::setTestNow();
    }
}
