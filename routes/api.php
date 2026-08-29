<?php

use App\Http\Controllers\Api\Tefa\AuthController;
use App\Http\Controllers\Api\Tefa\MerchantController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| TEFA API Routes
|--------------------------------------------------------------------------
|
| Semua endpoint wajib menyertakan header X-API-Key.
|
| Endpoint merchants & products: API Key saja (tidak perlu login).
| Endpoint auth (me): API Key + Bearer token Sanctum.
|
| Prefix  : /api/v1/tefa
| Format  : JSON (utf-8)
|
*/

Route::prefix('v1/tefa')->name('tefa.')->middleware('tefa.apikey')->group(function () {

    // ── Autentikasi & Manajemen Akun Merchant TEFA ────────────────────────
    Route::prefix('auth')->name('auth.')->group(function () {

        // POST /api/v1/tefa/auth/login  — API Key saja
        Route::post('login', [AuthController::class, 'login'])
            ->name('login');

        // POST /api/v1/tefa/auth/register  — API Key saja
        Route::post('register', [AuthController::class, 'register'])
            ->name('register');

        // GET /api/v1/tefa/auth/me  — API Key + Bearer token
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('me', [AuthController::class, 'me'])
                ->name('me');
        });
    });

    // ── Data Merchant & Produk Kantin TEFA ───────────────────────────────
    // API Key saja — tidak perlu login
    Route::prefix('merchants')->name('merchants.')->group(function () {

        // GET /api/v1/tefa/merchants
        Route::get('/', [MerchantController::class, 'index'])
            ->name('index');

        // GET /api/v1/tefa/merchants/{tefa_merchant_id}/products
        Route::get('{tefa_merchant_id}/products', [MerchantController::class, 'products'])
            ->name('products');
    });
});
