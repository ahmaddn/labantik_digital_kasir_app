<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Transaction;

$txs = Transaction::whereNull('reference')->get();
echo "Backfilling " . $txs->count() . " transactions...\n";
foreach ($txs as $tx) {
    $tx->update(['reference' => 'OLD-' . str_pad($tx->id, 6, '0', STR_PAD_LEFT)]);
}
echo "Done.\n";
