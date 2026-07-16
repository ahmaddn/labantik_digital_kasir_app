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
                'name' => 'kasir',
                'label' => 'Kasir',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['name' => $role['name']], ['label' => $role['label']]);
        }
    }
}
