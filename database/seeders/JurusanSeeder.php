<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use Illuminate\Database\Seeder;

class JurusanSeeder extends Seeder
{
    public function run(): void
    {
        $jurusans = ['RPL', 'TKJ', 'Otomotif'];

        foreach ($jurusans as $name) {
            Jurusan::updateOrCreate(['name' => $name]);
        }
    }
}
