<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\StockEntry;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DummyTransactionSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();
        if (! $admin) {
            return;
        }

        $products = Product::all();
        if ($products->isEmpty()) {
            return;
        }

        // Loop for the last 8 days to cover today, yesterday, and the weekly chart
        for ($i = 7; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dateString = $date->toDateString();

            // 1. Create Stock Entries for all products on this day
            foreach ($products as $product) {
                StockEntry::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'date' => $dateString,
                    ],
                    [
                        'opening_stock' => 100,
                        'closing_stock' => 100, // Will be updated by Transaction observer
                    ]
                );
            }

            // 2. Create random transactions on this day (between 3 to 8 transactions)
            $transactionCount = rand(3, 8);
            for ($tx = 0; $tx < $transactionCount; $tx++) {
                $product = $products->random();
                $qty = rand(1, 4);
                $reference = 'LBK-'.$date->format('Ymd').'-GST'.strtoupper(Str::random(4));

                // Random hour for transaction
                $transactedAt = $date->copy()->setHour(rand(8, 17))->setMinute(rand(0, 59))->setSecond(rand(0, 59));

                Transaction::create([
                    'jurusan_id' => $product->jurusan_id,
                    'reference' => $reference,
                    'user_id' => $admin->id,
                    'product_id' => $product->id,
                    'supplier_id' => $product->supplier_id,
                    'transacted_at' => $transactedAt,
                    'buyer_name' => 'GUEST '.rand(10, 99),
                    'quantity' => $qty,
                    'unit_price' => $product->price,
                    'unit_profit' => $product->profit,
                    'total_price' => $product->price * $qty,
                    'debt_amount' => 0,
                    'change_due' => 0,
                    'status' => 'uang_diterima',
                    'note' => 'Dummy seeded transaction',
                ]);
            }
        }
    }
}
