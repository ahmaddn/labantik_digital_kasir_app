<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Transaction;

class GlobalSearch extends Component
{
    public $search = '';

    public function getResults()
    {
        \Log::info('Global Search query: ' . $this->search);
        if (strlen($this->search) < 2) {
            return [];
        }

        $menus = [
            ['name' => 'Dashboard Overview', 'url' => route('dashboard'), 'icon' => 'layout-dashboard', 'type' => 'Menu'],
            ['name' => 'Buka Mode Kasir', 'url' => route('kasir'), 'icon' => 'shopping-cart', 'type' => 'Menu'],
            ['name' => 'History Transaksi', 'url' => route('transactions'), 'icon' => 'history', 'type' => 'Menu'],
            ['name' => 'Rekap Harian & Audit', 'url' => route('daily-recap'), 'icon' => 'receipt', 'type' => 'Menu'],
            ['name' => 'Manajemen Produk', 'url' => route('products'), 'icon' => 'package', 'type' => 'Menu'],
            ['name' => 'Kategori Produk', 'url' => route('categories'), 'icon' => 'tags', 'type' => 'Menu'],
            ['name' => 'Manajemen Supplier', 'url' => route('suppliers'), 'icon' => 'truck', 'type' => 'Menu'],
            ['name' => 'Hutang & Kembalian', 'url' => route('debts'), 'icon' => 'users', 'type' => 'Menu'],
            ['name' => 'Rekap Bulanan', 'url' => route('monthly-recap'), 'icon' => 'bar-chart-3', 'type' => 'Menu'],
            ['name' => 'Rekap Tahunan', 'url' => route('yearly-recap'), 'icon' => 'pie-chart', 'type' => 'Menu'],
            ['name' => 'Laporan Stok & Selisih', 'url' => route('inventory-report'), 'icon' => 'clipboard-check', 'type' => 'Menu'],
            ['name' => 'Bagi Hasil Mingguan', 'url' => route('bagi-hasil'), 'icon' => 'wallet', 'type' => 'Menu'],
        ];

        $filteredMenus = array_filter($menus, function($menu) {
            return str_contains(strtolower($menu['name']), strtolower($this->search));
        });

        $products = Product::where('name', 'like', "%{$this->search}%")
            ->limit(5)
            ->get()
            ->map(fn($p) => [
                'name' => $p->name,
                'url' => route('products') . '?highlight=' . $p->id,
                'icon' => 'package-search',
                'type' => 'Produk',
                'price' => 'Rp ' . number_format($p->price, 0, ',', '.')
            ]);

        $transactions = Transaction::where('reference', 'like', "%{$this->search}%")
            ->orWhere('buyer_name', 'like', "%{$this->search}%")
            ->limit(3)
            ->get()
            ->map(fn($t) => [
                'name' => $t->reference . ' (' . ($t->buyer_name ?: 'GUEST') . ')',
                'url' => route('transactions') . '?highlight=' . urlencode($t->reference),
                'icon' => 'receipt',
                'type' => 'Transaksi',
                'date' => $t->transacted_at->format('d/m/Y H:i')
            ]);

        $suppliers = \App\Models\Supplier::where('name', 'like', "%{$this->search}%")
            ->limit(3)
            ->get()
            ->map(fn($s) => [
                'name' => $s->name,
                'url' => route('suppliers') . '?highlight=' . $s->id,
                'icon' => 'truck',
                'type' => 'Supplier'
            ]);

        return array_merge($filteredMenus, $products->toArray(), $transactions->toArray(), $suppliers->toArray());
    }

    public function render()
    {
        return view('livewire.global-search', [
            'results' => $this->getResults()
        ]);
    }
}
