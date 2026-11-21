# Dataset AI NesaCourt — Foodcourt Baseball UNESA 1 Ketintang

Folder ini berisi dataset siap pakai untuk pengembangan, pelatihan, dan evaluasi dua fitur AI pada proyek **NesaCourt**:
1. **Smart Menu Recommendation** (untuk Pembeli)
2. **AI Demand Forecasting** (untuk Penjual — harian & per jam)

---

## 1. Sumber Kalibrasi & Metodologi Data

Karena penjual di lokasi masih mencatat secara tradisional (belum menggunakan POS digital atau pencatatan stok harian), dataset ini dibangun menggunakan metode **Data Master Riil + Data Transaksi Sintetis Terkalibrasi** (seed deterministik `20261121`) berdasarkan dua sumber primer:

1. **Wawancara Langsung 5 Tenant Foodcourt Baseball UNESA 1 Ketintang**:
   - **5 Tenant Asli**:
     1. Stan 01 - Gado-Gado Bu Ismah (5 makanan + 5 minuman = 10 menu)
     2. Stan 02 - Dapur Budhe' Gun (5 makanan + 5 minuman = 10 menu)
     3. Stan 03 - Bakul Sego Bu Ani (4 makanan + 5 minuman = 9 menu)
     4. Stan 04 - Nasi Campur Bestie (6 makanan + 5 minuman = 11 menu)
     5. Stan 05 - Nasi Goreng Markisa (5 makanan + 5 minuman = 10 menu)
   - **Stok harian/tenant**: 95–100 porsi makanan (rentang wawancara 80–100) & 115 cup minuman (rentang wawancara 100–120).
   - **Volume terjual/tenant/hari**:
     - Hari normal: rata-rata **87,4 porsi makanan** & **99,5 cup minuman** (target wawancara: ~90 & ~100).
     - Hari ramai (Selasa–Kamis / event kampus): **93–99 porsi makanan** & **108–115 cup minuman** (target wawancara: hingga 100 & 120).
     - Hari sepi (Sabtu / hujan): **50–60 porsi makanan** & **60–74 cup minuman** (target wawancara: 50–60 makanan).
   - **Waktu saji**: 4–5 menit (makanan), 2–3 menit (minuman).
   - **Rentang harga**: Rp10.000–Rp15.000 (makanan), Rp3.000–Rp6.000 (minuman).
2. **Referensi Jurnal Penelitian Lokasi Asli**:
   > Ramadhan, M. R., & Ruhana, A. (2026). *Preferensi Mahasiswa UNESA Terhadap Makanan dan Minuman yang Dijual di Foodcourt Baseball UNESA Surabaya*. Jurnal Gizi dan Kesehatan Nusantara, Vol. 06 No. 01, Hal. 7–15.
   - Bobot popularitas per menu dikalibrasi dari **Tabel 3 (Makanan)** dan **Tabel 4 (Minuman)** khusus responden Foodcourt Ketintang.
   - Komposisi fakultas pembeli dikalibrasi dari **Tabel 1** (Teknik, MIPA, FISIPOL, FEB, Vokasi).
   - Nilai gizi (`energy_kcal`, `protein_g`, `fat_g`, `carbs_g`) pada `menus_master.csv` diambil dari **Tabel 6 & Tabel 7**.

---

## 2. Daftar File Dataset (Rentang 90 Hari Kalender / 77 Hari Operasional)

| File | Jumlah Baris Data | Ukuran | Kegunaan Utama AI |
| :--- | ---: | ---: | :--- |
| `menus_master.csv` | 50 | ~5,5 KB | Fitur *Smart Menu Recommendation* (harga, waktu saji, tag rasa, stok harian, bobot jurnal, nilai gizi). |
| `sales_daily_per_menu.csv` | 3.850 | ~465 KB | Training & evaluasi *AI Demand Forecasting* harian (`period_type = daily`). Lengkap dengan fitur hari, cuaca, tanggal tua, event kampus, dan status *stockout*. |
| `sales_hourly_per_menu.csv` | 24.433 | ~2,3 MB | Training & evaluasi *AI Demand Forecasting* per jam (`period_type = hourly`, 08.00–16.00) dan pemodelan antrean jam sibuk (puncak 11.00–13.00). |
| `transactions_raw.csv` | 66.647 | ~11,8 MB | Data transaksi baris-per-item mentah 90 hari (termasuk ~3–4% transaksi `dibatalkan` untuk menguji aturan filter AI). |

*(Catatan: 21 hari kalender terakhir dari dataset ini juga tersinkronisasi langsung ke database MySQL `nesacourt` melalui `database/seeders/DatabaseSeeder.php` & `OrderSeeder.php`.)*

---

## 3. Kamus Kolom Utama (`sales_daily_per_menu.csv`)

- `date`: Tanggal transaksi (`YYYY-MM-DD`, Senin–Sabtu; Minggu libur).
- `day_of_week` / `day_name`: Hari ke-1 (Senin) s/d ke-6 (Sabtu).
- `is_weekend`: `1` untuk Sabtu, `0` untuk hari kerja.
- `is_end_of_month`: `1` jika tanggal >= 24 (efek tanggal tua mahasiswa).
- `weather`: `cerah` atau `hujan` (memengaruhi total kunjungan dan preferensi menu berkuah/kopi vs es).
- `has_campus_event`: `1` pada simulasi hari acara kampus (untuk menguji skenario lonjakan tidak biasa).
- `demand_regime`: Kategori kondisi hari (`ramai_event`, `ramai`, `normal`, `sedang_jumat`, `sepi`).
- `completed_qty_sold`: **Target variabel (`y`)** — total porsi terjual berstatus `selesai`.
- `cancelled_qty`: Jumlah porsi dari pesanan `dibatalkan` (**tidak boleh** dihitung sebagai penjualan selesai).
- `stock_utilization_pct` & `stockout_flag`: Persentase serapan stok harian & indikator habis stok (`1` jika terjual = kuota stok).

---

## 4. Cara Re-generate Ulang

Dataset CSV dan database MySQL dapat dibangun ulang secara identik kapan saja melalui Laragon PHP 8.3:
```bash
php artisan migrate:fresh --seed
```
