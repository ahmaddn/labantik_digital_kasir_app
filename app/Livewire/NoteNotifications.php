<?php

namespace App\Livewire;

use App\Models\CashierNote;
use App\Models\NoteReply;
use Livewire\Component;

class NoteNotifications extends Component
{
    public bool $isOpen = false;

    public function toggleDropdown(): void
    {
        $this->isOpen = !$this->isOpen;
    }

    public function render()
    {
        $user = auth()->user();
        $currentUserId = $user->id;
        $isSuperAdmin = $user->roles()->where('name', 'superadmin')->exists();
        $activeJurusanId = session('active_jurusan_id');

        // Unread replies for notes created by the current user OR target user, where reply was written by someone else
        $notifications = NoteReply::with(['note', 'user'])
            ->whereHas('note', function ($q) use ($isSuperAdmin, $activeJurusanId, $currentUserId) {
                if (!$isSuperAdmin && $activeJurusanId) {
                    $q->where('jurusan_id', $activeJurusanId);
                }
                if (!$isSuperAdmin) {
                    $q->where(function ($sq) use ($currentUserId) {
                        $sq->where('user_id', $currentUserId)
                           ->orWhere('target_user_id', $currentUserId);
                    });
                }
            })
            ->where('user_id', '!=', $currentUserId) // Don't notify self replies
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        $unreadCount = $notifications->count();

        return view('livewire.note-notifications', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }
}
