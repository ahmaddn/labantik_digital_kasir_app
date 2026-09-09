<?php

namespace App\Http\Resources\Api\Tefa;

use App\Models\StockEntry;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $today = now()->toDateString();
        
        /** @var StockEntry|null $stockEntry */
        $stockEntry = $this->stockEntries
            ? $this->stockEntries->firstWhere('date', $today) ?? $this->stockEntries->first()
            : null;

        $openingStock = $stockEntry?->opening_stock ?? 0;
        $closingStock = $stockEntry?->closing_stock ?? 0;
        $sold = max(0, $openingStock - $closingStock);

        return [
            'tefa_product_id' => $this->id,
            'name'            => $this->name,
            'label'           => $this->label,
            'status'          => ($this->is_active && $closingStock > 0) ? 'available' : 'out_of_stock',
            'is_active'       => (bool) $this->is_active,
            'date'            => $today,
            'opening_stock'   => (int) $openingStock,
            'sold_quantity'   => (int) $sold,
            'available_stock' => (int) max(0, $closingStock),
        ];
    }
}
