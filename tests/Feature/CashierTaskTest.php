<?php

namespace Tests\Feature;

use App\Livewire\Management\CashierTasks;
use App\Models\CashierTask;
use App\Models\CashierSchedule;
use App\Models\Jurusan;
use App\Models\Role;
use App\Models\TaskCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CashierTaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_create_cashier_task_with_custom_category_priority_and_deadline(): void
    {
        $jurusan = Jurusan::create(['name' => 'Test Jurusan']);
        $user = User::factory()->create(['name' => 'Kasir Test']);

        $task = CashierTask::create([
            'jurusan_id' => $jurusan->id,
            'assigned_to' => $user->id,
            'date' => now()->toDateString(),
            'task_name' => 'Cek stok harian',
            'description' => 'Periksa level stok produk di kasir',
            'deadline_at' => now()->addHours(4),
            'status' => 'new',
            'priority' => 'high',
            'category' => 'Rutin Harian',
            'is_routine' => true,
            'created_by' => $user->id,
        ]);

        $this->assertDatabaseHas('cashier_tasks', [
            'id' => $task->id,
            'category' => 'Rutin Harian',
            'priority' => 'high',
            'status' => 'new',
            'is_routine' => true,
        ]);

        $this->assertSame('Rutin Harian', $task->category);
        $this->assertSame('high', $task->priority);
        $this->assertSame('new', $task->status);
        $this->assertTrue($task->is_routine);
        $this->assertSame('Tinggi', $task->priority_label);
        $this->assertSame('Baru', $task->status_label);
    }

    public function test_it_can_create_and_retrieve_task_categories_for_selected_jurusan(): void
    {
        $jurusan = Jurusan::create(['name' => 'Test Jurusan']);
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->withSession([
                'active_jurusan_id' => $jurusan->id,
                'active_role_name' => 'superadmin',
            ])
            ->test(CashierTasks::class)
            ->set('newCategoryName', 'Operasional')
            ->call('storeCategory');

        $this->assertDatabaseHas('task_categories', [
            'jurusan_id' => $jurusan->id,
            'name' => 'Operasional',
        ]);

        $this->assertSame('Operasional', TaskCategory::where('jurusan_id', $jurusan->id)->first()->name);
    }

    public function test_assignee_mode_scheduled_returns_only_today_scheduled_cashiers(): void
    {
        $jurusan = Jurusan::create(['name' => 'Test Jurusan']);
        $role = Role::create(['name' => 'kasir', 'label' => 'Kasir']);
        $scheduledCashier = User::factory()->create(['name' => 'Scheduled Kasir']);
        $unscheduledCashier = User::factory()->create(['name' => 'Unscheduled Kasir']);

        $scheduledCashier->roles()->attach($role->id, ['jurusan_id' => $jurusan->id]);
        $unscheduledCashier->roles()->attach($role->id, ['jurusan_id' => $jurusan->id]);

        CashierSchedule::create([
            'jurusan_id' => $jurusan->id,
            'user_id' => $scheduledCashier->id,
            'date' => now()->toDateString(),
        ]);

        Livewire::actingAs($scheduledCashier)
            ->withSession([
                'active_jurusan_id' => $jurusan->id,
                'active_role_name' => 'superadmin',
            ])
            ->test(CashierTasks::class)
            ->set('assigneeMode', 'scheduled')
            ->assertViewHas('cashiers', function ($cashiers) use ($scheduledCashier, $unscheduledCashier) {
                return $cashiers->contains('id', $scheduledCashier->id)
                    && ! $cashiers->contains('id', $unscheduledCashier->id);
            });
    }
}
