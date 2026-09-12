# Panduan Lengkap & Mekanisme Penilaian Seleksi Calon Anggota Labantik

Dokumen ini memuat panduan lengkap mengenai alur pendaftaran, mekanisme penilaian mingguan, rumus perhitungan skor akhir, hingga alur kelulusan 15 Besar untuk calon anggota Labantik. Dokumen ini dirancang rapi dan siap dikonversi ke format PDF.

---

## 1. Hak Akses & Ketentuan Penguji

Penilaian dan pengelolaan seleksi calon anggota Labantik dapat dilakukan oleh pengguna dengan hak akses berikut:
* **Super Admin**
* **Pengelola Jurusan**
* **Kasir Jurusan RPL** (Kasir dari jurusan yang terdaftar sebagai RPL)

> **Catatan Pengisian Absensi & Nilai**:
> 1. **Absensi (Hadir/Sakit/Izin/Alfa)**: Bersifat global per peserta per pekan dan **cukup diisi oleh 1 user saja**. Semua user yang memiliki akses bebas mengklik tombol absensi dan menginput alasan. Data absensi yang diisi akan langsung berlaku untuk peserta tersebut.
> 2. **Nilai Akademik & Attitude**: Bebas diisi oleh masing-masing penguji/penilai. Jika terdapat lebih dari satu penguji yang memasukkan nilai untuk peserta yang sama di pekan tersebut, sistem akan secara otomatis menghitung **rata-rata dari seluruh penguji**.
> 3. **Perubahan Data**: Nilai maupun absensi dapat ditinjau dan **diperbarui (di-edit) berkali-kali** selama masa seleksi berlangsung.

---

## 2. Alur Pendaftaran & Portal Login Calon

```
[ Form Pendaftaran Online ] ──► [ Database Calon Labantik ] ──► [ Penilaian Mingguan (Pekan 1-12) ]
                                                                             │
[ Tampilan Pengumuman Calon ] ◄── [ Seleksi Akhir 15 Besar ] ◄── [ Tombol Selesai Seleksi ]
```

* **Pendaftaran**: Calon anggota mendaftar melalui form publik di `/labantik/form-registration`.
* **Login Cek Hasil**: Calon anggota dapat memeriksa hasil seleksi secara mandiri di portal `/labantik/login` menggunakan:
  1. **Nama Depan** (Kata pertama dari nama pendaftaran)
  2. **4 Digit Terakhir Nomor HP** (4 angka terakhir dari nomor HP terdaftar)

### Contoh Input Login Kandidat:

| Data Pendaftaran Peserta | Input Field Nama Depan | Input Field 4 Digit No HP | Status Login |
| :--- | :--- | :--- | :--- |
| Nama: **Ahmad Najmy Al-Farisi**<br>No HP: `081234567890` | `Ahmad` | `7890` | **Berhasil Login** |
| Nama: **Budi Santoso**<br>No HP: `085711223344` | `Budi` | `3344` | **Berhasil Login** |
| Nama: **Siti Nurhaliza**<br>No HP: `089699887766` | `Siti` | `7766` | **Berhasil Login** |

> *Catatan*: Huruf besar/kecil (case-insensitive) tidak berpengaruh saat mengetik nama depan. Sistem akan mencocokkan kata pertama dari nama lengkap peserta secara otomatis.

---

## 3. Komponen Penilaian Pekanan (Pekan 1 - 12)

Pada setiap pekan kegiatan (Pekan 1 hingga 12), user menginput data berikut:

| Komponen | Skala / Opsi | Pengisian | Keterangan & Ketentuan |
| :--- | :--- | :--- | :--- |
| **Kehadiran (Absensi)** | `H`, `S`, `I`, `A` | **Cukup 1 User** | **H (Hadir)**, **S (Sakit)**, **I (Izin)**, **A (Alfa)**. Alasan wajib diisi jika Sakit atau Izin. Berlaku untuk seluruh sistem. |
| **Nilai Akademik** | `0 - 100` | **Multi-Penilai (Rata-rata)** | Nilai evaluasi tugas, materi, atau pemahaman teknis. Hanya aktif jika status kehadiran = **Hadir (H)**. |
| **Nilai Attitude** | `0 - 100` | **Multi-Penilai (Rata-rata)** | Nilai kedisiplinan, etika, dan keaktifan. Hanya aktif jika status kehadiran = **Hadir (H)**. |

---

## 4. Rumus Perhitungan Skor Akhir Seleksi

Saat proses penilaian selesai, pengelola mengeklik tombol **"Selesai Seleksi"**. Sistem akan mengalkulasi total skor seluruh pekan menggunakan rumus matematika sebagai berikut:

$$\text{Skor Akhir} = \overline{\text{Nilai Akademik}} + \overline{\text{Nilai Attitude}} + (\text{Jumlah Hadir} \times 5) - (\text{Jumlah Alfa} \times 10) - (\text{Jumlah Izin} \times 2)$$

### Bobot & Akumulasi Kehadiran:
* **Hadir (H)**: Bonus $+5$ poin per pekan.
* **Alfa (A)**: Penalti $-10$ poin per ketidakhadiran tanpa alasan.
* **Izin (I)**: Penalti $-2$ poin per izin.
* **Sakit (S)**: $0$ poin (tidak mengurangi maupun menambah skor).

---

## 5. Alur Pemeringkatan & Seleksi 15 Besar

```
                      ┌──► Rank 1 s.d 15  ──► Masuk 15 Besar ──► Pengelola Set Status: [Lolos] / [Gagal]
[ Kalkulasi Skor ] ──┤
                      └──► Rank 16+       ──► Otomatis Tidak Lolos
```

1. **Pemeringkatan Otomatis**:
   Sistem mengurutkan seluruh peserta dari Skor Akhir tertinggi hingga terendah.
2. **Kualifikasi 15 Besar**:
   - **Peringkat 1 hingga 15**: Dimasukkan ke dalam tab **"Lolos Seleksi (15 Besar)"** dengan status awal `BELUM DITENTUKAN` (`pending`).
   - **Peringkat 16 ke bawah**: Otomatis berstatus `TIDAK LOLOS` (`rejected`).
3. **Seleksi Tingkat 2 (Pengelola)**:
   Di tab 15 Besar, pengelola memiliki kewenangan penuh untuk menetapkan keputusan akhir:
   - **Tombol `Lolos`**: Menetapkan peserta **Lolos Seleksi Akhir** (`passed`).
   - **Tombol `Gagal`**: Menetapkan peserta **Tidak Lolos** (`rejected`).

---

## 6. Alur Tampilan Saat Calon Melakukan Login

Ketika calon anggota melakukan login ke portal `/labantik/login`, alur tampilan layar dibagi menjadi 3 kondisi status pengumuman:

1. **Modal Peringatan Volume Perangkat (Pop-up)**:
   - Sebelum halaman hasil terbuka, layar menampilkan modal peringatan: *"Silakan nyalakan dan besarkan volume perangkat Anda sebelum melanjutkan!"*.
   - Modal akan menutup begitu tombol *"Saya Sudah Nyalakan Volume"* diklik oleh calon.

2. **Tampilan 1: Status Masih Dalam Proses Seleksi / Penetapan (`pending`)**:
   - Layar menampilkan **Animasi Loading/Spinning** bernuansa emas/amber.
   - **Kondisi A (Sebelum Selesai Seleksi / Belum Terbit Hasil)**: Pesan pengumuman: *"SELEKSI SEDANG DALAM PROSES. Rekapitulasi nilai calon anggota Labantik sedang berlangsung... Harap cek halaman ini secara berkala!"*.
   - **Kondisi B (Sudah Masuk 15 Besar, tapi Pengelola Belum Set Lolos/Gagal)**: Pesan pengumuman: *"SELEKSI SEDANG DALAM PROSES. Selamat! Anda telah masuk dalam 15 Besar Calon Anggota Labantik. Status akhir kelulusan Anda saat ini sedang dalam tahap verifikasi & penetapan oleh pengelola. Harap cek kembali secara berkala!"*.

3. **Tampilan 2: Status Lolos Seleksi Akhir (`passed`)**:
   - Terjadi jika pengelola telah mengeklik tombol **`Lolos`** pada peserta 15 Besar tersebut.
   - Tampilan selebrasi mewah bernuansa hijau/emas.
   - Efek hujan **Canvas Confetti** otomatis menyala dan menghiasi seluruh layar.
   - Lagu selebrasi *"Terimakasih Sudah Bertahan - Ghea Indrawari"* terputar secara otomatis.
   - Tersedia tombol hijau mencolok untuk **Gabung Grup WhatsApp Resmi Labantik**.

4. **Tampilan 3: Status Tidak Lolos (`rejected`)**:
   - Terjadi jika peserta berada di luar 15 Besar ATAU pengelola mengeklik tombol **`Gagal`**.
   - Layar menampilkan pesan apresiasi & kalimat motivasi penyemangat: *"Ingatlah bahwa satu kesempatan yang belum berhasil bukanlah akhir dari perjalananmu, melainkan langkah menuju pintu sukses yang jauh lebih besar. Tetaplah belajar, bersinar, dan tunjukkan potensi terbaikmu! ✨💪"*.
   - Tanpa pemutaran musik/lagu maupun efek confetti.

---

*Dokumen ini dapat langsung dicetak atau di-export ke format PDF melalui browser (Ctrl + P -> Save as PDF).*
