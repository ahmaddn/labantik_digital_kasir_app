<?php

namespace App\Http\Resources\Api\Tefa;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MerchantWithProductsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // pic_name: prioritaskan kolom jurusans.pic_name,
        // fallback ke nama user pertama berole 'pengelola' (sudah di-eager load).
        $picName = $this->pic_name
            ?? $this->whenLoaded('pengelolaUsers', fn () => $this->pengelolaUsers->first()?->name);

        return [
            'tefa_merchant_id' => $this->id,
            'store_name'       => $this->name,
            'pic_name'         => $picName,
            'stand_location'   => $this->stand_location,
            'products'         => ProductResource::collection($this->whenLoaded('products')),
        ];
    }
}
