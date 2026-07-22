<?php

namespace App\Livewire\Pos;

use App\Models\DailyRecap;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\StockEntry;
use App\Models\Transaction;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Kasir extends Component
{
    use WithPagination;

    public $transactionDate;

    // UI State
    public $rightSidebarTab = 'cart';

    // Stock Management Properties
    public $showOpeningStockModal = false;

    public $showClosingStockModal = false;

    public $stockItems = []; // Array to hold stock input values [product_id => quantity]

    public $lastClosingStocks = []; // Array to hold yesterday's closing stock [product_id => quantity]

    public $showDetailsModal = false;

    public $detailReference = null;

    public $showRecoveryModal = false;

    public $unfinishedSessionDate = null;

    public function mount(): void
    {
        $this->transactionDate = now()->toDateString();

        $activeJurusanId = session('active_jurusan_id');
        // Check if session for today is already finished
        $isTodayFinished = DailyRecap::whereDate('date', now())
            ->where('jurusan_id', $activeJurusanId)
            ->where('actual_cash', '>', 0)
            ->exists();

        if ($isTodayFinished) {
            session()->flash('error', 'Sesi kasir hari ini telah berakhir. Anda tidak dapat melakukan transaksi lagi.');
            $this->redirectRoute('dashboard', navigate: true);

            return;
        }

        // Detect if there's an unfinished session from a previous day
        $this->detectUnfinishedSession();

        if (! $this->showRecoveryModal) {
            $this->checkOpeningStock();
        }
    }

    protected function getActiveProducts()
    {
        $activeJurusanId = session('active_jurusan_id');

        return Product::where('is_active', true)
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->get();
    }

    protected function detectUnfinishedSession(): void
    {
        $today = now()->toDateString();
        $activeJurusanId = session('active_jurusan_id');

        // Find the most recent date with stock entries before today
        $lastSessionDate = StockEntry::where('date', '<', $today)
            ->whereHas('product', function ($q) use ($activeJurusanId) {
                if ($activeJurusanId) {
                    $q->where('jurusan_id', $activeJurusanId);
                }
            })
            ->orderBy('date', 'desc')
            ->value('date');

        if ($lastSessionDate) {
            // Check if this last session was finished
            $isFinished = DailyRecap::where('date', $lastSessionDate)
                ->where('jurusan_id', $activeJurusanId)
                ->where('actual_cash', '>', 0)
                ->exists();

            if (! $isFinished) {
                $this->unfinishedSessionDate = $lastSessionDate;
                $this->showRecoveryModal = true;
            }
        }
    }

    public function fixUnfinishedSession(): void
    {
        if (! $this->unfinishedSessionDate) {
            return;
        }

        $date = $this->unfinishedSessionDate;
        $allProducts = $this->getActiveProducts();

        foreach ($allProducts as $p) {
            $entry = StockEntry::where('product_id', $p->id)->where('date', $date)->first();
            if ($entry) {
                $sold = Transaction::where('product_id', $p->id)
                    ->whereDate('transacted_at', $date)
                    ->sum('quantity');

                $entry->update([
                    'closing_stock' => $entry->opening_stock - $sold,
                ]);
            }
        }

        // Mark as finished in DailyRecap
        $activeJurusanId = session('active_jurusan_id');
        DailyRecap::updateOrCreate(
            [
                'date' => $date,
                'jurusan_id' => $activeJurusanId,
            ],
            [
                'actual_cash' => 1,
                'cash_note' => 'Auto-finished by system (Forgot to click finish)',
            ]
        );

        $this->showRecoveryModal = false;
        $this->unfinishedSessionDate = null;
        $this->dispatch('toast', message: 'Sesi sebelumnya berhasil dipulihkan & ditutup.');

        // Now proceed to today's opening stock
        $this->checkOpeningStock();
    }

    public function editOpeningStock(): void
    {
        $today = $this->transactionDate ?: now()->toDateString();
        $activeJurusanId = session('active_jurusan_id');

        $lastSessionDate = StockEntry::where('date', '<', $today)
            ->whereHas('product', function ($q) use ($activeJurusanId) {
                if ($activeJurusanId) {
                    $q->where('jurusan_id', $activeJurusanId);
                }
            })
            ->orderBy('date', 'desc')
            ->value('date');

        $lastStocks = [];
        if ($lastSessionDate) {
            $lastStocks = StockEntry::where('date', $lastSessionDate)
                ->whereHas('product', function ($q) use ($activeJurusanId) {
                    if ($activeJurusanId) {
                        $q->where('jurusan_id', $activeJurusanId);
                    }
                })
                ->pluck('closing_stock', 'product_id')
                ->toArray();
        }

        $allProducts = $this->getActiveProducts();
        $this->lastClosingStocks = $lastStocks;

        foreach ($allProducts as $p) {
            $entry = StockEntry::where('product_id', $p->id)->where('date', $today)->first();
            if ($entry) {
                $this->stockItems[$p->id] = $entry->opening_stock;
            } else {
                $this->stockItems[$p->id] = $lastStocks[$p->id] ?? 0;
            }
        }

        $this->showOpeningStockModal = true;
    }

    protected function checkOpeningStock(): void
    {
        $today = now()->toDateString();
        $activeJurusanId = session('active_jurusan_id');

        $exists = StockEntry::where('date', $today)
            ->whereHas('product', function ($q) use ($activeJurusanId) {
                if ($activeJurusanId) {
                    $q->where('jurusan_id', $activeJurusanId);
                }
            })
            ->exists();

        if (! $exists) {
            $lastSessionDate = StockEntry::where('date', '<', $today)
                ->whereHas('product', function ($q) use ($activeJurusanId) {
                    if ($activeJurusanId) {
                        $q->where('jurusan_id', $activeJurusanId);
                    }
                })
                ->orderBy('date', 'desc')
                ->value('date');

            $lastStocks = [];
            if ($lastSessionDate) {
                $lastStocks = StockEntry::where('date', $lastSessionDate)
                    ->whereHas('product', function ($q) use ($activeJurusanId) {
                        if ($activeJurusanId) {
                            $q->where('jurusan_id', $activeJurusanId);
                        }
                    })
                    ->pluck('closing_stock', 'product_id')
                    ->toArray();
            }

            $allProducts = $this->getActiveProducts();
            $this->lastClosingStocks = $lastStocks;

            foreach ($allProducts as $p) {
                $this->stockItems[$p->id] = $lastStocks[$p->id] ?? 0;
            }
            $this->showOpeningStockModal = true;
        }
    }

    public function saveOpeningStock(): void
    {
        $today = $this->transactionDate ?: now()->toDateString();
        foreach ($this->stockItems as $productId => $qty) {
            $entry = StockEntry::updateOrCreate(
                ['product_id' => $productId, 'date' => $today],
                ['opening_stock' => $qty ?? 0]
            );

            $totalSold = Transaction::where('product_id', $productId)
                ->whereDate('transacted_at', $today)
                ->sum('quantity');

            $entry->update([
                'closing_stock' => $entry->opening_stock - $totalSold,
            ]);
        }
        $this->showOpeningStockModal = false;
        $this->stockItems = [];
        session()->flash('toast', 'Stok awal berhasil diperbarui.');
        $this->redirectRoute('kasir', navigate: true);
    }

    public function finishSession(): void
    {
        $today = $this->transactionDate ?: now()->toDateString();
        $allProducts = $this->getActiveProducts();
        foreach ($allProducts as $p) {
            $entry = StockEntry::where('product_id', $p->id)->where('date', $today)->first();
            $sold = Transaction::where('product_id', $p->id)
                ->whereDate('transacted_at', $today)
                ->sum('quantity');

            $this->stockItems[$p->id] = $entry ? ($entry->opening_stock - $sold) : 0;
        }

        $this->showClosingStockModal = true;
    }

    public function saveClosingStock(): void
    {
        $today = $this->transactionDate ?: now()->toDateString();
        foreach ($this->stockItems as $productId => $qty) {
            StockEntry::updateOrCreate(
                ['product_id' => $productId, 'date' => $today],
                ['closing_stock' => $qty ?? 0]
            );
        }

        // Mark session as finished in DailyRecap
        $activeJurusanId = session('active_jurusan_id');
        DailyRecap::updateOrCreate(
            [
                'date' => $today,
                'jurusan_id' => $activeJurusanId,
            ],
            ['actual_cash' => 1] // Set a placeholder value to mark as finished
        );

        $this->showClosingStockModal = false;
        $this->stockItems = [];

        session()->flash('toast', 'Sesi kasir berhasil diselesaikan.');
        $this->redirectRoute('dashboard', navigate: true);
    }

    public function checkout($cart, $total, $change, $buyer_name, $status, $note, $transactionDate = null): void
    {
        if (empty($cart)) {
            return;
        }

        $tDate = $transactionDate ?: now()->toDateString();
        $isBackdate = $tDate < now()->toDateString();

        // Ensure time is preserved if it's today, otherwise use current time on that past date
        $transactedAt = $tDate === now()->toDateString() ? now() : Carbon::parse($tDate.' '.now()->format('H:i:s'));

        $activeJurusanId = session('active_jurusan_id');
        $themeSettings = session('active_jurusan_theme') ?? [];
        if (empty($themeSettings) && $activeJurusanId) {
            $jurusan = \App\Models\Jurusan::find($activeJurusanId);
            $themeSettings = $jurusan ? ($jurusan->theme_settings ?? []) : [];
        }
        $prefix = $themeSettings['doc_prefix_transaction'] ?? 'LBK';

        $cleanName = preg_replace('/[^A-Za-z0-9]/', '', $buyer_name ?: 'GUEST');
        $initials = strtoupper(substr($cleanName, 0, 2));
        $reference = $prefix.'-'.now()->format('Ymd').'-'.$initials.strtoupper(bin2hex(random_bytes(2)));

        $first = true;
        foreach ($cart as $item) {
            Transaction::create([
                'jurusan_id' => $activeJurusanId,
                'reference' => $reference,
                'user_id' => auth()->id(),
                'product_id' => $item['id'],
                'supplier_id' => $item['supplier_id'] ?? null,
                'transacted_at' => $transactedAt,
                'buyer_name' => $buyer_name ?: null,
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
                'unit_profit' => $item['profit'],
                'total_price' => $item['price'] * $item['quantity'],
                'debt_amount' => in_array($status, ['belum_menerima_uang', 'uang_dipinjam']) ? ($item['price'] * $item['quantity']) : 0,
                'change_due' => ($status === 'belum_kembalian' && $first) ? $change : 0,
                'status' => $status,
                'note' => $note ?: ($change > 0 && $first ? 'Kembalian: Rp'.number_format($change, 0, ',', '.') : null),
            ]);
            $first = false;

            // Cascading stock reduction if backdating
            if ($isBackdate) {
                // Find all StockEntry from this date to today
                $entries = StockEntry::where('product_id', $item['id'])
                    ->where('date', '>=', $tDate)
                    ->orderBy('date', 'asc')
                    ->get();

                foreach ($entries as $ent) {
                    $soldLater = Transaction::where('product_id', $item['id'])
                        ->whereDate('transacted_at', $ent->date)
                        ->sum('quantity');

                    $ent->update([
                        'closing_stock' => $ent->opening_stock - $soldLater,
                    ]);
                }
            }
        }

        $this->dispatch('transaction-completed', reference: $reference);
        $this->dispatch('toast', message: 'Transaksi berhasil disimpan!');
    }

    public function viewDetails(string $reference): void
    {
        $this->detailReference = $reference;
        $this->showDetailsModal = true;
    }

    public function getDetailItemsProperty()
    {
        if (! $this->detailReference) {
            return collect();
        }

        return Transaction::with('product')->where('reference', $this->detailReference)->get();
    }

    #[Computed]
    public function categories()
    {
        $today = now()->toDateString();
        $activeJurusanId = session('active_jurusan_id');

        $activeProductCategoryIds = Product::where('is_active', true)
            ->whereHas('stockEntries', function ($q) use ($today) {
                $q->where('date', $today)->where('opening_stock', '>', 0);
            })
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->get()
            ->filter(function ($p) use ($today) {
                $entry = StockEntry::where('product_id', $p->id)->where('date', $today)->first();
                $sold = Transaction::where('product_id', $p->id)
                    ->whereDate('transacted_at', $today)
                    ->sum('quantity');

                return (($entry ? $entry->opening_stock : 0) - $sold) > 0;
            })
            ->pluck('category_id')
            ->filter()
            ->unique();

        return ProductCategory::whereIn('id', $activeProductCategoryIds)
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function recentTransactions()
    {
        $today = $this->transactionDate ?: now()->toDateString();
        $activeJurusanId = session('active_jurusan_id');

        return Transaction::query()
            ->whereDate('transacted_at', $today)
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->selectRaw('reference, buyer_name, status, transacted_at, SUM(total_price) as total_amount, SUM(quantity) as total_qty')
            ->groupBy('reference', 'buyer_name', 'status', 'transacted_at')
            ->latest('transacted_at')
            ->limit(10)
            ->get();
    }

    #[Computed]
    public function stockComparison()
    {
        $today = $this->transactionDate ?: now()->toDateString();
        $activeJurusanId = session('active_jurusan_id');

        return Product::where('is_active', true)
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->whereHas('stockEntries', function ($q) use ($today) {
                $q->where('date', $today)->where('opening_stock', '>', 0);
            })
            ->with(['stockEntries' => fn ($q) => $q->where('date', $today)])
            ->get()
            ->map(function ($p) use ($today) {
                $entry = $p->stockEntries->first();
                $sold = Transaction::where('product_id', $p->id)
                    ->whereDate('transacted_at', $today)
                    ->sum('quantity');

                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'category' => $p->category->name ?? 'Uncategorized',
                    'opening' => $entry ? $entry->opening_stock : 0,
                    'sold' => $sold,
                    'expected' => ($entry ? $entry->opening_stock : 0) - $sold,
                    'actual' => $entry ? $entry->closing_stock : 0,
                ];
            });
    }

    protected function getProductsForAlpine()
    {
        $today = now()->toDateString();
        $activeJurusanId = session('active_jurusan_id');

        return Product::with(['category', 'stockEntries' => fn ($q) => $q->where('date', $today)])
            ->where('is_active', true)
            ->whereHas('stockEntries', function ($q) use ($today) {
                $q->where('date', $today)->where('opening_stock', '>', 0);
            })
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->orderBy('name')
            ->get()
            ->map(function ($p) use ($today) {
                $entry = $p->stockEntries->first();
                $sold = Transaction::where('product_id', $p->id)
                    ->whereDate('transacted_at', $today)
                    ->sum('quantity');
                $available_stock = ($entry ? $entry->opening_stock : 0) - $sold;

                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'price' => (int) $p->price,
                    'profit' => (int) $p->profit,
                    'supplier_id' => $p->supplier_id,
                    'category_id' => $p->category_id,
                    'category_name' => $p->category->name ?? 'Uncategorized',
                    'initial' => substr($p->name, 0, 1),
                    'available_stock' => $available_stock,
                ];
            });
    }

    public function render()
    {
        $today = now()->toDateString();
        $activeJurusanId = session('active_jurusan_id');
        $allProducts = $this->getProductsForAlpine();

        $isSessionFinished = DailyRecap::where('date', $today)
            ->where('jurusan_id', $activeJurusanId)
            ->where('actual_cash', '>', 0)
            ->exists();

        return view('livewire.pos.kasir', [
            'allProductsJson' => $allProducts,
            'isSessionFinished' => $isSessionFinished,
        ])->layout('layouts.kasir');
    }
}
