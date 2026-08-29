# Dokumentasi API Integrasi TEFA
**Sistem Dompet Siswa SMKN 1 Talaga**

---

| | |
|---|---|
| **Versi API** | v1 |
| **Base URL** | `https://tefa.smkn1talaga.sch.id/api/v1/tefa` |
| **Format Respons** | JSON (utf-8) |
| **Autentikasi** | API Key + Bearer Token (untuk /me) |
| **Tipe ID** | String UUID |
| **Tanggal Dokumen** | Agustus 2026 |

---

## Daftar Isi

1. [Pengertian & Tujuan](#1-pengertian--tujuan)
2. [Autentikasi](#2-autentikasi)
3. [Format Respons Standar](#3-format-respons-standar)
4. [Endpoint — Autentikasi](#4-endpoint--autentikasi)
   - [POST /auth/login](#41-post-authlogin)
   - [POST /auth/register](#42-post-authregister)
   - [GET /auth/me](#43-get-authme)
5. [Endpoint — Merchant & Produk](#5-endpoint--merchant--produk)
   - [GET /merchants](#51-get-merchants)
   - [GET /merchants/{id}/products](#52-get-merchantsidproducts)
6. [Kode Error](#6-kode-error)
7. [Catatan Teknis](#7-catatan-teknis)

---

## 1. Pengertian & Tujuan

Dokumen ini merupakan acuan teknis API yang mengatur pertukaran data antara **Aplikasi TEFA (Teaching Factory)** dan **Sistem Dompet Siswa SMKN 1 Talaga**.

**Alur sistem:**
- Aplikasi TEFA menyediakan data merchant (kantin) dan produk menu
- Sistem Dompet Siswa mengonsumsi data tersebut dan menerbitkan **Kode QR** (`DS-MCH-XXXXXX`) untuk validasi transaksi pembayaran siswa

**Pemetaan data:**

| Konsep TEFA | Entitas di Sistem |
|---|---|
| Merchant / Kantin | Jurusan (unit multi-tenant) |
| `tefa_merchant_id` | `jurusans.id` (UUID) |
| `store_name` | `jurusans.name` |
| `pic_name` | User berole `pengelola_jurusan` di jurusan tersebut |
| Produk menu | `products` (scoped per jurusan) |
| `selling_price` | `products.price` |
| `estimated_cost_price` | `products.modal_price` |
| `profit_per_unit` | `products.profit` |

---

## 2. Autentikasi

API ini menggunakan dua layer keamanan:

### 2.1 API Key (Wajib di Semua Endpoint)

Setiap request ke API TEFA **wajib** menyertakan header:

```
X-API-Key: {api_key}
```

API key digenerate oleh admin sistem dan dishare secara private ke tim TEFA. Tanpa header ini semua endpoint akan mengembalikan `401 Unauthorized`.

> Untuk generate key baru: `php artisan tinker` → `(string) \Illuminate\Support\Str::uuid()`

### 2.2 Bearer Token (Hanya untuk `/auth/me`)

Endpoint `GET /auth/me` memerlukan **dua layer** — API Key + Bearer Token Sanctum:

1. Dapatkan token melalui `POST /auth/login`
2. Sertakan di header:

```
Authorization: Bearer {token}
```

### Ringkasan Auth per Endpoint

| Endpoint | X-API-Key | Bearer Token |
|---|---|---|
| `POST /auth/login` | ✅ Wajib | ❌ Tidak perlu |
| `POST /auth/register` | ✅ Wajib | ❌ Tidak perlu |
| `GET /auth/me` | ✅ Wajib | ✅ Wajib |
| `GET /merchants` | ✅ Wajib | ❌ Tidak perlu |
| `GET /merchants/{id}/products` | ✅ Wajib | ❌ Tidak perlu |

---

## 3. Format Respons Standar

Semua respons mengikuti struktur berikut:

### Sukses

```json
{
    "status": "success",
    "message": "Pesan deskriptif",
    "data": { ... }
}
```

### Error

```json
{
    "status": "error",
    "message": "Pesan error",
    "data": null
}
```

### Validasi Gagal (422)

```json
{
    "message": "Pesan utama",
    "errors": {
        "field": ["Pesan error detail"]
    }
}
```

---

## 4. Endpoint — Autentikasi

### 4.1 POST /auth/login

Otentikasi pengelola kantin TEFA. Mengembalikan Bearer Token beserta data merchant pertama yang dimiliki akun tersebut.

**URL**
```
POST /api/v1/tefa/auth/login
```

**Headers**

| Header | Value |
|---|---|
| `Content-Type` | `application/json` |
| `Accept` | `application/json` |
| `X-API-Key` | `{api_key}` |

**Request Body**

| Parameter | Tipe | Wajib | Keterangan |
|---|---|---|---|
| `username` | string | ✅ Ya | Email akun merchant |
| `password` | string | ✅ Ya | Kata sandi akun |

**Contoh Request**

```json
{
    "username": "kantin_tataboga@tefa.sch.id",
    "password": "PasswordTEFA123!"
}
```

**Contoh Respons (200 OK)**

```json
{
    "status": "success",
    "message": "Login berhasil",
    "data": {
        "token": "1|3hEFgD56q0IpHLGlKcDrePqs5xZU58sGNgABZ2HW27...",
        "merchant": {
            "tefa_merchant_id": "019fc582-6b7b-72d1-bfbf-7103020eae13",
            "store_name": "Kantin TEFA Tata Boga",
            "pic_name": "Ibu Siti Nurhaliza",
            "phone": "085223114455",
            "stand_location": "Gedung TEFA Blok A",
            "is_active": true
        }
    }
}
```

**Respons Error**

| HTTP Code | Kondisi |
|---|---|
| `401` | API Key tidak valid atau tidak disertakan |
| `422` | Username atau password salah |
| `422` | Field wajib tidak diisi |

---

### 4.2 POST /auth/register

Mendaftarkan unit kantin TEFA baru. Proses ini akan membuat akun User baru, Jurusan (merchant) baru, dan assign role `pengelola_jurusan` secara otomatis.

**URL**
```
POST /api/v1/tefa/auth/register
```

**Headers**

| Header | Value |
|---|---|
| `Content-Type` | `application/json` |
| `Accept` | `application/json` |
| `X-API-Key` | `{api_key}` |

**Request Body**

| Parameter | Tipe | Wajib | Keterangan |
|---|---|---|---|
| `store_name` | string | ✅ Ya | Nama stand / kantin TEFA |
| `pic_name` | string | ✅ Ya | Nama penanggung jawab |
| `username` | string | ✅ Ya | Email untuk login (harus unik) |
| `password` | string | ✅ Ya | Kata sandi (minimal 8 karakter) |
| `stand_location` | string | ❌ Tidak | Lokasi gedung/stand TEFA |
| `phone` | string | ❌ Tidak | Nomor telepon kantin |

**Contoh Request**

```json
{
    "store_name": "Kantin TEFA Barokah",
    "pic_name": "Bapak Ahmad",
    "username": "kantin_barokah@tefa.sch.id",
    "password": "PasswordTEFA123!",
    "stand_location": "Gedung TEFA Blok C",
    "phone": "085200001111"
}
```

**Contoh Respons (201 Created)**

```json
{
    "status": "success",
    "message": "Pendaftaran kantin TEFA berhasil",
    "data": {
        "tefa_merchant_id": "01a04c45-770b-7283-9985-f8dc9795eec6",
        "store_name": "Kantin TEFA Barokah",
        "pic_name": "Bapak Ahmad",
        "stand_location": "Gedung TEFA Blok C"
    }
}
```

**Respons Error**

| HTTP Code | Kondisi |
|---|---|
| `401` | API Key tidak valid atau tidak disertakan |
| `422` | Email sudah terdaftar |
| `422` | Field wajib tidak diisi |
| `422` | Password kurang dari 8 karakter |

---

### 4.3 GET /auth/me

Mengembalikan profil merchant TEFA yang sedang aktif login berdasarkan token.

**URL**
```
GET /api/v1/tefa/auth/me
```

**Headers**

| Header | Value |
|---|---|
| `Accept` | `application/json` |
| `X-API-Key` | `{api_key}` |
| `Authorization` | `Bearer {token}` |

**Contoh Respons (200 OK)**

```json
{
    "status": "success",
    "message": "Profil kantin TEFA dimuat",
    "data": {
        "tefa_merchant_id": "019fc582-6b7b-72d1-bfbf-7103020eae13",
        "store_name": "Kantin TEFA Tata Boga",
        "pic_name": "Ibu Siti Nurhaliza",
        "phone": "085223114455",
        "stand_location": "Gedung TEFA Blok A",
        "is_active": true,
        "username": "kantin_tataboga@tefa.sch.id"
    }
}
```

**Respons Error**

| HTTP Code | Kondisi |
|---|---|
| `401` | API Key tidak valid, token tidak ada, atau token tidak valid |
| `404` | Akun tidak memiliki merchant terdaftar |

---

## 5. Endpoint — Merchant & Produk

### 5.1 GET /merchants

Mengambil daftar seluruh merchant kantin TEFA aktif. Tidak memerlukan login — cukup API Key.

**URL**
```
GET /api/v1/tefa/merchants
```

**Headers**

| Header | Value |
|---|---|
| `Accept` | `application/json` |
| `X-API-Key` | `{api_key}` |

**Query Parameters**

| Parameter | Tipe | Wajib | Default | Keterangan |
|---|---|---|---|---|
| `status` | string | ❌ Tidak | `active` | Filter status: `active` atau `inactive` |

**Contoh Request**
```
GET /api/v1/tefa/merchants?status=active
```

**Contoh Respons (200 OK)**

```json
{
    "status": "success",
    "message": "Daftar merchant kantin TEFA berhasil dimuat",
    "data": [
        {
            "tefa_merchant_id": "019fc582-6b7b-72d1-bfbf-7103020eae13",
            "store_name": "Kantin TEFA Tata Boga",
            "pic_name": "Ibu Siti Nurhaliza",
            "phone": "085223114455",
            "stand_location": "Gedung TEFA Blok A",
            "is_active": true
        },
        {
            "tefa_merchant_id": "019fc582-6b82-7262-b5aa-82e4a2e9b7ec",
            "store_name": "Kantin TEFA Barokah",
            "pic_name": "Bapak Ahmad",
            "phone": "085200001111",
            "stand_location": "Gedung TEFA Blok C",
            "is_active": true
        }
    ]
}
```

**Respons Error**

| HTTP Code | Kondisi |
|---|---|
| `401` | API Key tidak valid atau tidak disertakan |

---

### 5.2 GET /merchants/{tefa_merchant_id}/products

Mengambil daftar produk (menu makanan/minuman) dari kantin TEFA tertentu. Tidak memerlukan login — cukup API Key. Hanya mengembalikan produk dengan status **available** (`is_active = true`).

**URL**
```
GET /api/v1/tefa/merchants/{tefa_merchant_id}/products
```

**Headers**

| Header | Value |
|---|---|
| `Accept` | `application/json` |
| `X-API-Key` | `{api_key}` |

**Path Parameters**

| Parameter | Tipe | Wajib | Keterangan |
|---|---|---|---|
| `tefa_merchant_id` | string (UUID) | ✅ Ya | ID merchant TEFA |

**Contoh Request**
```
GET /api/v1/tefa/merchants/019fc582-6b7b-72d1-bfbf-7103020eae13/products
```

**Contoh Respons (200 OK)**

```json
{
    "status": "success",
    "message": "Daftar produk menu TEFA berhasil dimuat",
    "data": {
        "tefa_merchant_id": "019fc582-6b7b-72d1-bfbf-7103020eae13",
        "store_name": "Kantin TEFA Tata Boga",
        "pic_name": "Ibu Siti Nurhaliza",
        "stand_location": "Gedung TEFA Blok A",
        "products": [
            {
                "tefa_product_id": "019fc582-6ca6-7295-b2a1-616ba1dfe2ae",
                "name": "Roti Pastry Cokelat TEFA",
                "category": "Makanan",
                "status": "available",
                "supplier": "CV Tata Boga Mandiri",
                "selling_price": 10000,
                "profit_per_unit": 2500,
                "estimated_cost_price": 7500
            },
            {
                "tefa_product_id": "019fc582-6d09-71b8-8fdc-04412e74d5a9",
                "name": "Teh Gelas",
                "category": "Minuman",
                "status": "available",
                "supplier": null,
                "selling_price": 1000,
                "profit_per_unit": 209,
                "estimated_cost_price": 791
            }
        ]
    }
}
```

**Skema Objek Produk**

| Field | Tipe | Keterangan |
|---|---|---|
| `tefa_product_id` | string (UUID) | ID unik produk |
| `name` | string | Nama produk |
| `category` | string / null | Kategori produk (Makanan, Minuman, Snack, dll.) |
| `status` | string | `available` atau `unavailable` |
| `supplier` | string / null | Nama supplier produk |
| `selling_price` | integer | Harga jual (dalam Rupiah) |
| `profit_per_unit` | integer | Keuntungan per unit (dalam Rupiah) |
| `estimated_cost_price` | integer | Estimasi harga modal (dalam Rupiah) |

**Respons Error**

| HTTP Code | Kondisi |
|---|---|
| `401` | API Key tidak valid atau tidak disertakan |
| `404` | Merchant dengan ID tersebut tidak ditemukan |

---

## 6. Kode Error

| HTTP Code | Status | Keterangan |
|---|---|---|
| `200` | OK | Request berhasil |
| `201` | Created | Data baru berhasil dibuat |
| `401` | Unauthorized | API Key tidak ada/salah, atau Bearer Token tidak valid |
| `404` | Not Found | Data yang dicari tidak ditemukan |
| `422` | Unprocessable Entity | Validasi input gagal |
| `500` | Internal Server Error | Terjadi kesalahan di sisi server |

---

## 7. Catatan Teknis

### Tipe ID
Seluruh ID (`tefa_merchant_id`, `tefa_product_id`) menggunakan format **UUID v7** (string), contoh: `019fc582-6b7b-72d1-bfbf-7103020eae13`.

### API Key
- Disimpan di `.env` server sebagai `TEFA_API_KEY`
- Digenerate via `php artisan tinker` → `(string) \Illuminate\Support\Str::uuid()`
- Dishare secara private ke tim pengembang Aplikasi TEFA
- Ganti secara berkala untuk keamanan

### pic_name
Field `pic_name` pada data merchant diambil dengan urutan prioritas:
1. Kolom `pic_name` di tabel `jurusans` (jika diisi)
2. Nama user pertama yang memiliki role `pengelola_jurusan` di jurusan tersebut
3. `null` jika tidak ada pengelola terdaftar

### Performa & Optimasi
Semua endpoint menggunakan **eager loading** — tidak ada query N+1. Jumlah query per endpoint:

| Endpoint | Jumlah Query |
|---|---|
| `GET /merchants` | 2 query (merchants + pengelola users) |
| `GET /merchants/{id}/products` | 3 query (merchant + pengelola + products with category & supplier) |

### Header Wajib untuk Semua Request
```
Accept: application/json
X-API-Key: {api_key}
```
Tanpa `Accept: application/json`, respons error akan dikembalikan dalam format HTML bukan JSON.

---

*Dokumen ini dibuat untuk keperluan integrasi Aplikasi TEFA dengan Sistem Dompet Siswa SMKN 1 Talaga.*
*© 2026 SMKN 1 Talaga*
