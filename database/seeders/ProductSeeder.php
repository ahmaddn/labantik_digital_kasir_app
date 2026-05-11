<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Makanan' => [
                ['Nasi Bakar', 3500, 200],
                ['Burger', 3500, 200],
                ['Mie', 3000, 200],
                ['Dimsum', 3000, 200],
                ['Risol', 2500, 200],
                ['Cilokbas', 3000, 200],
                ['Donut', 3000, 200],
                ['Puding', 3000, 200],
                ['Ubi Lumer', 2000, 200],
                ['Pizza', 3000, 200],
                ['Kue Sus', 3000, 200],
                ['Cromboloni', 3000, 200],
                ['Roti Fatih', 3000, 200],
            ],
            'Eskrim' => [
                ['Eskrim Milo', 2000, 400],
                ['Eskrim Miki', 2000, 400],
                ['Eskrim Jagung', 3000, 600],
                ['Eskrim Two Colors', 3000, 1200],
                ['Eskrim Taro', 3000, 840],
                ['Eskrim Og Dika', 3000, 300],
                ['Eskrim Oreo Dika', 3000, 300],
                ['Eskrim Semangka', 2000, 450],
                ['Eskrim Grape', 2000, 400],
                ['Eskrim Yoghurt', 3000, 600],
                ['Eskrim Strawberry Crispy', 4000, 840],
                ['Eskrim Melon', 2000, 625],
                ['Eskrim Jeruk', 2000, 440],
            ],
            'Minuman' => [
                ['Teh Gelas', 1000, 209],
                ['Teh Rio', 1000, 167],
                ['Teh Kotak', 4000, 709],
                ['Le Minerale & TGM', 3000, 1700],
                ['Aquviva', 3000, 1042],
                ['Mountea', 1000, 188],
                ['Ale-ale', 1000, 167],
                ['Power F', 1000, 167],
                ['Kopi Naga', 1000, 188],
                ['Cleo', 3000, 646],
                ['Okky Jelly', 1000, 167],
                ['Pucuk', 3000, 900],
                ['Floridina', 2000, 500],
                ['Aqua Gelas', 1000, 167],
            ],
            'Snack' => [
                ['Kerupuk', 1000, 200],
                ['Kerupuk Besar', 3000, 500],
                ['Beng Beng', 2500, 353],
                ['Choco Pie', 2000, 282],
                ['Sari Gandum', 2000, 500],
                ['Sari Gandum Coklat', 2000, 334],
                ['Better', 2000, 410],
                ['SIIP', 500, 50],
                ['All Ciki', 2000, 444],
                ['Permen & Go Potato', 500, 360],
                ['Malkis 2', 1000, 150],
                ['Malkis 3', 2000, 1100],
                ['Ritz', 1000, 250],
                ['Kalpa', 1000, 250],
                ['Yupi', 500, 63],
                ['Oreo', 2000, 750],
                ['Superstar', 1000, 125],
                ['Rolls', 500, 50],
                ['Tricks', 1000, 150],
                ['Astor', 1000, 125],
                ['Chocolatos', 500, 47],
                ['Nextar', 1500, 385],
                ['Dilan', 1000, 250],
                ['Slay Olay', 1000, 250],
                ['Choki-Choki', 500, 100],
                ['Apetito', 1000, 250],
                ['Good Time', 1000, 250],
                ['Kacang Koro', 500, 100],
                ['Kripca', 1000, 250],
                ['Citato', 1000, 250],
                ['Es Mambo', 1000, 167],
            ],
        ];

        foreach ($data as $categoryName => $products) {
            $category = ProductCategory::where('name', $categoryName)->first();
            foreach ($products as $p) {
                Product::create([
                    'category_id' => $category->id,
                    'name' => $p[0],
                    'label' => $p[0] . ' - Rp' . number_format($p[1], 0, ',', '.'),
                    'price' => $p[1],
                    'modal_price' => $p[1] - $p[2],
                    'profit' => $p[2],
                    'is_active' => true,
                ]);
            }
        }
    }
}
