<?php

use App\Http\Controllers\Api\Tefa\AuthController;
use App\Http\Controllers\Api\Tefa\MerchantController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| TEFA API Routes
|--------------------------------------------------------------------------
|
| Endpoint integrasi antara Aplikasi TEFA (Teaching Factory) dan
| Sistem Dompet Siswa SMKN 1 Talaga.
|
| Prefix  : /api/v1/tefa
| Auth    : Laravel Sanctum (Bearer token)
| Format  : JSON (utf-8)
|
*/

Route::prefix('v1/tefa')->name('tefa.')->group(function () {

    // ── Autentikasi & Manajemen Akun Merchant TEFA ────────────────────────
    Route::prefix('auth')->name('auth.')->group(function () {

        // POST /api/v1/tefa/auth/login
        Route::post('login', [AuthController::class, 'login'])
            ->name('login');

        // POST /api/v1/tefa/auth/register
        Route::post('register', [AuthController::class, 'register'])
            ->name('register');

        // GET /api/v1/tefa/auth/me  — butuh token
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('me', [AuthController::class, 'me'])
                ->name('me');
        });
    });

    // ── Data Merchant & Produk Kantin TEFA ───────────────────────────────
    Route::middleware('auth:sanctum')->prefix('merchants')->name('merchants.')->group(function () {

        // GET /api/v1/tefa/merchants
        Route::get('/', [MerchantController::class, 'index'])
            ->name('index');

        // GET /api/v1/tefa/merchants/{tefa_merchant_id}/products
        Route::get('{tefa_merchant_id}/products', [MerchantController::class, 'products'])
            ->name('products');
    });
});
