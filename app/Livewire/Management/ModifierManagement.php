<?php

namespace App\Livewire\Management;

use App\Models\Modifier;
use App\Models\ModifierGroup;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class ModifierManagement extends Component
{
    use WithPagination;

    // State untuk Modifiers (Topping)
    public $modifierName = '';
    public $modifierPrice = '';
    public $editingModifierId = null;
    public bool $showModifierModal = false;

    // State untuk Modifier Groups (Kelompok Topping)
    public $groupName = '';
    public $minSelection = 0;
    public $maxSelection = 1;
    public $editingGroupId = null;
    public $selectedModifiers = []; // Topping yang diikat ke kelompok ini
    public bool $showGroupModal = false;

    public $activeTab = 'modifiers'; // 'modifiers' | 'groups'

    protected function rules()
    {
        if ($this->activeTab === 'modifiers') {
            return [
                'modifierName' => 'required|string|max:255',
                'modifierPrice' => 'required|numeric|min:0',
            ];
        }
        return [
            'groupName' => 'required|string|max:255',
            'minSelection' => 'required|integer|min:0',
            'maxSelection' => 'required|integer|min:1',
            'selectedModifiers' => 'required|array|min:1',
        ];
    }

    public function mount()
    {
        $activeRole = session('active_role_name');
        if (!in_array($activeRole, ['superadmin', 'pengelola_jurusan'])) {
            abort(403, 'Unauthorized.');
        }
    }

    // --- Action Modifier (Topping) ---

    public function openModifierModal($id = null)
    {
        $this->resetValidation();
        $this->editingModifierId = $id;

        if ($id) {
            $mod = Modifier::findOrFail($id);
            $this->modifierName = $mod->name;
            $this->modifierPrice = $mod->price;
        } else {
            $this->modifierName = '';
            $this->modifierPrice = '';
        }

        $this->showModifierModal = true;
    }

    public function saveModifier()
    {
        $this->activeTab = 'modifiers';
        $this->validate();

        $activeJurusanId = session('active_jurusan_id');

        if ($this->editingModifierId) {
            $mod = Modifier::findOrFail($this->editingModifierId);
            $mod->update([
                'name' => $this->modifierName,
                'price' => $this->modifierPrice,
            ]);
            $this->dispatch('toast', message: 'Topping berhasil diperbarui.');
        } else {
            Modifier::create([
                'jurusan_id' => $activeJurusanId,
                'name' => $this->modifierName,
                'price' => $this->modifierPrice,
            ]);
            $this->dispatch('toast', message: 'Topping berhasil ditambahkan.');
        }

        $this->showModifierModal = false;
    }

    public function deleteModifier($id)
    {
        Modifier::findOrFail($id)->delete();
        $this->dispatch('toast', message: 'Topping berhasil dihapus.');
    }

    // --- Action Modifier Groups (Kelompok Topping) ---

    public function openGroupModal($id = null)
    {
        $this->resetValidation();
        $this->editingGroupId = $id;

        if ($id) {
            $group = ModifierGroup::with('modifiers')->findOrFail($id);
            $this->groupName = $group->name;
            $this->minSelection = $group->min_selection;
            $this->maxSelection = $group->max_selection;
            $this->selectedModifiers = $group->modifiers->pluck('id')->toArray();
        } else {
            $this->groupName = '';
            $this->minSelection = 0;
            $this->maxSelection = 1;
            $this->selectedModifiers = [];
        }

        $this->showGroupModal = true;
    }

    public function saveGroup()
    {
        $this->activeTab = 'groups';
        $this->validate();

        $activeJurusanId = session('active_jurusan_id');

        DB::transaction(function () use ($activeJurusanId) {
            if ($this->editingGroupId) {
                $group = ModifierGroup::findOrFail($this->editingGroupId);
                $group->update([
                    'name' => $this->groupName,
                    'min_selection' => $this->minSelection,
                    'max_selection' => $this->maxSelection,
                ]);
                $group->modifiers()->sync($this->selectedModifiers);
                $this->dispatch('toast', message: 'Kelompok topping berhasil diperbarui.');
            } else {
                $group = ModifierGroup::create([
                    'jurusan_id' => $activeJurusanId,
                    'name' => $this->groupName,
                    'min_selection' => $this->minSelection,
                    'max_selection' => $this->maxSelection,
                ]);
                $group->modifiers()->attach($this->selectedModifiers);
                $this->dispatch('toast', message: 'Kelompok topping berhasil ditambahkan.');
            }
        });

        $this->showGroupModal = false;
    }

    public function deleteGroup($id)
    {
        ModifierGroup::findOrFail($id)->delete();
        $this->dispatch('toast', message: 'Kelompok topping berhasil dihapus.');
    }

    public function render()
    {
        $activeJurusanId = session('active_jurusan_id');

        $modifiers = Modifier::where('jurusan_id', $activeJurusanId)
            ->orderBy('name')
            ->paginate(10, ['*'], 'modPage');

        $groups = ModifierGroup::with('modifiers')
            ->where('jurusan_id', $activeJurusanId)
            ->orderBy('name')
            ->paginate(10, ['*'], 'groupPage');

        $allModifiers = Modifier::where('jurusan_id', $activeJurusanId)
            ->orderBy('name')
            ->get();

        return view('livewire.management.modifier-management', [
            'modifiers' => $modifiers,
            'groups' => $groups,
            'allModifiers' => $allModifiers,
        ])->layout('layouts.app', ['title' => 'Manajemen Topping']);
    }
}
