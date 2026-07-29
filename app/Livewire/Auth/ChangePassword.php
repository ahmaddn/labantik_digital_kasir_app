<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class ChangePassword extends Component
{
    public $showModal = false;
    public $currentPassword = '';
    public $newPassword = '';
    public $newPasswordConfirmation = '';

    protected $listeners = ['open-change-password-modal' => 'openModal'];

    public function openModal()
    {
        $this->resetInput();
        $this->showModal = true;
    }

    public function resetInput()
    {
        $this->currentPassword = '';
        $this->newPassword = '';
        $this->newPasswordConfirmation = '';
    }

    public function savePassword()
    {
        $this->validate([
            'currentPassword' => 'required',
            'newPassword' => 'required|min:6|confirmed',
        ], [
            'currentPassword.required' => 'Password saat ini wajib diisi.',
            'newPassword.required' => 'Password baru wajib diisi.',
            'newPassword.min' => 'Password baru minimal 6 karakter.',
            'newPassword.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user = User::find(auth()->id());

        if (!Hash::check($this->currentPassword, $user->password)) {
            $this->addError('currentPassword', 'Password saat ini tidak sesuai.');
            return;
        }

        $user->password = Hash::make($this->newPassword);
        $user->save();

        $this->showModal = false;
        $this->resetInput();
        $this->dispatch('toast', message: 'Password berhasil diperbarui.');
    }

    public function render()
    {
        return view('livewire.auth.change-password');
    }
}
