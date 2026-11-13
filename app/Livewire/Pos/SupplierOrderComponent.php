<?php

namespace App\Livewire\Pos;

use App\Models\CashierSchedule;
use App\Models\Jurusan;
use App\Models\Product;
use App\Models\SupplierOrder;
use App\Models\SupplierOrderItem;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class SupplierOrderComponent extends Component
{
    use WithPagination;

    public string $activeTab = 'order'; // 'order' or 'history'

    // Form inputs: Toko / Seller & Kontak
    public string $seller_name = '';
    public string $seller_phone = '';
    public string $order_notes = '';
    
    // Array of order items: each has 'product_id', 'name', 'quantity', 'unit', 'notes'
    public array $items = [];

    // History filters
    public string $historySearch = '';
    public string $historyStatus = 'all';

    public bool $isUnauthorizedKasir = false;

    public function mount(): void
    {
        // Proteksi jadwal untuk role kasir murni
        if (session('active_role_name') === 'kasir') {
            $hasHigherRole = auth()->user()->roles()->whereIn('roles.name', ['superadmin', 'pengelola_jurusan', 'pengelola_jurusan_keuangan', 'pengelola_jurusan_penugasan', 'pengelola_jurusan_piket'])->exists();
            if (!$hasHigherRole) {
                $activeJurusanId = session('active_jurusan_id');
                $activeJurusan = Jurusan::find($activeJurusanId);
                $allowedJurusanIds = [$activeJurusanId];
                if ($activeJurusan && $activeJurusan->parent_id) {
                    $allowedJurusanIds[] = $activeJurusan->parent_id;
                }

                $isScheduled = CashierSchedule::where('user_id', auth()->id())
                    ->whereIn('jurusan_id', $allowedJurusanIds)
                    ->where('date', now()->toDateString())
                    ->exists();

                if (!$isScheduled) {
                    $this->isUnauthorizedKasir = true;
                    return;
                }
            }
        }

        $this->addItem();
    }

    public function addItem(): void
    {
        $this->items[] = [
            'product_id' => null,
            'name' => '',
            'quantity' => 1,
            'unit' => 'pcs', // pcs, pack, dus
            'notes' => '',
        ];
    }

    public function removeItem(int $index): void
    {
        if (isset($this->items[$index])) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
        }

        if (empty($this->items)) {
            $this->addItem();
        }
    }

    public function setItemProduct(int $index, ?string $productId, string $productName): void
    {
        if (isset($this->items[$index])) {
            $this->items[$index]['product_id'] = $productId;
            $this->items[$index]['name'] = $productName;
        }
    }

    /**
     * Generate concise and structured WhatsApp message
     */
    public function getWhatsappMessageProperty(): string
    {
        $salutation = ! empty(trim($this->seller_name)) 
            ? "Halo kak *{$this->seller_name}*," 
            : "Halo kak,";
        
        $lines = [];
        $lines[] = "{$salutation}";
        $lines[] = "Dari *LabAntik TEFA*, mau pesan stok barang:";
        $lines[] = "";

        $counter = 1;
        $hasItems = false;
        foreach ($this->items as $item) {
            $name = trim($item['name'] ?? '');
            if (! empty($name)) {
                $hasItems = true;
                $qty = (int) ($item['quantity'] ?? 1);
                $unit = strtoupper($item['unit'] ?? 'PCS');
                $note = ! empty($item['notes']) ? " _({$item['notes']})_" : '';
                $lines[] = "{$counter}. *{$name}* : {$qty} {$unit}{$note}";
                $counter++;
            }
        }

        if (! $hasItems) {
            $lines[] = "_(Belum ada barang diisi)_";
        }

        if (! empty(trim($this->order_notes))) {
            $lines[] = "";
            $lines[] = "Catatan: " . trim($this->order_notes);
        }

        $lines[] = "";
        $lines[] = "Totalnya jadi berapa ya kak? Terima kasih 🙏";

        return implode("\n", $lines);
    }

    /**
     * Generate wa.me link
     */
    public function getWhatsappUrlProperty(): string
    {
        $phone = SupplierOrder::formatWhatsAppNumber($this->seller_phone);
        $encodedText = rawurlencode($this->whatsapp_message);

        if (! empty($phone)) {
            return "https://wa.me/{$phone}?text={$encodedText}";
        }

        return "https://api.whatsapp.com/send?text={$encodedText}";
    }

    public function submitAndOpenWhatsApp(): void
    {
        $this->validate([
            'seller_name' => 'required|string|max:100',
            'seller_phone' => 'required|string|min:8|max:20',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:150',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit' => 'required|in:pcs,pack,dus',
        ], [
            'seller_name.required' => 'Nama seller/toko wajib diisi.',
            'seller_phone.required' => 'Nomor WhatsApp wajib diisi.',
            'items.*.name.required' => 'Nama produk tidak boleh kosong.',
            'items.*.quantity.min' => 'Jumlah minimal 1.',
        ]);

        DB::transaction(function () {
            $order = SupplierOrder::create([
                'supplier_id' => null,
                'supplier_name' => $this->seller_name,
                'supplier_phone' => SupplierOrder::formatWhatsAppNumber($this->seller_phone),
                'status' => 'ordered',
                'notes' => $this->order_notes,
                'total_items' => count($this->items),
                'created_by' => auth()->id(),
                'ordered_at' => now(),
            ]);

            foreach ($this->items as $item) {
                SupplierOrderItem::create([
                    'supplier_order_id' => $order->id,
                    'product_id' => ! empty($item['product_id']) ? $item['product_id'] : null,
                    'product_name' => $item['name'],
                    'quantity' => (int) $item['quantity'],
                    'unit' => $item['unit'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }
        });

        $url = $this->whatsapp_url;

        // Reset form
        $this->reset(['seller_name', 'seller_phone', 'order_notes']);
        $this->items = [];
        $this->addItem();

        session()->flash('success', 'Pesanan berhasil disimpan. WhatsApp telah dibuka di tab baru!');
        
        $this->dispatch('open-whatsapp', url: $url);
    }

    public function updateOrderStatus(string $orderId, string $newStatus): void
    {
        $order = SupplierOrder::find($orderId);
        if ($order && in_array($newStatus, ['pending', 'ordered', 'received', 'cancelled'])) {
            $order->status = $newStatus;
            if ($newStatus === 'received') {
                $order->received_at = now();
            }
            $order->save();

            session()->flash('history_message', "Status pesanan diubah ke: {$newStatus}.");
        }
    }

    public function resendWhatsApp(string $orderId): void
    {
        $order = SupplierOrder::with('items')->find($orderId);
        if (! $order) {
            return;
        }

        $lines = [];
        $lines[] = "Halo kak *{$order->supplier_name}*,";
        $lines[] = "Dari *LabAntik TEFA*, mau konfirmasi pesanan:";
        $lines[] = "";

        $counter = 1;
        foreach ($order->items as $item) {
            $unit = strtoupper($item->unit);
            $note = ! empty($item->notes) ? " _({$item->notes})_" : '';
            $lines[] = "{$counter}. *{$item->product_name}* : {$item->quantity} {$unit}{$note}";
            $counter++;
        }

        if (! empty(trim($order->notes ?? ''))) {
            $lines[] = "";
            $lines[] = "Catatan: " . trim($order->notes);
        }

        $lines[] = "";
        $lines[] = "Totalnya jadi berapa ya kak? Terima kasih 🙏";

        $phone = SupplierOrder::formatWhatsAppNumber($order->supplier_phone);
        $encodedText = rawurlencode(implode("\n", $lines));

        $url = "https://wa.me/{$phone}?text={$encodedText}";
        $this->dispatch('open-whatsapp', url: $url);
    }

    public function render()
    {
        $products = Product::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn($p) => ['id' => $p->id, 'name' => $p->name])
            ->toArray();

        $historyOrders = SupplierOrder::with(['items', 'creator'])
            ->when($this->historySearch, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('supplier_name', 'like', "%{$this->historySearch}%")
                        ->orWhere('supplier_phone', 'like', "%{$this->historySearch}%");
                });
            })
            ->when($this->historyStatus !== 'all', function ($q) {
                $q->where('status', $this->historyStatus);
            })
            ->latest()
            ->paginate(12);

        return view('livewire.pos.supplier-order', [
            'productsList' => $products,
            'historyOrders' => $historyOrders,
        ]);
    }
}
