<?php

namespace App\Imports\Sheets;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\Transaction;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class TransactionsSheetImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (!isset($row['reference']) || empty($row['reference'])) {
                continue;
            }

            // Avoid duplication based on reference
            $existingTx = Transaction::where('reference', $row['reference'])->first();
            if ($existingTx) {
                continue; // Skip if already exists
            }

            $productId = null;
            if (!empty($row['nama_produk'])) {
                $product = Product::where('name', $row['nama_produk'])->first();
                if ($product) {
                    $productId = $product->id;
                }
            }

            $supplierId = null;
            if (!empty($row['nama_supplier'])) {
                $supplier = Supplier::where('name', $row['nama_supplier'])->first();
                if ($supplier) {
                    $supplierId = $supplier->id;
                }
            }

            $transactedAt = null;
            if (!empty($row['transacted_at'])) {
                try {
                    $transactedAt = Carbon::parse($row['transacted_at']);
                } catch (\Exception $e) {
                    $transactedAt = now();
                }
            } else {
                $transactedAt = now();
            }

            Transaction::create([
                'product_id' => $productId,
                'supplier_id' => $supplierId,
                'user_id' => auth()->id() ?? 1, // Fallback to 1 if no auth
                'reference' => $row['reference'],
                'transacted_at' => $transactedAt,
                'buyer_name' => $row['buyer_name'],
                'quantity' => $row['quantity'] ?? 1,
                'unit_price' => $row['unit_price'] ?? 0,
                'unit_profit' => $row['unit_profit'] ?? 0,
                'total_price' => $row['total_price'] ?? 0,
                'debt_amount' => $row['debt_amount'] ?? 0,
                'change_due' => $row['change_due'] ?? 0,
                'status' => $row['status'] ?? 'uang_diterima',
                'note' => $row['note'] ?? null,
            ]);
        }
    }
}
