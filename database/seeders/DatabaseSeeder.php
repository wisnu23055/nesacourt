<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Menu;
use App\Models\SellerPaymentQris;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed data riil NesaCourt berdasarkan wawancara 5 tenant
     * Foodcourt Baseball UNESA 1 Ketintang dan referensi jurnal
     * Ramadhan & Ruhana (2026), Jurnal Gizi dan Kesehatan Nusantara Vol. 06 No. 01.
     */
    public function run(): void
    {
        $defaultPasswordHash = Hash::make('password');

        // 1. Admin Pengelola Foodcourt
        User::firstOrCreate(
            ['email' => 'admin@nesacourt.ac.id'],
            [
                'name' => 'Pengelola Food Court UNESA',
                'password' => $defaultPasswordHash,
                'role' => 'admin',
                'phone' => '081122334455',
            ]
        );

        // 2. Pembeli (20 Mahasiswa UNESA Kampus 1 Ketintang — proporsi fakultas sesuai Tabel 1 Jurnal)
        // 6 akun demo utama tetap dipertahankan agar kompatibel dengan dokumentasi tim.
        $buyerSeedList = [
            // Fakultas Teknik (~40% — jarak terdekat ke Foodcourt Baseball Ketintang, 21% total responden)
            ['email' => 'aditya.23048@mhs.unesa.ac.id', 'name' => 'Aditya Pratama', 'phone' => '23055310001', 'password' => Hash::make('aditya.23048@mhs.unesa.ac.id')],
            ['email' => 'rizky.kurniawan@mhs.unesa.ac.id', 'name' => 'Rizky Kurniawan', 'phone' => '23055310018', 'password' => $defaultPasswordHash],
            ['email' => 'budi.santoso@mhs.unesa.ac.id', 'name' => 'Budi Santoso', 'phone' => '23055310052', 'password' => $defaultPasswordHash],
            ['email' => 'fajar.nugroho@mhs.unesa.ac.id', 'name' => 'Fajar Nugroho', 'phone' => '24050974011', 'password' => $defaultPasswordHash],
            ['email' => 'dimas.saputra@mhs.unesa.ac.id', 'name' => 'Dimas Saputra', 'phone' => '24050974028', 'password' => $defaultPasswordHash],
            ['email' => 'putri.ramadhani@mhs.unesa.ac.id', 'name' => 'Putri Ramadhani', 'phone' => '24050974039', 'password' => $defaultPasswordHash],
            ['email' => 'bagas.pratama@mhs.unesa.ac.id', 'name' => 'Bagas Pratama', 'phone' => '22050974015', 'password' => $defaultPasswordHash],
            ['email' => 'intan.permata@mhs.unesa.ac.id', 'name' => 'Intan Permata Sari', 'phone' => '23050974064', 'password' => $defaultPasswordHash],

            // FMIPA (~25%)
            ['email' => 'nadia.salsabila@mhs.unesa.ac.id', 'name' => 'Nadia Salsabila', 'phone' => '23055310034', 'password' => $defaultPasswordHash],
            ['email' => 'dewi.lestari@mhs.unesa.ac.id', 'name' => 'Dewi Lestari', 'phone' => '23055310045', 'password' => $defaultPasswordHash],
            ['email' => 'aulia.zahra@mhs.unesa.ac.id', 'name' => 'Aulia Zahra', 'phone' => '24030174019', 'password' => $defaultPasswordHash],
            ['email' => 'nabila.fitria@mhs.unesa.ac.id', 'name' => 'Nabila Fitria', 'phone' => '24030174052', 'password' => $defaultPasswordHash],
            ['email' => 'rafi.hidayat@mhs.unesa.ac.id', 'name' => 'Rafi Hidayat', 'phone' => '22030174031', 'password' => $defaultPasswordHash],

            // FISIPOL (~15%)
            ['email' => 'siti.aisyah@mhs.unesa.ac.id', 'name' => 'Siti Aisyah', 'phone' => '23055310060', 'password' => $defaultPasswordHash],
            ['email' => 'citra.anggraini@mhs.unesa.ac.id', 'name' => 'Citra Anggraini', 'phone' => '24040674012', 'password' => $defaultPasswordHash],
            ['email' => 'melati.kusuma@mhs.unesa.ac.id', 'name' => 'Melati Kusuma', 'phone' => '22040674044', 'password' => $defaultPasswordHash],

            // FEB (~10%)
            ['email' => 'tiara.maharani@mhs.unesa.ac.id', 'name' => 'Tiara Maharani', 'phone' => '24080574023', 'password' => $defaultPasswordHash],
            ['email' => 'ilham.maulana@mhs.unesa.ac.id', 'name' => 'Ilham Maulana', 'phone' => '23080574041', 'password' => $defaultPasswordHash],

            // Vokasi (~10%)
            ['email' => 'galih.wicaksono@mhs.unesa.ac.id', 'name' => 'Galih Wicaksono', 'phone' => '24091397009', 'password' => $defaultPasswordHash],
            ['email' => 'rara.novita@mhs.unesa.ac.id', 'name' => 'Rara Novita', 'phone' => '23091397027', 'password' => $defaultPasswordHash],
        ];

        foreach ($buyerSeedList as $b) {
            User::firstOrCreate(
                ['email' => $b['email']],
                [
                    'name' => $b['name'],
                    'password' => $b['password'],
                    'role' => 'buyer',
                    'phone' => $b['phone'],
                ]
            );
        }

        // 3. Akun Penjual & Profil 5 Tenant Asli (Hasil Wawancara Foodcourt Baseball UNESA 1 Ketintang)
        $sellerConfigs = [
            'ismah' => [
                'email' => 'ismah@nesacourt.ac.id',
                'name' => 'Bu Ismah',
                'phone' => '081234560101',
                'stall_name' => 'Stan 01 - Gado-Gado Bu Ismah',
                'slug' => 'gado-gado-bu-ismah',
                'description' => 'Menyajikan gado-gado bumbu kacang ulek segar, soto daging hangat, ayam geprek, ayam kremes, nasi campur, dan aneka minuman dingin.',
                'logo_path' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=120&q=80',
                'location' => 'Counter No. 01 | Foodcourt Baseball UNESA 1 Ketintang',
                'open_time' => '07:30:00',
                'close_time' => '16:30:00',
            ],
            'budhegun' => [
                'email' => 'budhegun@nesacourt.ac.id',
                'name' => "Budhe' Gun",
                'phone' => '081234560102',
                'stall_name' => "Stan 02 - Dapur Budhe' Gun",
                'slug' => 'dapur-budhe-gun',
                'description' => 'Masakan rumahan favorit mahasiswa UNESA: nasi pecel, nasi campur, nasi capjae, geprek spesial, crazy rice (nasi gila), dan aneka minuman.',
                'logo_path' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=120&q=80',
                'location' => 'Counter No. 02 | Foodcourt Baseball UNESA 1 Ketintang',
                'open_time' => '07:30:00',
                'close_time' => '16:30:00',
            ],
            'buani' => [
                'email' => 'buani@nesacourt.ac.id',
                'name' => 'Bu Ani',
                'phone' => '081234560103',
                'stall_name' => 'Stan 03 - Bakul Sego Bu Ani',
                'slug' => 'bakul-sego-bu-ani',
                'description' => 'Spesialis olahan ayam kremes, ayam geprek, ayam bakar kecap manis, nasi campur komplit, serta aneka minuman segar harga mahasiswa.',
                'logo_path' => 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?auto=format&fit=crop&w=120&q=80',
                'location' => 'Counter No. 03 | Foodcourt Baseball UNESA 1 Ketintang',
                'open_time' => '08:00:00',
                'close_time' => '16:30:00',
            ],
            'bestie' => [
                'email' => 'bestie@nesacourt.ac.id',
                'name' => 'Pengelola Nasi Campur Bestie',
                'phone' => '081234560104',
                'stall_name' => 'Stan 04 - Nasi Campur Bestie',
                'slug' => 'nasi-campur-bestie',
                'description' => 'Pilihan lengkap nasi campur, nasi penyetan, rawon daging sapi, nasi ayam katsu, ayam kremes, ayam geprek, dan aneka minuman.',
                'logo_path' => 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=120&q=80',
                'location' => 'Counter No. 04 | Foodcourt Baseball UNESA 1 Ketintang',
                'open_time' => '08:00:00',
                'close_time' => '17:00:00',
            ],
            'markisa' => [
                'email' => 'markisa@nesacourt.ac.id',
                'name' => 'Pengelola Nasi Goreng Markisa',
                'phone' => '081234560105',
                'stall_name' => 'Stan 05 - Nasi Goreng Markisa',
                'slug' => 'nasi-goreng-markisa',
                'description' => 'Andalan nasi goreng aroma wok, nasi campur, nasi ayam bakar/madu, nasi sayur hemat, nasi geprek, dan aneka minuman dingin/panas.',
                'logo_path' => 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?auto=format&fit=crop&w=120&q=80',
                'location' => 'Counter No. 05 | Foodcourt Baseball UNESA 1 Ketintang',
                'open_time' => '08:00:00',
                'close_time' => '17:00:00',
            ],
        ];

        $stalls = [];
        foreach ($sellerConfigs as $key => $cfg) {
            $sellerUser = User::firstOrCreate(
                ['email' => $cfg['email']],
                [
                    'name' => $cfg['name'],
                    'password' => $defaultPasswordHash,
                    'role' => 'seller',
                    'phone' => $cfg['phone'],
                ]
            );

            $stall = SellerProfile::firstOrCreate(
                ['user_id' => $sellerUser->id],
                [
                    'stall_name' => $cfg['stall_name'],
                    'slug' => $cfg['slug'],
                    'description' => $cfg['description'],
                    'logo_path' => $cfg['logo_path'],
                    'location' => $cfg['location'],
                    'is_active' => true,
                    'open_time' => $cfg['open_time'],
                    'close_time' => $cfg['close_time'],
                ]
            );

            SellerPaymentQris::firstOrCreate(
                ['seller_id' => $stall->id],
                [
                    'image_path' => 'images/qris.jpeg',
                    'merchant_name' => $stall->stall_name,
                    'is_active' => true,
                ]
            );

            $stalls[$key] = $stall;
        }

        // 4. Categories (tetap menjaga 5 slug bawaan agar filter frontend kompatibel 100%)
        $categories = [
            'makanan' => Category::firstOrCreate(['slug' => 'makanan'], ['name' => 'Makanan Berat', 'sort_order' => 1, 'is_active' => true]),
            'camilan' => Category::firstOrCreate(['slug' => 'camilan'], ['name' => 'Camilan & Gorengan', 'sort_order' => 2, 'is_active' => true]),
            'minuman' => Category::firstOrCreate(['slug' => 'minuman'], ['name' => 'Minuman Dingin', 'sort_order' => 3, 'is_active' => true]),
            'kopi' => Category::firstOrCreate(['slug' => 'kopi'], ['name' => 'Kopi & Teh', 'sort_order' => 4, 'is_active' => true]),
            'paket' => Category::firstOrCreate(['slug' => 'paket'], ['name' => 'Paket Hemat Mahasiswa', 'sort_order' => 5, 'is_active' => true]),
        ];

        // 5. Menus (25 Menu Makanan + 25 Menu Minuman = 50 Menu Riil)
        // Harga makanan: Rp10.000 - Rp15.000 | Waktu saji: 4 - 5 menit | Stok harian total makanan: 95 - 100/tenant
        // Harga minuman: Rp3.000 - Rp6.000  | Waktu saji: 2 - 3 menit | Stok harian total minuman: 115/tenant
        // Semua taste_tags menyertakan exact match (Pedas/Manis/Berkuah/Segar/Gurih) untuk filter Smart Menu Recommendation.
        $foodMenus = [
            // === TENAN 1: Gado-Gado Bu Ismah (Total Stok Makanan = 95 porsi) ===
            [
                'stall' => 'ismah',
                'category' => 'makanan',
                'name' => 'Ayam Geprek',
                'price' => 13000,
                'description' => 'Ayam goreng tepung renyah diulek bersama sambal bawang pedas gurih dan nasi putih hangat. Menu favorit #1 mahasiswa UNESA (±484,6 kkal; 25,6g protein).',
                'image_path' => 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?auto=format&fit=crop&w=600&q=80',
                'estimated_time_minutes' => 5,
                'taste_tags' => ['Pedas', 'Gurih', 'Ayam'],
                'stock' => 30,
            ],
            [
                'stall' => 'ismah',
                'category' => 'makanan',
                'name' => 'Soto Daging',
                'price' => 15000,
                'description' => 'Kuah soto kaldu gurih hangat dengan potongan daging sapi empuk, nasi putih, tauge, seledri, dan sambal segar (±452,4 kkal; 19,1g protein).',
                'image_path' => 'https://images.unsplash.com/photo-1541832676-9b763b0239ab?auto=format&fit=crop&w=600&q=80',
                'estimated_time_minutes' => 4,
                'taste_tags' => ['Berkuah', 'Gurih', 'Daging Sapi'],
                'stock' => 20,
            ],
            [
                'stall' => 'ismah',
                'category' => 'makanan',
                'name' => 'Nasi Campur',
                'price' => 12000,
                'description' => 'Nasi putih dengan aneka lauk rumahan pilihan (lauk ayam/telur, mie, kering tempe, tumis sayur) yang mengenyangkan (±499,6 kkal; 24,3g protein).',
                'image_path' => 'https://images.unsplash.com/photo-1512058564366-18510be2db19?auto=format&fit=crop&w=600&q=80',
                'estimated_time_minutes' => 4,
                'taste_tags' => ['Gurih', 'Komplit', 'Nasi'],
                'stock' => 18,
            ],
            [
                'stall' => 'ismah',
                'category' => 'makanan',
                'name' => 'Ayam Kremes',
                'price' => 13000,
                'description' => 'Ayam goreng bumbu ungkep disajikan dengan taburan kremesan renyah gurih, lalapan segar, sambal, dan nasi putih hangat.',
                'image_path' => 'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?auto=format&fit=crop&w=600&q=80',
                'estimated_time_minutes' => 5,
                'taste_tags' => ['Gurih', 'Renyah', 'Ayam'],
                'stock' => 17,
            ],
            [
                'stall' => 'ismah',
                'category' => 'makanan',
                'name' => 'Gado-Gado',
                'price' => 11000,
                'description' => 'Sayuran rebus segar, lontong, tahu, tempe, dan telur disiram bumbu kacang ulek gurih manis dengan taburan kerupuk renyah.',
                'image_path' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=600&q=80',
                'estimated_time_minutes' => 4,
                'taste_tags' => ['Gurih', 'Manis', 'Sayur'],
                'stock' => 10,
            ],

            // === TENAN 2: Dapur Budhe' Gun (Total Stok Makanan = 95 porsi) ===
            [
                'stall' => 'budhegun',
                'category' => 'makanan',
                'name' => 'Geprek Spesial',
                'price' => 14000,
                'description' => 'Ayam geprek porsi spesial dengan sambal ulek dadakan pilihan level pedas dan nasi putih pulen (±484,6 kkal; 25,6g protein).',
                'image_path' => 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?auto=format&fit=crop&w=600&q=80',
                'estimated_time_minutes' => 5,
                'taste_tags' => ['Pedas', 'Gurih', 'Ayam'],
                'stock' => 32,
            ],
            [
                'stall' => 'budhegun',
                'category' => 'makanan',
                'name' => 'Nasi Campur',
                'price' => 12000,
                'description' => 'Nasi campur rumahan khas Dapur Budhe Gun lengkap dengan lauk harian, tumisan, dan sambal (±499,6 kkal; 24,3g protein).',
                'image_path' => 'https://images.unsplash.com/photo-1512058564366-18510be2db19?auto=format&fit=crop&w=600&q=80',
                'estimated_time_minutes' => 4,
                'taste_tags' => ['Gurih', 'Komplit', 'Nasi'],
                'stock' => 20,
            ],
            [
                'stall' => 'budhegun',
                'category' => 'makanan',
                'name' => 'Crazy Rice (Nasi Gila)',
                'price' => 14000,
                'description' => 'Nasi putih hangat dengan tumisan sosis, bakso, telur orak-arik, dan sayuran berbumbu pedas manis gurih aroma wok.',
                'image_path' => 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?auto=format&fit=crop&w=600&q=80',
                'estimated_time_minutes' => 5,
                'taste_tags' => ['Pedas', 'Gurih', 'Wok'],
                'stock' => 17,
            ],
            [
                'stall' => 'budhegun',
                'category' => 'makanan',
                'name' => 'Nasi Pecel',
                'price' => 10000,
                'description' => 'Nasi putih dengan sayuran rebus segar, siraman sambal kacang pecel khas Jawa Timur, tempe goreng, dan rempeyek renyah. Pilihan paling hemat!',
                'image_path' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=600&q=80',
                'estimated_time_minutes' => 4,
                'taste_tags' => ['Gurih', 'Pedas', 'Sayur'],
                'stock' => 14,
            ],
            [
                'stall' => 'budhegun',
                'category' => 'makanan',
                'name' => 'Nasi Capjae',
                'price' => 12000,
                'description' => 'Nasi putih hangat dengan capjae tumis aneka sayur segar, potongan ayam, dan kuah kental gurih kaya serat.',
                'image_path' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=600&q=80',
                'estimated_time_minutes' => 5,
                'taste_tags' => ['Gurih', 'Berkuah', 'Sayur'],
                'stock' => 12,
            ],

            // === TENAN 3: Bakul Sego Bu Ani (Total Stok Makanan = 95 porsi) ===
            [
                'stall' => 'buani',
                'category' => 'makanan',
                'name' => 'Ayam Geprek',
                'price' => 13000,
                'description' => 'Ayam crispy geprek sambal bawang pedas nikmat khas Bakul Sego Bu Ani plus nasi putih pulen (±484,6 kkal; 25,6g protein).',
                'image_path' => 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?auto=format&fit=crop&w=600&q=80',
                'estimated_time_minutes' => 5,
                'taste_tags' => ['Pedas', 'Gurih', 'Ayam'],
                'stock' => 34,
            ],
            [
                'stall' => 'buani',
                'category' => 'makanan',
                'name' => 'Ayam Kremes',
                'price' => 13000,
                'description' => 'Ayam goreng gurih dengan kremesan krispi melimpah, sambal terasi, lalapan timun, dan nasi putih hangat.',
                'image_path' => 'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?auto=format&fit=crop&w=600&q=80',
                'estimated_time_minutes' => 5,
                'taste_tags' => ['Gurih', 'Renyah', 'Ayam'],
                'stock' => 22,
            ],
            [
                'stall' => 'buani',
                'category' => 'makanan',
                'name' => 'Nasi Campur',
                'price' => 12000,
                'description' => 'Nasi campur porsi kenyang khas Bu Ani dengan pilihan lauk pauk rumahan bergizi (±499,6 kkal; 24,3g protein).',
                'image_path' => 'https://images.unsplash.com/photo-1512058564366-18510be2db19?auto=format&fit=crop&w=600&q=80',
                'estimated_time_minutes' => 4,
                'taste_tags' => ['Gurih', 'Komplit', 'Nasi'],
                'stock' => 20,
            ],
            [
                'stall' => 'buani',
                'category' => 'makanan',
                'name' => 'Ayam Bakar',
                'price' => 15000,
                'description' => 'Ayam bakar bumbu kecap manis rempah meresap dengan aroma panggangan sedap, sambal, lalapan, dan nasi putih.',
                'image_path' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&w=600&q=80',
                'estimated_time_minutes' => 5,
                'taste_tags' => ['Manis', 'Gurih', 'Ayam'],
                'stock' => 19,
            ],

            // === TENAN 4: Nasi Campur Bestie (Total Stok Makanan = 100 porsi) ===
            [
                'stall' => 'bestie',
                'category' => 'makanan',
                'name' => 'Nasi Ayam Geprek',
                'price' => 13000,
                'description' => 'Nasi putih dengan ayam goreng tepung geprek sambal pedas segar khas Bestie (±484,6 kkal; 25,6g protein).',
                'image_path' => 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?auto=format&fit=crop&w=600&q=80',
                'estimated_time_minutes' => 5,
                'taste_tags' => ['Pedas', 'Gurih', 'Ayam'],
                'stock' => 26,
            ],
            [
                'stall' => 'bestie',
                'category' => 'makanan',
                'name' => 'Nasi Campur',
                'price' => 12000,
                'description' => 'Menu andalan Nasi Campur Bestie: nasi hangat dengan lauk lengkap, mie goreng, orek tempe, dan sambal (±499,6 kkal; 24,3g protein).',
                'image_path' => 'https://images.unsplash.com/photo-1512058564366-18510be2db19?auto=format&fit=crop&w=600&q=80',
                'estimated_time_minutes' => 4,
                'taste_tags' => ['Gurih', 'Komplit', 'Nasi'],
                'stock' => 18,
            ],
            [
                'stall' => 'bestie',
                'category' => 'makanan',
                'name' => 'Nasi Ayam Kremes',
                'price' => 13000,
                'description' => 'Nasi putih hangat dengan ayam goreng berbumbu dan taburan kremesan renyah gurih plus sambal.',
                'image_path' => 'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?auto=format&fit=crop&w=600&q=80',
                'estimated_time_minutes' => 5,
                'taste_tags' => ['Gurih', 'Renyah', 'Ayam'],
                'stock' => 16,
            ],
            [
                'stall' => 'bestie',
                'category' => 'makanan',
                'name' => 'Nasi Ayam Katsu',
                'price' => 14000,
                'description' => 'Fillet daging ayam balut tepung roti renyah ala Jepang disajikan bersama nasi putih, salad kol, dan saus gurih.',
                'image_path' => 'https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?auto=format&fit=crop&w=600&q=80',
                'estimated_time_minutes' => 5,
                'taste_tags' => ['Gurih', 'Renyah', 'Ayam'],
                'stock' => 15,
            ],
            [
                'stall' => 'bestie',
                'category' => 'makanan',
                'name' => 'Nasi Penyetan',
                'price' => 13000,
                'description' => 'Lauk ayam/tempe/tahu goreng hangat dipenyet langsung di atas cobek sambal terasi pedas nikmat khas Surabaya.',
                'image_path' => 'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?auto=format&fit=crop&w=600&q=80',
                'estimated_time_minutes' => 5,
                'taste_tags' => ['Pedas', 'Gurih', 'Ayam'],
                'stock' => 14,
            ],
            [
                'stall' => 'bestie',
                'category' => 'makanan',
                'name' => 'Rawon',
                'price' => 15000,
                'description' => 'Rawon daging sapi kuah kluwek hitam gurih khas Jawa Timur disajikan dengan nasi putih, tauge pendek segar, dan sambal.',
                'image_path' => 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=600&q=80',
                'estimated_time_minutes' => 4,
                'taste_tags' => ['Berkuah', 'Gurih', 'Daging Sapi'],
                'stock' => 11,
            ],

            // === TENAN 5: Nasi Goreng Markisa (Total Stok Makanan = 95 porsi) ===
            [
                'stall' => 'markisa',
                'category' => 'makanan',
                'name' => 'Nasi Geprek',
                'price' => 13000,
                'description' => 'Nasi putih hangat dengan ayam geprek krispi dan sambal bawang ulek pedas segar (±484,6 kkal; 25,6g protein).',
                'image_path' => 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?auto=format&fit=crop&w=600&q=80',
                'estimated_time_minutes' => 5,
                'taste_tags' => ['Pedas', 'Gurih', 'Ayam'],
                'stock' => 27,
            ],
            [
                'stall' => 'markisa',
                'category' => 'makanan',
                'name' => 'Nasi Goreng',
                'price' => 12000,
                'description' => 'Nasi goreng spesial aroma wok khas Markisa dengan telur, suwiran ayam, dan kerupuk renyah (±360,5 kkal; 15,4g protein).',
                'image_path' => 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?auto=format&fit=crop&w=600&q=80',
                'estimated_time_minutes' => 5,
                'taste_tags' => ['Gurih', 'Pedas', 'Wok'],
                'stock' => 26,
            ],
            [
                'stall' => 'markisa',
                'category' => 'makanan',
                'name' => 'Nasi Campur',
                'price' => 12000,
                'description' => 'Nasi campur ekonomis khas Markisa dengan pilihan lauk rumahan segar setiap hari (±499,6 kkal; 24,3g protein).',
                'image_path' => 'https://images.unsplash.com/photo-1512058564366-18510be2db19?auto=format&fit=crop&w=600&q=80',
                'estimated_time_minutes' => 4,
                'taste_tags' => ['Gurih', 'Komplit', 'Nasi'],
                'stock' => 16,
            ],
            [
                'stall' => 'markisa',
                'category' => 'makanan',
                'name' => 'Nasi Ayam (Bakar/Madu)',
                'price' => 15000,
                'description' => 'Nasi putih dengan ayam bakar olesan bumbu madu manis gurih yang meresap sempurna, disajikan bersama sambal dan lalapan.',
                'image_path' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&w=600&q=80',
                'estimated_time_minutes' => 5,
                'taste_tags' => ['Manis', 'Gurih', 'Ayam'],
                'stock' => 15,
            ],
            [
                'stall' => 'markisa',
                'category' => 'makanan',
                'name' => 'Nasi Sayur',
                'price' => 10000,
                'description' => 'Pilihan paling ramah kantong mahasiswa: nasi putih dengan sayur berkuah hangat (sop/lodeh/asem) dan lauk tahu tempe.',
                'image_path' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=600&q=80',
                'estimated_time_minutes' => 4,
                'taste_tags' => ['Gurih', 'Berkuah', 'Sayur'],
                'stock' => 11,
            ],
        ];

        // 5 Minuman standar tersedia di seluruh 5 tenant (Total stok minuman = 115 cup/botol per tenant)
        $standardDrinks = [
            [
                'category' => 'minuman',
                'name' => 'Air Mineral',
                'price' => 3000,
                'description' => 'Air mineral kemasan dingin/normal yang higienis dan menyegarkan. Minuman favorit #1 mahasiswa UNESA (0 kkal; bebas gula).',
                'image_path' => 'https://images.unsplash.com/photo-1548839140-29a749e1cf4d?auto=format&fit=crop&w=600&q=80',
                'estimated_time_minutes' => 2,
                'taste_tags' => ['Segar', 'Sehat'],
                'stock' => 35,
            ],
            [
                'category' => 'kopi',
                'name' => 'Es Teh Manis',
                'price' => 4000,
                'description' => 'Es teh melati seduh segar dengan gula asli dan es batu melimpah. Minuman terlaris di Foodcourt UNESA Ketintang (±140,4 kkal; 36g KH).',
                'image_path' => 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?auto=format&fit=crop&w=600&q=80',
                'estimated_time_minutes' => 2,
                'taste_tags' => ['Manis', 'Segar'],
                'stock' => 35,
            ],
            [
                'category' => 'minuman',
                'name' => 'Es Nutrisari',
                'price' => 5000,
                'description' => 'Minuman serbuk sari buah jeruk/mangga segar disajikan dingin pelepas dahaga di siang hari (±120 kkal; 28g KH).',
                'image_path' => 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?auto=format&fit=crop&w=600&q=80',
                'estimated_time_minutes' => 3,
                'taste_tags' => ['Manis', 'Segar'],
                'stock' => 17,
            ],
            [
                'category' => 'minuman',
                'name' => 'Es Susu Coklat',
                'price' => 6000,
                'description' => 'Minuman susu coklat dingin manis creamy penambah energi di sela jadwal kuliah (±174,5 kkal; 1,8g protein; 27g KH).',
                'image_path' => 'https://images.unsplash.com/photo-1542990253-0d0f5be5f0ed?auto=format&fit=crop&w=600&q=80',
                'estimated_time_minutes' => 3,
                'taste_tags' => ['Manis', 'Segar'],
                'stock' => 16,
            ],
            [
                'category' => 'kopi',
                'name' => 'Kopi Hitam / Susu',
                'price' => 5000,
                'description' => 'Kopi seduh hangat atau es kopi susu pilihan untuk menemani mahasiswa mengerjakan tugas di foodcourt.',
                'image_path' => 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?auto=format&fit=crop&w=600&q=80',
                'estimated_time_minutes' => 3,
                'taste_tags' => ['Manis', 'Gurih'],
                'stock' => 12,
            ],
        ];

        foreach ($foodMenus as $item) {
            Menu::firstOrCreate(
                [
                    'seller_id' => $stalls[$item['stall']]->id,
                    'name' => $item['name'],
                ],
                [
                    'category_id' => $categories[$item['category']]->id,
                    'price' => $item['price'],
                    'description' => $item['description'],
                    'image_path' => $item['image_path'],
                    'estimated_time_minutes' => $item['estimated_time_minutes'],
                    'taste_tags' => $item['taste_tags'],
                    'stock' => $item['stock'],
                    'is_available' => true,
                    'is_active' => true,
                ]
            );
        }

        foreach ($stalls as $stall) {
            foreach ($standardDrinks as $drink) {
                Menu::firstOrCreate(
                    [
                        'seller_id' => $stall->id,
                        'name' => $drink['name'],
                    ],
                    [
                        'category_id' => $categories[$drink['category']]->id,
                        'price' => $drink['price'],
                        'description' => $drink['description'],
                        'image_path' => $drink['image_path'],
                        'estimated_time_minutes' => $drink['estimated_time_minutes'],
                        'taste_tags' => $drink['taste_tags'],
                        'stock' => $drink['stock'],
                        'is_available' => true,
                        'is_active' => true,
                    ]
                );
            }
        }

        // 6. Seed Riwayat Transaksi (21 hari MySQL + 90 hari CSV AI) & Log AI
        $this->call(OrderSeeder::class);
    }
}
