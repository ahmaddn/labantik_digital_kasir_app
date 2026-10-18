@props([
    'hub' => null,
    'title' => null,
    'subtitle' => null,
])

@php
    $activeRole = session('active_role_name');

    // Pengelompokkan Hub Navigasi disinkronkan 1:1 dengan struktur Sidebar
    if ($activeRole === 'kasir') {
        // === STRUKTUR HUB UNTUK ROLE KASIR ===
        $hubs = [
            'operasional' => [
                'name' => 'Operasional & Tugas',
                'desc' => 'Daftar instruksi harian, jadwal jaga kasir, dokumentasi kegiatan, catatan serah terima, dan papan skor.',
                'items' => [
                    ['label' => 'Tugas Saya', 'route' => 'my-tasks', 'active' => request()->routeIs('my-tasks*')],
                    ['label' => 'Jadwal Jaga Kasir', 'route' => 'schedules', 'active' => request()->routeIs('schedules*')],
                    ['label' => 'Jadwal Dokumentasi', 'route' => 'documentation-schedules', 'active' => request()->routeIs('documentation-schedules*')],
                    ['label' => 'Catatan & Serah Terima', 'route' => 'cashier-notes', 'active' => request()->routeIs('cashier-notes*')],
                    ['label' => 'Papan Skor & Poin', 'route' => 'leaderboard', 'active' => request()->routeIs('leaderboard*')],
                ],
            ],
            'transaksi' => [
                'name' => 'Transaksi & Kasir',
                'desc' => 'Riwayat transaksi penjualan kasir, pencatatan hutang & kembalian, serta rekap harian & sesi.',
                'items' => [
                    ['label' => 'Riwayat Transaksi', 'route' => 'transactions', 'active' => request()->routeIs('transactions*')],
                    ['label' => 'Hutang & Kembalian', 'route' => 'debts', 'active' => request()->routeIs('debts*')],
                    ['label' => 'Rekap Harian & Sesi', 'route' => 'daily-recap', 'active' => request()->routeIs('daily-recap*')],
                ],
            ],
        ];

        // Mapping dinamis untuk kasir agar rute transaksi/keuangan/laporan masuk ke Transaksi & Kasir
        if (in_array($hub, ['keuangan', 'laporan', 'transaksi'])) {
            $currentHub = $hubs['transaksi'];
        } elseif (in_array($hub, ['operasional', 'tugas'])) {
            $currentHub = $hubs['operasional'];
        } else {
            $currentHub = $hubs[$hub] ?? null;
        }
    } else {
        // === STRUKTUR HUB UNTUK PENGELOLA & SUPERADMIN ===
        $hubs = [
            'inventaris' => [
                'name' => 'Inventaris & Produk',
                'desc' => 'Katalog produk menu, kategori produk, topping & varian, supplier rekanan, dan laporan stok fisik.',
                'items' => [
                    ['label' => 'Daftar Produk', 'route' => 'products', 'active' => request()->routeIs('products*')],
                    ['label' => 'Kategori Produk', 'route' => 'categories', 'active' => request()->routeIs('categories*')],
                    ['label' => 'Topping & Varian', 'route' => 'modifiers', 'active' => request()->routeIs('modifiers*')],
                    ['label' => 'Supplier Rekanan', 'route' => 'suppliers', 'active' => request()->routeIs('suppliers*')],
                    ['label' => 'Laporan Stok & Selisih', 'route' => 'inventory-report', 'active' => request()->routeIs('inventory-report*')],
                ],
            ],
            'keuangan' => [
                'name' => 'Keuangan & Kas',
                'desc' => 'Pencatatan mutasi buku kas internal, kas virtual non-tunai, dan pengelolaan hutang & kembalian.',
                'items' => [
                    ['label' => 'Buku Kas Internal', 'route' => 'buku-kas', 'active' => request()->routeIs('buku-kas*')],
                    ['label' => 'Kas Virtual (QRIS / Bank)', 'route' => 'virtual-cash', 'active' => request()->routeIs('virtual-cash*')],
                    ['label' => 'Hutang & Kembalian', 'route' => 'debts', 'active' => request()->routeIs('debts*')],
                ],
            ],
            'operasional' => [
                'name' => 'Operasional Kasir',
                'desc' => 'Penugasan harian, jadwal piket shift, jadwal dokumentasi, presensi kasir, catatan briefing, dan papan skor.',
                'items' => [
                    ['label' => 'Penugasan & Verifikasi', 'route' => 'tasks', 'active' => request()->routeIs('tasks*')],
                    ['label' => 'Jadwal Piket Kasir', 'route' => 'schedules', 'active' => request()->routeIs('schedules*')],
                    ['label' => 'Jadwal Dokumentasi', 'route' => 'documentation-schedules', 'active' => request()->routeIs('documentation-schedules*')],
                    ['label' => 'Presensi & Shift Kasir', 'route' => 'attendances', 'active' => request()->routeIs('attendances*')],
                    ['label' => 'Catatan & Briefing Kasir', 'route' => 'cashier-notes', 'active' => request()->routeIs('cashier-notes*')],
                    ['label' => 'Papan Skor & Poin', 'route' => 'leaderboard', 'active' => request()->routeIs('leaderboard*')],
                ],
            ],
            'laporan' => [
                'name' => 'Laporan & Bagi Hasil',
                'desc' => 'Rekap penjualan & audit, riwayat transaksi, bagi hasil supplier, bagi hasil mingguan TEFA, performa mingguan, rekap bulanan, dan rekap tahunan.',
                'items' => [
                    ['label' => 'Rekap Harian & Audit', 'route' => 'daily-recap', 'active' => request()->routeIs('daily-recap*')],
                    ['label' => 'Riwayat Transaksi', 'route' => 'transactions', 'active' => request()->routeIs('transactions*')],
                    ['label' => 'Bagi Hasil Supplier', 'route' => 'supplier-report', 'active' => request()->routeIs('supplier-report*')],
                    ['label' => 'Bagi Hasil Mingguan TEFA', 'route' => 'bagi-hasil', 'active' => request()->routeIs('bagi-hasil*')],
                    ['label' => 'Performa Mingguan', 'route' => 'weekly-performance', 'active' => request()->routeIs('weekly-performance*')],
                    ['label' => 'Rekap Bulanan', 'route' => 'monthly-recap', 'active' => request()->routeIs('monthly-recap*')],
                    ['label' => 'Rekap Tahunan', 'route' => 'yearly-recap', 'active' => request()->routeIs('yearly-recap*')],
                ],
            ],
            'pengaturan' => [
                'name' => 'Pengaturan Sistem',
                'desc' => 'Manajemen pengguna, pengelolaan jurusan & unit TEFA, dan konfigurasi sistem.',
                'items' => [
                    ['label' => 'Manajemen User', 'route' => 'users', 'active' => request()->routeIs('users*')],
                    ['label' => 'Jurusan & Unit TEFA', 'route' => 'jurusans', 'active' => request()->routeIs('jurusans*')],
                ],
            ],
        ];

        $currentHub = $hub ? ($hubs[$hub] ?? null) : null;
    }
@endphp

@if ($currentHub)
<div class="mb-6 space-y-3">
    <!-- Hub Title & Description -->
    @if ($title !== false)
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-1">
        <div>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900 dark:text-white uppercase font-outfit">
                {{ $title ?? $currentHub['name'] }}
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                {{ $subtitle ?? $currentHub['desc'] }}
            </p>
        </div>
    </div>
    @endif

    <!-- Horizontal Sub-Navigation Tabs strictly matching Sidebar Groups -->
    <nav aria-label="Sub navigasi menu {{ $currentHub['name'] }}" class="flex items-center gap-2 overflow-x-auto no-scrollbar py-1">
        @foreach ($currentHub['items'] as $item)
            @if (Route::has($item['route']))
                <a href="{{ route($item['route']) }}" wire:navigate
                    class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all duration-150 flex items-center gap-1.5 shrink-0 {{ $item['active']
                        ? 'bg-blue-600 text-white shadow-sm ring-1 ring-blue-600'
                        : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-800 hover:text-slate-900 dark:hover:text-white hover:border-slate-300 dark:hover:border-slate-700' }}">
                    <span>{{ $item['label'] }}</span>
                </a>
            @endif
        @endforeach
    </nav>
</div>
@endif
