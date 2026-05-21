<?php

namespace App\Livewire\Finance;

use App\Models\CashTransaction;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class CashManagement extends Component
{
    use WithPagination;

    // Filters
    public string $filterMonth = '';

    // Form inputs
    public string $date = '';
    public string $cashType = 'modal';
    public string $type = 'expense';
    public $amount;
    public string $description = '';

    // Modal state
    public bool $showModal = false;

    public function mount()
    {
        $this->filterMonth = now()->format('Y-m');
        $this->date = now()->format('Y-m-d');
    }

    public function updatedFilterMonth()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->reset(['type', 'cashType', 'amount', 'description']);
        $this->date = now()->format('Y-m-d');
        $this->type = 'expense';
        $this->cashType = 'modal';
        $this->showModal = true;
    }

    public function saveTransaction()
    {
        $this->validate([
            'date' => 'required|date',
            'cashType' => 'required|in:modal,keuntungan',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string|max:255',
        ]);

        CashTransaction::create([
            'date' => $this->date,
            'cash_type' => $this->cashType,
            'type' => $this->type,
            'amount' => $this->amount,
            'description' => $this->description,
        ]);

        $this->showModal = false;
        $this->dispatch('toast', message: 'Data kas berhasil ditambahkan!');
    }

    public function deleteTransaction($id)
    {
        $transaction = CashTransaction::find($id);
        if ($transaction) {
            $transaction->delete();
            $this->dispatch('toast', message: 'Catatan kas berhasil dihapus.');
        }
    }

    public function render()
    {
        // Global Balance
        $modalIncome = CashTransaction::where('cash_type', 'modal')->where('type', 'income')->sum('amount');
        $modalExpense = CashTransaction::where('cash_type', 'modal')->where('type', 'expense')->sum('amount');
        $currentModalBalance = $modalIncome - $modalExpense;

        $profitIncome = CashTransaction::where('cash_type', 'keuntungan')->where('type', 'income')->sum('amount');
        $profitExpense = CashTransaction::where('cash_type', 'keuntungan')->where('type', 'expense')->sum('amount');
        $currentProfitBalance = $profitIncome - $profitExpense;

        // Monthly Stats based on filter
        $monthlyTransactions = CashTransaction::whereYear('date', Carbon::parse($this->filterMonth)->year)
            ->whereMonth('date', Carbon::parse($this->filterMonth)->month);

        $monthlyIncome = (clone $monthlyTransactions)->where('type', 'income')->sum('amount');
        $monthlyExpense = (clone $monthlyTransactions)->where('type', 'expense')->sum('amount');

        // Paginated Data
        $transactions = $monthlyTransactions->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate(15);

        return view('livewire.finance.cash-management', [
            'transactions' => $transactions,
            'currentModalBalance' => $currentModalBalance,
            'currentProfitBalance' => $currentProfitBalance,
            'monthlyIncome' => $monthlyIncome,
            'monthlyExpense' => $monthlyExpense,
        ])->layout('layouts.app', ['title' => 'Buku Kas Internal']);
    }
}
