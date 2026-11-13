<?php

use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $granularRoles = [
            [
                'name' => 'pengelola_jurusan_keuangan',
                'label' => 'Pengelola Jurusan (Keuangan)',
            ],
            [
                'name' => 'pengelola_jurusan_penugasan',
                'label' => 'Pengelola Jurusan (Penugasan)',
            ],
            [
                'name' => 'pengelola_jurusan_piket',
                'label' => 'Pengelola Jurusan (Piket)',
            ],
        ];

        foreach ($granularRoles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name']],
                [
                    'id' => (string) Str::uuid(),
                    'label' => $role['label'],
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Role::whereIn('name', [
            'pengelola_jurusan_keuangan',
            'pengelola_jurusan_penugasan',
            'pengelola_jurusan_piket',
        ])->delete();
    }
};
