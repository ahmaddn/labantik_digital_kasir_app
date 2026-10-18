<?php

namespace App\Livewire;

use App\Models\Notification;
use Livewire\Component;

class NoteNotifications extends Component
{
    public function markAllAsRead()
    {
        $user = auth()->user();
        if ($user) {
            Notification::where('user_id', $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }
    }

    public function render()
    {
        $user = auth()->user();
        if (! $user) {
            return view('livewire.note-notifications', [
                'notifications' => collect(),
                'unreadCount' => 0,
            ]);
        }

        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $unreadCount = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        return view('livewire.note-notifications', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }
}
