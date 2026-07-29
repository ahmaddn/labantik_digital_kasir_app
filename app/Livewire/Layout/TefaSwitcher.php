<?php

namespace App\Livewire\Layout;

use App\Models\Jurusan;
use Livewire\Component;

class TefaSwitcher extends Component
{
    public $availableUnits = [];
    public $selectedUnitId = '';

    public function mount()
    {
        $activeRole = session('active_role_name');
        $activeJurusanId = session('active_jurusan_id');

        if ($activeRole === 'superadmin') {
            $this->availableUnits = Jurusan::all()->toArray();
        } else {
            // Find parent unit if current is sub-unit or main
            $currentJurusan = Jurusan::find($activeJurusanId);
            if ($currentJurusan) {
                $parentId = $currentJurusan->parent_id ?: $currentJurusan->id;
                $this->availableUnits = Jurusan::where('id', $parentId)
                    ->orWhere('parent_id', $parentId)
                    ->get()
                    ->toArray();
            }
        }

        $this->selectedUnitId = $activeJurusanId;
    }

    public function switchUnit($unitId)
    {
        $unit = Jurusan::find($unitId);
        if (!$unit) {
            return;
        }

        // Validate access
        $activeRole = session('active_role_name');
        if ($activeRole !== 'superadmin') {
            $activeJurusanId = session('active_jurusan_id');
            $currentJurusan = Jurusan::find($activeJurusanId);
            if ($currentJurusan) {
                $parentId = $currentJurusan->parent_id ?: $currentJurusan->id;
                if ($unit->id !== $parentId && $unit->parent_id !== $parentId) {
                    abort(403);
                }
            }
        }

        session([
            'active_jurusan_id' => $unit->id,
            'active_jurusan_name' => $unit->name,
            'active_jurusan_theme' => $unit->theme_settings ?: null,
        ]);

        return redirect(request()->header('Referer') ?: route('dashboard'));
    }

    public function render()
    {
        $activeRole = session('active_role_name');
        $showSwitcher = in_array($activeRole, ['superadmin', 'pengelola_jurusan', 'pengelola']);

        return view('livewire.layout.tefa-switcher', [
            'showSwitcher' => $showSwitcher,
        ]);
    }
}
