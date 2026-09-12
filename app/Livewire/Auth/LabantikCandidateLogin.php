<?php

namespace App\Livewire\Auth;

use App\Models\LabantikRegistration;
use Livewire\Component;

class LabantikCandidateLogin extends Component
{
    public string $firstName = '';
    public string $lastFourPhone = '';
    public string $errorMessage = '';

    public function loginCandidate()
    {
        $this->errorMessage = '';

        $fname = trim(strtolower($this->firstName));
        $phoneDigits = preg_replace('/[^0-9]/', '', $this->lastFourPhone);

        if (empty($fname) || empty($phoneDigits)) {
            $this->errorMessage = 'Silakan isi Nama Depan dan 4 Digit Terakhir No HP Anda.';
            return;
        }

        $candidates = LabantikRegistration::all();
        $matched = $candidates->first(function ($candidate) use ($fname, $phoneDigits) {
            $nameParts = explode(' ', strtolower(trim($candidate->full_name)));
            $firstWord = $nameParts[0] ?? '';
            
            $cleanPhone = preg_replace('/[^0-9]/', '', $candidate->phone_number);
            $last4 = substr($cleanPhone, -4);

            $isNameMatch = ($firstWord === $fname) || str_contains(strtolower($candidate->full_name), $fname);
            $isPhoneMatch = ($last4 === $phoneDigits) || str_contains($cleanPhone, $phoneDigits);

            return $isNameMatch && $isPhoneMatch;
        });

        if (!$matched) {
            $this->errorMessage = 'Data tidak ditemukan. Pastikan Nama Depan dan 4 digit terakhir No HP sudah sesuai dengan data pendaftaran.';
            return;
        }

        session(['labantik_candidate_id' => $matched->id]);

        return redirect()->route('labantik.status');
    }

    public function render()
    {
        return view('livewire.auth.labantik-candidate-login')
            ->layout('layouts.blank', ['title' => 'Login Portal Calon Labantik']);
    }
}
