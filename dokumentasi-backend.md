# Dokumentasi Progress Backend — NesaCourt

Dokumentasi ini disusun oleh tim **Backend (Wisnu — Laravel / Database)** sebagai acuan perkembangan integrasi logika server, model Eloquent, skema basis data, dan autentikasi sistem NesaCourt bagi tim **Frontend (Adit)** dan **Project Manager (Yoga)**.

---

## 1. Ringkasan Status & Capaian (Tahap 1 Selesai)

Saat ini telah diselesaikan implementasi **Tahap 1: Fondasi Data, Skema Basis Data, dan Autentikasi / RBAC**:
- **Versi Platform**: PHP ^8.3 (sesuai Blueprint terbaru), Laravel ^12.0, basis data MySQL `nesacourt`.
- **Skema Basis Data**: 11 tabel domain termigrasi mandiri (1 file per tabel, urutan sekuensial bebas konflik dependensi foreign key).
- **Model Eloquent**: 11 model Eloquent lengkap dengan relasi timbal balik (*inverse relationships*), *mass assignment protection* (`$fillable`), dan *type casting*.
- **Data Awal (Seeding)**: Tersedia seeder bawaan untuk 6 akun pengguna multi-role, 5 kategori menu, 4 stan kantin (profil & QRIS statis), dan 10 menu makanan/minuman yang sinkron dengan tampilan mockup frontend.
- **Autentikasi Terpadu**: Login multi-identifier (bisa menggunakan **Email Kampus** maupun **NIM Mahasiswa**), registrasi khusus mahasiswa, logout aman, serta proteksi grup rute menggunakan middleware peran (*Role-Based Access Control / RBAC*).

---

## 2. Pemetaan Model Eloquent & Relasi Database

| Model | Tabel Database | Relasi Utama | Keterangan |
| :--- | :--- | :--- | :--- |
| `User` | `users` | `hasOne(SellerProfile)`, `hasMany(Order)`, `hasMany(OrderStatusLog)`, `hasMany(PaymentConfirmation)`, `hasMany(RecommendationLog)` | Entitas pengguna multi-role (`admin`, `seller`, `buyer`). Kolom `phone` difungsikan menyimpan nomor telepon atau NIM mahasiswa. |
| `Category` | `categories` | `hasMany(Menu)` | Klasifikasi menu hidangan (`makanan`, `camilan`, `minuman`, `kopi`, `paket`). |
| `SellerProfile` | `seller_profiles` | `belongsTo(User)`, `hasOne(SellerPaymentQris)`, `hasMany(Menu)`, `hasMany(Order)` | Profil stan kantin di Food Court UNESA Ketintang (nama stan, slug, deskripsi, lokasi counter, jam buka/tutup). |
| `SellerPaymentQris` | `seller_payment_qris` | `belongsTo(SellerProfile)` | Registrasi data QRIS statis tiap stan (`image_path`, `merchant_name`, status aktif). |
| `Menu` | `menus` | `belongsTo(SellerProfile)`, `belongsTo(Category)`, `hasMany(OrderItem)` | Katalog makanan/minuman. Menyimpan `stock` porsi bilangan bulat, `taste_tags` (JSON), estimasi waktu saji, dan flag ketersediaan. |
| `RecommendationLog` | `recommendation_logs` | `belongsTo(User)` | Riwayat pencarian rekomendasi menu cerdas berbasis AI (`session_id`, `budget`, `taste_preference`, `max_wait_minutes`, `recommended_menu_ids` JSON). |
| `Order` | `orders` | `belongsTo(User, 'buyer_id')`, `belongsTo(SellerProfile)`, `hasMany(OrderItem)`, `hasMany(OrderStatusLog)`, `hasOne(PaymentConfirmation)` | Transaksi pesanan. Wajib terhubung ke akun pembeli (`buyer_id`), dilengkapi `access_token` pengaman status, serta pemisahan `order_status` dan `payment_status`. |
| `OrderItem` | `order_items` | `belongsTo(Order)`, `belongsTo(Menu)` | Rincian porsi pesanan. Menyimpan snapshot `menu_name_snapshot` dan `menu_price_snapshot` saat order dibuat untuk menjaga integritas riwayat harga. |
| `OrderStatusLog` | `order_status_logs` | `belongsTo(Order)`, `belongsTo(User, 'actor_user_id')` | Audit jejak riwayat status (`actor_type`: `system`, `seller`, `admin`, `buyer`). |
| `PaymentConfirmation` | `payment_confirmations` | `belongsTo(Order)`, `belongsTo(SellerProfile)`, `belongsTo(User, 'confirmed_by')` | Konfirmasi pembayaran QRIS yang diverifikasi oleh kasir/penjual atau admin. |
| `PredictionLog` | `prediction_logs` | `belongsTo(SellerProfile)`, `belongsTo(Menu)` | Log histori prediksi kebutuhan stok menu harian stan untuk modul forecasting AI. |

---

## 3. Rute & Autentikasi (RBAC)

### A. Rute Autentikasi

| Method | Endpoint | Controller Action | Keterangan |
| :--- | :--- | :--- | :--- |
| `GET` | `/login` | `AuthController@showLogin` | Menampilkan form login; otomatis redirect jika user sudah terautentikasi. |
| `POST` | `/login` | `AuthController@login` | Memvalidasi kredensial (Email/NIM + Password), regenerasi sesi, dan redirect berdasarkan role. |
| `GET` | `/register` | `AuthController@showRegister` | Menampilkan form pendaftaran mahasiswa baru. |
| `POST` | `/register` | `AuthController@register` | Registrasi user baru otomatis ber-role `buyer`, menyimpan NIM ke kolom `phone`. |
| `GET/POST` | `/logout` | `AuthController@logout` | Membatalkan sesi pengguna, meregenerasi token CSRF, dan kembali ke halaman login. |

### B. Aturan Pengalihan Berdasarkan Peran (*Role Redirection*)
Setelah berhasil masuk, controller secara otomatis mengarahkan pengguna ke portal masing-masing:
- Role **`buyer`**: Redirect ke `/dashboard` (Portal Mahasiswa).
- Role **`seller`**: Redirect ke `/penjual/dashboard` (Portal Dapur Stan).
- Role **`admin`**: Redirect ke `/admin/dashboard` (Portal Pengelola Food Court).

### C. Proteksi Rute (Middleware `CheckRole`)
Rute pada `routes/web.php` telah dikelompokkan dan dilindungi dengan middleware:
1. **Area Publik**: `/`, `/katalog`, `/rekomendasi`, `/keranjang`, `/login`, `/register`, `/forgot-password`.
2. **Grup Pembeli (`middleware: ['auth', 'role:buyer,admin']`)**:
   - `/dashboard`, `/checkout`, `/pesanan/bayar`, `/pesanan/status`, `/riwayat`, `/profil`.
3. **Grup Penjual (`middleware: ['auth', 'role:seller,admin']`)**:
   - Prefix `/penjual/*`: dashboard, menu, stok, qris, pesanan, laporan, prediksi, profil.
4. **Grup Admin (`middleware: ['auth', 'role:admin']`)**:
   - Prefix `/admin/*`: dashboard, stan, menu, pesanan, laporan, log.

---

## 4. Akun Uji Coba Demo (*Seed Data*)

Seluruh akun demo di bawah ini telah dibuat melalui `php artisan db:seed` dengan kata sandi bawaan yang seragam:

> **Kata Sandi Default Semua Akun**: `password`

| Role | Nama Akun | Login Identifier (Email / NIM) | Akses Portal Utama |
| :--- | :--- | :--- | :--- |
| **Admin** | Pengelola Food Court UNESA | `admin@nesacourt.ac.id` | `/admin/dashboard` |
| **Buyer** | Aditya Pratama | `aditya.23048@mhs.unesa.ac.id` atau NIM `23055310001` | `/dashboard` |
| **Seller 1** | Pemilik Barokah Rasa (Stan 01) | `barokah@nesacourt.ac.id` | `/penjual/dashboard` |
| **Seller 2** | Bu Siti (Stan 02 - Soto) | `soto@nesacourt.ac.id` | `/penjual/dashboard` |
| **Seller 3** | Mas Bro (Stan 03 - Geprek & Nasgor) | `geprek@nesacourt.ac.id` | `/penjual/dashboard` |
| **Seller 4** | Pengelola Jus Vokasi (Stan 04) | `jus@nesacourt.ac.id` | `/penjual/dashboard` |

---

## 5. Integrasi Sisi Pembeli / Mahasiswa (Tahap 2 — SELESAI)

Seluruh fitur Sisi Pembeli (**F-05 s/d F-14**) telah terhubung penuh secara dinamis antara controller backend Laravel, model Eloquent basis data, dan tampilan Blade Alpine.js:

### A. Capaian Fitur & Pengujian
1. **F-05: Dashboard Pembeli (`/dashboard`)**:
   - Menampilkan pesanan aktif yang sedang diproses secara dinamis dari database (`Order::whereIn('order_status', ...)`).
   - Menampilkan menu populer kantin dengan status stok dan durasi masak riil.
   - Menyapa nama dan NIM mahasiswa yang sedang login secara dinamis dari akun sesi SSO.
2. **F-06 & F-07: Katalog Menu & Stan Dinamis (`/katalog`)**:
   - Daftar kategori diambil langsung dari tabel `categories` (termasuk filter tab horizontal).
   - Filter dropdown stan terisi otomatis dari tabel `seller_profiles`.
   - Data menu dimuat langsung dari tabel `menus` berelasi dengan `seller` dan `category`.
   - Modal detail cepat (*quick view*) terintegrasi dengan penambahan porsi dan pembatasan stok habis (*out of stock*).
3. **F-08: Rekomendasi Menu Cerdas AI (`/rekomendasi`)**:
   - Filter multivariabel cerdas (anggaran `price <= budget`, preferensi rasa `pedas/gurih/manis/segar/berkuah`, dan batas toleransi estimasi masak `prepTime`).
   - Beroperasi langsung pada dataset menu riil dari database.
4. **F-09: Keranjang Belanja (`/keranjang`)**:
   - Terhubung dengan *Cart Store* Alpine.js pada `layouts/buyer.blade.php`.
   - Menggunakan ID menu dan stan riil dari database basis data.
5. **F-10: Pemrosesan Pesanan Atomik (`POST /checkout`)**:
   - Validasi data pemesan, tipe layanan (*Dine-in* dengan nomor meja atau *Takeaway* bawa pulang).
   - Validasi ketat kepatuhan *Single-Seller* (satu pesanan hanya memuat menu dari satu stan).
   - Transaksi database atomik (`DB::transaction` + `lockForUpdate`):
     - Memeriksa ketersediaan porsi dan memotong stok porsi menu secara atomik (`$menu->decrement('stock', $qty)`).
     - Menghasilkan `order_number` unik (format `NC-YYYYMMDD-XXXX`) dan hash `access_token` 32-byte.
     - Menyimpan entri `Order`, entri rincian `OrderItem` (lengkap dengan snapshot nama & harga menu), serta entri audit log perdana pada `OrderStatusLog`.
6. **F-11: Pembayaran QRIS Statis Stan (`/pesanan/bayar` & `POST /pesanan/{order}/konfirmasi-bayar`)**:
   - Memuat data tagihan riil dan barcode QRIS statis spesifik stan penjual terkait (`$order->seller->qris`).
   - Aksi konfirmasi "Saya Sudah Membayar" memperbarui status pembayaran pesanan menjadi `perlu_pemeriksaan` dan mencatat riwayat transisi ke tabel `order_status_logs`.
7. **F-12: Pelacakan Status Real-time (`/pesanan/status`)**:
   - Visualisasi alur 6 tahap pelacakan pesanan (*Dibuat ➔ Menunggu Bayar ➔ Dikonfirmasi ➔ Dimasak ➔ Siap Diambil ➔ Selesai*).
   - Nomor antrean dinamis (`A-XX`), estimasi durasi penyiapan, lokasi stan kantin, dan rincian menu yang dipesan.
8. **F-13: Riwayat Transaksi & Struk Digital (`/riwayat`)**:
   - Menampilkan seluruh riwayat pesanan mahasiswa dari tabel `orders` yang dikelompokkan berdasarkan tab status (*Semua*, *Sedang Berjalan*, *Selesai*).
   - Modal struk digital lengkap dengan nomor nota, tanggal transaksi WIB, stan penyedia, dan total pembayaran.
9. **F-14: Profil Akun Pembeli (`/profil`)**:
   - Statistik riil: total transaksi selesai (`$totalCompleted`) dan stan langganan utama (`$favStall`).
   - Form pembaruan data kontak WhatsApp (`POST /profil`).
   - Form pembaruan kata sandi akun dengan verifikasi hash sandi lama (`POST /profil/password`).

### B. Otomasi Pengujian Unit / Feature
- Telah dibuat berkas pengujian otomatis `tests/Feature/BuyerTest.php` yang memverifikasi seluruh siklus alur belanja pembeli: autentikasi pembeli, perambanan katalog, submit checkout atomik, pemotongan stok porsi di basis data, konfirmasi transfer QRIS, hingga pelacakan status dan riwayat.
- Hasil uji: **18 assertions passed (exit 0)**.

---

## 6. Rencana Kerja Selanjutnya

### A. Tahap 3: Integrasi Sisi Penjual / Stan (F-15 s/d F-24)
1. CRUD Menu masakan dan upload berkas foto menu ke `storage/app/public/menus`.
2. Pengaturan stok porsi harian langsung update ke kolom `menus.stock`.
3. Manajemen pesanan masuk dapur, transisi tahapan status (`dibuat` ➔ `diproses` ➔ `siap` ➔ `selesai`), dan pencatatan riwayat ke `order_status_logs`.
4. Konfirmasi pembayaran QRIS ke tabel `payment_confirmations`.
5. Rekap laporan omset penjualan dan modul prediksi kebutuhan porsi masakan.

### B. Tahap 4: Integrasi Sisi Admin / Pengelola Food Court (F-25 s/d F-31)
1. Monitoring status stan kantin aktif/nonaktif.
2. Rekapitulasi transaksi keseluruhan food court untuk evaluasi pengelola.
3. Audit log aktivitas perubahan data sistem.
