# Penjelasan Migrasi MySQL ke PostgreSQL (Supabase)

## Apakah Bisa Menggunakan Supabase (PostgreSQL) agar Online?
**Tentu saja bisa, dan ini adalah ide yang sangat brilian!** 

Laravel memiliki dukungan bawaan (*native*) yang sangat baik untuk **PostgreSQL**. Dengan menggunakan Supabase (yang pada dasarnya adalah database PostgreSQL yang *di-hosting* di cloud), aplikasi kasir Anda akan menjadi tersentralisasi (online). 

**Keuntungannya:**
- Anda tidak perlu lagi melakukan Export dan Import data secara manual antar *device*.
- Jika ada kasir 1 (di laptop A) dan kasir 2 (di laptop B) melakukan transaksi, datanya akan langsung masuk ke database Supabase secara *real-time*.
- Anda bisa memantau laporan (*dashboard*) dari HP atau perangkat mana pun kapan saja.

---

## Persiapan di Aplikasi Laravel

Untuk menghubungkan aplikasi Laravel Anda dengan Supabase, Anda hanya perlu melakukan sedikit penyesuaian:

1. **Aktifkan Ekstensi PostgreSQL di Laragon**
   Secara *default*, Laragon menggunakan MySQL. Anda perlu mengaktifkan ekstensi PostgreSQL untuk PHP.
   - Buka Laragon -> Klik Kanan -> PHP -> Quick Settings -> Centang `php_pdo_pgsql` dan `php_pgsql`.
   - *Restart* Apache/Nginx di Laragon.

2. **Dapatkan Kredensial Database dari Supabase**
   - Buat project di [Supabase](https://supabase.com/).
   - Masuk ke menu **Project Settings -> Database**.
   - Catat Host, Port (biasanya 5432), Database Name, User, dan Password.

3. **Ubah file `.env` di Laravel**
   Ubah konfigurasi koneksi database Anda menjadi seperti ini:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=aws-0-ap-southeast-1.pooler.supabase.com # Contoh host supabase
   DB_PORT=5432
   DB_DATABASE=postgres
   DB_USERNAME=postgres.nama_project_anda
   DB_PASSWORD=password_supabase_anda
   ```

4. **Jalankan Migrasi**
   Untuk membuat struktur tabel yang kosong di Supabase, jalankan perintah ini di terminal:
   ```bash
   php artisan migrate:fresh
   ```

---

## Cara Memindahkan Data Lama (MySQL) ke Supabase (PostgreSQL)

Karena MySQL dan PostgreSQL menggunakan "dialek" bahasa SQL yang sedikit berbeda, Anda tidak bisa sekadar meng-*eksport* `.sql` dari phpMyAdmin dan meng-*import* nya langsung ke Supabase.

Berikut adalah 3 cara paling aman untuk memindahkan datanya:

### Cara 1: Menggunakan DBeaver (Paling Mudah & Direkomendasikan)
DBeaver adalah aplikasi pengelola database gratis. Fitur "Data Transfer" miliknya akan otomatis menerjemahkan tipe data dari MySQL ke PostgreSQL.
1. Download dan instal **DBeaver**.
2. Buat 2 koneksi di DBeaver: 
   - Koneksi 1: MySQL lokal Anda (di Laragon).
   - Koneksi 2: PostgreSQL Supabase Anda.
3. Di koneksi MySQL, blok semua tabel data (products, transactions, dll).
4. Klik kanan -> **Export Data**.
5. Pilih target/tujuannya ke **Database**, lalu pilih koneksi Supabase Anda.
6. DBeaver akan menyedot data dari MySQL dan memasukkannya ke Supabase dengan aman.

### Cara 2: Menggunakan Laravel Script (Database Seeder/Command)
Jika Anda paham sedikit *coding*, Anda bisa membuat 2 koneksi di `config/database.php` Laravel (satu `mysql` dan satu `pgsql`). Lalu Anda bisa membuat *Artisan Command* sederhana yang membaca data dari koneksi `mysql` dan menyimpannya ke koneksi `pgsql`.
```php
// Contoh logika script
$products = DB::connection('mysql')->table('products')->get();
foreach ($products as $product) {
    DB::connection('pgsql')->table('products')->insert((array) $product);
}
```

### Cara 3: Menggunakan `pgloader` (Untuk Programmer Lanjut)
`pgloader` adalah *tool* otomatis (dijalankan lewat command line) yang khusus dibuat untuk migrasi ini.
Namun, karena Anda menggunakan Windows, *tool* ini sedikit repot digunakan (biasanya harus menggunakan WSL/Linux). Perintahnya cukup satu baris:
```bash
pgloader mysql://user:pass@localhost/labantik_db postgresql://user:pass@supabase-host/postgres
```

---

## Kesimpulan & Saran
Sangat disarankan untuk melakukan transisi ke Supabase jika toko sudah memiliki banyak perangkat. 

Langkah yang paling tepat:
1. Daftar Supabase.
2. Hubungkan `.env` Laravel ke Supabase.
3. Lakukan `php artisan migrate`.
4. Gunakan **DBeaver** untuk memompa (*pump*) data lama dari MySQL lokal ke Supabase.
5. Setelah selesai, semua *device* tinggal diatur `.env`-nya untuk menunjuk ke Supabase yang sama!
