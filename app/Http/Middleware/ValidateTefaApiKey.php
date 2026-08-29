<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateTefaApiKey
{
    /**
     * Validasi X-API-Key header pada setiap request ke API TEFA.
     * Key dikonfigurasi via TEFA_API_KEY di .env.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-Key');
        $validKey = config('tefa.api_key');

        if (empty($apiKey) || empty($validKey) || ! hash_equals($validKey, $apiKey)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'API key tidak valid atau tidak disertakan.',
                'data'    => null,
            ], 401);
        }

        return $next($request);
    }
}
