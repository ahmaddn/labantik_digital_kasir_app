<?php

namespace Tests\Feature\Finance;

use App\Livewire\Finance\CashManagement;
use App\Models\CashCategory;
use App\Models\CashTransaction;
use App\Models\Jurusan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use Tests\TestCase;

class CashManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_filter_month_updates_summary_balances(): void
    {
        Cache::flush();

        $jurusan = Jurusan::query()->create(['name' => 'Jurusan Test']);
        $category = CashCategory::query()->create([
            'jurusan_id' => $jurusan->id,
            'name' => 'Operasional',
        ]);
        $user = User::factory()->create();

        session([
            'active_jurusan_id' => $jurusan->id,
        ]);

        CashTransaction::query()->create([
            'jurusan_id' => $jurusan->id,
            'date' => '2026-07-10',
            'cash_type' => 'modal',
            'cash_category_id' => $category->id,
            'type' => 'income',
            'amount' => 100000,
            'description' => 'Pemasukan Juli',
        ]);

        CashTransaction::query()->create([
            'jurusan_id' => $jurusan->id,
            'date' => '2026-07-15',
            'cash_type' => 'modal',
            'cash_category_id' => $category->id,
            'type' => 'expense',
            'amount' => 30000,
            'description' => 'Pengeluaran Juli',
        ]);

        CashTransaction::query()->create([
            'jurusan_id' => $jurusan->id,
            'date' => '2026-08-05',
            'cash_type' => 'modal',
            'cash_category_id' => $category->id,
            'type' => 'income',
            'amount' => 500000,
            'description' => 'Pemasukan Agustus',
        ]);

        Livewire::actingAs($user)
            ->withSession(['active_jurusan_id' => $jurusan->id])
            ->test(CashManagement::class)
            ->set('filterMonth', '2026-07')
            ->assertSee('Rp70.000');
    }
}
