<?php

namespace App\Livewire\Management;

use App\Models\Jurusan;
use Livewire\Component;

class ThemeCustomizer extends Component
{
    public $jurusans = [];

    public $selectedJurusanId = '';

    // Theme configurations
    public $primaryColor = '#2563EB';

    public $secondaryColor = '#EF4444';

    public $fontFamily = 'Outfit';

    public $themeStyle = 'classic-premium';

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
    ];

    public function mount(): void
    {
        $this->jurusans = Jurusan::all();

        $activeJurusanId = session('active_jurusan_id');
        $activeRole = session('active_role_name');

        if ($activeRole === 'superadmin') {
            // Superadmin defaults to first jurusan or let them select
            $this->selectedJurusanId = $this->jurusans->first() ? $this->jurusans->first()->id : '';
        } else {
            // Non-superadmin is locked to active jurusan
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
        if ($jurusan && $jurusan->theme_settings) {
            $settings = $jurusan->theme_settings;
            $this->primaryColor = $settings['primary_color'] ?? '#2563EB';
            $this->secondaryColor = $settings['secondary_color'] ?? '#EF4444';
            $this->fontFamily = $settings['font_family'] ?? 'Outfit';
            $this->themeStyle = $settings['theme_style'] ?? 'classic-premium';
        } else {
            // Default settings
            $this->primaryColor = '#2563EB';
            $this->secondaryColor = '#EF4444';
            $this->fontFamily = 'Outfit';
            $this->themeStyle = 'classic-premium';
        }
    }

    public function saveTheme(): void
    {
        if (! $this->selectedJurusanId) {
            $this->dispatch('toast', message: 'Silakan pilih jurusan terlebih dahulu.', type: 'error');

            return;
        }

        $jurusan = Jurusan::findOrFail($this->selectedJurusanId);

        $themeSettings = [
            'primary_color' => $this->primaryColor,
            'secondary_color' => $this->secondaryColor,
            'font_family' => $this->fontFamily,
            'theme_style' => $this->themeStyle,
        ];

        $jurusan->update([
            'theme_settings' => $themeSettings,
        ]);

        // If this is the active jurusan, update the session values immediately so the theme updates live!
        if ($this->selectedJurusanId === session('active_jurusan_id')) {
            session(['active_jurusan_theme' => $themeSettings]);
        }

        $this->dispatch('toast', message: 'Tampilan tema berhasil disimpan & diterapkan!');
    }

    public function render()
    {
        return view('livewire.management.theme-customizer')
            ->layout('layouts.app', ['title' => 'Kustomisasi Tampilan TEFA']);
    }
}
