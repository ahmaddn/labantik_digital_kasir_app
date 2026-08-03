<?php

namespace Tests\Feature;

use App\Models\CashierTask;
use App\Models\Jurusan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
