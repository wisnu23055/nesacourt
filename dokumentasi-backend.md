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

## 5. Rencana Kerja Selanjutnya (Mulai Besok)

### A. Tahap 2: Integrasi Sisi Pembeli / Mahasiswa (F-05 s/d F-14)
1. **Katalog & Detail Menu Dinamis (`F-05`, `F-06`, `F-07`)**:
   - Mengambil data kategori dan menu aktif langsung dari database (`Category::all()`, `Menu::with('seller')->where('is_active', true)`).
   - Sinkronisasi ketersediaan porsi riil ke komponen frontend Alpine.js.
2. **Mesin Rekomendasi Menu AI (`F-08`)**:
   - Endpoint filter cerdas berbasis constraint anggaran (`price <= budget`), preferensi rasa, dan toleransi estimasi masak (`estimated_time_minutes <= max_wait`).
   - Logging riwayat rekomendasi ke tabel `recommendation_logs`.
3. **Order & Checkout Processing (`F-09`, `F-10`, `F-11`)**:
   - Controller penerima payload keranjang belanja dari frontend.
   - Validasi kepatuhan *Single-Seller* (satu order hanya untuk satu `seller_id`).
   - Pembuatan record `Order` (menghasilkan `order_number` acak/berurutan dan `access_token`), serta entri detail `OrderItem` dengan snapshot harga.
   - Pengurangan stok porsi menu secara atomik (`lockForUpdate` / transaksi database).
4. **Pelacakan Status & Riwayat Transaksi (`F-12`, `F-13`, `F-14`)**:
   - Endpoint status pesanan aktif berdasarkan `order_number` dan kepemilikan `buyer_id`.
   - Riwayat transaksi belanja mahasiswa dan struk digital nota.

### B. Tahap 3: Integrasi Sisi Penjual / Stan (F-15 s/d F-24)
1. CRUD Menu masakan dan upload berkas foto menu ke `storage/app/public/menus`.
2. Pengaturan stok porsi harian langsung update ke kolom `menus.stock`.
3. Manajemen pesanan masuk dapur, transisi tahapan status (`dibuat` ➔ `diproses` ➔ `siap` ➔ `selesai`), dan pencatatan riwayat ke `order_status_logs`.
4. Konfirmasi pembayaran QRIS ke tabel `payment_confirmations`.
5. Rekap laporan omset penjualan dan modul prediksi kebutuhan porsi masakan.

### C. Tahap 4: Integrasi Sisi Admin / Pengelola Food Court (F-25 s/d F-31)
1. Monitoring status stan kantin aktif/nonaktif.
2. Rekapitulasi transaksi keseluruhan food court untuk evaluasi pengelola.
3. Audit log aktivitas perubahan data sistem.
