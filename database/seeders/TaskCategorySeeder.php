<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use App\Models\TaskCategory;
use Illuminate\Database\Seeder;

class TaskCategorySeeder extends Seeder
{
    public function run(): void
    {
        $defaultCategories = [
            'Operasional',
            'Laporan Harian',
            'Pengisian Stok',
            'Pembersihan',
            'Layanan Pelanggan',
        ];

        Jurusan::all()->each(function (Jurusan $jurusan) use ($defaultCategories) {
            foreach ($defaultCategories as $categoryName) {
                TaskCategory::updateOrCreate([
                    'jurusan_id' => $jurusan->id,
                    'name' => $categoryName,
                ], [
                    'created_by' => null,
                ]);
            }
        });
    }
}
