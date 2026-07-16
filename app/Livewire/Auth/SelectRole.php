<?php

namespace App\Livewire\Auth;

use Livewire\Component;

class SelectRole extends Component
{
    public $accesses = [];

    public function mount()
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $this->accesses = $user->getAvailableAccesses()->toArray();

        if (count($this->accesses) === 0) {
            auth()->logout();
            session()->flash('error', 'Akun Anda tidak memiliki hak akses yang terdaftar. Hubungi Administrator.');

            return redirect()->route('login');
        }
    }

    public function selectAccess($accessId)
    {
        $selected = collect($this->accesses)->firstWhere('access_id', $accessId);

        if ($selected) {
            $selected = (object) $selected;
            session([
                'active_access_id' => $selected->access_id,
                'active_role_id' => $selected->role_id,
                'active_role_name' => $selected->role_name,
                'active_role_label' => $selected->role_label,
                'active_jurusan_id' => $selected->jurusan_id,
                'active_jurusan_name' => $selected->jurusan_name ?? 'Superadmin Context',
            ]);

            session()->flash('toast', 'Berhasil masuk sebagai '.$selected->role_label.($selected->jurusan_name ? ' ('.$selected->jurusan_name.')' : ''));

            return redirect()->route('dashboard');
        }
    }

    public function render()
    {
        return view('livewire.auth.select-role')
            ->layout('layouts.blank', ['title' => 'Pilih Hak Akses | Superapps TEFA']);
    }
}
