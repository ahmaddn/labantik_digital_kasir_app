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

    // Edit candidate properties
    public bool $showEditModal = false;
    public string $editId = '';
    public string $edit_full_name = '';
    public string $edit_class_name = '';
    public string $edit_jurusan_id = '';
    public string $edit_phone_number = '';
    public string $edit_parent_phone_number = '';
    public string $edit_address = '';
    public string $edit_reason = '';
    public string $edit_illness_history = '';

    // Finish selection & Single scoring properties
    public bool $showFinishConfirmModal = false;
    public bool $showResetWeekConfirmModal = false;
    public bool $showSingleScoringModal = false;
    public ?LabantikRegistration $scoringCandidate = null;
    public array $singleScores = [];
    public array $singleAttendances = [];

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

    public function checkPermission(): bool
    {
        $activeRole = session('active_role_name');
        if (in_array($activeRole, ['superadmin', 'pengelola_jurusan'])) {
            return true;
        }

        if ($activeRole === 'kasir') {
            $userJurusanId = session('active_jurusan_id') ?? auth()->user()->jurusan_id;
            if ($userJurusanId) {
                $jurusan = Jurusan::find($userJurusanId);
                if ($jurusan && str_contains(strtolower($jurusan->name), 'rpl')) {
                    return true;
                }
            }
        }

        return false;
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
                ->where(function($q) {
                    $q->where('user_id', auth()->id())->orWhereNull('user_id');
                })
                ->first();
            
            $attendanceModel = LabantikCandidateAttendance::where('registration_id', $candidate->id)
                ->where('week_number', $this->selectedWeek)
                ->first();

            $this->scores[$candidate->id] = [
                'score' => $scoreModel ? $scoreModel->score : '',
                'attitude_score' => $scoreModel ? $scoreModel->attitude_score : '',
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
        if (!$this->checkPermission()) {
            $this->dispatch('toast', message: 'Hanya superadmin/pengelola atau kasir jurusan RPL yang dapat menginput nilai.');
            return;
        }

        foreach ($this->scores as $candidateId => $scoreData) {
            $attData = $this->attendances[$candidateId] ?? ['status' => 'hadir', 'reason' => ''];
            $statusVal = $attData['status'];
            $reasonVal = $attData['reason'];

            if ($statusVal !== 'hadir') {
                // Clear scores on database if not present
                LabantikCandidateScore::where('registration_id', $candidateId)
                    ->where('week_number', $this->selectedWeek)
                    ->where('user_id', auth()->id())
                    ->delete();
            } else {
                $scoreVal = $scoreData['score'];
                $attitudeVal = $scoreData['attitude_score'] ?? '';
                $notesVal = $scoreData['notes'] ?? '';

                if ($scoreVal !== '' || $attitudeVal !== '') {
                    LabantikCandidateScore::updateOrCreate(
                        [
                            'registration_id' => $candidateId, 
                            'week_number' => $this->selectedWeek,
                            'user_id' => auth()->id()
                        ],
                        [
                            'score' => $scoreVal !== '' ? intval($scoreVal) : 0,
                            'attitude_score' => $attitudeVal !== '' ? intval($attitudeVal) : 0,
                            'notes' => $notesVal
                        ]
                    );
                }
            }

            if (in_array($statusVal, ['sakit', 'izin']) && empty(trim($reasonVal))) {
                $this->dispatch('toast', message: 'Alasan sakit/izin wajib diisi untuk masing-masing peserta.');
                return;
            }

            LabantikCandidateAttendance::updateOrCreate(
                ['registration_id' => $candidateId, 'week_number' => $this->selectedWeek],
                ['status' => $statusVal, 'reason' => $statusVal === 'hadir' || $statusVal === 'alfa' ? null : $reasonVal]
            );
        }

        $this->dispatch('toast', message: 'Nilai dan absensi pekan ini berhasil disimpan!');
        $this->loadScoringData();
    }

    public function resetWeekScoring(): void
    {
        if (!$this->checkPermission()) {
            $this->dispatch('toast', message: 'Hanya superadmin/pengelola yang dapat mereset nilai.');
            return;
        }

        $activeJurusanId = session('active_jurusan_id') ?: $this->selectedJurusanId;
        $candidateIds = LabantikRegistration::when($activeJurusanId, function($q) use ($activeJurusanId) {
            $q->where('jurusan_id', $activeJurusanId)->orWhereNull('jurusan_id');
        })->pluck('id');

        LabantikCandidateScore::whereIn('registration_id', $candidateIds)
            ->where('week_number', $this->selectedWeek)
            ->where(function($q) {
                $q->where('user_id', auth()->id())->orWhereNull('user_id');
            })
            ->delete();

        $this->showResetWeekConfirmModal = false;
        $this->dispatch('toast', message: "Nilai masukan pada Pekan {$this->selectedWeek} berhasil direset!");
        $this->loadScoringData();
    }

    public function finishSelection(): void
    {
        if (!$this->checkPermission()) {
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
            $c->final_status = 'rejected';
            $c->save();
        }

        $calculated = [];
        foreach ($candidates as $candidate) {
            // Averages across all scores given by all users (graders) and weeks
            $avgScore = $candidate->scores->avg('score') ?: 0;
            $avgAttitude = $candidate->scores->avg('attitude_score') ?: 0;

            // Attendance calculations: +5 for hadir, -10 for alfa, -2 for izin
            $hadirCount = $candidate->attendances->where('status', 'hadir')->count();
            $alfaCount = $candidate->attendances->where('status', 'alfa')->count();
            $izinCount = $candidate->attendances->where('status', 'izin')->count();
            
            $finalScore = $avgScore + $avgAttitude + ($hadirCount * 5) - ($alfaCount * 10) - ($izinCount * 2);

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
            $item['candidate']->final_status = 'pending';
            $item['candidate']->save();
        }

        $this->activeTab = 'accepted';
        $this->dispatch('toast', message: 'Proses seleksi selesai! 15 calon terbaik telah terpilih.');
    }

    public function updateFinalStatus(string $candidateId, string $status): void
    {
        if (!$this->checkPermission()) {
            $this->dispatch('toast', message: 'Hanya pengelola / superadmin yang dapat merubah status seleksi.');
            return;
        }

        if (!in_array($status, ['passed', 'rejected', 'pending'])) {
            return;
        }

        $candidate = LabantikRegistration::findOrFail($candidateId);
        $candidate->final_status = $status;
        $candidate->save();

        $statusText = $status === 'passed' ? 'LOLOS SELEKSI' : ($status === 'rejected' ? 'TIDAK LOLOS' : 'PENDING');
        $this->dispatch('toast', message: "Status seleksi {$candidate->full_name} berhasil diubah menjadi {$statusText}.");
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

    public function openSingleScoringModal(string $candidateId): void
    {
        $this->scoringCandidate = LabantikRegistration::findOrFail($candidateId);
        $this->singleScores = [];
        $this->singleAttendances = [];

        for ($w = 1; $w <= 12; $w++) {
            $scoreModel = LabantikCandidateScore::where('registration_id', $candidateId)
                ->where('week_number', $w)
                ->where(function($q) {
                    $q->where('user_id', auth()->id())->orWhereNull('user_id');
                })
                ->first();
            
            $attendanceModel = LabantikCandidateAttendance::where('registration_id', $candidateId)
                ->where('week_number', $w)
                ->first();

            $this->singleScores[$w] = [
                'score' => $scoreModel ? $scoreModel->score : '',
                'attitude_score' => $scoreModel ? $scoreModel->attitude_score : '',
                'notes' => $scoreModel ? $scoreModel->notes : '',
            ];

            $this->singleAttendances[$w] = [
                'status' => $attendanceModel ? $attendanceModel->status : 'hadir',
                'reason' => $attendanceModel ? $attendanceModel->reason : '',
            ];
        }

        $this->showSingleScoringModal = true;
    }

    public function saveSingleScoring(): void
    {
        if (!$this->checkPermission()) {
            $this->dispatch('toast', message: 'Hanya superadmin/pengelola atau kasir jurusan RPL yang dapat menginput nilai.');
            return;
        }

        foreach ($this->singleScores as $w => $scoreData) {
            $attData = $this->singleAttendances[$w] ?? ['status' => 'hadir', 'reason' => ''];
            $statusVal = $attData['status'];
            $reasonVal = $attData['reason'];

            if ($statusVal !== 'hadir') {
                // Clear scores on database if not present
                LabantikCandidateScore::where('registration_id', $this->scoringCandidate->id)
                    ->where('week_number', $w)
                    ->where('user_id', auth()->id())
                    ->delete();
            } else {
                $scoreVal = $scoreData['score'];
                $attitudeVal = $scoreData['attitude_score'] ?? '';
                $notesVal = $scoreData['notes'] ?? '';

                if ($scoreVal !== '' || $attitudeVal !== '') {
                    LabantikCandidateScore::updateOrCreate(
                        [
                            'registration_id' => $this->scoringCandidate->id, 
                            'week_number' => $w,
                            'user_id' => auth()->id()
                        ],
                        [
                            'score' => $scoreVal !== '' ? intval($scoreVal) : 0,
                            'attitude_score' => $attitudeVal !== '' ? intval($attitudeVal) : 0,
                            'notes' => $notesVal
                        ]
                    );
                }
            }

            if (in_array($statusVal, ['sakit', 'izin']) && empty(trim($reasonVal))) {
                $this->dispatch('toast', message: "Alasan sakit/izin pada pekan {$w} wajib diisi.");
                return;
            }

            LabantikCandidateAttendance::updateOrCreate(
                ['registration_id' => $this->scoringCandidate->id, 'week_number' => $w],
                ['status' => $statusVal, 'reason' => $statusVal === 'hadir' || $statusVal === 'alfa' ? null : $reasonVal]
            );
        }

        $this->showSingleScoringModal = false;
        $this->dispatch('toast', message: 'Penilaian calon anggota berhasil diperbarui!');
        $this->loadScoringData();
    }

    public function openCreateModal(): void
    {
        if (!$this->checkPermission()) {
            $this->dispatch('toast', message: 'Hanya superadmin/pengelola yang dapat menambah calon anggota.');
            return;
        }

        $this->new_full_name = '';
        $this->new_class_name = '';
        $this->new_jurusan_id = '';
        $this->new_phone_number = '';
        $this->new_parent_phone_number = '';
        $this->new_address = '';
        $this->new_reason = '';
        $this->new_illness_history = '';

        $this->showCreateModal = true;
    }

    public function storeCandidate(): void
    {
        if (!$this->checkPermission()) {
            $this->dispatch('toast', message: 'Hanya superadmin/pengelola yang dapat menambah calon anggota.');
            return;
        }

        $this->validate([
            'new_full_name' => 'required|string|max:255',
            'new_class_name' => 'required|string|max:50',
            'new_jurusan_id' => 'required|uuid|exists:jurusans,id',
            'new_phone_number' => 'required|string|max:20',
            'new_parent_phone_number' => 'required|string|max:20',
            'new_address' => 'required|string|max:500',
            'new_reason' => 'nullable|string|max:1000',
            'new_illness_history' => 'nullable|string|max:500',
        ], [], [
            'new_full_name' => 'Nama Lengkap',
            'new_class_name' => 'Kelas',
            'new_jurusan_id' => 'Jurusan Tujuan',
            'new_phone_number' => 'No HP Calon',
            'new_parent_phone_number' => 'No HP Orang Tua',
            'new_address' => 'Alamat Rumah',
        ]);

        LabantikRegistration::create([
            'full_name' => $this->new_full_name,
            'jurusan_id' => $this->new_jurusan_id,
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

    public function openEditModal(string $id): void
    {
        if (!$this->checkPermission()) {
            $this->dispatch('toast', message: 'Hanya superadmin/pengelola yang dapat mengubah data calon anggota.');
            return;
        }

        $candidate = LabantikRegistration::findOrFail($id);
        $this->editId = $candidate->id;
        $this->edit_full_name = $candidate->full_name ?? '';
        $this->edit_class_name = $candidate->class_name ?? '';
        $this->edit_jurusan_id = $candidate->jurusan_id ?? '';
        $this->edit_phone_number = $candidate->phone_number ?? '';
        $this->edit_parent_phone_number = $candidate->parent_phone_number ?? '';
        $this->edit_address = $candidate->address ?? '';
        $this->edit_reason = $candidate->reason ?? '';
        $this->edit_illness_history = $candidate->illness_history ?? '';

        $this->showEditModal = true;
    }

    public function updateCandidate(): void
    {
        if (!$this->checkPermission()) {
            $this->dispatch('toast', message: 'Hanya superadmin/pengelola yang dapat mengubah data calon anggota.');
            return;
        }

        $this->validate([
            'edit_full_name' => 'required|string|max:255',
            'edit_class_name' => 'required|string|max:50',
            'edit_jurusan_id' => 'nullable|uuid|exists:jurusans,id',
            'edit_phone_number' => 'required|string|max:20',
            'edit_parent_phone_number' => 'required|string|max:20',
            'edit_address' => 'required|string|max:500',
            'edit_reason' => 'nullable|string|max:1000',
            'edit_illness_history' => 'nullable|string|max:500',
        ], [], [
            'edit_full_name' => 'Nama Lengkap',
            'edit_class_name' => 'Kelas',
            'edit_jurusan_id' => 'Jurusan',
            'edit_phone_number' => 'No HP Calon',
            'edit_parent_phone_number' => 'No HP Orang Tua',
            'edit_address' => 'Alamat Rumah',
        ]);

        $candidate = LabantikRegistration::findOrFail($this->editId);
        $candidate->update([
            'full_name' => $this->edit_full_name,
            'class_name' => $this->edit_class_name,
            'jurusan_id' => $this->edit_jurusan_id ?: null,
            'phone_number' => $this->edit_phone_number,
            'parent_phone_number' => $this->edit_parent_phone_number,
            'address' => $this->edit_address,
            'reason' => $this->edit_reason,
            'illness_history' => $this->edit_illness_history ?: null,
        ]);

        $this->showEditModal = false;
        $this->dispatch('toast', message: 'Data calon anggota berhasil diperbarui!');
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
        if (!$this->checkPermission()) {
            abort(403, 'Unauthorized.');
        }

        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteCandidate(): void
    {
        if (!$this->checkPermission()) {
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
        if (!$this->checkPermission()) {
            abort(403, 'Unauthorized.');
        }

        $candidate = LabantikRegistration::findOrFail($id);
        $candidate->is_joined_group = !$candidate->is_joined_group;
        $candidate->save();

        $this->dispatch('toast', message: 'Status grup WhatsApp berhasil diperbarui.');
    }

    public function toggleRegistration(): void
    {
        if (!$this->checkPermission()) {
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
        $candidates = $query->orderBy('created_at', 'desc')->paginate(10, ['*'], 'candidatesPage');

        // 2. Tab scoring (Input Nilai & Absen)
        $scoringQuery = LabantikRegistration::query();
        if ($activeJurusanId) {
            $scoringQuery->where(function ($q) use ($activeJurusanId) {
                $q->where('jurusan_id', $activeJurusanId)->orWhereNull('jurusan_id');
            });
        }
        $scoringCandidates = $scoringQuery->orderBy('created_at', 'desc')->paginate(10, ['*'], 'scoringPage');

        // 3. Tab Accepted (15 Besar Terpilih)
        $acceptedQuery = LabantikRegistration::with(['scores', 'attendances'])->where('is_accepted', true);
        if ($activeJurusanId) {
            $acceptedQuery->where(function ($q) use ($activeJurusanId) {
                $q->where('jurusan_id', $activeJurusanId)->orWhereNull('jurusan_id');
            });
        }
        $acceptedCandidates = $acceptedQuery->get()->map(function($c) {
            $avg = $c->scores->avg('score') ?: 0;
            $avgAttitude = $c->scores->avg('attitude_score') ?: 0;
            $hadir = $c->attendances->where('status', 'hadir')->count();
            $alfa = $c->attendances->where('status', 'alfa')->count();
            $izin = $c->attendances->where('status', 'izin')->count();
            $c->final_score = max(0, $avg + $avgAttitude + ($hadir * 5) - ($alfa * 10) - ($izin * 2));
            return $c;
        })->sortByDesc('final_score');

        $acceptedPage = $this->paginators['acceptedPage'] ?? 1;
        $acceptedPerPage = 10;
        $acceptedSliced = $acceptedCandidates->slice(($acceptedPage - 1) * $acceptedPerPage, $acceptedPerPage)->all();
        $paginatedAccepted = new \Illuminate\Pagination\LengthAwarePaginator(
            $acceptedSliced,
            $acceptedCandidates->count(),
            $acceptedPerPage,
            $acceptedPage,
            ['path' => request()->url(), 'pageName' => 'acceptedPage']
        );

        return view('livewire.management.labantik-candidates', [
            'candidates' => $candidates,
            'scoringCandidates' => $scoringCandidates,
            'acceptedCandidates' => $paginatedAccepted,
            'jurusans' => $jurusans,
            'isSuperAdmin' => $activeRole === 'superadmin',
            'isPengelola' => $this->checkPermission(),
        ])->layout('layouts.app', ['title' => 'Data Calon Anggota Labantik']);
    }
}
