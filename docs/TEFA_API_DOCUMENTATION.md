# Dokumentasi API TEFA & Integrasi Dompet Siswa

Dokumentasi ini menjelaskan secara rinci endpoint API TEFA (Teaching Factory) yang digunakan untuk integrasi antara **Aplikasi TEFA Kasir** dan **Aplikasi Dompet Siswa / External Apps**.

---

## 1. Otentikasi API Key

Semua request HTTP ke API TEFA wajib mencantumkan **API Key** pada Header HTTP:

```http
X-API-Key: tefa_live_xxxxxxxxxxxxxxxxxxxxxxxx
```

> **Catatan Pengelola/Superadmin:**
> API Key dapat digenerate dan dikelola langsung melalui Dashboard Superadmin/Pengelola pada menu **API Key TEFA** (`/settings/api-keys`).
> Setiap request API akan diverifikasi apakah API Key berstatus aktif (`is_active = true`).

---

## 2. Base URL

```
http://localhost/api/v1/tefa
```

_(Sesuaikan domain/host dengan server tempat aplikasi dideploy)_

---

## 3. Daftar Endpoint API

### A. Dapatkan Semua Merchant (Kantin/Unit usaha TEFA)

- **URL:** `GET /merchants`
- **Header:** `X-API-Key: <YOUR_API_KEY>`
- **Deskripsi:** Mengembalikan daftar merchant/kantin jurusan aktif.

**Contoh Response `200 OK`:**

```json
{
    "status": "success",
    "message": "Daftar merchant kantin TEFA berhasil dimuat",
    "data": [
        {
            "tefa_merchant_id": "9b1deb4d-3b7d-4bad-9bdd-2b0d7b3d0001",
            "store_name": "RPL",
            "pic_name": "Pengelola RPL",
            "phone": "081234567890",
            "stand_location": "Kantin Utama Stand 01",
            "is_active": true
        }
    ]
}
```

---

### B. Dapatkan Produk Lengkap berdasarkan Merchant ID

- **URL:** `GET /merchants/{tefa_merchant_id}/products`
- **Header:** `X-API-Key: <YOUR_API_KEY>`
- **Deskripsi:** Mengembalikan daftar produk lengkap pada merchant tertentu, mencakup kategori, supplier, `stock_entries` (stok awal & sisa tutup), dan `modifier_groups` (pilihan topping/level).

**Contoh Response `200 OK`:**

```json
{
    "status": "success",
    "message": "Daftar produk menu TEFA berhasil dimuat",
    "data": {
        "tefa_merchant_id": "9b1deb4d-3b7d-4bad-9bdd-2b0d7b3d0001",
        "store_name": "RPL",
        "pic_name": "Pengelola RPL",
        "stand_location": "Kantin Utama Stand 01",
        "products": [
            {
                "tefa_product_id": "9c123456-ab01-cd02-ef03-123456789001",
                "name": "Nasi Bakar",
                "label": "Nasi Bakar - Rp3.500",
                "category": "Makanan",
                "category_details": {
                    "id": "9c123456-cat1-0001",
                    "name": "Makanan",
                    "slug": "makanan"
                },
                "status": "available",
                "is_active": true,
                "supplier": "Koperasi Sekolah UP RPL",
                "supplier_details": {
                    "id": "9c123456-sup1-0001",
                    "name": "Koperasi Sekolah UP RPL",
                    "contact": "081234567890",
                    "address": "Gedung UP RPL",
                    "note": "Supplier Resmi Harian"
                },
                "selling_price": "3500.00",
                "profit_per_unit": "200.00",
                "estimated_cost_price": "3300.00",
                "stock_entries": [
                    {
                        "id": "9c123456-stk1-0001",
                        "date": "2026-09-09",
                        "opening_stock": 50,
                        "closing_stock": 45,
                        "recorded_by": "Kasir RPL"
                    }
                ],
                "modifier_groups": [
                    {
                        "id": "9c123456-modg1-0001",
                        "name": "Level Pedas",
                        "min_selection": 0,
                        "max_selection": 1,
                        "modifiers": [
                            {
                                "id": "9c123456-mod1-0001",
                                "name": "Pedas Sedang",
                                "price": 0.0
                            },
                            {
                                "id": "9c123456-mod1-0002",
                                "name": "Extra Pedas",
                                "price": 500.0
                            }
                        ]
                    }
                ],
                "created_at": "2026-09-09T07:00:00.000000Z",
                "updated_at": "2026-09-09T07:00:00.000000Z"
            },
            {
                "tefa_product_id": "9c123456-ab01-cd02-ef03-123456789002",
                "name": "Teh Kotak",
                "label": "Teh Kotak - Rp4.000",
                "category": "Minuman",
                "category_details": {
                    "id": "9c123456-cat1-0002",
                    "name": "Minuman",
                    "slug": "minuman"
                },
                "status": "available",
                "is_active": true,
                "supplier": "Koperasi Sekolah UP RPL",
                "supplier_details": {
                    "id": "9c123456-sup1-0001",
                    "name": "Koperasi Sekolah UP RPL",
                    "contact": "081234567890",
                    "address": "Gedung UP RPL",
                    "note": "Supplier Resmi Harian"
                },
                "selling_price": "4000.00",
                "profit_per_unit": "709.00",
                "estimated_cost_price": "3291.00",
                "stock_entries": [
                    {
                        "id": "9c123456-stk1-0002",
                        "date": "2026-09-09",
                        "opening_stock": 30,
                        "closing_stock": 30,
                        "recorded_by": "Kasir RPL"
                    }
                ],
                "modifier_groups": [
                    {
                        "id": "9c123456-modg1-0002",
                        "name": "Opsi Suhu / Penyajian",
                        "min_selection": 0,
                        "max_selection": 1,
                        "modifiers": [
                            {
                                "id": "9c123456-mod1-0003",
                                "name": "Dingin / Pakai Es",
                                "price": 0.0
                            },
                            {
                                "id": "9c123456-mod1-0004",
                                "name": "Tanpa Es",
                                "price": 0.0
                            }
                        ]
                    }
                ],
                "created_at": "2026-09-09T07:00:00.000000Z",
                "updated_at": "2026-09-09T07:00:00.000000Z"
            }
        ]
    }
}
```

---

### C. Cek Stok Produk Spesifik

- **URL:** `GET /products/{product_id}/stock`
- **Header:** `X-API-Key: <YOUR_API_KEY>`
- **Deskripsi:** Mengecek jumlah sisa stok real-time untuk 1 produk spesifik berdasarkan Product ID.

**Contoh Response `200 OK`:**

```json
{
    "status": "success",
    "message": "Stok produk berhasil dimuat",
    "data": {
        "tefa_product_id": "9c123456-ab01-cd02-ef03-123456789001",
        "name": "Nasi Bakar",
        "label": "Nasi Bakar - Rp3.500",
        "status": "available",
        "is_active": true,
        "date": "2026-09-09",
        "opening_stock": 50,
        "sold_quantity": 5,
        "available_stock": 45
    }
}
```

---

### D. Cek Semua Stok Produk pada Merchant Spesifik

- **URL:** `GET /merchants/{tefa_merchant_id}/stock`
- **Header:** `X-API-Key: <YOUR_API_KEY>`
- **Deskripsi:** Mengecek daftar sisa stok seluruh produk menu yang dimiliki oleh merchant tertentu.

**Contoh Response `200 OK`:**

```json
{
    "status": "success",
    "message": "Daftar stok produk merchant berhasil dimuat",
    "data": {
        "tefa_merchant_id": "9b1deb4d-3b7d-4bad-9bdd-2b0d7b3d0001",
        "store_name": "RPL",
        "stocks": [
            {
                "tefa_product_id": "9c123456-ab01-cd02-ef03-123456789001",
                "name": "Nasi Bakar",
                "label": "Nasi Bakar - Rp3.500",
                "status": "available",
                "is_active": true,
                "date": "2026-09-09",
                "opening_stock": 50,
                "sold_quantity": 5,
                "available_stock": 45
            },
            {
                "tefa_product_id": "9c123456-ab01-cd02-ef03-123456789002",
                "name": "Teh Kotak",
                "label": "Teh Kotak - Rp4.000",
                "status": "available",
                "is_active": true,
                "date": "2026-09-09",
                "opening_stock": 30,
                "sold_quantity": 0,
                "available_stock": 30
            },
            {
                "tefa_product_id": "9c123456-ab01-cd02-ef03-123456789003",
                "name": "Beng Beng",
                "label": "Beng Beng - Rp2.500",
                "status": "out_of_stock",
                "is_active": true,
                "date": "2026-09-09",
                "opening_stock": 20,
                "sold_quantity": 20,
                "available_stock": 0
            }
        ]
    }
}
```

---

### E. Pengurangan Stok Otomatis (Deduct Stock dari Aplikasi Dompet)

- **URL:** `POST /stock/deduct`
- **Header:**
    - `X-API-Key: <YOUR_API_KEY>`
    - `Content-Type: application/json`
- **Deskripsi:** Mengurangi sisa stok produk secara atomic (menggunakan DB Transaction & pessimistic locking `lockForUpdate`) ketika terjadi transaksi pembelian dari Aplikasi Dompet Siswa.

**Request Body (`JSON`):**

```json
{
    "items": [
        {
            "tefa_product_id": "9c123456-ab01-cd02-ef03-123456789001",
            "quantity": 2
        },
        {
            "tefa_product_id": "9c123456-ab01-cd02-ef03-123456789002",
            "quantity": 1
        }
    ],
    "reference_id": "TX-DOMPET-20260909-001"
}
```

**Contoh Response Berhasil (`200 OK`):**

```json
{
    "status": "success",
    "message": "Pengurangan stok berhasil diproses",
    "data": {
        "reference_id": "TX-DOMPET-20260909-001",
        "deducted_at": "2026-09-09T12:33:00.000000Z",
        "deducted_items": [
            {
                "tefa_product_id": "9c123456-ab01-cd02-ef03-123456789001",
                "name": "Nasi Bakar",
                "deducted_quantity": 2,
                "remaining_stock": 43
            },
            {
                "tefa_product_id": "9c123456-ab01-cd02-ef03-123456789002",
                "name": "Teh Kotak",
                "deducted_quantity": 1,
                "remaining_stock": 29
            }
        ]
    }
}
```

**Contoh Response Gagal - Stok Tidak Mencukupi (`400 Bad Request`):**

```json
{
    "status": "error",
    "message": "Stok produk 'Nasi Bakar' tidak mencukupi. Tersisa: 43, diminta: 100.",
    "data": null
}
```

**Contoh Response Gagal - API Key Tidak Valid (`401 Unauthorized`):**

```json
{
    "status": "error",
    "message": "Unauthorized. Invalid or missing TEFA API Key."
}
```

---

## 4. Penanganan Error (Status Code)

| Code  | Meaning              | Deskripsi                                                                        |
| ----- | -------------------- | -------------------------------------------------------------------------------- |
| `200` | OK                   | Request berhasil diproses.                                                       |
| `400` | Bad Request          | Parameter request tidak valid atau stok tidak mencukupi saat proses pengurangan. |
| `401` | Unauthorized         | Header `X-API-Key` kosong, tidak valid, atau telah dinonaktifkan.                |
| `404` | Not Found            | Merchant atau Produk tidak ditemukan.                                            |
| `422` | Unprocessable Entity | Validasi format payload JSON request gagal.                                      |
| `500` | Server Error         | Failover internal server.                                                        |

---

## 5. Histori Transaksi Kantin TEFA (Koneksi Dua Arah & Dompet Digital)

### A. Otentikasi & Konfigurasi API Key
Seluruh HTTP Request antara Aplikasi TEFA dan Aplikasi Dompet Siswa menggunakan API Key resmi yang disimpan pada file `.env`:

```env
TEFA_API_KEY=ds_live_R8VgLxdlIfj3iPxnCMs10FeTe8tg2U8Q
DOMPET_SISWA_API_KEY=ds_live_R8VgLxdlIfj3iPxnCMs10FeTe8tg2U8Q
```

* **Header Request:** `X-API-Key: ds_live_R8VgLxdlIfj3iPxnCMs10FeTe8tg2U8Q`

---

### B. Otomatisasi Pencatatan Transaksi Dompet Digital saat `deduct`
Setiap kali Aplikasi Dompet Siswa mengirimkan request pengurangan stok ke `POST /api/v1/tefa/stock/deduct`, Aplikasi Kasir TEFA secara otomatis:
1. Memotong stok fisik pada `stock_entries` secara atomic (`lockForUpdate`).
2. Membuat entri baru pada tabel `transactions` di Aplikasi Kasir TEFA dengan field:
   - `payment_method`: `dompet_digital`
   - `status`: `lunas`
   - `buyer_name`: `Siswa (Dompet Digital)`
   - `reference`: Nomor referensi transaksi dari Dompet Siswa (`reference_id`).

---

### C. Endpoint Histori Transaksi Penjualan Kantin

Aplikasi Dompet Siswa maupun sistem eksternal dapat menarik histori transaksi penjualan kantin TEFA melalui endpoint:

* **URL:** `GET /api/v1/tefa/transactions`
* **Header:** `X-API-Key: ds_live_R8VgLxdlIfj3iPxnCMs10FeTe8tg2U8Q`
* **Query Parameters:**
  - `tefa_merchant_id` (String, Opsional): ID Kantin TEFA / Jurusan.
  - `start_date` (Date, Opsional): Format `YYYY-MM-DD` (misal `2026-09-01`).
  - `end_date` (Date, Opsional): Format `YYYY-MM-DD` (misal `2026-09-06`).
  - `payment_method` (String, Opsional): `dompet_digital` | `cash` | `qris`.
  - `status` (String, Opsional): `lunas` | `belum_menerima_uang` | `all` (Default: `all`).
  - `search` (String, Opsional): Pencarian nama pembeli, nama produk, atau nomor referensi (`PAY-XXXX`).
  - `page` (Integer, Opsional): Halaman data (Default: `1`).
  - `per_page` (Integer, Opsional): Jumlah baris per halaman (Default: `15`, Max: `100`).

**Contoh Response Sukses (`200 OK`):**
```json
{
  "status": "success",
  "message": "Histori transaksi kantin TEFA berhasil dimuat.",
  "meta": {
    "tefa_merchant_id": "9b1deb4d-3b7d-4bad-9bdd-2b0d7b3d0001",
    "merchant_name": "RPL",
    "filters": {
      "start_date": "2026-09-01",
      "end_date": "2026-09-06",
      "payment_method": "dompet_digital",
      "status": "lunas"
    },
    "summary": {
      "total_transactions": 2,
      "total_revenue": 35000.00
    },
    "pagination": {
      "current_page": 1,
      "per_page": 15,
      "total_items": 2,
      "total_pages": 1
    }
  },
  "data": [
    {
      "transaction_id": "9c123456-tx01-0001",
      "reference_number": "TX-DOMPET-20260909-001",
      "merchant_id": "9b1deb4d-3b7d-4bad-9bdd-2b0d7b3d0001",
      "merchant_name": "RPL",
      "buyer_name": "Siswa (Dompet Digital)",
      "product_id": "9c123456-ab01-cd02-ef03-123456789001",
      "product_name": "Nasi Bakar",
      "quantity": 2,
      "unit_price": 3500.00,
      "total_price": 7000.00,
      "payment_method": "dompet_digital",
      "status": "lunas",
      "transacted_at": "2026-09-09T12:38:00+07:00",
      "note": "Transaksi via API Dompet Siswa (Saldo Digital)"
    }
  ]
}
```


