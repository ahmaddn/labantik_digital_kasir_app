<?php

namespace App\Http\Resources\Api\Tefa;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'tefa_product_id'      => $this->id,
            'name'                 => $this->name,
            'category'             => $this->whenLoaded('category', fn () => $this->category?->name),
            'status'               => $this->is_active ? 'available' : 'unavailable',
            'supplier'             => $this->whenLoaded('supplier', fn () => $this->supplier?->name),
            'selling_price'        => $this->price,
            'profit_per_unit'      => $this->profit,
            'estimated_cost_price' => $this->modal_price,
        ];
    }
}
