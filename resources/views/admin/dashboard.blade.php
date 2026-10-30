@extends('layouts.admin')

@section('title', 'Dashboard Pengelola — NesaCourt')

@section('content')
<div class="space-y-6" x-data="{
    dateFilter: 'hari_ini',
    stalls: {{ Js::from($stallsData ?? []) }},
    liveOrders: {{ Js::from($recentOrdersData ?? []) }}
}">

    <!-- Welcome & Operational Header Card (Identik dengan Tema Desain Dashboard Penjual) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-7 shadow-xs space-y-5">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3.5">
                <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&w=120&q=80" 
                     alt="Foto Profil Admin" 
                     class="w-12 h-12 rounded-xl object-cover ring-1 ring-slate-200 shrink-0">
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">Halo, {{ auth()->user()->name ?? 'Pengelola Food Court' }}</h1>
                        <span class="text-slate-300 font-normal">|</span>
                        <span class="text-xs font-semibold text-brand-600">Kepala Pengelola Food Court</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Monitoring Operasional & Arus Keuangan | Kampus UNESA Ketintang</p>
                </div>
            </div>

            <!-- Operating Status Indicator (Tanpa Container, Rapi) -->
            <div class="flex items-center gap-2 text-xs text-slate-600 shrink-0 self-start md:self-auto">
                <span class="relative flex h-2 w-2 shrink-0">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <span>Kantin Ketintang Aktif | 08.00 - 17.00 WIB</span>
            </div>
        </div>

        <!-- Direct Fast Shortcuts -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <a href="{{ url('/admin/pesanan') }}" class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 hover:border-brand-200 hover:bg-brand-50/40 transition flex items-center gap-2.5 group">
                <div class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-700 group-hover:text-brand-600 shrink-0 shadow-2xs">
                    <i data-lucide="shopping-bag" class="w-4 h-4 text-slate-900"></i>
                </div>
                <div class="min-w-0">
                    <span class="text-xs font-bold text-slate-900 block truncate group-hover:text-brand-600">Monitoring Pesanan</span>
                    <span class="text-[10px] text-amber-600 font-semibold block truncate">Live Arus Pesanan</span>
                </div>
            </a>

            <a href="{{ url('/admin/stan') }}" class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 hover:border-brand-200 hover:bg-brand-50/40 transition flex items-center gap-2.5 group">
                <div class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-700 group-hover:text-brand-600 shrink-0 shadow-2xs">
                    <i data-lucide="store" class="w-4 h-4 text-slate-900"></i>
                </div>
                <div class="min-w-0">
                    <span class="text-xs font-bold text-slate-900 block truncate group-hover:text-brand-600">Manajemen Stan</span>
                    <span class="text-[10px] text-slate-400 block truncate">4 Stan Terdaftar</span>
                </div>
            </a>

            <a href="{{ url('/admin/menu') }}" class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 hover:border-brand-200 hover:bg-brand-50/40 transition flex items-center gap-2.5 group">
                <div class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-700 group-hover:text-brand-600 shrink-0 shadow-2xs">
                    <i data-lucide="utensils" class="w-4 h-4 text-slate-900"></i>
                </div>
                <div class="min-w-0">
                    <span class="text-xs font-bold text-slate-900 block truncate group-hover:text-brand-600">Monitoring Menu</span>
                    <span class="text-[10px] text-slate-400 block truncate">Katalog Seluruh Stan</span>
                </div>
            </a>

            <a href="{{ url('/admin/laporan') }}" class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 hover:border-brand-200 hover:bg-brand-50/40 transition flex items-center gap-2.5 group">
                <div class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-700 group-hover:text-brand-600 shrink-0 shadow-2xs">
                    <i data-lucide="bar-chart-3" class="w-4 h-4 text-slate-900"></i>
                </div>
                <div class="min-w-0">
                    <span class="text-xs font-bold text-slate-900 block truncate group-hover:text-brand-600">Laporan Keuangan</span>
                    <span class="text-[10px] text-slate-400 block truncate">Omzet & Rekap Sewa</span>
                </div>
            </a>
        </div>
    </div>

    <!-- 4 Key Statistics Cards (Card Info Samain Desain Halaman Penjual) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 shadow-xs">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <!-- Card 1: Total Omzet -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Total Omzet Hari Ini</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5">Rp {{ number_format($omsetToday ?? 0, 0, ',', '.') }}</span>
                <span class="text-[10px] text-slate-500 block mt-0.5">Akumulasi transaksi valid</span>
            </div>

            <!-- Card 2: Pesanan Selesai / Masuk -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Pesanan Selesai / Total</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5">{{ $ordersFinished ?? 0 }} / {{ $ordersTotal ?? 0 }}</span>
                <span class="text-[10px] text-slate-500 block mt-0.5">{{ max(0, ($ordersTotal ?? 0) - ($ordersFinished ?? 0)) }} pesanan aktif</span>
            </div>

            <!-- Card 3: Stan Beroperasi Buka -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Stan Beroperasi Buka</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5">{{ $openStallsCount ?? 0 }} dari {{ $totalStallsCount ?? 0 }} Stan</span>
                <span class="text-[10px] text-slate-500 block mt-0.5">{{ $totalStallsCount ? round(($openStallsCount / $totalStallsCount) * 100) : 0 }}% counter aktif</span>
            </div>

            <!-- Card 4: Rata-rata Waktu Saji -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Rata-rata Waktu Saji</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5">~{{ $avgPrepTime ?? 6 }} Menit</span>
                <span class="text-[10px] text-slate-500 block mt-0.5">Layanan cepat & lancar</span>
            </div>
        </div>
    </div>

    <!-- Active Stalls Real-time Status Grid -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 space-y-5 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
            <div>
                <div class="flex items-center gap-2">
                    <i data-lucide="store" class="w-4 h-4 text-slate-900"></i>
                    <h2 class="text-sm sm:text-base font-bold text-slate-900">Status & Performa Stan Kantin Ketintang</h2>
                </div>
                <p class="text-xs text-slate-500 mt-1">Pemantauan status operasional, volume penjualan, dan omzet masing-masing stan di Counter Barat & Timur.</p>
            </div>

            <!-- Periode Dropdown dengan Panah Agak ke Kiri Biar Kanan Ada Space -->
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-500 font-medium">Periode:</span>
                <div class="relative inline-block">
                    <select x-model="dateFilter" class="appearance-none text-xs font-semibold py-2 pl-3.5 pr-10 rounded-xl border border-slate-200 bg-white text-slate-800 shadow-2xs focus:ring-2 focus:ring-brand-500 cursor-pointer">
                        <option value="hari_ini">Hari Ini (25 Okt 2026)</option>
                        <option value="minggu_ini">Minggu Ini (20 - 25 Okt)</option>
                        <option value="bulan_ini">Bulan Ini (Oktober 2026)</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-3.5 flex items-center text-slate-400">
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5" stroke-width="1.5"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <template x-for="stall in stalls" :key="stall.id">
                <div class="p-4 rounded-xl border border-slate-200/90 bg-white hover:border-slate-300 transition space-y-3.5 shadow-2xs">
                    <!-- Stall Cover (Tulisan Buka di Gambar Dihilangkan Sesuai Instruksi) -->
                    <div class="rounded-lg overflow-hidden h-28 bg-slate-100">
                        <img :src="stall.stallImage" :alt="stall.name" class="w-full h-full object-cover">
                    </div>

                    <!-- Stall Title & Location -->
                    <div>
                        <h3 class="text-xs font-bold text-slate-900 truncate" x-text="stall.name"></h3>
                        <p class="text-[11px] text-slate-500 mt-0.5" x-text="stall.location"></p>
                    </div>

                    <!-- Owner Profile Info -->
                    <div class="flex items-center gap-2 pt-2 border-t border-slate-100">
                        <img :src="stall.ownerImage" :alt="stall.owner" class="w-7 h-7 rounded-lg object-cover ring-1 ring-slate-200 shrink-0">
                        <div class="min-w-0">
                            <span class="text-[11px] font-bold text-slate-800 block truncate" x-text="stall.owner"></span>
                            <span class="text-[10px] text-slate-400 block truncate">Pemilik Stan</span>
                        </div>
                    </div>

                    <!-- Today Summary Numbers -->
                    <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
                        <div>
                            <span class="text-[10px] text-slate-400 block">Pesanan</span>
                            <span class="font-bold text-slate-800" x-text="stall.todayOrders + ' Porsi'"></span>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] text-slate-400 block">Omzet</span>
                            <span class="font-bold text-brand-700" x-text="stall.todayRevenue"></span>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Live Orders Feed & Top Menu Leaderboard -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left 2 Cols: Live Order Stream -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 space-y-4 shadow-xs">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <i data-lucide="activity" class="w-4 h-4 text-slate-900"></i>
                    <h2 class="text-sm sm:text-base font-bold text-slate-900">Arus Pesanan Terkini (Live Feed)</h2>
                </div>
                <a href="{{ url('/admin/pesanan') }}" class="whitespace-nowrap inline-flex items-center gap-1 text-xs font-semibold text-brand-600 hover:text-brand-700">
                    <span>Lihat Semua</span>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>

            <div class="divide-y divide-slate-100">
                <template x-for="order in liveOrders" :key="order.id">
                    <div class="py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <!-- Food Photo -->
                            <img :src="order.menuImage" :alt="order.menuName" class="w-12 h-12 rounded-xl object-cover ring-1 ring-slate-200 shrink-0 shadow-2xs">
                            
                            <div class="min-w-0 space-y-0.5">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-slate-900 truncate" x-text="order.menuName"></span>
                                    <span class="text-[10px] text-slate-400" x-text="'(' + order.table + ')'"></span>
                                </div>
                                <div class="flex items-center gap-2 text-[11px] text-slate-500">
                                    <span class="font-semibold text-brand-700" x-text="order.stallName"></span>
                                    <span class="text-slate-300 font-normal">|</span>
                                    <span x-text="order.time"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Buyer Info & Status (Tanpa Container Pembungkus) -->
                        <div class="flex items-center justify-between sm:justify-end gap-3 shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-50">
                            <div class="flex items-center gap-2">
                                <img :src="order.buyerImage" :alt="order.buyerName" class="w-6 h-6 rounded-full object-cover ring-1 ring-slate-200 shrink-0">
                                <span class="text-xs text-slate-700 font-medium" x-text="order.buyerName"></span>
                            </div>

                            <!-- Tulisan Status Tanpa Container Pembungkus -->
                            <span class="text-xs font-semibold shrink-0"
                                  :class="{
                                      'text-amber-600': order.statusCode === 'proses',
                                      'text-emerald-600': order.statusCode === 'siap',
                                      'text-slate-600': order.statusCode === 'selesai'
                                  }"
                                  x-text="order.status">
                            </span>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Right 1 Col: Top Selling Leaderboard -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 space-y-4 shadow-xs">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <i data-lucide="award" class="w-4 h-4 text-slate-900"></i>
                <h2 class="text-sm sm:text-base font-bold text-slate-900">Menu Terlaris Hari Ini</h2>
            </div>

            <div class="space-y-3.5">
                <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                    <img src="https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?auto=format&fit=crop&w=300&q=80" 
                         alt="Ayam Geprek Sambal Bawang" 
                         class="w-12 h-12 rounded-lg object-cover ring-1 ring-slate-200 shrink-0">
                    <div class="flex-1 min-w-0">
                        <span class="text-xs font-bold text-slate-900 block truncate">Ayam Geprek Sambal Bawang</span>
                        <span class="text-[10px] text-slate-500 block truncate">Stan 01 - Barokah Rasa</span>
                        <div class="flex items-center justify-between text-[11px] font-semibold mt-1">
                            <span class="text-brand-700">Rp 13.000</span>
                            <span class="text-slate-600">34 Terjual</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                    <img src="https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?auto=format&fit=crop&w=300&q=80" 
                         alt="Es Teh Manis Jumbo Vokasi" 
                         class="w-12 h-12 rounded-lg object-cover ring-1 ring-slate-200 shrink-0">
                    <div class="flex-1 min-w-0">
                        <span class="text-xs font-bold text-slate-900 block truncate">Es Teh Manis Jumbo</span>
                        <span class="text-[10px] text-slate-500 block truncate">Stan 04 - Aneka Jus Vokasi</span>
                        <div class="flex items-center justify-between text-[11px] font-semibold mt-1">
                            <span class="text-brand-700">Rp 4.000</span>
                            <span class="text-slate-600">45 Terjual</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                    <img src="https://images.unsplash.com/photo-1541832676-9b763b0239ab?auto=format&fit=crop&w=300&q=80" 
                         alt="Soto Ayam Lamongan Koya" 
                         class="w-12 h-12 rounded-lg object-cover ring-1 ring-slate-200 shrink-0">
                    <div class="flex-1 min-w-0">
                        <span class="text-xs font-bold text-slate-900 block truncate">Soto Ayam Lamongan Koya</span>
                        <span class="text-[10px] text-slate-500 block truncate">Stan 02 - Soto Bu Siti</span>
                        <div class="flex items-center justify-between text-[11px] font-semibold mt-1">
                            <span class="text-brand-700">Rp 14.000</span>
                            <span class="text-slate-600">28 Terjual</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
