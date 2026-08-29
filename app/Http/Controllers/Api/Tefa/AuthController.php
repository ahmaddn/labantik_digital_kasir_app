<?php

namespace App\Http\Controllers\Api\Tefa;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Tefa\LoginRequest;
use App\Http\Requests\Api\Tefa\RegisterRequest;
use App\Http\Resources\Api\Tefa\MerchantResource;
use App\Models\Jurusan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * POST /api/v1/tefa/auth/login
     *
     * Otentikasi pengelola kantin TEFA.
     * Mengembalikan token Sanctum + data merchant (jurusan) pertama
     * yang dimiliki user dengan role 'pengelola'.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        // Cari user berdasarkan email (username di spec = email di sistem)
        $user = User::where('email', $request->username)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['Username atau password salah.'],
            ]);
        }

        // Ambil jurusan pertama milik user ini yang berole 'pengelola',
        // eager load pengelolaUsers supaya MerchantResource bisa pakai pic_name fallback.
        $merchant = Jurusan::with('pengelolaUsers')
            ->whereHas('users', function ($q) use ($user) {
                $q->where('role_user.user_id', $user->id)
                    ->whereHas('roles', fn ($r) => $r->where('roles.name', 'pengelola_jurusan'));
            })
            ->first();

        // Buat token Sanctum dengan nama 'tefa-token'
        $token = $user->createToken('tefa-token')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'Login berhasil',
            'data'    => [
                'token'    => $token,
                'merchant' => $merchant ? new MerchantResource($merchant) : null,
            ],
        ]);
    }

    /**
     * POST /api/v1/tefa/auth/register
     *
     * Mendaftarkan unit kantin TEFA baru:
     * 1. Buat User baru (email = username di spec)
     * 2. Buat Jurusan baru sebagai merchant/kantin
     * 3. Assign role 'pengelola' ke user di jurusan tersebut
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $jurusan = DB::transaction(function () use ($request) {
            // Buat akun user
            $user = User::create([
                'name'     => $request->pic_name,
                'email'    => $request->username,
                'password' => Hash::make($request->password),
            ]);

            // Buat jurusan/merchant baru
            $jurusan = Jurusan::create([
                'name'           => $request->store_name,
                'pic_name'       => $request->pic_name,
                'phone'          => $request->phone,
                'stand_location' => $request->stand_location,
                'is_active'      => true,
            ]);

            // Cari atau buat role 'pengelola_jurusan'
            $role = Role::firstOrCreate(
                ['name' => 'pengelola_jurusan'],
                ['label' => 'Pengelola Jurusan']
            );

            // Assign role ke user di jurusan ini
            DB::table('role_user')->insert([
                'id'         => \Illuminate\Support\Str::uuid(),
                'user_id'    => $user->id,
                'role_id'    => $role->id,
                'jurusan_id' => $jurusan->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return $jurusan;
        });

        return response()->json([
            'status'  => 'success',
            'message' => 'Pendaftaran kantin TEFA berhasil',
            'data'    => [
                'tefa_merchant_id' => $jurusan->id,
                'store_name'       => $jurusan->name,
                'pic_name'         => $jurusan->pic_name,
                'stand_location'   => $jurusan->stand_location,
            ],
        ], 201);
    }

    /**
     * GET /api/v1/tefa/auth/me
     *
     * Mengembalikan profil merchant TEFA yang sedang login.
     * Eager load pengelolaUsers untuk pic_name fallback — tidak ada N+1.
     */
    public function me(): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();

        // Ambil jurusan (merchant) milik user ini berole 'pengelola'
        // with('pengelolaUsers') sudah eager load, tidak ada query tambahan di Resource
        $merchant = Jurusan::with('pengelolaUsers')
            ->whereHas('users', function ($q) use ($user) {
                $q->where('role_user.user_id', $user->id)
                    ->whereHas('roles', fn ($r) => $r->where('roles.name', 'pengelola_jurusan'));
            })
            ->first();

        if (! $merchant) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Merchant tidak ditemukan untuk akun ini.',
                'data'    => null,
            ], 404);
        }

        // Tambah field username & is_active langsung di sini (tidak perlu Resource berbeda)
        $data = (new MerchantResource($merchant))->toArray(request());
        $data['username']  = $user->email;
        $data['is_active'] = $merchant->is_active;

        return response()->json([
            'status'  => 'success',
            'message' => 'Profil kantin TEFA dimuat',
            'data'    => $data,
        ]);
    }
}
