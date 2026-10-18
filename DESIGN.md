# DESIGN DIRECTION: LabAntik Superapps TEFA SMKN 1 Talaga

> Dokumen acuan desain tunggal (*Single Source of Truth*) untuk antarmuka LabAntik Superapps TEFA SMKN 1 Talaga.

## 1. Identitas & Karakter Produk
- **Nama Produk:** LabAntik Superapps TEFA SMKN 1 Talaga
- **Karakter Visual:** Utilitarian, gesit, tegas, berwibawa khas unit Teaching Factory (TEFA) sekolah kejuruan modern (*modern school enterprise*).
- **Filosofi Desain:** Mengutamakan kecepatan kasir dan keterbacaan data keuangan dengan satu tangan di layar ponsel (*mobile-first*), serta menyajikan ruang kerja yang lapang dan teratur di desktop. Menolak segala bentuk *AI slop* (tidak ada gradien ungu-biru generik, tidak ada sudut membulat berlebihan, tidak ada kartu melayang tanpa tujuan, dan tidak ada ornamen tanpa fungsi).

## 2. Palet Warna (Color System)
Warna utama dipertahankan persis seperti identitas yang ada, dengan rasio kontras tinggi (WCAG AA):

### Warna Primer (Brand Identity)
- **Primary Blue:** `#2563EB` (Darker Royal Blue) & `#3B82F6` (Royal Blue)
  - *Fungsi:* Elemen navigasi aktif, identitas utama TEFA, link penting, header tabel.
- **Primary Blue Soft (Latar Aksen):** `#EFF6FF` (Light mode) / `#1E293B` (Dark mode)

### Warna Aksi & Sorotan (Action Accent)
- **Rose Red:** `#E11D48` (Rose 600) & `#F43F5E` (Rose 500)
  - *Fungsi:* Tombol kasir utama (Buka Kasir, Bayar, Checkout), indikator stok menipis, peringatan darurat.
- **Rose Soft:** `#FFF1F2` (Light mode) / `#4C0519` (Dark mode)

### Warna Status & Notifikasi
- **Warm Amber:** `#D97706` (Amber 600) & `#F59E0B` (Amber 500)
  - *Fungsi:* Status hutang belum lunas, sesi belum closing, peringatan waktu shift.
- **Emerald Green:** `#059669` (Emerald 600) & `#10B981` (Emerald 500)
  - *Fungsi:* Kas masuk, transaksi sukses, status kehadiran tepat waktu.

### Latar & Permukaan Netral (Neutral Surfaces)
- **Mode Terang (Light Mode):**
  - Background Utama: `#F8FAFC` (Slate 50) / `#F1F5F9` (Slate 100)
  - Kartu & Kontainer: `#FFFFFF` (Pure White)
  - Border: `#E2E8F0` (Slate 200)
  - Teks Utama: `#0F172A` (Slate 900)
  - Teks Sekunder: `#64748B` (Slate 500)
- **Mode Gelap (Dark Mode):**
  - Background Utama: `#020617` (Slate 950)
  - Kartu & Kontainer: `#0F172A` (Slate 900)
  - Border: `#1E293B` (Slate 800)
  - Teks Utama: `#F8FAFC` (Slate 50)
  - Teks Sekunder: `#94A3B8` (Slate 400)

## 3. Tipografi
- **Font Utama:** `Outfit`, sans-serif (alternatif: `Plus Jakarta Sans`, `Inter`).
- **Skala Font:**
  - H1 Page Title: `1.5rem` (24px) di mobile, `1.875rem` (30px) di desktop, font-bold / tracking-tight.
  - Section Header: `1.125rem` (18px) hingga `1.25rem` (20px), font-bold.
  - Body Text: `0.875rem` (14px), font-normal atau font-medium.
  - Caption / Meta: `0.75rem` (12px), font-medium / tracking-wide.
  - Angka Keuangan & Nominal: font-semibold / font-bold tabular numbers (agar angka sejajar dan mudah dibaca kasir).

## 4. Spasi, Bentuk & Elevasi
- **Border Radius:**
  - Tombol & Input: `rounded-xl` (12px).
  - Kartu & Kontainer: `rounded-2xl` (16px).
  - Badge & Pill Filter: `rounded-lg` (8px) atau `rounded-full` (khusus pill filter).
  - *Larangan:* Dilarang menggunakan `rounded-[3rem]` atau pill shape pada semua kartu/input.
- **Shadows (Elevasi):**
  - Default Kartu: `shadow-sm` atau border tegas `border border-slate-200 dark:border-slate-800`.
  - Dropdown & Bottom Sheet: `shadow-xl` lembut dan terfokus.
  - *Larangan:* Dilarang menggunakan bayangan brutal hitam 4px atau glow warna-warni pada setiap elemen.
- **Touch Targets:**
  - Minimal 44x44px pada seluruh kontrol interaktif mobile (tombol keranjang, navigasi bawah, tombol kuantitas produk, tab switcher).

## 5. Dials Anti-Slop
- **ENERGY: 2** (Fungsional & Tegas): Visual bersih, terstruktur, tidak monoton kosong (*sterile*), namun tidak bising dengan ornamen tak berguna.
- **RHYTHM: 2** (Harmonis & Teratur): Spasi antara section konsisten (16px di mobile, 24-32px di desktop). Grid data disesuaikan dengan jenis konten.
- **MOTION: 1** (Mikro-Interaksi Cepat): Transisi hover dan klik instan (150-200ms `ease-out`). Tidak ada animasi looping mengambang, bouncing tak berujung, atau teks berkedip tanpa aksi pengguna.
