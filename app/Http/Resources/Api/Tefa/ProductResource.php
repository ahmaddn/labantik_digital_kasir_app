<?php

namespace App\Http\Resources\Api\Tefa;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $today = now()->toDateString();
        $stockEntry = $this->whenLoaded('stockEntries', fn () => 
            $this->stockEntries->firstWhere('date', $today) ?? $this->stockEntries->first()
        );

        $openingStock = (int) ($stockEntry?->opening_stock ?? 0);
        $closingStock = (int) max(0, $stockEntry?->closing_stock ?? 0);
        $soldQuantity = (int) max(0, $openingStock - $closingStock);
        $isAvailable = $this->is_active && $closingStock > 0;

        return [
            'tefa_product_id'      => $this->id,
            'name'                 => $this->name,
            'label'                => $this->label,
            'category'             => $this->whenLoaded('category', fn () => $this->category?->name),
            'category_details'     => $this->whenLoaded('category', fn () => [
                'id'   => $this->category?->id,
                'name' => $this->category?->name,
                'slug' => $this->category?->slug,
            ]),
            'status'               => $isAvailable ? 'available' : 'out_of_stock',
            'is_active'            => (bool) $this->is_active,
            'opening_stock'        => $openingStock,
            'sold_quantity'        => $soldQuantity,
            'remaining_stock'      => $closingStock,
            'available_stock'      => $closingStock,
            'supplier'             => $this->whenLoaded('supplier', fn () => $this->supplier?->name),
            'supplier_details'     => $this->whenLoaded('supplier', fn () => [
                'id'      => $this->supplier?->id,
                'name'    => $this->supplier?->name,
                'contact' => $this->supplier?->contact,
                'address' => $this->supplier?->address,
                'note'    => $this->supplier?->note,
            ]),
            'selling_price'        => $this->price,
            'profit_per_unit'      => $this->profit,
            'estimated_cost_price' => $this->modal_price,
            'stock_entries'        => $this->whenLoaded('stockEntries', fn () => $this->stockEntries->map(fn ($entry) => [
                'id'            => $entry->id,
                'date'          => $entry->date,
                'opening_stock' => $entry->opening_stock,
                'closing_stock' => $entry->closing_stock,
                'recorded_by'   => $entry->user?->name,
            ])),
            'modifier_groups'      => $this->whenLoaded('modifierGroups', fn () => $this->modifierGroups->map(fn ($group) => [
                'id'            => $group->id,
                'name'          => $group->name,
                'min_selection' => $group->min_selection,
                'max_selection' => $group->max_selection,
                'modifiers'     => $group->modifiers->map(fn ($mod) => [
                    'id'    => $mod->id,
                    'name'  => $mod->name,
                    'price' => (float) $mod->price,
                ]),
            ])),
            'created_at'           => $this->created_at?->toIso8601String(),
            'updated_at'           => $this->updated_at?->toIso8601String(),
        ];
    }
}
