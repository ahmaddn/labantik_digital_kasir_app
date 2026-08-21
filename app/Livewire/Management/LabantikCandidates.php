<?php

namespace App\Livewire\Management;

use App\Models\Jurusan;
use App\Models\LabantikRegistration;
use App\Models\LabantikCandidateScore;
use App\Models\LabantikCandidateAttendance;
use Livewire\Component;
use Livewire\WithPagination;

class LabantikCandidates extends Component
{
    use WithPagination;

    public string $search = '';
    public string $selectedJurusanId = '';

    // Tab control
    public string $activeTab = 'candidates'; // candidates, scoring, accepted

    // Scoring & Attendance parameters
    public int $selectedWeek = 1;
    public array $scores = [];
    public array $attendances = [];

    // WhatsApp Group Link Settings
    public string $waGroupLink = '';
    public bool $showWaLinkModal = false;
    public bool $isRegistrationOpen = false;

    // Delete properties
    public string $deleteId = '';
    public bool $showDeleteModal = false;

    // Detail modal properties
    public bool $showDetailModal = false;
    public ?LabantikRegistration $detailCandidate = null;
    public string $detailAttendancesJson = '[]';

    // Create candidate properties
    public bool $showCreateModal = false;
    public string $new_full_name = '';
    public string $new_class_name = '';
    public string $new_jurusan_id = '';
    public string $new_phone_number = '';
    public string $new_parent_phone_number = '';
    public string $new_address = '';
    public string $new_reason = '';
    public string $new_illness_history = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedJurusanId' => ['except' => ''],
        'activeTab' => ['except' => 'candidates'],
    ];

    public function mount(): void
    {
        $settings = json_decode(@file_get_contents(storage_path('app/settings.json')), true) ?: [];
        $this->waGroupLink = $settings['wa_group_link'] ?? '';
        $this->isRegistrationOpen = (bool) ($settings['is_registration_open'] ?? false);
        $this->loadScoringData();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedJurusanId(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedWeek(): void
    {
        $this->loadScoringData();
    }

    public function loadScoringData(): void
    {
        $activeJurusanId = session('active_jurusan_id') ?: $this->selectedJurusanId;
        $candidates = LabantikRegistration::when($activeJurusanId, function($q) use ($activeJurusanId) {
            $q->where('jurusan_id', $activeJurusanId)->orWhereNull('jurusan_id');
        })->get();

        $this->scores = [];
        $this->attendances = [];

        foreach ($candidates as $candidate) {
            $scoreModel = LabantikCandidateScore::where('registration_id', $candidate->id)
                ->where('week_number', $this->selectedWeek)
                ->first();
            
            $attendanceModel = LabantikCandidateAttendance::where('registration_id', $candidate->id)
                ->where('week_number', $this->selectedWeek)
                ->first();

            $this->scores[$candidate->id] = [
                'score' => $scoreModel ? $scoreModel->score : '',
                'notes' => $scoreModel ? $scoreModel->notes : '',
            ];

            $this->attendances[$candidate->id] = [
                'status' => $attendanceModel ? $attendanceModel->status : 'hadir',
                'reason' => $attendanceModel ? $attendanceModel->reason : '',
            ];
        }
    }

    public function saveScoring(): void
    {
        $activeRole = session('active_role_name');
        if (!in_array($activeRole, ['superadmin', 'pengelola_jurusan'])) {
            $this->dispatch('toast', message: 'Hanya superadmin/pengelola yang dapat menginput nilai.');
            return;
        }

        foreach ($this->scores as $candidateId => $scoreData) {
            $scoreVal = $scoreData['score'];
            $notesVal = $scoreData['notes'];

            if ($scoreVal !== '') {
                LabantikCandidateScore::updateOrCreate(
                    ['registration_id' => $candidateId, 'week_number' => $this->selectedWeek],
                    ['score' => intval($scoreVal), 'notes' => $notesVal]
                );
            }

            $attData = $this->attendances[$candidateId] ?? ['status' => 'hadir', 'reason' => ''];
            $statusVal = $attData['status'];
            $reasonVal = $attData['reason'];

            if (in_array($statusVal, ['hadir', 'sakit']) && empty(trim($reasonVal))) {
                $this->dispatch('toast', message: 'Alasan hadir/sakit wajib diisi untuk masing-masing peserta.');
                return;
            }

            LabantikCandidateAttendance::updateOrCreate(
                ['registration_id' => $candidateId, 'week_number' => $this->selectedWeek],
                ['status' => $statusVal, 'reason' => $reasonVal]
            );
        }

        $this->dispatch('toast', message: 'Nilai dan absensi pekan ini berhasil disimpan!');
        $this->loadScoringData();
    }

    public function finishSelection(): void
    {
        $activeRole = session('active_role_name');
        if (!in_array($activeRole, ['superadmin', 'pengelola_jurusan'])) {
            abort(403, 'Unauthorized.');
        }

        $activeJurusanId = session('active_jurusan_id') ?: $this->selectedJurusanId;
        $candidates = LabantikRegistration::with(['scores', 'attendances'])
            ->when($activeJurusanId, function($q) use ($activeJurusanId) {
                $q->where('jurusan_id', $activeJurusanId)->orWhereNull('jurusan_id');
            })
            ->get();

        if ($candidates->isEmpty()) {
            $this->dispatch('toast', message: 'Tidak ada peserta untuk diseleksi.');
            return;
        }

        // Reset accepted status first
        foreach ($candidates as $c) {
            $c->is_accepted = false;
            $c->save();
        }

        $calculated = [];
        foreach ($candidates as $candidate) {
            $totalScore = 0;
            $weeks = count($candidate->scores);
            
            if ($weeks > 0) {
                foreach ($candidate->scores as $s) {
                    $totalScore += $s->score;
                }
                $avgScore = $totalScore / $weeks;
            } else {
                $avgScore = 0;
            }

            // Deduct penalty: -5 for alfa, -2 for izin
            $alfaCount = $candidate->attendances->where('status', 'alfa')->count();
            $izinCount = $candidate->attendances->where('status', 'izin')->count();
            
            $finalScore = $avgScore - ($alfaCount * 5) - ($izinCount * 2);

            $calculated[] = [
                'candidate' => $candidate,
                'final_score' => $finalScore
            ];
        }

        // Sort by final_score descending
        usort($calculated, function($a, $b) {
            return $b['final_score'] <=> $a['final_score'];
        });

        // Top 15 mark as accepted
        $top15 = array_slice($calculated, 0, 15);
        foreach ($top15 as $item) {
            $item['candidate']->is_accepted = true;
            $item['candidate']->save();
        }

        $this->activeTab = 'accepted';
        $this->dispatch('toast', message: 'Proses seleksi selesai! 15 calon terbaik telah terpilih.');
    }

    public function showDetails(string $id): void
    {
        $this->detailCandidate = LabantikRegistration::with(['scores', 'attendances'])->findOrFail($id);

        // Map weeks to dates in 2026-08 for FullCalendar display
        $events = [];
        foreach ($this->detailCandidate->attendances as $att) {
            $date = date('Y-m-d', strtotime("2026-08-01 + " . ($att->week_number - 1) . " weeks"));
            
            $color = '#6b7280';
            if ($att->status === 'hadir') $color = '#10b981';
            elseif ($att->status === 'sakit') $color = '#3b82f6';
            elseif ($att->status === 'izin') $color = '#f59e0b';
            elseif ($att->status === 'alfa') $color = '#ef4444';

            $events[] = [
                'title' => 'Pekan ' . $att->week_number . ': ' . strtoupper($att->status) . ($att->reason ? ' - ' . $att->reason : ''),
                'start' => $date,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'allDay' => true
            ];
        }

        $this->detailAttendancesJson = json_encode($events);
        $this->showDetailModal = true;
    }

    public function openCreateModal(): void
    {
        $this->reset([
            'new_full_name', 'new_class_name', 'new_jurusan_id', 
            'new_phone_number', 'new_parent_phone_number', 
            'new_address', 'new_reason', 'new_illness_history'
        ]);
        $this->showCreateModal = true;
    }

    public function storeCandidate(): void
    {
        $activeRole = session('active_role_name');
        if (!in_array($activeRole, ['superadmin', 'pengelola_jurusan'])) {
            abort(403, 'Unauthorized.');
        }

        $this->validate([
            'new_full_name' => 'required|string|max:255',
            'new_class_name' => 'required|string|max:100',
            'new_jurusan_id' => 'nullable|exists:jurusans,id',
            'new_phone_number' => 'required|string|max:20',
            'new_parent_phone_number' => 'required|string|max:20',
            'new_address' => 'required|string',
            'new_reason' => 'nullable|string',
            'new_illness_history' => 'nullable|string|max:255',
        ], [], [
            'new_full_name' => 'Nama Lengkap',
            'new_class_name' => 'Kelas',
            'new_jurusan_id' => 'Jurusan',
            'new_phone_number' => 'No HP',
            'new_parent_phone_number' => 'No HP Orang Tua',
            'new_address' => 'Alamat',
        ]);

        LabantikRegistration::create([
            'jurusan_id' => $this->new_jurusan_id ?: null,
            'full_name' => $this->new_full_name,
            'class_name' => $this->new_class_name,
            'phone_number' => $this->new_phone_number,
            'parent_phone_number' => $this->new_parent_phone_number,
            'address' => $this->new_address,
            'reason' => $this->new_reason,
            'illness_history' => $this->new_illness_history,
            'is_joined_group' => false,
            'is_accepted' => false,
        ]);

        $this->showCreateModal = false;
        $this->dispatch('toast', message: 'Calon anggota baru berhasil ditambahkan!');
        $this->loadScoringData();
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

    public function toggleJoinedGroup(string $id): void
    {
        $activeRole = session('active_role_name');
        if (!in_array($activeRole, ['superadmin', 'pengelola_jurusan', 'kasir'])) {
            abort(403, 'Unauthorized.');
        }

        $candidate = LabantikRegistration::findOrFail($id);
        $candidate->is_joined_group = !$candidate->is_joined_group;
        $candidate->save();

        $this->dispatch('toast', message: 'Status grup WhatsApp berhasil diperbarui.');
    }

    public function toggleRegistration(): void
    {
        $activeRole = session('active_role_name');
        if (!in_array($activeRole, ['superadmin', 'pengelola_jurusan'])) {
            abort(403, 'Unauthorized.');
        }

        $settings = json_decode(@file_get_contents(storage_path('app/settings.json')), true) ?: [];
        $this->isRegistrationOpen = !$this->isRegistrationOpen;
        $settings['is_registration_open'] = $this->isRegistrationOpen;

        if (!file_exists(storage_path('app'))) {
            mkdir(storage_path('app'), 0755, true);
        }

        file_put_contents(storage_path('app/settings.json'), json_encode($settings));
        
        $status = $this->isRegistrationOpen ? 'dibuka' : 'ditutup';
        $this->dispatch('toast', message: "Pendaftaran calon anggota Labantik berhasil {$status}!");
    }

    public function render()
    {
        $activeRole = session('active_role_name');
        if (!in_array($activeRole, ['superadmin', 'pengelola_jurusan', 'kasir'])) {
            abort(403, 'Unauthorized.');
        }

        $activeJurusanId = session('active_jurusan_id') ?: $this->selectedJurusanId;

        $jurusans = Jurusan::all();

        // 1. Tab candidates (Pendaftar)
        $query = LabantikRegistration::with('jurusan');
        if ($activeJurusanId) {
            $query->where(function ($q) use ($activeJurusanId) {
                $q->where('jurusan_id', $activeJurusanId)->orWhereNull('jurusan_id');
            });
        }
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%')
                  ->orWhere('class_name', 'like', '%' . $this->search . '%');
            });
        }
        $candidates = $query->orderBy('created_at', 'desc')->paginate(15);

        // 2. Tab Accepted (15 Besar Terpilih)
        $acceptedQuery = LabantikRegistration::with(['scores', 'attendances'])->where('is_accepted', true);
        if ($activeJurusanId) {
            $acceptedQuery->where(function ($q) use ($activeJurusanId) {
                $q->where('jurusan_id', $activeJurusanId)->orWhereNull('jurusan_id');
            });
        }
        $acceptedCandidates = $acceptedQuery->get()->map(function($c) {
            // Calculate final average score
            $avg = $c->scores->avg('score') ?: 0;
            $alfa = $c->attendances->where('status', 'alfa')->count();
            $izin = $c->attendances->where('status', 'izin')->count();
            $c->final_score = max(0, $avg - ($alfa * 5) - ($izin * 2));
            return $c;
        })->sortByDesc('final_score');

        return view('livewire.management.labantik-candidates', [
            'candidates' => $candidates,
            'acceptedCandidates' => $acceptedCandidates,
            'jurusans' => $jurusans,
            'isSuperAdmin' => $activeRole === 'superadmin',
            'isPengelola' => in_array($activeRole, ['superadmin', 'pengelola_jurusan']),
        ])->layout('layouts.app', ['title' => 'Data Calon Anggota Labantik']);
    }
}
