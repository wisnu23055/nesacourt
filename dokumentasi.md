# Dokumentasi Progress Frontend — NesaCourt

Dokumentasi ini disusun oleh tim **Frontend (Adit — UI/UX / Frontend)** sebagai acuan bagi tim **Backend (Wisnu — Laravel / Database)** dan **Project Manager (Yoga)** untuk mengetahui implementasi antarmuka yang telah selesai dan kebutuhan integrasi backend.

---

## 1. Ringkasan Modul yang Telah Selesai

Saat ini telah selesai diimplementasikan secara penuh 2 kelompok modul antarmuka:
1. **Kelompok 1: Autentikasi & Halaman Publik (`F-01` s/d `F-04`)**
2. **Kelompok 2: Sisi Pembeli / Mahasiswa (`F-05` s/d `F-14`) — 10 Fitur Penuh**

### Karakteristik Arsitektur Frontend:
- **Teknologi**: Laravel Blade Template Engine dipadukan dengan **Tailwind CSS (CDN)**, **Alpine.js (CDN)** untuk reaktivitas ringan, dan **Lucide Icons**.
- **Bebas Dependensi Node.js**: Tidak membutuhkan `npm install` atau `npm run dev` (bisa langsung dijalankan via `php artisan serve`).
- **Tipografi**: Menggunakan font resmi **Plus Jakarta Sans**, hierarki ukuran rapi, dan tanpa huruf kapital semua (*no ALL CAPS*).
- **Responsif**: Mendukung penuh tata letak Mobile, Tablet, dan Desktop. Dilengkapi dengan **Mobile Bottom Navigation Bar** pada antarmuka pembeli.
- **Tombol Single-Line**: Seluruh tombol dan tautan diamankan dengan `whitespace-nowrap inline-flex items-center justify-center` agar tidak patah menjadi dua baris di perangkat apa pun.
- **Proteksi Single-Seller**: Keranjang belanja menerapkan proteksi satu transaksi = satu stan karena perbedaan QRIS statis antar stan.

---

## 2. Pemetaan Rute dan File Tampilan

### A. Kelompok 1: Autentikasi & Akun Publik
| ID Fitur | Halaman | Method & URI | File View Blade | Keterangan |
| :--- | :--- | :--- | :--- | :--- |
| `F-01` | Landing Page | `GET /` | `resources/views/landing.blade.php` | Beranda publik, menu populer, fitur AI showcase, dan footer kampus. |
| `F-02` | Login | `GET /login` | `resources/views/auth/login.blade.php` | Form autentikasi mahasiswa, stan, dan admin. |
| `F-03` | Registrasi | `GET /register` | `resources/views/auth/register.blade.php` | Form pendaftaran akun mahasiswa baru. |
| `F-04` | Lupa Sandi | `GET /forgot-password` | `resources/views/auth/forgot-password.blade.php` | Form permintaan tautan pemulihan kata sandi. |

### B. Kelompok 2: Sisi Pembeli (10 Poin)
| ID Fitur | Halaman | Method & URI | File View Blade | Keterangan |
| :--- | :--- | :--- | :--- | :--- |
| `F-05` | Dashboard Pembeli | `GET /dashboard` | `resources/views/buyer/dashboard.blade.php` | Sapaan mahasiswa, pelacak pesanan berjalan, stan populer, dan menu terlaris. |
| `F-06` | Katalog Menu & Stan | `GET /katalog` | `resources/views/buyer/catalog.blade.php` | Filter kategori, filter stan kantin, pencarian, dan badge stok porsi. |
| `F-07` | Detail Menu Pop-up | Terintegrasi di `/katalog` | Terintegrasi via Alpine.js Modal | Modal quick view menu, estimasi masak, input catatan khusus, counter porsi. |
| `F-08` | Rekomendasi Menu AI | `GET /rekomendasi` | `resources/views/buyer/recommendation.blade.php` | Form kriteria budget slider, selera rasa, waktu tunggu, skor kecocokan AI & alasan. |
| `F-09` | Keranjang Belanja | `GET /keranjang` | `resources/views/buyer/cart.blade.php` | Halaman penuh keranjang belanja, rincian porsi, catatan, subtotal, dan slide drawer. |
| `F-10` | Konfirmasi Checkout | `GET /checkout` | `resources/views/buyer/checkout.blade.php` | Opsi Makan di Tempat (Nomor Meja) vs Bawa Pulang, info pemesan, catatan stan. |
| `F-11` | Pembayaran QRIS Statis | `GET /pesanan/bayar` | `resources/views/buyer/payment.blade.php` | Kode QRIS dari `public/images/qris.png`, nomor order unik, countdown timer 15 mnt. |
| `F-12` | Pelacakan Status Real-time | `GET /pesanan/status` | `resources/views/buyer/order-status.blade.php` | Visual stepper 6 tahapan pesanan, nomor antrean stan, estimasi siap ambil. |
| `F-13` | Riwayat & Struk Digital | `GET /riwayat` | `resources/views/buyer/history.blade.php` | Tab filter transaksi, rincian biaya, tombol pesan lagi, dan pop-up cetak struk nota. |
| `F-14` | Profil Akun Pembeli | `GET /profil` | `resources/views/buyer/profile.blade.php` | Kartu ID mahasiswa UNESA, update nomor WhatsApp, form ganti sandi, dan logout. |

---

## 3. Ketentuan Gambar QRIS Statis

Untuk sementara waktu, sistem pembayaran menggunakan **QRIS Statis**:
- **Lokasi File Gambar**: `public/images/qris.jpeg`
- **Path Lengkap di Proyek**: `c:\laragon\www\nesacourt\public\images\qris.jpeg`
- **Cara Mengganti Gambar**: Letakkan atau timpa file `qris.jpeg` milik Anda langsung di folder tersebut. Halaman `/pesanan/bayar` akan otomatis memuat gambar QRIS tersebut secara langsung melalui helper Laravel `asset('images/qris.jpeg')`.

---

## 4. Spesifikasi Integrasi Backend (Wisnu)

Frontend telah menyiapkan markup Blade, form binding, dan Alpine.js state yang siap disambungkan ke Controller & Model Laravel:

1. **Dashboard & Katalog (`F-05`, `F-06`, `F-07`)**:
   - Model yang dibutuhkan: `Stall` (Stan), `Menu` (Menu makanan/minuman), `Category`.
   - Kolom porsi stok: `Menu::portion_stock` (bila `0`, tombol tambah otomatis dinonaktifkan).

2. **Rekomendasi Cerdas AI (`F-08`)**:
   - Parameter input: `budget` (integer), `taste` (string), `max_prep_time` (integer), `category` (string).
   - Logika filtering mengurutkan menu stan yang `price <= budget` dan `prep_time <= max_prep_time`.

3. **Checkout & Order Creation (`F-10`, `F-11`)**:
   - Action: `POST /orders`
   - Payload:
     - `seller_id`: ID Stan terpilih (aturan 1 order = 1 stan).
     - `service_type`: `'dine_in'` | `'takeaway'`.
     - `table_number`: String nomor meja (wajib jika `dine_in`).
     - `customer_phone`: String nomor WhatsApp aktif.
     - `order_notes`: Catatan pesanan khusus.
     - `items`: Array item `[{ menu_id, qty, notes, price }]`.
   - Response: Redirect ke `/pesanan/bayar` membawa objek pesanan baru dengan status awal `menunggu_pembayaran`.

4. **Status Pesanan Real-time (`F-12`)**:
   - Tahapan status:
     `dibuat` ➔ `menunggu_pembayaran` ➔ `dikonfirmasi` ➔ `diproses` ➔ `siap_diambil` ➔ `selesai`.
   - Endpoint polling atau SSE/WebSocket jika backend siap.

---

## 5. Cara Menjalankan untuk Pengujian

1. Server lokal Laravel:
   ```bash
   php artisan serve
   ```
2. Daftar URL siap diakses di browser:
   - Landing Page: `http://127.0.0.1:8000/`
   - Login: `http://127.0.0.1:8000/login`
   - Registrasi: `http://127.0.0.1:8000/register`
   - Lupa Sandi: `http://127.0.0.1:8000/forgot-password`
   - Dashboard Pembeli: `http://127.0.0.1:8000/dashboard`
   - Katalog Menu & Stan: `http://127.0.0.1:8000/katalog`
   - Rekomendasi Menu AI: `http://127.0.0.1:8000/rekomendasi`
   - Keranjang Belanja: `http://127.0.0.1:8000/keranjang`
   - Checkout Pesanan: `http://127.0.0.1:8000/checkout`
   - Pembayaran QRIS Statis: `http://127.0.0.1:8000/pesanan/bayar`
   - Status Pesanan: `http://127.0.0.1:8000/pesanan/status`
   - Riwayat Transaksi & Struk: `http://127.0.0.1:8000/riwayat`
   - Profil Akun: `http://127.0.0.1:8000/profil`

---

## 6. Catatan Penyempurnaan UI & Testing Keranjang Terkini

1. **Data Dummy Pengujian Keranjang**:
   - Sistem Alpine.js (`layouts/buyer.blade.php`) kini secara otomatis menyediakan 2 menu contoh dari *Stan 01 - Barokah Rasa* (Ayam Geprek Sambal Bawang Rp 13.000 + 2x Es Teh Manis Jumbo Rp 4.000 = Rp 21.000) saat keranjang dalam kondisi kosong.
   - Pengguna/tester dapat langsung menguji counter badge navbar, slide-over drawer keranjang, penambahan/pengurangan kuantitas, subtotal otomatis, hingga alur halaman `/checkout` tanpa perlu memasukkan menu secara manual terlebih dahulu.
2. **Harmonisasi Warna Kuning Tua & Kuning Transparan**:
   - Tombol utama `[Filter Kriteria Lengkap]` mempertahankan warna kuning tua solid (`bg-amber-600 hover:bg-amber-700 text-white`).
   - Pilihan cepat batas anggaran (pada dashboard dan rekomendasi) kini menggunakan sentuhan **kuning transparan halus** (`bg-amber-50/80 border-amber-300 text-amber-900`) agar tampil elegan, bernuansa lembut, dan tidak sama/menyaingi tombol utama.
   - Tombol `[Pantau Status Real-time]` dilengkapi icon panah ke kanan (`arrow-right`) dan hover state kuning transparan senada.
3. **Penyempurnaan Dropdown Filter Stan (`/katalog`)**:
   - Menggunakan wrapper relatif dengan `appearance-none`, padding kanan `pr-9`, serta custom icon chevron `pr-3` sehingga icon panah memiliki jarak aman (padding) dari tepi kanan container dan tidak lagi menempel ke border.
4. **Tata Letak Kriteria Rekomendasi Terpadu Berwarna Biru (`/rekomendasi`)**:
   - Bagian *Tentukan Kriteria Menu Pilihan Anda* menggunakan warna **biru resmi (brand blue)** untuk seluruh ikon profesional, chip pilihan, dan slider.
   - Tata letak dirombak menjadi grid 10-kolom yang simetris:
     - **Sisi Kiri (3 Kolom)**: Preferensi Rasa (6 chip selera).
     - **Tengah (4 Kolom)**: **Batas Anggaran Mahasiswa** diletakkan tepat di tengah dan berdimensi lebih panjang (33% lebih lebar) agar slider anggaran bergerak leluasa dan menjadi pusat perhatian interaksi.
     - **Sisi Kanan (3 Kolom)**: Maksimal Waktu Tunggu (3 chip durasi persiapan).
5. **Perbaikan Backdrop Modal Full Browser & Sentralisasi Ikon**:
   - Modal detail menu (`/katalog`) dan modal struk digital (`/riwayat`) kini dibungkus dengan Alpine.js `<template x-teleport="body">` serta kelas `z-[60]` dan `backdrop-blur-sm`. Latar belakang gelap dan efek blur kini menutup penuh 100% jendela browser tanpa terpotong oleh sticky navbar di bagian atas.
   - Modal konfirmasi *Ganti Stan Kantin?* telah diperbaiki agar ikon peringatan berada presisi di tengah (`mx-auto`) dengan tata letak modal yang simetris dan rapi.
6. **Filter Interaktif Batas Anggaran di Dashboard (`/dashboard`)**:
   - Pilihan cepat batas anggaran (*Semua Anggaran, ≤ Rp 5.000, ≤ Rp 10.000, ≤ Rp 15.000, ≤ Rp 20.000*) kini mem-filter langsung kartu pada section **Menu Terlaris Hari Ini** secara instan di halaman yang sama (*in-place reactive filtering* menggunakan Alpine.js), tanpa berpindah/redirect ke `/rekomendasi`.
   - Dilengkapi badge indikator budget aktif serta empty state jika tidak ada menu dalam rentang harga yang dipilih.
7. **Standarisasi dan Penyeragaman Tinggi Tombol Aksi**:
   - Seluruh tombol call-to-action utama diseragamkan tingginya setara dengan tombol tambah keranjang (`h-10 py-2.5 px-4 text-xs font-semibold rounded-xl`):
     - Tombol `[Terapkan Kriteria Rekomendasi]` pada `/rekomendasi`.
     - Tombol `[Lanjut Konfirmasi Pesanan]` pada `/keranjang`.
     - Tombol `[Buat Pesanan & Bayar QRIS]` pada `/checkout`.
     - Tombol `[Saya Sudah Membayar]` pada `/pesanan/bayar`.
8. **Penyempurnaan Halaman Pembayaran QRIS (`/pesanan/bayar`)**:
   - Gambar QRIS kini ditampilkan secara **penuh (full display)** di dalam container proporsional dari sumber gambar statis `public/images/qris.jpeg` (`asset('images/qris.jpeg')`).
   - Teks statis nama stan dan NMID di bawah QRIS telah dihilangkan agar gambar QRIS bersih dan tidak tumpang tindih dengan data asli di dalam gambar.
   - Ukuran font label dan nilai untuk *Nomor Pesanan* serta *Total Tagihan Pesanan* telah diperkecil secara seimbang dan menggunakan jenis font yang sama (Plus Jakarta Sans) tanpa ketimpangan tipografi.
9. **Rombak Ulang Halaman Profil Mahasiswa (`/profil`)**:
   - Desain lama gradien hitam pekat diganti dengan tema visual selaras: header identitas mahasiswa UNESA, pemisah tanda `|`, badge status SSO UNESA aktif dengan indikator titik hijau berkedip.
   - Menambahkan 3 kartu ringkasan aktivitas (Total Pesanan Selesai, Stan Langganan, Metode Pembayaran Utama).
   - Form biodata dan keamanan akun kini simetris dengan tombol aksi berdimensi seragam (`h-10 py-2.5 px-4 text-xs font-semibold rounded-xl`).
   - Area logout diperbarui dengan card ber-border rose yang aman dan tombol konfirmasi keluar yang rapi.
10. **Rombak Ulang & Presisi Halaman Status Pesanan Real-time (`/pesanan/status`)**:
    - Header diselaraskan dengan tema utama NesaCourt, dilengkapi dropdown simulasi status interaktif di sudut kanan atas dengan custom chevron icon dan padding kanan lega (`pr-8`) sehingga anak panah tidak lagi menempel pada teks.
    - Card status terkini dirombak total menjadi banner elegan berikon tematik dinamis per tahap, judul status tegas, pemisah tanda `|`, badge titik hijau "Dalam Proses" / "Siap Diambil", serta counter tahapan yang rapi.
    - Garis konektor stepper horizontal diposisikan presisi 100% membelah tepat di tengah-tengah lingkaran indikator (`top-4 -translate-y-1/2`) dari pusat lingkaran ke pusat lingkaran, tanpa pergeseran vertikal maupun horizontal.
    - Tampilan stepper di mobile dan tablet kini berbentuk satu garis lurus horizontal yang rapi dan dapat digeser/di-scroll ke kanan-kiri (`min-w-[620px] overflow-x-auto thin-scrollbar`) dengan scrollbar kapsul ultra-ramping (4px), tanpa panah tombol default browser yang tebal, serta dilengkapi petunjuk geser tipis di layar mobile.
    - Dilengkapi banner notifikasi siap ambil jika pesanan mencapai tahap ke-5 (*Siap Diambil*), rincian menu pesanan, dan tombol aksi struk digital serta pemesanan menu baru berdimensi seragam.


