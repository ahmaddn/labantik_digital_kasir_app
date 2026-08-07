<?php

namespace App\Livewire\Auth;

use App\Models\LabantikRegistration;
use Livewire\Component;

class FormRegistration extends Component
{
    public string $fullName = '';
    public string $className = '';
    public string $address = '';
    public string $phoneNumber = '';
    public string $parentPhoneNumber = '';
    public string $reason = '';
    public string $illnessHistory = '';

    public bool $isOpen = false;
    public bool $isSubmitted = false;
    public string $waGroupLink = '';

    protected array $rules = [
        'fullName' => 'required|string|min:3|max:255',
        'className' => 'required|string|max:50',
        'address' => 'required|string|min:5',
        'phoneNumber' => 'required|string|min:9|max:20',
        'parentPhoneNumber' => 'required|string|min:9|max:20',
        'reason' => 'required|string|min:10',
        'illnessHistory' => 'nullable|string',
    ];

    protected array $validationAttributes = [
        'fullName' => 'Nama Lengkap',
        'className' => 'Kelas',
        'address' => 'Alamat',
        'phoneNumber' => 'No HP',
        'parentPhoneNumber' => 'No HP Orang Tua',
        'reason' => 'Alasan masuk Labantik',
        'illnessHistory' => 'Riwayat penyakit bawaan',
    ];

    public function submitRegistration()
    {
        $this->validate();

        LabantikRegistration::create([
            'jurusan_id' => null,
            'full_name' => $this->fullName,
            'class_name' => $this->className,
            'address' => $this->address,
            'phone_number' => $this->phoneNumber,
            'parent_phone_number' => $this->parentPhoneNumber,
            'reason' => $this->reason,
            'illness_history' => $this->illnessHistory ?: null,
        ]);

        // Load the WhatsApp Group Link setting
        $settings = json_decode(@file_get_contents(storage_path('app/settings.json')), true) ?: [];
        $this->waGroupLink = $settings['wa_group_link'] ?? '';

        $this->isSubmitted = true;
        $this->reset(['fullName', 'className', 'address', 'phoneNumber', 'parentPhoneNumber', 'reason', 'illnessHistory']);
    }

    public function render()
    {
        return view('livewire.auth.form-registration')
            ->layout('layouts.blank', ['title' => 'Pendaftaran Calon Anggota Labantik']);
    }
}
