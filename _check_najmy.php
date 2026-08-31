<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$txs = \App\Models\CashTransaction::where('description', 'like', '%Najmy%')
    ->orWhereHas('cashCategory', fn($q) => $q->where('name', 'like', '%Najmy%'))
    ->with('cashCategory')
    ->get();

foreach ($txs as $tx) {
    echo "ID      : {$tx->id}" . PHP_EOL;
    echo "Tanggal : {$tx->date}" . PHP_EOL;
    echo "Tipe    : {$tx->type}" . PHP_EOL;
    echo "Amount  : Rp" . number_format($tx->amount, 0, ',', '.') . PHP_EOL;
    echo "Kategori: " . ($tx->cashCategory?->name ?? 'null') . PHP_EOL;
    echo "Deskripsi: {$tx->description}" . PHP_EOL;
    echo "---" . PHP_EOL;
}
