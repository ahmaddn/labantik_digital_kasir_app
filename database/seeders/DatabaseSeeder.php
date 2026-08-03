<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            JurusanSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            ProductCategorySeeder::class,
            ProductSeeder::class,
            TaskCategorySeeder::class,
        ]);

        // Link default admin to all roles/jurusans for testing
        $admin = User::where('email', 'admin@gmail.com')->first();
        if ($admin) {
            $superadminRole = Role::where('name', 'superadmin')->first();
            $pengelolaRole = Role::where('name', 'pengelola_jurusan')->first();
            $kasirRole = Role::where('name', 'kasir')->first();

            $jurusans = Jurusan::all();

            // Assign Superadmin (jurusan_id = null)
            if ($superadminRole) {
                \DB::table('role_user')->insert([
                    'id' => (string) Str::uuid(),
                    'user_id' => $admin->id,
                    'role_id' => $superadminRole->id,
                    'jurusan_id' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Assign Pengelola Jurusan & Kasir for each Jurusan
            foreach ($jurusans as $jurusan) {
                if ($pengelolaRole) {
                    \DB::table('role_user')->insert([
                        'id' => (string) Str::uuid(),
                        'user_id' => $admin->id,
                        'role_id' => $pengelolaRole->id,
                        'jurusan_id' => $jurusan->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                if ($kasirRole) {
                    \DB::table('role_user')->insert([
                        'id' => (string) Str::uuid(),
                        'user_id' => $admin->id,
                        'role_id' => $kasirRole->id,
                        'jurusan_id' => $jurusan->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
