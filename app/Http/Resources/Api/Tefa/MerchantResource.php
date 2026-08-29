<?php

namespace App\Http\Resources\Api\Tefa;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MerchantResource extends JsonResource
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
            'phone'            => $this->phone,
            'stand_location'   => $this->stand_location,
            'is_active'        => $this->is_active,
        ];
    }
}
