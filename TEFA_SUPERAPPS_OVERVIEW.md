# Garis Besar Perombakan: Superapps TEFA SMKN 1 Talaga

Dokumen ini menjelaskan rancangan sistem baru untuk mengubah aplikasi Kasir LabAntik menjadi **Superapps TEFA SMKN 1 Talaga** (Kasir & Manajemen Keuangan Multi-Tefa).

---

## 1. Struktur Hak Akses (Role & Jurusan/TEFA)

Sistem akan memiliki 3 tingkatan hak akses utama:

*   **Superadmin**:
    *   Dapat melihat, mengelola, dan mengakses seluruh data transaksi, produk, dan laporan keuangan dari semua jurusan.
    *   Dapat mengelola akun pengguna (user) dan memberikan hak akses.
*   **Pengelola Jurusan**:
    *   Perwakilan atau pimpinan unit TEFA masing-masing jurusan (RPL, TKJ, Otomotif, dll.).
    *   Hanya dapat melihat dan mengelola data produk, transaksi, dan laporan untuk jurusan mereka sendiri.
*   **Kasir**:
    *   Petugas kasir di unit TEFA tertentu.
    *   Memiliki akses fitur kasir dasar (input transaksi, stok opname awal/akhir) khusus untuk jurusan tempat dia bertugas.

Setiap user dapat memiliki **lebih dari satu role** atau hak akses di jurusan berbeda (misal: Kasir di RPL sekaligus Pengelola di TKJ).

---

## 2. Struktur Tabel Baru (Relasi Database)

Untuk mengimplementasikan multi-role & multi-tefa, kita akan menggunakan skema berikut:

```
┌──────────────┐       ┌─────────────────┐       ┌──────────┐
│    users     │       │    role_user    │       │  roles   │
├──────────────┤       ├─────────────────┤       ├──────────┤
│ id           │◄─────╼│ user_id         │      ┌│ id       │
│ name         │       │ role_id         │╍╍╍╍╍╍││ name     │
│ email        │       │ jurusan_id (null)│      ││ label    │
│ password     │       └────────┬────────┘       │└──────────┘
└──────────────┘                │                │
                                ▼                │
                       ┌─────────────────┐       │
                       │    jurusans     │       │
                       ├─────────────────┤       │
                       │ id              │◄╍╍╍╍╍╍┘
                       │ name            │
                       └─────────────────┘
```

*   `jurusans`: Menyimpan daftar Jurusan/Unit TEFA (contoh: `RPL`, `TKJ`, `Otomotif`).
*   `roles`: Menyimpan daftar tingkatan hak akses (`superadmin`, `pengelola_jurusan`, `kasir`).
*   `role_user` (Pivot): Menghubungkan user dengan role dan jurusan. Jika `role_id` adalah `superadmin`, kolom `jurusan_id` dikosongkan (`null`) karena superadmin menguasai seluruh jurusan.

---

## 3. Alur Login & Pemilihan Akses

1.  **Form Login**: User memasukkan email dan password seperti biasa (pada tampilan baru dengan 2 logo dummy & judul **Superapps TEFA SMKN 1 Talaga**).
2.  **Deteksi Hak Akses**:
    *   Jika user **hanya memiliki 1 hak akses** (misal: hanya Kasir RPL), sistem akan langsung mengarahkan ke dashboard dengan mengaktifkan konteks tersebut secara otomatis.
    *   Jika user **memiliki lebih dari 1 hak akses** (misal: Kasir RPL & Pengelola Otomotif), user akan diarahkan ke halaman **Pilih Hak Akses** (`/select-role`) yang menampilkan opsi kartu akses.
3.  **Aktivasi Session**: Konteks akses aktif (`active_role_id` dan `active_jurusan_id`) disimpan di dalam session agar sistem dapat menyaring data produk, transaksi, dan tampilan menu sesuai dengan TEFA yang dipilih.

---

## 4. Rencana Implementasi Bertahap

*   **Tahap 1 (Sekarang)**:
    *   Membuat migrasi dan seeder untuk tabel `jurusans`, `roles`, dan `role_user`.
    *   Mengubah halaman Login (Logo dummy & nama baru).
    *   Membuat halaman & logika pemilihan hak akses (`/select-role`) beserta middleware pelindungnya.
    *   Membuat modul **Manajemen User** di dashboard untuk membuat akun baru dan menentukan hak aksesnya.
*   **Tahap 2 (Berikutnya)**:
    *   Membatasi query data (produk, transaksi, laporan) di dashboard agar disaring berdasarkan `active_jurusan_id` yang sedang aktif di session.
    *   Menyesuaikan tema/tampilan visual dashboard sesuai dengan jurusan yang dipilih.
