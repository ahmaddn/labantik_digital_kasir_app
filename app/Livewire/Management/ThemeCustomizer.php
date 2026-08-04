<?php

namespace App\Livewire\Management;

use App\Models\Jurusan;
use Livewire\Component;
use Livewire\WithFileUploads;

class ThemeCustomizer extends Component
{
    use WithFileUploads;

    public $jurusans = [];

    public $selectedJurusanId = '';

    // Theme configurations
    public $primaryColor = '#2563EB';

    public $secondaryColor = '#EF4444';

    public $fontFamily = 'Outfit';

    public $themeStyle = 'classic-premium';

    // TEFA configurations
    public $tefaName = 'TEFA LABANTIK';

    public $docPrefixInvoice = 'INV-SUP';

    public $docPrefixTransaction = 'LBK';

    public $tefaLogo;

    public $existingTefaLogo = '';

    // Attendance & Penalty configurations
    public $clockInTime = '07:00';

    public $clockOutTime = '15:00';

    public $lateClockInPenalty = 0;

    public $lateClockOutPenalty = 0;

    // Color presets
    public $colorPresets = [
        ['name' => 'Royal Blue & Crimson', 'primary' => '#2563EB', 'secondary' => '#EF4444'],
        ['name' => 'Emerald Forest & Gold', 'primary' => '#10B981', 'secondary' => '#F59E0B'],
        ['name' => 'Cyber Violet & Pink', 'primary' => '#8B5CF6', 'secondary' => '#EC4899'],
        ['name' => 'Sunset Orange & Navy', 'primary' => '#F97316', 'secondary' => '#1E3A8A'],
        ['name' => 'Classic Dark & Silver', 'primary' => '#1E293B', 'secondary' => '#94A3B8'],
        ['name' => 'Eco Green & Mint', 'primary' => '#059669', 'secondary' => '#34D399'],
    ];

    // Font presets
    public $fontPresets = [
        ['name' => 'Outfit (Modern & Bold)', 'value' => 'Outfit'],
        ['name' => 'Inter (Clean & Professional)', 'value' => 'Inter'],
        ['name' => 'Plus Jakarta Sans (Sleek & Tech)', 'value' => 'Plus Jakarta Sans'],
        ['name' => 'Syne (Artistic & Unique)', 'value' => 'Syne'],
    ];

    // Style presets
    public $stylePresets = [
        ['name' => 'Classic Premium', 'value' => 'classic-premium', 'desc' => 'Desain premium dengan sudut membulat lebar dan bayangan lembut.'],
        ['name' => 'Glassmorphism', 'value' => 'glassmorphism', 'desc' => 'Tampilan modern transparan dengan efek blur kaca di latar belakang.'],
        ['name' => 'Neon Cyberpunk', 'value' => 'neon-cyberpunk', 'desc' => 'Kontras tinggi, bingkai menyala, dan aksen neon futuristik.'],
        ['name' => 'Restoran & Warung (Warm Culinary Style)', 'value' => 'restaurant-aesthetic', 'desc' => 'Aksen hangat, bayangan tebal, ideal untuk warung atau depot makanan.'],
        ['name' => 'Retail Minimarket (Clean Grid Style)', 'value' => 'retail-aesthetic', 'desc' => 'Tampilan modular ultra bersih dengan sudut tajam untuk toko/retail.'],
        ['name' => 'Bank Mini (Corporate FinTech Style)', 'value' => 'bank-aesthetic', 'desc' => 'Desain biru/hijau korporat terstruktur, font formal, dan nuansa finansial.'],
    ];

    public function mount(): void
    {
        $activeRole = session('active_role_name');
        if (!in_array($activeRole, ['superadmin', 'pengelola_jurusan', 'pengelola', 'kasir'])) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengatur konfigurasi tampilan TEFA.');
        }

        $activeJurusanId = session('active_jurusan_id');

        if ($activeRole === 'superadmin') {
            $this->jurusans = Jurusan::all();
            $this->selectedJurusanId = $this->jurusans->first() ? $this->jurusans->first()->id : '';
        } else {
            // Can edit their own parent jurusan and all its sub-units
            $this->jurusans = Jurusan::where('id', $activeJurusanId)
                ->orWhere('parent_id', $activeJurusanId)
                ->get();
            $this->selectedJurusanId = $activeJurusanId;
        }

        $this->loadThemeSettings();
    }

    public function updatedSelectedJurusanId(): void
    {
        $this->loadThemeSettings();
    }

    public function applyPreset(string $primary, string $secondary): void
    {
        $this->primaryColor = $primary;
        $this->secondaryColor = $secondary;
    }

    public function loadThemeSettings(): void
    {
        if (! $this->selectedJurusanId) {
            return;
        }

        $jurusan = Jurusan::find($this->selectedJurusanId);
        if ($jurusan) {
            $settings = $jurusan->theme_settings ?: [];
            $this->primaryColor = $settings['primary_color'] ?? '#2563EB';
            $this->secondaryColor = $settings['secondary_color'] ?? '#EF4444';
            $this->fontFamily = $settings['font_family'] ?? 'Outfit';
            
            $isSubUnit = !is_null($jurusan->parent_id);
            $loadedStyle = $settings['theme_style'] ?? null;

            if ($isSubUnit) {
                // Sub-unit: fallback to culinary/restaurant if not business style
                if (!in_array($loadedStyle, ['restaurant-aesthetic', 'retail-aesthetic', 'bank-aesthetic'])) {
                    $this->themeStyle = 'restaurant-aesthetic';
                } else {
                    $this->themeStyle = $loadedStyle;
                }
            } else {
                // Parent: fallback to classic-premium if using a business style
                if (in_array($loadedStyle, ['restaurant-aesthetic', 'retail-aesthetic', 'bank-aesthetic'])) {
                    $this->themeStyle = 'classic-premium';
                } else {
                    $this->themeStyle = $loadedStyle ?: 'classic-premium';
                }
            }

            $this->tefaName = $settings['tefa_name'] ?? $jurusan->name;
            $this->docPrefixInvoice = $settings['doc_prefix_invoice'] ?? 'INV-SUP';
            $this->docPrefixTransaction = $settings['doc_prefix_transaction'] ?? 'LBK';
            $this->existingTefaLogo = $settings['tefa_logo'] ?? '';
            $this->clockInTime = $settings['clock_in_time'] ?? '07:00';
            $this->clockOutTime = $settings['clock_out_time'] ?? '15:00';
            $this->lateClockInPenalty = $settings['late_clock_in_penalty'] ?? 0;
            $this->lateClockOutPenalty = $settings['late_clock_out_penalty'] ?? 0;
        } else {
            // Default settings
            $this->primaryColor = '#2563EB';
            $this->secondaryColor = '#EF4444';
            $this->fontFamily = 'Outfit';
            $this->themeStyle = 'classic-premium';
            $this->tefaName = 'TEFA LABANTIK';
            $this->docPrefixInvoice = 'INV-SUP';
            $this->docPrefixTransaction = 'LBK';
            $this->existingTefaLogo = '';
            $this->clockInTime = '07:00';
            $this->clockOutTime = '15:00';
            $this->lateClockInPenalty = 0;
            $this->lateClockOutPenalty = 0;
        }
    }

    public function saveTheme(): void
    {
        if (! $this->selectedJurusanId) {
            $this->dispatch('toast', message: 'Silakan pilih jurusan terlebih dahulu.', type: 'error');

            return;
        }

        $jurusan = Jurusan::findOrFail($this->selectedJurusanId);

        $logoPath = $this->existingTefaLogo;
        if ($this->tefaLogo) {
            $this->validate([
                'tefaLogo' => 'image|max:1024', // max 1MB
            ]);
            $logoPath = $this->tefaLogo->store('logos', 'public');
        }

        $themeSettings = [
            'primary_color' => $this->primaryColor,
            'secondary_color' => $this->secondaryColor,
            'font_family' => $this->fontFamily,
            'theme_style' => $this->themeStyle,
            'tefa_name' => $this->tefaName,
            'doc_prefix_invoice' => $this->docPrefixInvoice,
            'doc_prefix_transaction' => $this->docPrefixTransaction,
            'tefa_logo' => $logoPath,
            'clock_in_time' => $this->clockInTime,
            'clock_out_time' => $this->clockOutTime,
            'late_clock_in_penalty' => (int) $this->lateClockInPenalty,
            'late_clock_out_penalty' => (int) $this->lateClockOutPenalty,
        ];

        $jurusan->update([
            'theme_settings' => $themeSettings,
        ]);

        // If this is the active jurusan, update the session values immediately so the theme updates live!
        if ($this->selectedJurusanId === session('active_jurusan_id')) {
            session(['active_jurusan_theme' => $themeSettings]);
        }

        $this->dispatch('toast', message: 'Tampilan & Konfigurasi TEFA berhasil disimpan!');
    }

    public function render()
    {
        $selectedJurusan = Jurusan::find($this->selectedJurusanId);
        $isSubUnit = $selectedJurusan && !is_null($selectedJurusan->parent_id);

        $filteredStyles = [];
        foreach ($this->stylePresets as $style) {
            $isBusinessStyle = in_array($style['value'], ['restaurant-aesthetic', 'retail-aesthetic', 'bank-aesthetic']);
            
            if ($isSubUnit) {
                // For sub-units: show only the business presets
                if ($isBusinessStyle) {
                    $filteredStyles[] = $style;
                }
            } else {
                // For parent units: show only the general presets
                if (!$isBusinessStyle) {
                    $filteredStyles[] = $style;
                }
            }
        }

        return view('livewire.management.theme-customizer', [
            'availableStyles' => $filteredStyles,
            'isSubUnit' => $isSubUnit,
        ])->layout('layouts.app', ['title' => 'Kustomisasi Tampilan TEFA']);
    }
}
