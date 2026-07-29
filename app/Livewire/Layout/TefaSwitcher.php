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
            // Get all jurusan_id values assigned to this user in role_user pivot
            $assignedJurusanIds = \DB::table('role_user')
                ->where('user_id', auth()->id())
                ->pluck('jurusan_id')
                ->filter()
                ->unique();

            $allowedParentIds = [];
            foreach ($assignedJurusanIds as $jid) {
                $jurusan = Jurusan::find($jid);
                if ($jurusan) {
                    $parentId = $jurusan->parent_id ?: $jurusan->id;
                    $allowedParentIds[] = $parentId;
                }
            }

            // Only allow parent units and their sub-units
            $this->availableUnits = Jurusan::whereIn('id', $allowedParentIds)
                ->orWhereIn('parent_id', $allowedParentIds)
                ->get()
                ->toArray();
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
            $assignedJurusanIds = \DB::table('role_user')
                ->where('user_id', auth()->id())
                ->pluck('jurusan_id')
                ->filter()
                ->unique();

            $allowedParentIds = [];
            foreach ($assignedJurusanIds as $jid) {
                $jurusan = Jurusan::find($jid);
                if ($jurusan) {
                    $parentId = $jurusan->parent_id ?: $jurusan->id;
                    $allowedParentIds[] = $parentId;
                }
            }

            if (!in_array($unit->id, $allowedParentIds) && !in_array($unit->parent_id, $allowedParentIds)) {
                abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang mengakses unit ini.');
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
        $showSwitcher = in_array($activeRole, ['superadmin', 'pengelola_jurusan', 'pengelola', 'kasir']);

        return view('livewire.layout.tefa-switcher', [
            'showSwitcher' => $showSwitcher,
        ]);
    }
}
