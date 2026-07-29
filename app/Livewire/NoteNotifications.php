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
        if ($this->isOpen) {
            $this->markAllAsRead();
        }
    }

    public function markAllAsRead()
    {
        $user = auth()->user();
        if ($user) {
            \App\Models\Notification::where('user_id', $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }
    }

    public function render()
    {
        $user = auth()->user();
        if (!$user) {
            return view('livewire.note-notifications', [
                'notifications' => collect(),
                'unreadCount' => 0,
            ]);
        }

        $notifications = \App\Models\Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $unreadCount = \App\Models\Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        return view('livewire.note-notifications', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }
}
