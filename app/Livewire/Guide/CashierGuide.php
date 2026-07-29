<?php

namespace App\Livewire\Guide;

use Livewire\Component;

class CashierGuide extends Component
{
    public $activeTab = 'sop-transaksi';

    public function selectTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        $leaderboard = \App\Models\User::select('*')
            ->selectRaw('(points + pending_points) as total_score')
            ->orderByDesc('total_score')
            ->get();

        return view('livewire.guide.cashier-guide', [
            'leaderboard' => $leaderboard
        ])->layout('layouts.app', ['title' => 'Petunjuk & SOP Kasir']);
    }
}
