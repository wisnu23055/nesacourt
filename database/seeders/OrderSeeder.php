<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\SellerProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    /**
     * Menghasilkan dataset sintetis terkalibrasi (Opsi 2):
     * - 90 hari kalender penuh diekspor ke ai_dataset/*.csv untuk training & evaluasi AI.
     * - 21 hari kalender terakhir (+ pesanan aktif hari ini) dimasukkan ke MySQL agar
     *   halaman laporan, dashboard, dan demo lokal tetap ringan namun realistis.
     *
     * Sumber kalibrasi:
     * 1. Wawancara 5 tenant Foodcourt Baseball UNESA 1 Ketintang
     *    - Stok/hari/tenant: 80-100 makanan, 100-120 minuman
     *    - Penjualan/hari/tenant: rata-rata ~90 makanan & ~100 minuman;
     *      ramai 95-100 makanan & 110-120 minuman; sepi 50-60 makanan & 60-75 minuman
     * 2. Jurnal Ramadhan & Ruhana (2026), Jurnal Gizi & Kesehatan Nusantara Vol. 06 No. 01
     */
    public function run(): void
    {
        // Fixed seed agar hasil 100% reproducible setiap kali migrate:fresh --seed
        mt_srand(20261121);

        $stalls = SellerProfile::with(['menus.category', 'user'])->orderBy('id')->get();
        $buyers = User::where('role', 'buyer')->orderBy('id')->get();

        if ($stalls->isEmpty() || $buyers->isEmpty()) {
            return;
        }

        // Petakan fakultas pembeli untuk kolom analitik CSV (sesuai urutan seed di DatabaseSeeder)
        $buyerFaculties = $this->mapBuyerFaculties($buyers);

        // Referensi gizi dari Tabel 6 & 7 Ramadhan & Ruhana (2026)
        $nutritionMap = $this->nutritionLookup();

        // Siapkan folder output CSV khusus AI
        $aiDir = base_path('ai_dataset');
        if (!is_dir($aiDir)) {
            mkdir($aiDir, 0755, true);
        }

        // 1. Export menus_master.csv
        $this->exportMenusMasterCsv($aiDir . DIRECTORY_SEPARATOR . 'menus_master.csv', $stalls, $nutritionMap);

        // Siapkan file handle streaming untuk CSV 90 hari
        $rawFp = fopen($aiDir . DIRECTORY_SEPARATOR . 'transactions_raw.csv', 'w');
        $dailyFp = fopen($aiDir . DIRECTORY_SEPARATOR . 'sales_daily_per_menu.csv', 'w');
        $hourlyFp = fopen($aiDir . DIRECTORY_SEPARATOR . 'sales_hourly_per_menu.csv', 'w');

        fputcsv($rawFp, [
            'order_number',
            'order_datetime',
            'date',
            'hour',
            'day_of_week',
            'day_name',
            'demand_regime',
            'weather',
            'is_end_of_month',
            'has_campus_event',
            'seller_id',
            'stall_name',
            'buyer_id',
            'buyer_faculty',
            'menu_id',
            'menu_name',
            'item_type',
            'price_idr',
            'quantity',
            'subtotal_idr',
            'order_status',
            'payment_status',
        ]);

        fputcsv($dailyFp, [
            'date',
            'day_of_week',
            'day_name',
            'is_weekend',
            'is_end_of_month',
            'weather',
            'has_campus_event',
            'demand_regime',
            'seller_id',
            'stall_name',
            'menu_id',
            'menu_name',
            'item_type',
            'price_idr',
            'daily_stock_quota',
            'completed_qty_sold',
            'cancelled_qty',
            'revenue_idr',
            'stock_utilization_pct',
            'stockout_flag',
        ]);

        fputcsv($hourlyFp, [
            'date',
            'hour',
            'day_of_week',
            'demand_regime',
            'weather',
            'seller_id',
            'stall_name',
            'menu_id',
            'menu_name',
            'item_type',
            'completed_qty_sold',
            'revenue_idr',
        ]);

        // Pre-index menu per tenant (dipisah makanan vs minuman)
        $stallMenus = [];
        foreach ($stalls as $stall) {
            $foods = [];
            $drinks = [];
            foreach ($stall->menus as $menu) {
                $isDrink = in_array($menu->category?->slug, ['minuman', 'kopi'], true);
                if ($isDrink) {
                    $drinks[] = $menu;
                } else {
                    $foods[] = $menu;
                }
            }
            $stallMenus[$stall->id] = [
                'stall' => $stall,
                'foods' => $foods,
                'drinks' => $drinks,
                'food_quota' => array_sum(array_map(fn($m) => (int) $m->stock, $foods)),
                'drink_quota' => array_sum(array_map(fn($m) => (int) $m->stock, $drinks)),
            ];
        }

        // Bobot distribusi jam operasional (08:00 - 16:59), puncak makan siang 11:00 - 13:59 (~55%)
        $hourWeights = [
            8 => 6,
            9 => 9,
            10 => 10,
            11 => 18,
            12 => 24,
            13 => 13,
            14 => 9,
            15 => 7,
            16 => 4,
        ];

        $dayNamesId = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];

        $notesPool = [
            'Makan di Tempat - Meja 02',
            'Makan di Tempat - Meja 05',
            'Makan di Tempat - Meja 08',
            'Makan di Tempat - Meja 12',
            'Makan di Tempat - Meja 15',
            'Bungkus / Bawa Pulang',
            'Bungkus / Bawa Pulang. Sambal dipisah ya bu.',
            'Makan di Tempat - Meja 04. Es sedikit saja.',
            'Bungkus / Bawa Pulang. Tanpa sendok plastik.',
            'Makan di Tempat - Meja 10. Pedas sedang.',
        ];

        $today = Carbon::today();
        $totalCalendarDays = 90;
        $mysqlWindowDays = 21; // Opsi 2: 21 hari terakhir masuk MySQL

        $startDate = $today->copy()->subDays($totalCalendarDays);
        $mysqlStartDate = $today->copy()->subDays($mysqlWindowDays);

        $nextOrderId = (int) (DB::table('orders')->max('id') ?? 0);
        $buyerCount = $buyers->count();

        // Simpan histori harian per menu untuk menyusun prediction_logs (7 hari operasional terakhir)
        $dailyHistoryForPrediction = []; // [menu_id => [ ['date'=>..., 'seller_id'=>..., 'actual'=>...] ]]
        $hourlyHistoryForPrediction = []; // [menu_id => [ ['date'=>..., 'seller_id'=>..., 'hour'=>..., 'actual'=>...] ]]

        // Loop dari H-90 sampai H-1 (hari selesai penuh)
        for ($d = 0; $d < $totalCalendarDays; $d++) {
            $currentDate = $startDate->copy()->addDays($d);
            $dow = $currentDate->dayOfWeekIso; // 1=Senin .. 7=Minggu

            // Minggu: Foodcourt kampus libur/tutup
            if ($dow === 7) {
                continue;
            }

            $dateStr = $currentDate->format('Y-m-d');
            $ymd = $currentDate->format('Ymd');
            $dayName = $dayNamesId[$dow];
            $isWeekend = $dow === 6 ? 1 : 0;
            $isEndOfMonth = $currentDate->day >= 24 ? 1 : 0;

            // Simulasi cuaca (cerah ~82%, hujan ~18%) & acara kampus (~3 hari dalam 90 hari)
            $weatherRoll = mt_rand(1, 100);
            $weather = $weatherRoll <= 18 ? 'hujan' : 'cerah';
            $hasCampusEvent = ($dow >= 2 && $dow <= 4 && in_array($d, [18, 47, 74], true)) ? 1 : 0;

            // Tentukan rezim demand harian berdasarkan wawancara + faktor kampus
            if ($hasCampusEvent) {
                $regime = 'ramai_event';
            } elseif ($dow === 6) {
                $regime = 'sepi';
            } elseif ($dow === 5) {
                $regime = $weather === 'hujan' ? 'sepi' : 'sedang_jumat';
            } elseif ($dow >= 2 && $dow <= 4) {
                $regime = ($weather === 'hujan' || $isEndOfMonth) ? 'normal' : 'ramai';
            } else {
                // Senin
                $regime = $weather === 'hujan' ? 'sepi' : 'normal';
            }

            $insertToMysql = $currentDate->greaterThanOrEqualTo($mysqlStartDate);
            $dailyOrderSeq = 0;

            // Buffer batch MySQL per hari agar memori hemat & insert cepat
            $ordersBatch = [];
            $itemsBatch = [];
            $logsBatch = [];
            $paymentsBatch = [];

            foreach ($stallMenus as $sellerId => $meta) {
                $stall = $meta['stall'];
                $foods = $meta['foods'];
                $drinks = $meta['drinks'];
                $foodQuota = $meta['food_quota'];
                $drinkQuota = $meta['drink_quota'];

                // Target porsi makanan & minuman terjual per tenant sesuai hasil wawancara:
                // - Ramai: 95-100 makanan, 110-120 minuman (capped stok harian)
                // - Normal: 86-93 makanan, 96-108 minuman
                // - Sedang Jumat: 66-78 makanan, 75-90 minuman
                // - Sepi: 50-60 makanan, 60-74 minuman
                [$targetFood, $targetDrink] = match ($regime) {
                    'ramai_event' => [min($foodQuota, mt_rand(96, 100)), min($drinkQuota, mt_rand(112, 115))],
                    'ramai' => [min($foodQuota, mt_rand(93, 99)), min($drinkQuota, mt_rand(108, 115))],
                    'normal' => [min($foodQuota, mt_rand(85, 93)), min($drinkQuota, mt_rand(96, 107))],
                    'sedang_jumat' => [min($foodQuota, mt_rand(65, 76)), min($drinkQuota, mt_rand(74, 88))],
                    default => [min($foodQuota, mt_rand(50, 60)), min($drinkQuota, mt_rand(60, 74))],
                };

                // Sedikit efek tanggal tua (-3 s/d -6 porsi)
                if ($isEndOfMonth && !$hasCampusEvent && $regime !== 'sepi') {
                    $targetFood = max(50, $targetFood - mt_rand(2, 5));
                    $targetDrink = max(60, $targetDrink - mt_rand(3, 6));
                }

                // Alokasikan target porsi ke masing-masing menu sesuai bobot popularitas jurnal + faktor cuaca/tanggal tua
                $foodAlloc = $this->allocateMenuQuantities($foods, $targetFood, $weather, (bool) $isEndOfMonth);
                $drinkAlloc = $this->allocateMenuQuantities($drinks, $targetDrink, $weather, (bool) $isEndOfMonth);

                // Bentuk antrean item per menu untuk dipasangkan menjadi transaksi order (single-seller)
                $foodQueue = [];
                foreach ($foods as $m) {
                    $qty = $foodAlloc[$m->id] ?? 0;
                    for ($i = 0; $i < $qty; $i++) {
                        $foodQueue[] = $m;
                    }
                }
                $drinkQueue = [];
                foreach ($drinks as $m) {
                    $qty = $drinkAlloc[$m->id] ?? 0;
                    for ($i = 0; $i < $qty; $i++) {
                        $drinkQueue[] = $m;
                    }
                }
                $this->deterministicShuffle($foodQueue);
                $this->deterministicShuffle($drinkQueue);

                // Tracker agregasi harian & per jam untuk CSV
                $dailyAgg = [];
                foreach (array_merge($foods, $drinks) as $m) {
                    $dailyAgg[$m->id] = [
                        'menu' => $m,
                        'item_type' => in_array($m->category?->slug, ['minuman', 'kopi'], true) ? 'minuman' : 'makanan',
                        'completed' => 0,
                        'cancelled' => 0,
                        'revenue' => 0,
                    ];
                }
                $hourlyAgg = []; // ["$hour|$menuId" => ['hour'=>..., 'menu'=>..., 'item_type'=>..., 'qty'=>..., 'rev'=>...]]

                // Pasangkan makanan + minuman menjadi transaksi realistis (sekitar 70% combo 1 makanan + 1 minuman)
                $fIdx = 0;
                $dIdx = 0;
                $fTotal = count($foodQueue);
                $dTotal = count($drinkQueue);

                while ($fIdx < $fTotal || $dIdx < $dTotal) {
                    $orderMenus = []; // [menu_id => ['menu'=>Menu, 'qty'=>int]]

                    $roll = mt_rand(1, 100);
                    if ($fIdx < $fTotal && $dIdx < $dTotal && $roll <= 76) {
                        // Combo: 1 makanan + 1 minuman
                        $this->addMenuToCart($orderMenus, $foodQueue[$fIdx++]);
                        $this->addMenuToCart($orderMenus, $drinkQueue[$dIdx++]);
                        // Kadang beli 2 porsi bareng teman (~10%)
                        if ($fIdx < $fTotal && mt_rand(1, 100) <= 10) {
                            $this->addMenuToCart($orderMenus, $foodQueue[$fIdx++]);
                        }
                    } elseif ($fIdx < $fTotal && ($dIdx >= $dTotal || $roll <= 88)) {
                        // Hanya makanan (1 atau 2 porsi)
                        $this->addMenuToCart($orderMenus, $foodQueue[$fIdx++]);
                        if ($fIdx < $fTotal && mt_rand(1, 100) <= 15) {
                            $this->addMenuToCart($orderMenus, $foodQueue[$fIdx++]);
                        }
                    } else {
                        // Hanya minuman (1 atau 2 cup)
                        $this->addMenuToCart($orderMenus, $drinkQueue[$dIdx++]);
                        if ($dIdx < $dTotal && mt_rand(1, 100) <= 22) {
                            $this->addMenuToCart($orderMenus, $drinkQueue[$dIdx++]);
                        }
                    }

                    $dailyOrderSeq++;
                    $orderNumber = sprintf('NC-%s-%04d', $ymd, $dailyOrderSeq);
                    $hour = $this->weightedRandomHour($hourWeights);
                    $minute = mt_rand(0, 59);
                    $second = mt_rand(0, 59);
                    $orderTime = $currentDate->copy()->setTime($hour, $minute, $second);
                    $orderTimeStr = $orderTime->format('Y-m-d H:i:s');

                    $buyer = $buyers[mt_rand(0, $buyerCount - 1)];
                    $buyerFaculty = $buyerFaculties[$buyer->id] ?? 'Teknik';

                    $subtotal = 0;
                    foreach ($orderMenus as $entry) {
                        $subtotal += (int) $entry['menu']->price * $entry['qty'];
                    }

                    // Catat ke agregasi & raw CSV (status selesai)
                    foreach ($orderMenus as $entry) {
                        $m = $entry['menu'];
                        $qty = $entry['qty'];
                        $lineSub = (int) $m->price * $qty;
                        $itemType = $dailyAgg[$m->id]['item_type'];

                        $dailyAgg[$m->id]['completed'] += $qty;
                        $dailyAgg[$m->id]['revenue'] += $lineSub;

                        $hKey = $hour . '|' . $m->id;
                        if (!isset($hourlyAgg[$hKey])) {
                            $hourlyAgg[$hKey] = [
                                'hour' => $hour,
                                'menu' => $m,
                                'item_type' => $itemType,
                                'qty' => 0,
                                'rev' => 0,
                            ];
                        }
                        $hourlyAgg[$hKey]['qty'] += $qty;
                        $hourlyAgg[$hKey]['rev'] += $lineSub;

                        fputcsv($rawFp, [
                            $orderNumber,
                            $orderTimeStr,
                            $dateStr,
                            $hour,
                            $dow,
                            $dayName,
                            $regime,
                            $weather,
                            $isEndOfMonth,
                            $hasCampusEvent,
                            $sellerId,
                            $stall->stall_name,
                            $buyer->id,
                            $buyerFaculty,
                            $m->id,
                            $m->name,
                            $itemType,
                            (int) $m->price,
                            $qty,
                            $lineSub,
                            'selesai',
                            'terkonfirmasi',
                        ]);
                    }

                    if ($insertToMysql) {
                        $nextOrderId++;
                        $note = $notesPool[mt_rand(0, count($notesPool) - 1)];
                        $deadline = $orderTime->copy()->addMinutes(15)->format('Y-m-d H:i:s');
                        $confirmedAt = $orderTime->copy()->addMinutes(mt_rand(1, 3))->format('Y-m-d H:i:s');

                        $ordersBatch[] = [
                            'id' => $nextOrderId,
                            'order_number' => $orderNumber,
                            'buyer_id' => $buyer->id,
                            'access_token' => bin2hex(random_bytes(16)),
                            'seller_id' => $sellerId,
                            'customer_name' => $buyer->name,
                            'order_status' => 'selesai',
                            'payment_status' => 'terkonfirmasi',
                            'subtotal' => $subtotal,
                            'total' => $subtotal,
                            'payment_deadline' => $deadline,
                            'note' => $note,
                            'created_at' => $orderTimeStr,
                            'updated_at' => $confirmedAt,
                        ];

                        foreach ($orderMenus as $entry) {
                            $m = $entry['menu'];
                            $qty = $entry['qty'];
                            $itemsBatch[] = [
                                'order_id' => $nextOrderId,
                                'menu_id' => $m->id,
                                'menu_name_snapshot' => $m->name,
                                'menu_price_snapshot' => (int) $m->price,
                                'quantity' => $qty,
                                'subtotal' => (int) $m->price * $qty,
                                'note' => null,
                                'created_at' => $orderTimeStr,
                                'updated_at' => $orderTimeStr,
                            ];
                        }

                        $logsBatch[] = [
                            'order_id' => $nextOrderId,
                            'actor_user_id' => $stall->user_id,
                            'actor_type' => 'seller',
                            'old_order_status' => 'siap',
                            'new_order_status' => 'selesai',
                            'old_payment_status' => 'terkonfirmasi',
                            'new_payment_status' => 'terkonfirmasi',
                            'note' => 'Pesanan selesai diserahkan kepada pembeli.',
                            'created_at' => $confirmedAt,
                        ];

                        $paymentsBatch[] = [
                            'order_id' => $nextOrderId,
                            'seller_id' => $sellerId,
                            'amount' => $subtotal,
                            'confirmed_by' => $stall->user_id,
                            'merchant_reference' => 'QRIS-' . $orderNumber,
                            'confirmed_at' => $confirmedAt,
                            'created_at' => $confirmedAt,
                            'updated_at' => $confirmedAt,
                        ];
                    }
                }

                // Tambahkan 2-4 transaksi DIBATALKAN per tenant/hari (~3-4% untuk uji aturan AI: batal != terjual)
                $cancelledCount = mt_rand(2, 4);
                $allMenus = array_merge($foods, $drinks);
                for ($c = 0; $c < $cancelledCount; $c++) {
                    $m = $allMenus[mt_rand(0, count($allMenus) - 1)];
                    $dailyOrderSeq++;
                    $orderNumber = sprintf('NC-%s-%04d', $ymd, $dailyOrderSeq);
                    $hour = $this->weightedRandomHour($hourWeights);
                    $orderTime = $currentDate->copy()->setTime($hour, mt_rand(0, 59), mt_rand(0, 59));
                    $orderTimeStr = $orderTime->format('Y-m-d H:i:s');
                    $buyer = $buyers[mt_rand(0, $buyerCount - 1)];
                    $buyerFaculty = $buyerFaculties[$buyer->id] ?? 'Teknik';
                    $price = (int) $m->price;
                    $itemType = $dailyAgg[$m->id]['item_type'];

                    $dailyAgg[$m->id]['cancelled'] += 1;

                    fputcsv($rawFp, [
                        $orderNumber,
                        $orderTimeStr,
                        $dateStr,
                        $hour,
                        $dow,
                        $dayName,
                        $regime,
                        $weather,
                        $isEndOfMonth,
                        $hasCampusEvent,
                        $sellerId,
                        $stall->stall_name,
                        $buyer->id,
                        $buyerFaculty,
                        $m->id,
                        $m->name,
                        $itemType,
                        $price,
                        1,
                        $price,
                        'dibatalkan',
                        'dibatalkan',
                    ]);

                    if ($insertToMysql) {
                        $nextOrderId++;
                        $ordersBatch[] = [
                            'id' => $nextOrderId,
                            'order_number' => $orderNumber,
                            'buyer_id' => $buyer->id,
                            'access_token' => bin2hex(random_bytes(16)),
                            'seller_id' => $sellerId,
                            'customer_name' => $buyer->name,
                            'order_status' => 'dibatalkan',
                            'payment_status' => 'dibatalkan',
                            'subtotal' => $price,
                            'total' => $price,
                            'payment_deadline' => $orderTime->copy()->addMinutes(15)->format('Y-m-d H:i:s'),
                            'note' => 'Dibatalkan otomatis karena melewati batas waktu pembayaran QRIS.',
                            'created_at' => $orderTimeStr,
                            'updated_at' => $orderTimeStr,
                        ];

                        $itemsBatch[] = [
                            'order_id' => $nextOrderId,
                            'menu_id' => $m->id,
                            'menu_name_snapshot' => $m->name,
                            'menu_price_snapshot' => $price,
                            'quantity' => 1,
                            'subtotal' => $price,
                            'note' => null,
                            'created_at' => $orderTimeStr,
                            'updated_at' => $orderTimeStr,
                        ];

                        $logsBatch[] = [
                            'order_id' => $nextOrderId,
                            'actor_user_id' => null,
                            'actor_type' => 'system',
                            'old_order_status' => 'dibuat',
                            'new_order_status' => 'dibatalkan',
                            'old_payment_status' => 'menunggu',
                            'new_payment_status' => 'dibatalkan',
                            'note' => 'Pesanan kadaluarsa (tidak dikonfirmasi dalam 15 menit).',
                            'created_at' => $orderTimeStr,
                        ];
                    }
                }

                // Tulis agregasi harian ke sales_daily_per_menu.csv
                foreach ($dailyAgg as $menuId => $row) {
                    $m = $row['menu'];
                    $quota = max(1, (int) $m->stock);
                    $sold = $row['completed'];
                    $util = round(($sold / $quota) * 100, 1);
                    $stockout = $sold >= $quota ? 1 : 0;

                    fputcsv($dailyFp, [
                        $dateStr,
                        $dow,
                        $dayName,
                        $isWeekend,
                        $isEndOfMonth,
                        $weather,
                        $hasCampusEvent,
                        $regime,
                        $sellerId,
                        $stall->stall_name,
                        $menuId,
                        $m->name,
                        $row['item_type'],
                        (int) $m->price,
                        $quota,
                        $sold,
                        $row['cancelled'],
                        $row['revenue'],
                        $util,
                        $stockout,
                    ]);

                    $dailyHistoryForPrediction[$menuId][] = [
                        'date' => $dateStr,
                        'seller_id' => $sellerId,
                        'actual' => $sold,
                    ];
                }

                // Tulis agregasi per jam ke sales_hourly_per_menu.csv
                ksort($hourlyAgg);
                foreach ($hourlyAgg as $hRow) {
                    $m = $hRow['menu'];
                    fputcsv($hourlyFp, [
                        $dateStr,
                        $hRow['hour'],
                        $dow,
                        $regime,
                        $weather,
                        $sellerId,
                        $stall->stall_name,
                        $m->id,
                        $m->name,
                        $hRow['item_type'],
                        $hRow['qty'],
                        $hRow['rev'],
                    ]);

                    if ($hRow['hour'] === 12) {
                        $hourlyHistoryForPrediction[$m->id][] = [
                            'date' => $dateStr,
                            'seller_id' => $sellerId,
                            'actual' => $hRow['qty'],
                        ];
                    }
                }
            }

            // Flush batch MySQL per hari (chunk 500)
            if ($insertToMysql && !empty($ordersBatch)) {
                foreach (array_chunk($ordersBatch, 500) as $chunk) {
                    DB::table('orders')->insert($chunk);
                }
                foreach (array_chunk($itemsBatch, 500) as $chunk) {
                    DB::table('order_items')->insert($chunk);
                }
                foreach (array_chunk($logsBatch, 500) as $chunk) {
                    DB::table('order_status_logs')->insert($chunk);
                }
                foreach (array_chunk($paymentsBatch, 500) as $chunk) {
                    DB::table('payment_confirmations')->insert($chunk);
                }
            }
        }

        fclose($rawFp);
        fclose($dailyFp);
        fclose($hourlyFp);

        // 2. Tambahkan pesanan aktif HARI INI untuk demo live dashboard pembeli & 5 penjual
        $this->seedTodayLiveOrders($stalls, $buyers, $nextOrderId);

        // 3. Isi sample tabel AI: prediction_logs (daily & hourly) & recommendation_logs
        $this->seedPredictionLogs($dailyHistoryForPrediction, $hourlyHistoryForPrediction);
        $this->seedRecommendationLogs($buyers);
    }

    /**
     * Alokasi porsi terjual per menu sesuai bobot preferensi jurnal Ramadhan & Ruhana (2026)
     * dengan penyesuaian cuaca & tanggal tua serta memastikan tidak melebihi stok harian menu.
     */
    private function allocateMenuQuantities(array $menus, int $targetTotal, string $weather, bool $isEndOfMonth): array
    {
        $weights = [];
        $caps = [];
        foreach ($menus as $m) {
            $baseWeight = $this->journalPopularityWeight($m->name);

            // Efek cuaca hujan: menu berkuah & kopi sedikit lebih diminati; minuman dingin sedikit turun
            $nameLower = strtolower($m->name);
            if ($weather === 'hujan') {
                if (str_contains($nameLower, 'soto') || str_contains($nameLower, 'rawon') || str_contains($nameLower, 'kopi') || str_contains($nameLower, 'sayur')) {
                    $baseWeight *= 1.25;
                } elseif (str_contains($nameLower, 'es ')) {
                    $baseWeight *= 0.85;
                }
            }

            // Efek tanggal tua: menu <= Rp12.000 & Air Mineral lebih dicari mahasiswa
            if ($isEndOfMonth) {
                if ((int) $m->price <= 12000 && !str_contains($nameLower, 'es susu')) {
                    $baseWeight *= 1.18;
                } elseif ((int) $m->price >= 15000 || str_contains($nameLower, 'es susu')) {
                    $baseWeight *= 0.85;
                }
            }

            // Noise harian realistis (±12%)
            $noise = mt_rand(88, 112) / 100;
            $weights[$m->id] = max(0.5, $baseWeight * $noise);
            $caps[$m->id] = max(1, (int) $m->stock);
        }

        $maxPossible = array_sum($caps);
        $remaining = min($targetTotal, $maxPossible);
        $alloc = array_fill_keys(array_keys($caps), 0);

        // Distribusikan secara proporsional mengikuti kapasitas stok masing-masing menu
        while ($remaining > 0) {
            $openWeightSum = 0.0;
            foreach ($caps as $id => $cap) {
                if ($alloc[$id] < $cap) {
                    $openWeightSum += $weights[$id];
                }
            }
            if ($openWeightSum <= 0) {
                break;
            }

            $distributed = 0;
            foreach ($caps as $id => $cap) {
                if ($alloc[$id] >= $cap || $remaining <= 0) {
                    continue;
                }
                $share = (int) floor($remaining * ($weights[$id] / $openWeightSum));
                $room = $cap - $alloc[$id];
                $add = min($share, $room, $remaining);
                if ($add > 0) {
                    $alloc[$id] += $add;
                    $remaining -= $add;
                    $distributed += $add;
                }
            }

            // Sisa pembulatan dibagi 1 per 1 ke menu dengan bobot tertinggi yang masih punya ruang stok
            if ($distributed === 0 && $remaining > 0) {
                arsort($weights);
                foreach ($weights as $id => $_w) {
                    if ($remaining <= 0) {
                        break;
                    }
                    if ($alloc[$id] < $caps[$id]) {
                        $alloc[$id]++;
                        $remaining--;
                    }
                }
            }
        }

        return $alloc;
    }

    /**
     * Bobot preferensi menu dikalibrasi dari Tabel 3 & Tabel 4 Jurnal Ramadhan & Ruhana (2026)
     * (khususnya responden Foodcourt Baseball UNESA 1 Ketintang).
     */
    private function journalPopularityWeight(string $menuName): float
    {
        $n = strtolower($menuName);

        return match (true) {
            // Minuman (Tabel 4 FC Ketintang: Es Teh n=36, Air Mineral n=34, Es Coklat/Susu n=15, Nutrisari n=9)
            str_contains($n, 'es teh') => 35.0,
            str_contains($n, 'air mineral') => 34.0,
            str_contains($n, 'nutrisari') => 16.0,
            str_contains($n, 'susu coklat') => 15.0,
            str_contains($n, 'kopi') => 11.0,

            // Makanan (Tabel 3 FC Ketintang: Geprek n=34, Soto n=14, Nasgor n=14, Campur n=9, Kremes n=9, Katsu n=6, Penyetan n=5, Pecel n=5, Gado-gado n=2)
            str_contains($n, 'geprek') => 34.0,
            str_contains($n, 'nasi goreng') => 24.0,
            str_contains($n, 'soto') => 20.0,
            str_contains($n, 'nasi campur') => 18.0,
            str_contains($n, 'kremes') => 17.0,
            str_contains($n, 'crazy rice') => 16.0,
            str_contains($n, 'bakar') => 15.0,
            str_contains($n, 'katsu') => 14.0,
            str_contains($n, 'penyetan') => 13.0,
            str_contains($n, 'pecel') => 13.0,
            str_contains($n, 'capjae') => 11.0,
            str_contains($n, 'rawon') => 11.0,
            str_contains($n, 'sayur') => 10.0,
            str_contains($n, 'gado') => 9.0,
            default => 12.0,
        };
    }

    private function addMenuToCart(array &$orderMenus, Menu $menu): void
    {
        if (!isset($orderMenus[$menu->id])) {
            $orderMenus[$menu->id] = ['menu' => $menu, 'qty' => 0];
        }
        $orderMenus[$menu->id]['qty']++;
    }

    private function weightedRandomHour(array $hourWeights): int
    {
        $total = array_sum($hourWeights);
        $roll = mt_rand(1, $total);
        $cum = 0;
        foreach ($hourWeights as $hour => $w) {
            $cum += $w;
            if ($roll <= $cum) {
                return $hour;
            }
        }

        return 12;
    }

    private function deterministicShuffle(array &$items): void
    {
        $count = count($items);
        for ($i = $count - 1; $i > 0; $i--) {
            $j = mt_rand(0, $i);
            [$items[$i], $items[$j]] = [$items[$j], $items[$i]];
        }
    }

    private function mapBuyerFaculties($buyers): array
    {
        $faculties = [];
        foreach ($buyers as $idx => $b) {
            $faculties[$b->id] = match (true) {
                $idx < 8 => 'Teknik',
                $idx < 13 => 'MIPA',
                $idx < 16 => 'FISIPOL',
                $idx < 18 => 'FEB',
                default => 'Vokasi',
            };
        }

        return $faculties;
    }

    private function nutritionLookup(): array
    {
        return [
            'geprek' => [484.6, 25.6, 14.5, 62.4, 1],
            'soto' => [452.4, 19.1, 12.8, 62.0, 1],
            'nasi goreng' => [360.5, 15.4, 31.8, 27.8, 1],
            'nasi campur' => [499.6, 24.3, 18.8, 57.1, 1],
            'air mineral' => [0.0, 0.0, 0.0, 0.0, 1],
            'es teh' => [140.4, 0.0, 0.0, 36.0, 1],
            'nutrisari' => [120.0, 0.0, 0.0, 28.0, 1],
            'susu coklat' => [174.5, 1.8, 8.4, 27.0, 1],
        ];
    }

    private function exportMenusMasterCsv(string $path, $stalls, array $nutritionMap): void
    {
        $fp = fopen($path, 'w');
        fputcsv($fp, [
            'menu_id',
            'seller_id',
            'stall_name',
            'category_slug',
            'item_type',
            'menu_name',
            'price_idr',
            'estimated_time_minutes',
            'daily_stock_quota',
            'taste_tags',
            'journal_popularity_weight',
            'energy_kcal',
            'protein_g',
            'fat_g',
            'carbs_g',
            'is_journal_measured',
        ]);

        foreach ($stalls as $stall) {
            foreach ($stall->menus as $m) {
                $n = strtolower($m->name);
                $nut = ['', '', '', '', 0];
                foreach ($nutritionMap as $key => $vals) {
                    if (str_contains($n, $key)) {
                        $nut = $vals;
                        break;
                    }
                }
                $catSlug = $m->category?->slug ?? 'makanan';
                $itemType = in_array($catSlug, ['minuman', 'kopi'], true) ? 'minuman' : 'makanan';

                fputcsv($fp, [
                    $m->id,
                    $stall->id,
                    $stall->stall_name,
                    $catSlug,
                    $itemType,
                    $m->name,
                    (int) $m->price,
                    (int) $m->estimated_time_minutes,
                    (int) $m->stock,
                    is_array($m->taste_tags) ? implode('|', $m->taste_tags) : '',
                    $this->journalPopularityWeight($m->name),
                    $nut[0],
                    $nut[1],
                    $nut[2],
                    $nut[3],
                    $nut[4],
                ]);
            }
        }

        fclose($fp);
    }

    /**
     * Pesanan aktif hari ini agar demo live dashboard Pembeli & ke-5 Penjual langsung hidup.
     */
    private function seedTodayLiveOrders($stalls, $buyers, int &$nextOrderId): void
    {
        $todayStr = Carbon::today()->format('Ymd');
        $seq = 9001;

        $statuses = [
            ['order_status' => 'dibuat', 'payment_status' => 'perlu_pemeriksaan', 'mins_ago' => 8, 'confirmed' => false, 'note' => 'Makan di Tempat - Meja 08. Sambal pedas dipisah ya bu.'],
            ['order_status' => 'diproses', 'payment_status' => 'terkonfirmasi', 'mins_ago' => 16, 'confirmed' => true, 'note' => 'Makan di Tempat - Meja 12.'],
            ['order_status' => 'siap', 'payment_status' => 'terkonfirmasi', 'mins_ago' => 24, 'confirmed' => true, 'note' => 'Bungkus / Bawa Pulang.'],
        ];

        $orders = [];
        $items = [];
        $logs = [];
        $payments = [];

        foreach ($stalls as $sIdx => $stall) {
            $food = $stall->menus->first(fn($m) => !in_array($m->category?->slug, ['minuman', 'kopi'], true));
            $drink = $stall->menus->first(fn($m) => in_array($m->category?->slug, ['minuman', 'kopi'], true));
            if (!$food) {
                continue;
            }

            foreach ($statuses as $stIdx => $st) {
                $buyer = $buyers[($sIdx + $stIdx) % $buyers->count()];
                $nextOrderId++;
                $seq++;
                $orderNumber = sprintf('NC-%s-%04d', $todayStr, $seq);
                $createdAt = now()->subMinutes($st['mins_ago']);
                $createdStr = $createdAt->format('Y-m-d H:i:s');

                $subtotal = (int) $food->price + ($drink ? (int) $drink->price : 0);

                $orders[] = [
                    'id' => $nextOrderId,
                    'order_number' => $orderNumber,
                    'buyer_id' => $buyer->id,
                    'access_token' => Str::random(32),
                    'seller_id' => $stall->id,
                    'customer_name' => $buyer->name,
                    'order_status' => $st['order_status'],
                    'payment_status' => $st['payment_status'],
                    'subtotal' => $subtotal,
                    'total' => $subtotal,
                    'payment_deadline' => now()->addMinutes(15)->format('Y-m-d H:i:s'),
                    'note' => $st['note'],
                    'created_at' => $createdStr,
                    'updated_at' => $createdStr,
                ];

                $items[] = [
                    'order_id' => $nextOrderId,
                    'menu_id' => $food->id,
                    'menu_name_snapshot' => $food->name,
                    'menu_price_snapshot' => (int) $food->price,
                    'quantity' => 1,
                    'subtotal' => (int) $food->price,
                    'note' => null,
                    'created_at' => $createdStr,
                    'updated_at' => $createdStr,
                ];

                if ($drink) {
                    $items[] = [
                        'order_id' => $nextOrderId,
                        'menu_id' => $drink->id,
                        'menu_name_snapshot' => $drink->name,
                        'menu_price_snapshot' => (int) $drink->price,
                        'quantity' => 1,
                        'subtotal' => (int) $drink->price,
                        'note' => null,
                        'created_at' => $createdStr,
                        'updated_at' => $createdStr,
                    ];
                }

                $logs[] = [
                    'order_id' => $nextOrderId,
                    'actor_user_id' => $st['confirmed'] ? $stall->user_id : $buyer->id,
                    'actor_type' => $st['confirmed'] ? 'seller' : 'buyer',
                    'old_order_status' => 'dibuat',
                    'new_order_status' => $st['order_status'],
                    'old_payment_status' => 'menunggu',
                    'new_payment_status' => $st['payment_status'],
                    'note' => $st['confirmed']
                        ? 'Pembayaran QRIS terverifikasi, pesanan diproses dapur.'
                        : 'Pembeli telah mengonfirmasi transfer QRIS statis stan.',
                    'created_at' => $createdStr,
                ];

                if ($st['confirmed']) {
                    $payments[] = [
                        'order_id' => $nextOrderId,
                        'seller_id' => $stall->id,
                        'amount' => $subtotal,
                        'confirmed_by' => $stall->user_id,
                        'merchant_reference' => 'QRIS-' . $orderNumber,
                        'confirmed_at' => $createdStr,
                        'created_at' => $createdStr,
                        'updated_at' => $createdStr,
                    ];
                }
            }
        }

        if (!empty($orders)) {
            DB::table('orders')->insert($orders);
            DB::table('order_items')->insert($items);
            DB::table('order_status_logs')->insert($logs);
            DB::table('payment_confirmations')->insert($payments);
        }
    }

    /**
     * Isi tabel prediction_logs dengan baseline 7-day moving average (daily & hourly)
     * agar tabel AI Demand Forecasting terisi contoh evaluasi prediksi vs aktual.
     */
    private function seedPredictionLogs(array $dailyHistory, array $hourlyHistory): void
    {
        $rows = [];
        $now = now()->format('Y-m-d H:i:s');

        foreach ($dailyHistory as $menuId => $series) {
            $n = count($series);
            if ($n < 10) {
                continue;
            }
            // Evaluasi 5 hari operasional terakhir menggunakan 7-day moving average
            for ($i = $n - 5; $i < $n; $i++) {
                $window = array_slice($series, max(0, $i - 7), 7);
                $avg = array_sum(array_column($window, 'actual')) / max(1, count($window));
                $rows[] = [
                    'seller_id' => $series[$i]['seller_id'],
                    'menu_id' => $menuId,
                    'prediction_date' => $series[$i]['date'],
                    'period_type' => 'daily',
                    'predicted_quantity' => max(1, (int) round($avg)),
                    'actual_quantity' => $series[$i]['actual'],
                    'model_version' => 'ma7-baseline-v1',
                    'input_data_points' => count($window),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // Tambah sampel prediksi jam puncak (12:00) untuk 2 hari terakhir
        foreach ($hourlyHistory as $menuId => $series) {
            $n = count($series);
            if ($n < 8) {
                continue;
            }
            for ($i = $n - 2; $i < $n; $i++) {
                $window = array_slice($series, max(0, $i - 7), 7);
                $avg = array_sum(array_column($window, 'actual')) / max(1, count($window));
                $rows[] = [
                    'seller_id' => $series[$i]['seller_id'],
                    'menu_id' => $menuId,
                    'prediction_date' => $series[$i]['date'],
                    'period_type' => 'hourly',
                    'predicted_quantity' => max(1, (int) round($avg)),
                    'actual_quantity' => $series[$i]['actual'],
                    'model_version' => 'ma7-peak-hour-v1',
                    'input_data_points' => count($window),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('prediction_logs')->insert($chunk);
        }
    }

    /**
     * Isi 60 sampel log pencarian Smart Menu Recommendation sesuai pola budget & rasa mahasiswa UNESA.
     */
    private function seedRecommendationLogs($buyers): void
    {
        $menus = Menu::where('is_active', true)->where('stock', '>', 0)->get();
        $budgets = [12000, 13000, 15000, 18000, 20000];
        $tastes = ['pedas', 'gurih', 'manis', 'berkuah', 'segar'];
        $maxWaits = [3, 4, 5, 6, 8];

        $rows = [];
        for ($i = 0; $i < 60; $i++) {
            $buyer = $buyers[$i % $buyers->count()];
            $budget = $budgets[mt_rand(0, count($budgets) - 1)];
            $taste = $tastes[mt_rand(0, count($tastes) - 1)];
            $maxWait = $maxWaits[mt_rand(0, count($maxWaits) - 1)];

            $matched = $menus->filter(function (Menu $m) use ($budget, $taste, $maxWait) {
                $tags = is_array($m->taste_tags) ? array_map('strtolower', $m->taste_tags) : [];

                return (int) $m->price <= $budget
                    && (int) $m->estimated_time_minutes <= $maxWait
                    && in_array($taste, $tags, true);
            })->take(5)->pluck('id')->values()->all();

            $rows[] = [
                'user_id' => $buyer->id,
                'session_id' => 'sess-' . Str::lower(Str::random(16)),
                'budget' => $budget,
                'taste_preference' => $taste,
                'max_wait_minutes' => $maxWait,
                'recommended_menu_ids' => json_encode($matched),
                'created_at' => now()->subHours(mt_rand(1, 336))->format('Y-m-d H:i:s'),
            ];
        }

        DB::table('recommendation_logs')->insert($rows);
    }
}
