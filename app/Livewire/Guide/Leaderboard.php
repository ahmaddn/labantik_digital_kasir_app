<?php

namespace App\Livewire\Guide;

use App\Models\User;
use Livewire\Component;

class Leaderboard extends Component
{
    public function render()
    {
        $leaderboard = User::select('*')
            ->selectRaw('(points + pending_points) as total_score')
            ->orderByDesc('total_score')
            ->get();

        return view('livewire.guide.leaderboard', [
            'leaderboard' => $leaderboard
        ])->layout('layouts.app', ['title' => 'Sistem Peringkat & Poin']);
    }
}
