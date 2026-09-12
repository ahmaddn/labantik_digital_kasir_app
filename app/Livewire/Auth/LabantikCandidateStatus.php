<?php

namespace App\Livewire\Auth;

use App\Models\LabantikRegistration;
use Livewire\Component;

class LabantikCandidateStatus extends Component
{
    public ?LabantikRegistration $candidate = null;
    public string $waGroupLink = '';

    public function mount()
    {
        $candidateId = session('labantik_candidate_id');
        if (!$candidateId) {
            return redirect()->route('labantik.login');
        }

        $this->candidate = LabantikRegistration::find($candidateId);
        if (!$this->candidate) {
            session()->forget('labantik_candidate_id');
            return redirect()->route('labantik.login');
        }

        $settings = json_decode(@file_get_contents(storage_path('app/settings.json')), true) ?: [];
        $this->waGroupLink = $settings['wa_group_link'] ?? '';
    }

    public function logoutCandidate()
    {
        session()->forget('labantik_candidate_id');
        return redirect()->route('labantik.login');
    }

    public function render()
    {
        return view('livewire.auth.labantik-candidate-status')
            ->layout('layouts.blank', ['title' => 'Hasil Seleksi Labantik']);
    }
}
