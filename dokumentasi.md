# Dokumentasi Progress Frontend — NesaCourt

Dokumentasi ini disusun oleh tim **Frontend (Adit — UI/UX / Frontend)** sebagai acuan bagi tim **Backend (Wisnu — Laravel / Database)** dan **Project Manager (Yoga)** untuk mengetahui implementasi antarmuka yang telah selesai dan kebutuhan integrasi backend.

---

## 1. Ringkasan Pengerjaan
Modul yang telah selesai dikerjakan adalah **Kelompok 1: Autentikasi & Halaman Publik** (mengacu pada `Blueprint_NESACOURT.pdf` poin `F-01` s/d `F-04`).

### Karakteristik Arsitektur Frontend:
- **Teknologi**: Laravel Blade Template Engine dipadukan dengan **Tailwind CSS (CDN)**, **Alpine.js (CDN)** untuk reaktivitas ringan, dan **Lucide Icons**.
- **Bebas Dependensi Node.js**: Tidak membutuhkan `npm install` atau `npm run dev` (bisa langsung dijalankan via `php artisan serve`).
- **Tipografi**: Menggunakan font resmi **Plus Jakarta Sans**, hierarki ukuran rapi, dan tanpa huruf kapital semua (*no ALL CAPS*).
- **Responsif**: Mendukung penuh tata letak Mobile, Tablet, dan Desktop. Gambar kafetaria pada halaman autentikasi otomatis disembunyikan pada layar `< 1024px` (mobile/tablet) agar pengguna langsung fokus ke form.
- **Tombol Single-Line**: Seluruh tombol dan tautan diamankan dengan `whitespace-nowrap` agar tidak patah menjadi dua baris.

---

## 2. Pemetaan Rute dan File Tampilan

| ID Fitur | Halaman | Method & URI | File View Blade | Keterangan |
| :--- | :--- | :--- | :--- | :--- |
| `F-01` | Landing Page | `GET /` | `resources/views/landing.blade.php` | Beranda publik, menu populer, fitur AI showcase, dan footer kampus. |
| `F-02` | Login | `GET /login` | `resources/views/auth/login.blade.php` | Form autentikasi mahasiswa, stan, dan admin. |
| `F-03` | Registrasi | `GET /register` | `resources/views/auth/register.blade.php` | Form pendaftaran akun mahasiswa baru. |
| `F-04` | Lupa Sandi | `GET /forgot-password` | `resources/views/auth/forgot-password.blade.php` | Form permintaan tautan pemulihan kata sandi. |
| Shared | Master Auth | - | `resources/views/layouts/auth.blade.php` | Layout dasar auth dengan navbar logo bersih dan footer 4 kolom. |

---

## 3. Spesifikasi Kontrak Form (Kebutuhan Handler Backend)

Tim Backend dapat langsung menghubungkan form yang telah dibuat ke controller/action Laravel dengan spesifikasi payload berikut:

### A. Form Login Mahasiswa / Pengguna (`F-02`)
- **Action**: `POST /login`
- **CSRF**: `@csrf` sudah terpasang.
- **Payload Request**:
  - `login` (*string, required*): Email kampus (`nama@mhs.unesa.ac.id`) atau 11 digit NIM.
  - `password` (*string, required*): Kata sandi pengguna.
  - `remember` (*boolean, optional*): Checkbox "Ingat saya di perangkat ini".
- **Ekspektasi Response**:
  - Sukses: Redirect ke dashboard sesuai role (`buyer` ➔ `/dashboard`, `seller` ➔ `/penjual/dashboard`, `admin` ➔ `/admin/dashboard`).
  - Gagal: Redirect back dengan pesan error standar Laravel `$errors`.

### B. Form Registrasi Mahasiswa Baru (`F-03`)
- **Action**: `POST /register`
- **CSRF**: `@csrf` sudah terpasang.
- **Payload Request**:
  - `name` (*string, required*): Nama lengkap mahasiswa.
  - `nim` (*string, required*): Nomor Induk Mahasiswa (11 digit unik).
  - `email` (*string, required, email*): Email kampus (`@mhs.unesa.ac.id`) atau email aktif.
  - `password` (*string, required, min:8*): Kata sandi.
  - `password_confirmation` (*string, required*): Konfirmasi kata sandi.
  - `terms` (*accepted*): Checkbox persetujuan syarat & ketentuan.
- **Ekspektasi Response**:
  - Sukses: Menyimpan ke tabel `users` dengan `role = 'buyer'`, membuat sesi login, dan redirect ke dashboard pembeli.

### C. Form Pemulihan Kata Sandi (`F-04`)
- **Action**: `POST /forgot-password`
- **CSRF**: `@csrf` sudah terpasang.
- **Payload Request**:
  - `recovery_identifier` (*string, required*): Email kampus terdaftar atau NIM.
- **Ekspektasi Response**:
  - Mengirim tautan token reset password ke email terkait.

---

## 4. Cara Menjalankan untuk Pengujian

1. Pastikan database MySQL aktif dan `.env` telah terkonfigurasi.
2. Jalankan server lokal Laravel:
   ```bash
   php artisan serve
   ```
3. Akses pada browser:
   - Landing Page: `http://127.0.0.1:8000/`
   - Login: `http://127.0.0.1:8000/login`
   - Registrasi: `http://127.0.0.1:8000/register`
   - Lupa Sandi: `http://127.0.0.1:8000/forgot-password`

---

## 5. Rencana Tahap Selanjutnya (Frontend)
Setelah integrasi autentikasi oleh Backend siap, Frontend akan melanjutkan ke:
1. **Kelompok 2: Sisi Pembeli (`F-06` Katalog Menu)**: Filter stan, kategori, pencarian, dan indikator stok porsi.
2. **Kelompok 2: AI Smart Menu Recommendation (`F-08`)**: Form kriteria budget, rasa, dan estimasi waktu siap.
