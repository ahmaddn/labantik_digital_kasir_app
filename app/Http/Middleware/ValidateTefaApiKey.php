<?php

namespace App\Http\Middleware;

use App\Models\TefaApiKey;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateTefaApiKey
{
    /**
     * Validasi X-API-Key header pada setiap request ke API TEFA.
     * Cek ke database (tefa_api_keys) atau fallback ke config('tefa.api_key').
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-Key');

        if (empty($apiKey)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'API key tidak disertakan pada header X-API-Key.',
                'data'    => null,
            ], 401);
        }

        // 1. Cek database tefa_api_keys
        $dbKey = TefaApiKey::where('key', $apiKey)
            ->where('is_active', true)
            ->first();

        if ($dbKey) {
            $dbKey->update(['last_used_at' => now()]);
            return $next($request);
        }

        // 2. Fallback ke config('tefa.api_key') di .env jika dikonfigurasi
        $envKey = config('tefa.api_key');
        if (! empty($envKey) && hash_equals($envKey, $apiKey)) {
            return $next($request);
        }

        return response()->json([
            'status'  => 'error',
            'message' => 'API key tidak valid atau sudah tidak aktif.',
            'data'    => null,
        ], 401);
    }
}
