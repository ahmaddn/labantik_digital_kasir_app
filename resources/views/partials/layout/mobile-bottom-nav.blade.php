<!-- Mobile Bottom Navigation Bar (Mobile-First Ergonomics) -->
<nav class="lg:hidden fixed bottom-0 inset-x-0 z-40 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-t border-slate-200/80 dark:border-slate-800/80 px-2 py-1.5 flex items-center justify-around shadow-[0_-4px_20px_rgba(0,0,0,0.05)] dark:shadow-[0_-4px_20px_rgba(0,0,0,0.4)]" aria-label="Navigasi Utama Mobile">
    
    <!-- 1. Beranda -->
    <a href="{{ route('dashboard') }}"
        class="flex flex-col items-center justify-center min-w-[56px] min-h-[48px] py-1 px-2 rounded-xl transition-colors {{ request()->routeIs('dashboard') ? 'text-blue-600 dark:text-blue-400 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200' }}">
        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
        </svg>
        <span class="text-[10px] tracking-tight mt-0.5">Beranda</span>
    </a>

    <!-- 2. Produk / Inventaris Hub atau Riwayat Transaksi -->
    @if(session('active_role_name') === 'kasir')
    <a href="{{ route('transactions') }}"
        class="flex flex-col items-center justify-center min-w-[56px] min-h-[48px] py-1 px-2 rounded-xl transition-colors {{ request()->routeIs('transactions*') ? 'text-blue-600 dark:text-blue-400 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200' }}">
        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm0 5.25h.007v.008H3.75V12Zm0 5.25h.007v.008H3.75v-.008Z" />
        </svg>
        <span class="text-[10px] tracking-tight mt-0.5">Riwayat</span>
    </a>
    @else
    <a href="{{ route('products') }}"
        class="flex flex-col items-center justify-center min-w-[56px] min-h-[48px] py-1 px-2 rounded-xl transition-colors {{ request()->routeIs('products*', 'categories*', 'modifiers*', 'suppliers*') ? 'text-blue-600 dark:text-blue-400 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200' }}">
        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
        </svg>
        <span class="text-[10px] tracking-tight mt-0.5">Produk</span>
    </a>
    @endif

    <!-- 3. Tombol Kasir POS (Floating Action Hero) -->
    <a href="{{ route('kasir') }}"
        class="flex flex-col items-center justify-center -mt-4 min-w-[56px] min-h-[56px] w-14 h-14 rounded-full bg-rose-600 hover:bg-rose-700 text-white shadow-lg shadow-rose-600/30 transition-transform active:scale-95 border-2 border-white dark:border-slate-900"
        title="Buka Mesin Kasir">
        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
        </svg>
    </a>

    <!-- 4. Keuangan / Buku Kas -->
    @if(session('active_role_name') !== 'kasir')
    <a href="{{ route('buku-kas') }}"
        class="flex flex-col items-center justify-center min-w-[56px] min-h-[48px] py-1 px-2 rounded-xl transition-colors {{ request()->routeIs('buku-kas*', 'virtual-cash*', 'debts*', 'bagi-hasil*') ? 'text-blue-600 dark:text-blue-400 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200' }}">
        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6H2.25m0 0v10.5m0-10.5h19.5m0 0v10.5m0-10.5A.75.75 0 0 0 21 4.5v-.75m-18 0A.75.75 0 0 1 3.75 3h16.5A.75.75 0 0 1 21 3.75v.75m-18 0v.75A.75.75 0 0 0 3.75 6" />
        </svg>
        <span class="text-[10px] tracking-tight mt-0.5">Keuangan</span>
    </a>
    @else
    <a href="{{ route('my-tasks') }}"
        class="flex flex-col items-center justify-center min-w-[56px] min-h-[48px] py-1 px-2 rounded-xl transition-colors {{ request()->routeIs('my-tasks*') ? 'text-blue-600 dark:text-blue-400 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200' }}">
        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg>
        <span class="text-[10px] tracking-tight mt-0.5">Tugas</span>
    </a>
    @endif

    <!-- 5. Menu Drawer (Akses Seluruh Hub Lainnya) -->
    <button @click="toggleSidebar()" type="button"
        class="flex flex-col items-center justify-center min-w-[56px] min-h-[48px] py-1 px-2 rounded-xl text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 transition-colors"
        title="Buka Menu Lengkap">
        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
        <span class="text-[10px] tracking-tight mt-0.5">Menu</span>
    </button>
</nav>
