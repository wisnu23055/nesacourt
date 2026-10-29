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
     * Seed initial application data for NesaCourt.
     */
    public function run(): void
    {
        // 1. Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@nesacourt.ac.id'],
            [
                'name' => 'Pengelola Food Court UNESA',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '081122334455',
            ]
        );

        $buyer = User::firstOrCreate(
            ['email' => 'aditya.23048@mhs.unesa.ac.id'],
            [
                'name' => 'Aditya Pratama',
                'password' => Hash::make('password'),
                'role' => 'buyer',
                'phone' => '23055310001', // NIM Mahasiswa
            ]
        );

        $sellerUsers = [
            'barokah' => User::firstOrCreate(
                ['email' => 'barokah@nesacourt.ac.id'],
                [
                    'name' => 'Pemilik Barokah Rasa',
                    'password' => Hash::make('password'),
                    'role' => 'seller',
                    'phone' => '081234567801',
                ]
            ),
            'soto' => User::firstOrCreate(
                ['email' => 'soto@nesacourt.ac.id'],
                [
                    'name' => 'Bu Siti',
                    'password' => Hash::make('password'),
                    'role' => 'seller',
                    'phone' => '081234567802',
                ]
            ),
            'geprek' => User::firstOrCreate(
                ['email' => 'geprek@nesacourt.ac.id'],
                [
                    'name' => 'Mas Bro',
                    'password' => Hash::make('password'),
                    'role' => 'seller',
                    'phone' => '081234567803',
                ]
            ),
            'jus' => User::firstOrCreate(
                ['email' => 'jus@nesacourt.ac.id'],
                [
                    'name' => 'Pengelola Jus Vokasi',
                    'password' => Hash::make('password'),
                    'role' => 'seller',
                    'phone' => '081234567804',
                ]
            ),
        ];

        // 2. Categories
        $categories = [
            'makanan' => Category::firstOrCreate(['slug' => 'makanan'], ['name' => 'Makanan Berat', 'sort_order' => 1, 'is_active' => true]),
            'camilan' => Category::firstOrCreate(['slug' => 'camilan'], ['name' => 'Camilan & Gorengan', 'sort_order' => 2, 'is_active' => true]),
            'minuman' => Category::firstOrCreate(['slug' => 'minuman'], ['name' => 'Minuman Dingin', 'sort_order' => 3, 'is_active' => true]),
            'kopi' => Category::firstOrCreate(['slug' => 'kopi'], ['name' => 'Kopi & Teh', 'sort_order' => 4, 'is_active' => true]),
            'paket' => Category::firstOrCreate(['slug' => 'paket'], ['name' => 'Paket Hemat Mahasiswa', 'sort_order' => 5, 'is_active' => true]),
        ];

        // 3. Stalls / Seller Profiles
        $stalls = [
            'barokah' => SellerProfile::firstOrCreate(
                ['user_id' => $sellerUsers['barokah']->id],
                [
                    'stall_name' => 'Stan 01 - Barokah Rasa',
                    'slug' => 'barokah-rasa',
                    'description' => 'Spesialis olahan ayam geprek bumbu sambal bawang dan penyet khas Surabaya.',
                    'logo_path' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=120&q=80',
                    'location' => 'Counter Barat No. 01 | Food Court Ketintang',
                    'is_active' => true,
                    'open_time' => '08:00:00',
                    'close_time' => '17:00:00',
                ]
            ),
            'soto' => SellerProfile::firstOrCreate(
                ['user_id' => $sellerUsers['soto']->id],
                [
                    'stall_name' => 'Stan 02 - Soto Bu Siti',
                    'slug' => 'soto-bu-siti',
                    'description' => 'Soto ayam Lamongan koya gurih dan rawon daging sapi empuk khas Jawa Timur.',
                    'logo_path' => 'https://images.unsplash.com/photo-1541832676-9b763b0239ab?auto=format&fit=crop&w=120&q=80',
                    'location' => 'Counter Barat No. 02 | Food Court Ketintang',
                    'is_active' => true,
                    'open_time' => '08:00:00',
                    'close_time' => '16:30:00',
                ]
            ),
            'geprek' => SellerProfile::firstOrCreate(
                ['user_id' => $sellerUsers['geprek']->id],
                [
                    'stall_name' => 'Stan 03 - Geprek Mas Bro',
                    'slug' => 'geprek-mas-bro',
                    'description' => 'Nasi goreng spesial aroma wok dan aneka camilan kentang garing.',
                    'logo_path' => 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?auto=format&fit=crop&w=120&q=80',
                    'location' => 'Counter Timur No. 03 | Food Court Ketintang',
                    'is_active' => true,
                    'open_time' => '08:30:00',
                    'close_time' => '17:30:00',
                ]
            ),
            'jus' => SellerProfile::firstOrCreate(
                ['user_id' => $sellerUsers['jus']->id],
                [
                    'stall_name' => 'Stan 04 - Aneka Jus Vokasi',
                    'slug' => 'aneka-jus-vokasi',
                    'description' => 'Pusat es teh jumbo, aneka jus buah segar asli, dan kopi susu gula aren.',
                    'logo_path' => 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?auto=format&fit=crop&w=120&q=80',
                    'location' => 'Counter Timur No. 04 | Food Court Ketintang',
                    'is_active' => true,
                    'open_time' => '07:30:00',
                    'close_time' => '17:00:00',
                ]
            ),
        ];

        // 4. Seller Payment QRIS
        foreach ($stalls as $key => $stall) {
            SellerPaymentQris::firstOrCreate(
                ['seller_id' => $stall->id],
                [
                    'image_path' => 'images/qris.jpeg',
                    'merchant_name' => $stall->stall_name,
                    'is_active' => true,
                ]
            );
        }

        // 5. Menus
        $menuList = [
            [
                'seller_id' => $stalls['barokah']->id,
                'category_id' => $categories['makanan']->id,
                'name' => 'Ayam Geprek Sambal Bawang Level 3',
                'price' => 13000,
                'description' => 'Daging ayam segar renyah dengan balutan tepung bumbu khas, diulek langsung bersama cabai rawit merah dan bawang putih segar.',
                'image_path' => 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?auto=format&fit=crop&w=600&q=80',
                'estimated_time_minutes' => 7,
                'taste_tags' => ['Pedas', 'Gurih', 'Nasi Putih'],
                'stock' => 14,
                'is_available' => true,
                'is_active' => true,
            ],
            [
                'seller_id' => $stalls['barokah']->id,
                'category_id' => $categories['makanan']->id,
                'name' => 'Ayam Penyet Sambal Ijo Padang',
                'price' => 14000,
                'description' => 'Ayam ungkep bumbu rempah kuning digoreng garing dengan siraman sambal cabe ijo khas Minang yang gurih pedas nikmat.',
                'image_path' => 'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?auto=format&fit=crop&w=600&q=80',
                'estimated_time_minutes' => 8,
                'taste_tags' => ['Pedas Segar', 'Ayam Ungkep'],
                'stock' => 9,
                'is_available' => true,
                'is_active' => true,
            ],
            [
                'seller_id' => $stalls['soto']->id,
                'category_id' => $categories['makanan']->id,
                'name' => 'Soto Ayam Lamongan Lengkap Koya',
                'price' => 14000,
                'description' => 'Kuah soto kuning gurih beraroma kaldu ayam kampung, suwiran daging ayam, soun lembut, tauge, daun seledri, dan bubuk koya gurih.',
                'image_path' => 'https://images.unsplash.com/photo-1541832676-9b763b0239ab?auto=format&fit=crop&w=600&q=80',
                'estimated_time_minutes' => 5,
                'taste_tags' => ['Berkuah Hangat', 'Koya Gurih'],
                'stock' => 11,
                'is_available' => true,
                'is_active' => true,
            ],
            [
                'seller_id' => $stalls['soto']->id,
                'category_id' => $categories['makanan']->id,
                'name' => 'Rawon Daging Sapi Kuah Hitam',
                'price' => 18000,
                'description' => 'Potongan daging sapi empuk dalam kuah kluwek hitam gurih khas Jawa Timur, disajikan lengkap bersama tauge pendek segar dan sambal terasi.',
                'image_path' => 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=600&q=80',
                'estimated_time_minutes' => 6,
                'taste_tags' => ['Daging Sapi', 'Khas Jatim'],
                'stock' => 6,
                'is_available' => true,
                'is_active' => true,
            ],
            [
                'seller_id' => $stalls['geprek']->id,
                'category_id' => $categories['makanan']->id,
                'name' => 'Nasi Goreng Spesial Telur Ceplok',
                'price' => 12000,
                'description' => 'Nasi goreng harum beraroma wok dengan kecap manis bango, bumbu bawang, irisan baso, suwiran ayam, dan telur ceplok setengah matang.',
                'image_path' => 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?auto=format&fit=crop&w=600&q=80',
                'estimated_time_minutes' => 7,
                'taste_tags' => ['Wok Aroma', 'Porsi Pas'],
                'stock' => 18,
                'is_available' => true,
                'is_active' => true,
            ],
            [
                'seller_id' => $stalls['geprek']->id,
                'category_id' => $categories['camilan']->id,
                'name' => 'Kentang Goreng Krispi French Fries',
                'price' => 8000,
                'description' => 'Kentang potong garing bertabur bumbu keju manis atau balado gurih, cocok untuk camilan santai saat menunggu jadwal kuliah.',
                'image_path' => 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?auto=format&fit=crop&w=600&q=80',
                'estimated_time_minutes' => 4,
                'taste_tags' => ['Camilan Garing', 'Tabur Bumbu'],
                'stock' => 15,
                'is_available' => true,
                'is_active' => true,
            ],
            [
                'seller_id' => $stalls['jus']->id,
                'category_id' => $categories['minuman']->id,
                'name' => 'Es Teh Manis Jumbo Vokasi',
                'price' => 4000,
                'description' => 'Teh melati seduh segar dengan gula asli dan es batu melimpah. Ukuran cup jumbo 22 oz yang bikin dahaga langsung hilang.',
                'image_path' => 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?auto=format&fit=crop&w=600&q=80',
                'estimated_time_minutes' => 2,
                'taste_tags' => ['Dingin Segar', 'Paling Murah'],
                'stock' => 40,
                'is_available' => true,
                'is_active' => true,
            ],
            [
                'seller_id' => $stalls['jus']->id,
                'category_id' => $categories['minuman']->id,
                'name' => 'Jus Alpukat Kental Susu Cokelat',
                'price' => 10000,
                'description' => 'Alpukat mentega matang pilihan diblender lembut dengan kental manis cokelat di sekeliling gelas.',
                'image_path' => 'https://images.unsplash.com/photo-1553530666-ba11a7da3888?auto=format&fit=crop&w=600&q=80',
                'estimated_time_minutes' => 4,
                'taste_tags' => ['Kental', 'Buah Segar'],
                'stock' => 8,
                'is_available' => true,
                'is_active' => true,
            ],
            [
                'seller_id' => $stalls['jus']->id,
                'category_id' => $categories['kopi']->id,
                'name' => 'Kopi Susu Gula Aren Ketintang',
                'price' => 9000,
                'description' => 'Perpaduan biji kopi robusta nusantara, susu segar creamy, dan manisnya sirup aren murni asli Tuban.',
                'image_path' => 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?auto=format&fit=crop&w=600&q=80',
                'estimated_time_minutes' => 3,
                'taste_tags' => ['Kopi Hits', 'Stok Habis'],
                'stock' => 0,
                'is_available' => false,
                'is_active' => true,
            ],
            [
                'seller_id' => $stalls['barokah']->id,
                'category_id' => $categories['paket']->id,
                'name' => 'Paket Kenyang Hemat: Nasi Ayam + Es Teh',
                'price' => 15000,
                'description' => 'Solusi tanggal tua mahasiswa: 1 porsi Nasi Ayam Geprek / Penyet komplit dengan 1 cup Es Teh Manis Jumbo.',
                'image_path' => 'https://images.unsplash.com/photo-1569058242253-92a9c755a0ec?auto=format&fit=crop&w=600&q=80',
                'estimated_time_minutes' => 6,
                'taste_tags' => ['Paling Hemat', 'Komplit'],
                'stock' => 12,
                'is_available' => true,
                'is_active' => true,
            ],
        ];

        foreach ($menuList as $item) {
            Menu::firstOrCreate(
                [
                    'seller_id' => $item['seller_id'],
                    'name' => $item['name'],
                ],
                $item
            );
        }
    }
}
