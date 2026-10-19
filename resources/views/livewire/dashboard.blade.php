<div class="py-4 space-y-6">
    <!-- Top Bar: Title, Context Filter & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                {{ $isCashier ? 'Dashboard Kasir' : 'Dashboard Overview' }}
            </h1>
            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-0.5">
                @if($isCashier)
                    Shift Kasir • {{ session('active_jurusan_name') ? 'TEFA ' . session('active_jurusan_name') : 'Unit TEFA' }} • {{ \Carbon\Carbon::parse($today)->translatedFormat('l, d F Y') }}
                @else
                    {{ \Carbon\Carbon::parse($today)->translatedFormat('l, d F Y') }}
                @endif
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 sm:justify-end">
            <!-- Global Jurusan Filter (Only on Manager/Admin view if not fixed to specific jurusan) -->
            @if (!$isCashier && !session('active_jurusan_id'))
                <div class="flex items-center bg-white dark:bg-slate-900 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
                    <svg class="w-4 h-4 text-blue-600 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                    <select wire:model.live="filterJurusan" class="bg-transparent border-none p-0 text-xs font-semibold text-slate-800 dark:text-slate-200 focus:ring-0 cursor-pointer">
                        <option value="">Semua Unit TEFA</option>
                        @foreach ($jurusans as $jur)
                            <option value="{{ $jur->id }}">TEFA {{ $jur->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <!-- Leaderboard Shortcut -->
            <a href="{{ route('leaderboard') }}"
                class="inline-flex items-center gap-2 px-3.5 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/80 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-semibold shadow-xs transition-colors">
                <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                <span>Peringkat</span>
            </a>

            <!-- Cashier Action Button -->
            @php
                $hasHigherRole = auth()->user()->roles()->whereIn('roles.name', ['superadmin', 'pengelola_jurusan'])->exists()
                    || in_array(session('active_role_name'), ['superadmin', 'pengelola_jurusan']);
                $isSessionClosed = $isSessionClosed ?? (($isSessionFinished ?? false) || (session('active_role_name') === 'kasir' && ($hasClockedOut ?? false)));
            @endphp

            @if ($hasHigherRole)
                {{-- Superadmin & Pengelola: Memiliki wewenang Pasca-Closing dan Mode Darurat saat sesi tutup --}}
                @if ($isSessionFinished ?? false)
                    <div class="flex items-center gap-2" x-data="{ showEmergencyConfirm: false }">
                        <a href="{{ route('kasir') }}"
                            class="inline-flex items-center gap-2 px-3.5 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs uppercase tracking-wider rounded-xl shadow-xs transition-colors"
                            title="Buka Kasir Mode Pasca-Closing (Transaksi Masuk Besok H+1)">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            <span>Pasca-Closing</span>
                        </a>

                        <button type="button" @click="showEmergencyConfirm = true"
                            class="px-3 py-2 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/40 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800 rounded-xl text-xs font-bold transition-colors flex items-center gap-1.5"
                            title="Buka Mode Kasir Darurat (Buka Kembali Sesi Hari Ini)">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                            </svg>
                            <span>Buka Darurat</span>
                        </button>

                        <!-- Emergency Modal -->
                        <div x-show="showEmergencyConfirm" x-cloak
                            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm">
                            <div @click.outside="showEmergencyConfirm = false"
                                class="bg-white dark:bg-slate-900 w-full max-w-md rounded-2xl shadow-xl border border-slate-200 dark:border-slate-800 p-6 space-y-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Buka Mode Kasir Darurat?</h3>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">Sesi kasir hari ini akan diaktifkan kembali sehingga transaksi dapat dibukukan pada sesi hari ini.</p>
                                    </div>
                                </div>
                                <div class="flex justify-end gap-2.5 pt-2">
                                    <button type="button" @click="showEmergencyConfirm = false"
                                        class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors">
                                        Batal
                                    </button>
                                    <button type="button" wire:click="emergencyReactivateSession" @click="showEmergencyConfirm = false"
                                        class="px-4 py-2 text-xs font-semibold bg-rose-600 hover:bg-rose-700 text-white rounded-xl shadow-xs transition-colors">
                                        Ya, Aktifkan Sesi Darurat
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('kasir') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                        </svg>
                        <span>Buka Kasir</span>
                    </a>
                @endif
            @else
                {{-- Kasir Murni: Tidak ada pasca-closing dan tidak ada mode darurat --}}
                @php
                    $activeJurusanId = session('active_jurusan_id');
                    $activeJurusan = \App\Models\Jurusan::find($activeJurusanId);
                    $allowedJurusanIds = [$activeJurusanId];
                    if ($activeJurusan && $activeJurusan->parent_id) {
                        $allowedJurusanIds[] = $activeJurusan->parent_id;
                    }
                    $isScheduled = \App\Models\CashierSchedule::where('user_id', auth()->id())
                        ->whereIn('jurusan_id', $allowedJurusanIds)
                        ->where('date', now()->toDateString())
                        ->exists();
                @endphp

                @if ($isSessionClosed)
                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 text-xs font-semibold">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                        <span>Sesi Hari Ini Selesai</span>
                    </div>
                @elseif (!$isScheduled)
                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 text-xs font-semibold border border-amber-500/20">
                        <span>Tidak Ada Jadwal Hari Ini</span>
                    </div>
                @else
                    <a href="{{ route('kasir') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                        </svg>
                        <span>Buka Kasir</span>
                    </a>
                @endif
            @endif
        </div>
    </div>

    @if ($isCashier)
        <!-- CASHIER SPECIALIZED DASHBOARD -->
        <div class="space-y-6" x-data="{
            loginTime: '{{ $todayAttendance && $todayAttendance->clock_in ? \Carbon\Carbon::parse($todayAttendance->clock_in)->format('Y-m-d H:i:s') : '' }}',
            clockOutTime: '{{ $todayAttendance && $todayAttendance->clock_out ? \Carbon\Carbon::parse($todayAttendance->clock_out)->format('Y-m-d H:i:s') : '' }}',
            targetClosingStr: '{{ $targetClockOutCarbon ? $targetClockOutCarbon->format('Y-m-d H:i:s') : '' }}',
            isFinished: {{ (($hasClockedOut ?? false) || ($isSessionFinished ?? false)) ? 'true' : 'false' }},
            elapsedHours: '00',
            elapsedMinutes: '00',
            elapsedSeconds: '00',
            isPastClosing: {{ $isPastClosingTarget ? 'true' : 'false' }},
            remainingText: '',
            init() {
                if (this.loginTime) {
                    this.updateTimer();
                    if (!this.isFinished) {
                        setInterval(() => { this.updateTimer(); }, 1000);
                    }
                }
            },
            updateTimer() {
                const now = new Date();
                const start = new Date(this.loginTime.replace(/-/g, '/'));
                const end = (this.isFinished && this.clockOutTime) ? new Date(this.clockOutTime.replace(/-/g, '/')) : now;
                const diffMs = Math.max(0, end - start);
                const totalSec = Math.floor(diffMs / 1000);
                this.elapsedHours = String(Math.floor(totalSec / 3600)).padStart(2, '0');
                this.elapsedMinutes = String(Math.floor((totalSec % 3600) / 60)).padStart(2, '0');
                this.elapsedSeconds = String(totalSec % 60).padStart(2, '0');

                if (this.targetClosingStr) {
                    const target = new Date(this.targetClosingStr.replace(/-/g, '/'));
                    if (now >= target) {
                        this.isPastClosing = true;
                    } else {
                        this.isPastClosing = false;
                        const remSec = Math.floor((target - now) / 1000);
                        const rH = Math.floor(remSec / 3600);
                        const rM = Math.floor((remSec % 3600) / 60);
                        this.remainingText = `${rH}j ${rM}m lagi menuju batas closing`;
                    }
                }
            }
        }">
            <!-- Shift Status & Timer Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- 1. Check-in Time -->
                <div class="app-card p-5 flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Absensi Shift Hari Ini</span>
                        @if ($todayAttendance && $todayAttendance->clock_in_status === 'late')
                            <span class="px-2 py-0.5 bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 rounded-md text-[10px] font-bold">Terlambat</span>
                        @else
                            <span class="px-2 py-0.5 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 rounded-md text-[10px] font-bold">Tepat Waktu</span>
                        @endif
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-slate-900 dark:text-white tabular-nums">
                            {{ $todayAttendance && $todayAttendance->clock_in ? \Carbon\Carbon::parse($todayAttendance->clock_in)->format('H:i:s') : now()->format('H:i:s') }}
                            <span class="text-xs font-normal text-slate-400">WIB</span>
                        </div>
                        <p class="text-xs text-slate-400 mt-1">Batas mulai shift: {{ $targetClockIn }} WIB</p>
                    </div>
                </div>

                <!-- 2. Elapsed Duration -->
                <div class="app-card p-5 flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Durasi Jaga Kasir</span>
                        @if (($hasClockedOut ?? false) || ($isSessionFinished ?? false))
                            <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-md text-[10px] font-bold">Shift Selesai</span>
                        @else
                            <span class="px-2 py-0.5 bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 rounded-md text-[10px] font-bold">Aktif</span>
                        @endif
                    </div>
                    <div>
                        <div class="text-2xl font-bold font-mono text-slate-900 dark:text-white tracking-tight" x-text="`${elapsedHours}:${elapsedMinutes}:${elapsedSeconds}`">
                            00:00:00
                        </div>
                        <p class="text-xs text-slate-400 mt-1">{{ (($hasClockedOut ?? false) || ($isSessionFinished ?? false)) ? 'Total durasi shift hari ini' : 'Waktu berjalan di perangkat' }}</p>
                    </div>
                </div>

                <!-- 3. Closing Target -->
                <div class="app-card p-5 flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Batas Minimal Closing</span>
                        @if (($hasClockedOut ?? false) || ($isSessionFinished ?? false))
                            <span class="px-2 py-0.5 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 rounded-md text-[10px] font-bold">
                                Sesi Ditutup
                            </span>
                        @else
                            <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-md text-[10px] font-bold">
                                {{ $targetClockOut }} WIB
                            </span>
                        @endif
                    </div>
                    <div>
                        @if (($hasClockedOut ?? false) || ($isSessionFinished ?? false))
                            <div class="text-sm font-bold text-slate-700 dark:text-slate-300">
                                <span>Rekap Sesi Telah Ditutup</span>
                            </div>
                            <p class="text-xs text-slate-400 mt-1">Gunakan Mode Pasca-Closing untuk transaksi lanjutan.</p>
                        @else
                            <div class="text-sm font-bold" :class="isPastClosing ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-700 dark:text-slate-300'">
                                <span x-show="isPastClosing">Target Terpenuhi (+10 Poin)</span>
                                <span x-show="!isPastClosing" x-text="remainingText || 'Bertugaslah hingga batas closing'"></span>
                            </div>
                            <p class="text-xs text-slate-400 mt-1">Klaim bonus poin saat rekap harian ditutup.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Primary POS Launch Banner -->
            @if ($isSessionClosed)
                <div class="app-card p-5 sm:p-6 flex flex-col sm:flex-row items-center justify-between gap-4 bg-slate-50 dark:bg-slate-900/40 border border-slate-200 dark:border-slate-800">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 flex items-center justify-center shrink-0 border border-slate-200 dark:border-slate-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">Sesi Kasir Hari Ini Telah Ditutup</h3>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-300 dark:border-slate-700">Tutup</span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Sesi kasir hari ini telah selesai ditutup. Mode pasca-closing dan mode kasir darurat hanya dapat dibuka oleh Pengelola atau Superadmin.</p>
                        </div>
                    </div>
                </div>
            @else
                <div class="app-card p-5 sm:p-6 flex flex-col sm:flex-row items-center justify-between gap-4 bg-gradient-to-r from-blue-600/5 to-transparent border-blue-600/20">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Akses Mesin Kasir (POS)</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Layani transaksi penjualan, input topping, cetak nota, dan catat pembayaran.</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2.5 w-full sm:w-auto">
                        <form method="POST" action="{{ route('logout') }}" class="w-full sm:w-auto">
                            @csrf
                            <button type="submit"
                                class="w-full sm:w-auto px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-semibold transition-colors">
                                Selesai & Keluar
                            </button>
                        </form>
                        <a href="{{ route('kasir') }}"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-sm transition-colors">
                            <span>Buka Kasir Sekarang</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    </div>
                </div>
            @endif

            <!-- Cashier Daily Tasks List -->
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">Tugas Kasir Hari Ini</h2>
                    <a href="{{ route('my-tasks') }}" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                        Lihat Semua Tugas &rarr;
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @forelse($todayTaskAssignments as $assignment)
                        <div class="app-card p-4 flex flex-col justify-between space-y-3">
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400">
                                        {{ $assignment->taskDefinition->is_routine ? 'Tugas Rutin' : 'Tugas Khusus' }}
                                    </span>
                                    <span class="text-[10px] font-semibold text-slate-400">
                                        {{ $assignment->assignment_status }}
                                    </span>
                                </div>
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white line-clamp-1">
                                    {{ $assignment->taskDefinition->task_name }}
                                </h4>
                                @if($assignment->taskDefinition->description)
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-2">
                                        {{ $assignment->taskDefinition->description }}
                                    </p>
                                @endif
                            </div>
                            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                                <span class="text-xs font-bold text-amber-500">+{{ $assignment->taskDefinition->reward_points ?? 5 }} Poin</span>
                                <a href="{{ route('my-tasks') }}" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                                    Kerjakan &rarr;
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full app-card p-6 text-center text-slate-400">
                            <p class="text-xs">Tidak ada tugas tambahan yang tertunda hari ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Cashier Documentation Schedule (If available) -->
            @if(isset($myDocSchedules) && $myDocSchedules->count() > 0)
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">Jadwal Dokumentasi Shift</h2>
                        <a href="{{ route('documentation-schedules') }}" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                            Lihat Jadwal &rarr;
                        </a>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($myDocSchedules as $doc)
                            <div class="app-card p-4 flex items-center justify-between">
                                <div class="min-w-0">
                                    <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate">
                                        {{ $doc->activity->name ?? 'Dokumentasi Kasir' }}
                                    </h4>
                                    <span class="text-[10px] text-slate-400">
                                        {{ \Carbon\Carbon::parse($doc->date)->translatedFormat('d F Y') }} • {{ $doc->start_time }} - {{ $doc->end_time }}
                                    </span>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400 shrink-0">
                                    {{ $doc->status ?? 'Terjadwal' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @else
        <!-- ADMIN & PENGELOLA DASHBOARD -->

        @if ($orphanCarryForwardAmount > 0 && in_array(session('active_role_name'), ['pengelola_jurusan', 'superadmin']))
            <div class="app-card p-4 border-amber-500/30 bg-amber-500/5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <p class="text-xs text-amber-700 dark:text-amber-400">
                    Ditemukan saldo bawaan sebesar <strong>Rp{{ number_format($orphanCarryForwardAmount, 0, ',', '.') }}</strong> tanggal {{ now()->startOfMonth()->translatedFormat('d F Y') }}.
                </p>
                <button wire:click="cleanupOrphanCarryForward"
                    class="px-3.5 py-1.5 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-semibold shrink-0 transition-colors">
                    Bersihkan Saldo Yatim
                </button>
            </div>
        @endif

        <!-- Primary KPI Metrics (Consolidated 3 Cards) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- 1. Omzet Hari Ini -->
            <div class="app-card p-5 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Omzet Penjualan (Hari Ini)</span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded {{ $stats->revenue_change >= 0 ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400' : 'bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400' }}">
                            {{ $stats->revenue_change >= 0 ? '+' : '' }}{{ number_format($stats->revenue_change, 1) }}% vs Kemarin
                        </span>
                    </div>
                    <div class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white tabular-nums" :class="censorMode ? 'privacy-blur' : ''">
                        Rp{{ number_format($stats->today_revenue, 0, ',', '.') }}
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                    <span>Murni Hak TEFA:</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200 tabular-nums" :class="censorMode ? 'privacy-blur' : ''">
                        Rp{{ number_format($stats->today_internal_revenue, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- 2. Profit Bersih Hari Ini -->
            <div class="app-card p-5 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Keuntungan Bersih (Hari Ini)</span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded {{ $stats->profit_change >= 0 ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400' : 'bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400' }}">
                            {{ $stats->profit_change >= 0 ? '+' : '' }}{{ number_format($stats->profit_change, 1) }}% vs Kemarin
                        </span>
                    </div>
                    <div class="text-2xl sm:text-3xl font-bold text-emerald-600 dark:text-emerald-400 tabular-nums" :class="censorMode ? 'privacy-blur' : ''">
                        Rp{{ number_format($stats->today_profit, 0, ',', '.') }}
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                    <span>Transaksi Berhasil:</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200">
                        {{ $stats->today_transactions }} Pesanan
                    </span>
                </div>
            </div>

            <!-- 3. Kas Fisik & Piutang -->
            <div class="app-card p-5 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Saldo Kas & Piutang</span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-blue-50 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400">Total Akumulasi</span>
                    </div>
                    <div class="text-2xl sm:text-3xl font-bold text-blue-600 dark:text-blue-400 tabular-nums" :class="censorMode ? 'privacy-blur' : ''">
                        Rp{{ number_format($stats->total_audit_cash, 0, ',', '.') }}
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                    <span>Piutang Belum Lunas:</span>
                    <span class="font-bold text-rose-600 dark:text-rose-400 tabular-nums" :class="censorMode ? 'privacy-blur' : ''">
                        Rp{{ number_format($stats->total_outstanding_debt, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Monthly Balance Comparison Strip -->
        <div class="app-card p-4 sm:p-5 grid grid-cols-2 sm:grid-cols-4 gap-4 divide-y sm:divide-y-0 sm:divide-x divide-slate-100 dark:divide-slate-800">
            <div class="pt-2 sm:pt-0">
                <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block">Pemasukan Bulan Ini</span>
                <span class="text-sm sm:text-base font-bold text-emerald-600 dark:text-emerald-400 tabular-nums" :class="censorMode ? 'privacy-blur' : ''">
                    +Rp{{ number_format($stats->monthly_income, 0, ',', '.') }}
                </span>
            </div>
            <div class="pt-2 sm:pt-0 sm:pl-4">
                <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block">Pengeluaran Bulan Ini</span>
                <span class="text-sm sm:text-base font-bold text-rose-600 dark:text-rose-400 tabular-nums" :class="censorMode ? 'privacy-blur' : ''">
                    -Rp{{ number_format($stats->monthly_expense, 0, ',', '.') }}
                </span>
            </div>
            <div class="pt-2 sm:pt-0 sm:pl-4">
                <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block">Saldo Kas Modal</span>
                <span class="text-sm sm:text-base font-bold text-slate-900 dark:text-white tabular-nums" :class="censorMode ? 'privacy-blur' : ''">
                    Rp{{ number_format($stats->total_modal_balance, 0, ',', '.') }}
                </span>
            </div>
            <div class="pt-2 sm:pt-0 sm:pl-4">
                <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block">Total Omzet Sepanjang Waktu</span>
                <span class="text-sm sm:text-base font-bold text-slate-900 dark:text-white tabular-nums" :class="censorMode ? 'privacy-blur' : ''">
                    Rp{{ number_format($stats->total_all_time_revenue, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- Weekly Chart & Top Products -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Weekly Chart -->
            <div class="app-card p-5 lg:col-span-2 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">Tren Penjualan & Profit Mingguan</h2>
                        <p class="text-xs text-slate-400">Perbandingan omzet harian dan keuntungan 7 hari terakhir</p>
                    </div>
                    <a href="{{ route('weekly-performance') }}" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                        Detail Analitik &rarr;
                    </a>
                </div>

                <div class="h-64 relative" wire:ignore>
                    <canvas id="dashboardWeeklyChart"></canvas>
                </div>
            </div>

            <!-- Top Products -->
            <div class="app-card p-5 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">Produk Terlaris</h2>
                    <a href="{{ route('products') }}" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                        Katalog &rarr;
                    </a>
                </div>

                <!-- Category filter chips -->
                <div class="flex gap-1.5 overflow-x-auto no-scrollbar pb-1">
                    <button wire:click="setTopCategory('all')" type="button"
                        class="px-2.5 py-1 text-[10px] font-bold rounded-lg transition-colors {{ ($selectedTopCategory ?? 'all') === 'all' ? 'bg-blue-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300' }}">
                        Semua
                    </button>
                    @foreach($categories as $cat)
                        <button wire:click="setTopCategory('{{ $cat->id }}')" type="button"
                            class="px-2.5 py-1 text-[10px] font-bold rounded-lg whitespace-nowrap transition-colors {{ ($selectedTopCategory ?? 'all') == $cat->id ? 'bg-blue-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300' }}">
                            {{ $cat->name }}
                        </button>
                    @endforeach
                </div>

                <div class="space-y-2.5">
                    @forelse($topProducts as $idx => $prod)
                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/50">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="w-6 h-6 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-xs font-bold text-slate-600 dark:text-slate-300 shrink-0">
                                    {{ $idx + 1 }}
                                </span>
                                <div class="min-w-0">
                                    <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate">
                                        {{ $prod->product->name ?? 'Produk Dihapus' }}
                                    </h4>
                                    <span class="text-[10px] text-slate-400">
                                        {{ $prod->total_qty ?? 0 }} Unit Terjual
                                    </span>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">
                                    Rp{{ number_format($prod->total_revenue ?? 0, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-6">Belum ada data penjualan tercatat.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('livewire:navigated', function () {
                initDashboardChart();
            });
            document.addEventListener('DOMContentLoaded', function () {
                initDashboardChart();
            });

            function initDashboardChart() {
                const ctx = document.getElementById('dashboardWeeklyChart');
                if (!ctx || typeof Chart === 'undefined') return;

                const existingChart = Chart.getChart(ctx);
                if (existingChart) {
                    existingChart.destroy();
                }

                const weeklyData = @json($weeklyData ?? []);
                const isDark = document.documentElement.classList.contains('dark');

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: weeklyData.map(d => d.day),
                        datasets: [
                            {
                                label: 'Omzet',
                                data: weeklyData.map(d => d.revenue),
                                borderColor: '#2563EB',
                                backgroundColor: 'rgba(37, 99, 235, 0.08)',
                                tension: 0.3,
                                borderWidth: 2,
                                pointRadius: 3,
                                fill: true
                            },
                            {
                                label: 'Profit Bersih',
                                data: weeklyData.map(d => d.profit),
                                borderColor: '#10B981',
                                backgroundColor: 'rgba(16, 185, 129, 0.08)',
                                tension: 0.3,
                                borderWidth: 2,
                                pointRadius: 3,
                                fill: true
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    color: isDark ? '#94A3B8' : '#64748B',
                                    font: { size: 11, weight: 'bold' }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { color: isDark ? '#64748B' : '#94A3B8', font: { size: 10 } }
                            },
                            y: {
                                grid: { color: isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)' },
                                ticks: {
                                    color: isDark ? '#64748B' : '#94A3B8',
                                    font: { size: 10 },
                                    callback: function(val) { return 'Rp' + (val >= 1000 ? (val / 1000) + 'k' : val); }
                                }
                            }
                        }
                    }
                });
            }
        </script>
    @endif
</div>
