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
        return view('livewire.guide.cashier-guide')
            ->layout('layouts.app', ['title' => 'Petunjuk & SOP Kasir']);
    }
}
