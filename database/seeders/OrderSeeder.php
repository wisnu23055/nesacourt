<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusLog;
use App\Models\PaymentConfirmation;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Pastikan Akun Pembeli Mahasiswa Tersedia
        $buyers = [
            'aditya' => User::firstOrCreate(
                ['email' => 'aditya.23048@mhs.unesa.ac.id'],
                [
                    'name' => 'Aditya Pratama',
                    'password' => Hash::make('password'),
                    'role' => 'buyer',
                    'phone' => '23055310001', // NIM Mahasiswa
                ]
            ),
            'nadia' => User::firstOrCreate(
                ['email' => 'nadia.salsabila@mhs.unesa.ac.id'],
                [
                    'name' => 'Nadia Salsabila',
                    'password' => Hash::make('password'),
                    'role' => 'buyer',
                    'phone' => '23055310034',
                ]
            ),
            'rizky' => User::firstOrCreate(
                ['email' => 'rizky.kurniawan@mhs.unesa.ac.id'],
                [
                    'name' => 'Rizky Kurniawan',
                    'password' => Hash::make('password'),
                    'role' => 'buyer',
                    'phone' => '23055310018',
                ]
            ),
            'dewi' => User::firstOrCreate(
                ['email' => 'dewi.lestari@mhs.unesa.ac.id'],
                [
                    'name' => 'Dewi Lestari',
                    'password' => Hash::make('password'),
                    'role' => 'buyer',
                    'phone' => '23055310045',
                ]
            ),
            'budi' => User::firstOrCreate(
                ['email' => 'budi.santoso@mhs.unesa.ac.id'],
                [
                    'name' => 'Budi Santoso',
                    'password' => Hash::make('password'),
                    'role' => 'buyer',
                    'phone' => '23055310052',
                ]
            ),
            'siti' => User::firstOrCreate(
                ['email' => 'siti.aisyah@mhs.unesa.ac.id'],
                [
                    'name' => 'Siti Aisyah',
                    'password' => Hash::make('password'),
                    'role' => 'buyer',
                    'phone' => '23055310060',
                ]
            ),
        ];

        // 2. Ambil Stan dan Menu
        $stallBarokah = SellerProfile::where('slug', 'barokah-rasa')->orWhere('id', 1)->first();
        $stallSoto = SellerProfile::where('slug', 'soto-bu-siti')->orWhere('id', 2)->first();
        $stallGeprek = SellerProfile::where('slug', 'geprek-mas-bro')->orWhere('id', 3)->first();
        $stallJus = SellerProfile::where('slug', 'aneka-jus-vokasi')->orWhere('id', 4)->first();

        if (!$stallBarokah) {
            return;
        }

        $menuBarokahGeprek = Menu::where('seller_id', $stallBarokah->id)->where('name', 'like', '%Geprek%')->first();
        $menuBarokahPenyet = Menu::where('seller_id', $stallBarokah->id)->where('name', 'like', '%Penyet%')->first();
        $menuBarokahPaket = Menu::where('seller_id', $stallBarokah->id)->where('name', 'like', '%Paket%')->first();

        $menuSoto = $stallSoto ? Menu::where('seller_id', $stallSoto->id)->first() : null;
        $menuGeprekNasgor = $stallGeprek ? Menu::where('seller_id', $stallGeprek->id)->first() : null;
        $menuJusTeh = $stallJus ? Menu::where('seller_id', $stallJus->id)->first() : null;

        $sellerUserBarokah = $stallBarokah->user_id;

        // 3. Dataset Pesanan untuk Stan 01 - Barokah Rasa (Mencakup Semua Status)
        $orderScenarios = [
            // Status 1: Menunggu Konfirmasi / Verifikasi QRIS Dapur
            [
                'order_number' => 'NC-20261104-001',
                'buyer' => $buyers['aditya'],
                'seller_id' => $stallBarokah->id,
                'customer_name' => 'Aditya Pratama',
                'order_status' => 'dibuat',
                'payment_status' => 'perlu_pemeriksaan',
                'note' => 'Makan di Tempat - Meja 08. Sambal pedas dipisah ya mas.',
                'created_at' => now()->subMinutes(12),
                'items' => [
                    ['menu' => $menuBarokahGeprek, 'name' => 'Ayam Geprek Sambal Bawang Level 3', 'price' => 13000, 'qty' => 1, 'note' => 'Sambal pedas dipisah'],
                    ['menu' => $menuJusTeh, 'name' => 'Es Teh Manis Jumbo Vokasi', 'price' => 4000, 'qty' => 1, 'note' => 'Sedikit gula'],
                ],
                'logs' => [
                    ['old_o' => 'dibuat', 'new_o' => 'dibuat', 'old_p' => 'menunggu', 'new_p' => 'perlu_pemeriksaan', 'note' => 'Pembeli mengonfirmasi telah transfer via QRIS statis stan.', 'actor_type' => 'buyer'],
                ],
                'is_confirmed_payment' => false,
            ],

            // Status 2: Sedang Dimasak di Dapur
            [
                'order_number' => 'NC-20261104-002',
                'buyer' => $buyers['nadia'],
                'seller_id' => $stallBarokah->id,
                'customer_name' => 'Nadia Salsabila',
                'order_status' => 'diproses',
                'payment_status' => 'terkonfirmasi',
                'note' => 'Makan di Tempat - Meja 12. Ayam digoreng agak garing.',
                'created_at' => now()->subMinutes(25),
                'items' => [
                    ['menu' => $menuBarokahPaket, 'name' => 'Paket Kenyang Hemat: Nasi Ayam + Es Teh', 'price' => 15000, 'qty' => 2, 'note' => 'Ayam garing renyah'],
                ],
                'logs' => [
                    ['old_o' => 'dibuat', 'new_o' => 'dibuat', 'old_p' => 'menunggu', 'new_p' => 'perlu_pemeriksaan', 'note' => 'Pembeli telah membayar QRIS.', 'actor_type' => 'buyer'],
                    ['old_o' => 'dibuat', 'new_o' => 'diproses', 'old_p' => 'perlu_pemeriksaan', 'new_p' => 'terkonfirmasi', 'note' => 'Pembayaran diverifikasi kasir, pesanan mulai dimasak.', 'actor_type' => 'seller'],
                ],
                'is_confirmed_payment' => true,
            ],

            // Status 3: Siap Diambil di Meja Counter
            [
                'order_number' => 'NC-20261104-003',
                'buyer' => $buyers['rizky'],
                'seller_id' => $stallBarokah->id,
                'customer_name' => 'Rizky Kurniawan',
                'order_status' => 'siap',
                'payment_status' => 'terkonfirmasi',
                'note' => 'Bungkus / Bawa Pulang. Tolong sendok garpu plastik 2 pasang.',
                'created_at' => now()->subMinutes(35),
                'items' => [
                    ['menu' => $menuBarokahPenyet, 'name' => 'Ayam Penyet Sambal Ijo Padang', 'price' => 14000, 'qty' => 1, 'note' => 'Bungkus rapi'],
                    ['menu' => $menuBarokahGeprek, 'name' => 'Ayam Geprek Sambal Bawang Level 3', 'price' => 13000, 'qty' => 1, 'note' => 'Level 1 saja'],
                ],
                'logs' => [
                    ['old_o' => 'dibuat', 'new_o' => 'diproses', 'old_p' => 'perlu_pemeriksaan', 'new_p' => 'terkonfirmasi', 'note' => 'Pesanan dimasak.', 'actor_type' => 'seller'],
                    ['old_o' => 'diproses', 'new_o' => 'siap', 'old_p' => 'terkonfirmasi', 'new_p' => 'terkonfirmasi', 'note' => 'Makanan selesai disiapkan dan siap diambil di counter.', 'actor_type' => 'seller'],
                ],
                'is_confirmed_payment' => true,
            ],

            // Status 4: Selesai Hari Ini #1
            [
                'order_number' => 'NC-20261104-004',
                'buyer' => $buyers['dewi'],
                'seller_id' => $stallBarokah->id,
                'customer_name' => 'Dewi Lestari',
                'order_status' => 'selesai',
                'payment_status' => 'terkonfirmasi',
                'note' => 'Makan di Tempat - Meja 04',
                'created_at' => now()->subHours(1),
                'items' => [
                    ['menu' => $menuBarokahGeprek, 'name' => 'Ayam Geprek Sambal Bawang Level 3', 'price' => 13000, 'qty' => 1, 'note' => ''],
                ],
                'logs' => [
                    ['old_o' => 'dibuat', 'new_o' => 'diproses', 'old_p' => 'menunggu', 'new_p' => 'terkonfirmasi', 'note' => 'Dimasak.', 'actor_type' => 'seller'],
                    ['old_o' => 'diproses', 'new_o' => 'siap', 'old_p' => 'terkonfirmasi', 'new_p' => 'terkonfirmasi', 'note' => 'Siap.', 'actor_type' => 'seller'],
                    ['old_o' => 'siap', 'new_o' => 'selesai', 'old_p' => 'terkonfirmasi', 'new_p' => 'terkonfirmasi', 'note' => 'Makanan telah diserahkan ke mahasiswa.', 'actor_type' => 'seller'],
                ],
                'is_confirmed_payment' => true,
            ],

            // Status 5: Selesai Hari Ini #2
            [
                'order_number' => 'NC-20261104-005',
                'buyer' => $buyers['budi'],
                'seller_id' => $stallBarokah->id,
                'customer_name' => 'Budi Santoso',
                'order_status' => 'selesai',
                'payment_status' => 'terkonfirmasi',
                'note' => 'Makan di Tempat - Meja 02',
                'created_at' => now()->subHours(2),
                'items' => [
                    ['menu' => $menuBarokahPaket, 'name' => 'Paket Kenyang Hemat: Nasi Ayam + Es Teh', 'price' => 15000, 'qty' => 1, 'note' => 'Es teh tawar'],
                ],
                'logs' => [
                    ['old_o' => 'siap', 'new_o' => 'selesai', 'old_p' => 'terkonfirmasi', 'new_p' => 'terkonfirmasi', 'note' => 'Transaksi selesai.', 'actor_type' => 'seller'],
                ],
                'is_confirmed_payment' => true,
            ],

            // Status 6: Selesai 3 Hari Lalu (Untuk menguji Laporan 7 Hari Terakhir)
            [
                'order_number' => 'NC-20261101-089',
                'buyer' => $buyers['siti'],
                'seller_id' => $stallBarokah->id,
                'customer_name' => 'Siti Aisyah',
                'order_status' => 'selesai',
                'payment_status' => 'terkonfirmasi',
                'note' => 'Makan di Tempat - Meja 10',
                'created_at' => now()->subDays(3)->setTime(12, 15, 0),
                'items' => [
                    ['menu' => $menuBarokahPenyet, 'name' => 'Ayam Penyet Sambal Ijo Padang', 'price' => 14000, 'qty' => 2, 'note' => ''],
                ],
                'logs' => [
                    ['old_o' => 'siap', 'new_o' => 'selesai', 'old_p' => 'terkonfirmasi', 'new_p' => 'terkonfirmasi', 'note' => 'Pesanan selesai.', 'actor_type' => 'seller'],
                ],
                'is_confirmed_payment' => true,
            ],

            // Status 7: Selesai 10 Hari Lalu (Untuk menguji Laporan Bulan Ini)
            [
                'order_number' => 'NC-20261025-045',
                'buyer' => $buyers['aditya'],
                'seller_id' => $stallBarokah->id,
                'customer_name' => 'Aditya Pratama',
                'order_status' => 'selesai',
                'payment_status' => 'terkonfirmasi',
                'note' => 'Bungkus / Bawa Pulang',
                'created_at' => now()->subDays(10)->setTime(13, 05, 0),
                'items' => [
                    ['menu' => $menuBarokahGeprek, 'name' => 'Ayam Geprek Sambal Bawang Level 3', 'price' => 13000, 'qty' => 2, 'note' => 'Level 5 extra pedas'],
                ],
                'logs' => [
                    ['old_o' => 'siap', 'new_o' => 'selesai', 'old_p' => 'terkonfirmasi', 'new_p' => 'terkonfirmasi', 'note' => 'Pesanan selesai dan dibawa pulang.', 'actor_type' => 'seller'],
                ],
                'is_confirmed_payment' => true,
            ],

            // Status 8: Dibatalkan (Untuk menguji filter & histori pembatalan)
            [
                'order_number' => 'NC-20261104-006',
                'buyer' => $buyers['nadia'],
                'seller_id' => $stallBarokah->id,
                'customer_name' => 'Nadia Salsabila',
                'order_status' => 'dibatalkan',
                'payment_status' => 'dibatalkan',
                'note' => 'Dibatalkan karena salah pilih meja.',
                'created_at' => now()->subMinutes(50),
                'items' => [
                    ['menu' => $menuBarokahGeprek, 'name' => 'Ayam Geprek Sambal Bawang Level 3', 'price' => 13000, 'qty' => 1, 'note' => ''],
                ],
                'logs' => [
                    ['old_o' => 'dibuat', 'new_o' => 'dibatalkan', 'old_p' => 'menunggu', 'new_p' => 'dibatalkan', 'note' => 'Pesanan dibatalkan oleh pembeli sebelum membayar.', 'actor_type' => 'buyer'],
                ],
                'is_confirmed_payment' => false,
            ],
        ];

        // 4. Eksekusi Seeding Pesanan
        foreach ($orderScenarios as $sc) {
            $subtotal = 0;
            foreach ($sc['items'] as $item) {
                $subtotal += ($item['price'] * $item['qty']);
            }
            $total = $subtotal;

            $order = Order::updateOrCreate(
                ['order_number' => $sc['order_number']],
                [
                    'buyer_id' => $sc['buyer']->id,
                    'seller_id' => $sc['seller_id'],
                    'customer_name' => $sc['customer_name'],
                    'access_token' => Str::random(32),
                    'order_status' => $sc['order_status'],
                    'payment_status' => $sc['payment_status'],
                    'subtotal' => $subtotal,
                    'total' => $total,
                    'note' => $sc['note'],
                    'payment_deadline' => now()->addMinutes(15),
                    'created_at' => $sc['created_at'],
                    'updated_at' => $sc['created_at'],
                ]
            );

            // Seed Order Items
            foreach ($sc['items'] as $item) {
                OrderItem::firstOrCreate(
                    [
                        'order_id' => $order->id,
                        'menu_name_snapshot' => $item['name'],
                    ],
                    [
                        'menu_id' => $item['menu']?->id,
                        'menu_price_snapshot' => $item['price'],
                        'quantity' => $item['qty'],
                        'subtotal' => $item['price'] * $item['qty'],
                        'note' => $item['note'],
                    ]
                );
            }

            // Seed Status Logs
            foreach ($sc['logs'] as $log) {
                OrderStatusLog::create([
                    'order_id' => $order->id,
                    'actor_user_id' => $log['actor_type'] === 'seller' ? $sellerUserBarokah : $sc['buyer']->id,
                    'actor_type' => $log['actor_type'],
                    'old_order_status' => $log['old_o'],
                    'new_order_status' => $log['new_o'],
                    'old_payment_status' => $log['old_p'],
                    'new_payment_status' => $log['new_p'],
                    'note' => $log['note'],
                    'created_at' => $sc['created_at'],
                ]);
            }

            // Seed Payment Confirmation jika status terkonfirmasi
            if ($sc['is_confirmed_payment']) {
                PaymentConfirmation::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'seller_id' => $sc['seller_id'],
                        'amount' => $total,
                        'confirmed_by' => $sellerUserBarokah,
                        'merchant_reference' => 'QRIS-' . $order->order_number,
                        'confirmed_at' => $sc['created_at'],
                        'created_at' => $sc['created_at'],
                        'updated_at' => $sc['created_at'],
                    ]
                );
            }
        }

        // 5. Tambahkan juga sample pesanan untuk stan lain (Stan 02, 03, 04)
        if ($stallSoto && $menuSoto) {
            $orderSoto = Order::updateOrCreate(
                ['order_number' => 'NC-20261104-007'],
                [
                    'buyer_id' => $buyers['rizky']->id,
                    'seller_id' => $stallSoto->id,
                    'customer_name' => 'Rizky Kurniawan',
                    'access_token' => Str::random(32),
                    'order_status' => 'diproses',
                    'payment_status' => 'terkonfirmasi',
                    'subtotal' => 14000,
                    'total' => 14000,
                    'note' => 'Makan di Tempat - Meja 05. Kuah dipisah.',
                    'created_at' => now()->subMinutes(18),
                ]
            );
            OrderItem::firstOrCreate(
                ['order_id' => $orderSoto->id, 'menu_name_snapshot' => $menuSoto->name],
                ['menu_id' => $menuSoto->id, 'menu_price_snapshot' => $menuSoto->price, 'quantity' => 1, 'subtotal' => $menuSoto->price]
            );
        }

        if ($stallJus && $menuJusTeh) {
            $orderJus = Order::updateOrCreate(
                ['order_number' => 'NC-20261104-008'],
                [
                    'buyer_id' => $buyers['dewi']->id,
                    'seller_id' => $stallJus->id,
                    'customer_name' => 'Dewi Lestari',
                    'access_token' => Str::random(32),
                    'order_status' => 'siap',
                    'payment_status' => 'terkonfirmasi',
                    'subtotal' => 8000,
                    'total' => 8000,
                    'note' => 'Bungkus / Bawa Pulang',
                    'created_at' => now()->subMinutes(10),
                ]
            );
            OrderItem::firstOrCreate(
                ['order_id' => $orderJus->id, 'menu_name_snapshot' => $menuJusTeh->name],
                ['menu_id' => $menuJusTeh->id, 'menu_price_snapshot' => $menuJusTeh->price, 'quantity' => 2, 'subtotal' => 8000]
            );
        }
    }
}

