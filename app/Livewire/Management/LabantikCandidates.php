<?php

namespace App\Livewire\Management;

use App\Models\Jurusan;
use App\Models\LabantikRegistration;
use Livewire\Component;
use Livewire\WithPagination;

class LabantikCandidates extends Component
{
    use WithPagination;

    public string $search = '';
    public string $selectedJurusanId = '';

    // WhatsApp Group Link Settings
    public string $waGroupLink = '';
    public bool $showWaLinkModal = false;

    // Delete properties
    public string $deleteId = '';
    public bool $showDeleteModal = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedJurusanId' => ['except' => ''],
    ];

    public function mount(): void
    {
        $settings = json_decode(@file_get_contents(storage_path('app/settings.json')), true) ?: [];
        $this->waGroupLink = $settings['wa_group_link'] ?? '';
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedJurusanId(): void
    {
        $this->resetPage();
    }

    public function saveWaLink(): void
    {
        $this->validate([
            'waGroupLink' => 'nullable|url|max:500',
        ], [], [
            'waGroupLink' => 'Link Grup WhatsApp'
        ]);

        $settings = json_decode(@file_get_contents(storage_path('app/settings.json')), true) ?: [];
        $settings['wa_group_link'] = trim($this->waGroupLink);
        
        // Ensure storage directory exists
        if (!file_exists(storage_path('app'))) {
            mkdir(storage_path('app'), 0755, true);
        }
        
        file_put_contents(storage_path('app/settings.json'), json_encode($settings));

        $this->showWaLinkModal = false;
        $this->dispatch('toast', message: 'Link grup WhatsApp berhasil disimpan!');
    }

    public function exportExcel()
    {
        $activeJurusanId = session('active_jurusan_id') ?: $this->selectedJurusanId;

        $query = LabantikRegistration::with('jurusan');

        if ($activeJurusanId) {
            $query->where(function ($q) use ($activeJurusanId) {
                $q->where('jurusan_id', $activeJurusanId)
                  ->orWhereNull('jurusan_id');
            });
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%')
                  ->orWhere('class_name', 'like', '%' . $this->search . '%')
                  ->orWhere('address', 'like', '%' . $this->search . '%');
            });
        }

        $candidates = $query->orderBy('created_at', 'desc')->get();
        $filename = 'calon_labantik_export_' . now()->format('Y_m_d_His') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\LabantikCandidatesExport($candidates), 
            $filename
        );
    }

    public function confirmDelete(string $id): void
    {
        $activeRole = session('active_role_name');
        if (!in_array($activeRole, ['superadmin', 'pengelola_jurusan', 'kasir'])) {
            abort(403, 'Unauthorized.');
        }

        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteCandidate(): void
    {
        $activeRole = session('active_role_name');
        if (!in_array($activeRole, ['superadmin', 'pengelola_jurusan', 'kasir'])) {
            abort(403, 'Unauthorized.');
        }

        if ($this->deleteId) {
            LabantikRegistration::findOrFail($this->deleteId)->delete();
            $this->dispatch('toast', message: 'Data calon Labantik berhasil dihapus.');
            $this->showDeleteModal = false;
            $this->deleteId = '';
        }
    }

    public function render()
    {
        $activeRole = session('active_role_name');
        if (!in_array($activeRole, ['superadmin', 'pengelola_jurusan', 'kasir'])) {
            abort(403, 'Unauthorized.');
        }

        $activeJurusanId = session('active_jurusan_id') ?: $this->selectedJurusanId;

        $query = LabantikRegistration::with('jurusan');

        if ($activeJurusanId) {
            $query->where(function ($q) use ($activeJurusanId) {
                $q->where('jurusan_id', $activeJurusanId)
                  ->orWhereNull('jurusan_id');
            });
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%')
                  ->orWhere('class_name', 'like', '%' . $this->search . '%')
                  ->orWhere('address', 'like', '%' . $this->search . '%');
            });
        }

        $candidates = $query->orderBy('created_at', 'desc')->paginate(15);
        $jurusans = Jurusan::all();

        return view('livewire.management.labantik-candidates', [
            'candidates' => $candidates,
            'jurusans' => $jurusans,
            'isSuperAdmin' => $activeRole === 'superadmin',
        ])->layout('layouts.app', ['title' => 'Data Calon Anggota Labantik']);
    }
}
