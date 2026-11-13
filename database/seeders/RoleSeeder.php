<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'superadmin',
                'label' => 'Superadmin',
            ],
            [
                'name' => 'pengelola_jurusan',
                'label' => 'Pengelola Jurusan',
            ],
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
            [
                'name' => 'kasir',
                'label' => 'Kasir',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['name' => $role['name']], ['label' => $role['label']]);
        }
    }
}
