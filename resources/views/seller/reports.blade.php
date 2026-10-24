@extends('layouts.seller')

@section('title', 'Riwayat Penjualan & Laporan — NesaCourt')

@section('content')
<div class="space-y-6" x-data="{
    period: 'hari_ini',
    exportModal: false
}">

    <!-- Page Header & Action Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-7 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                    <a href="{{ url('/penjual/dashboard') }}" class="hover:text-brand-600">Portal Stan</a>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-900"></i>
                    <span class="text-slate-800 font-semibold">Riwayat Penjualan</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-semibold text-slate-900 tracking-tight">Laporan Penjualan & Rekap Omset</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-2 max-w-xl">Pantau performa penjualan masakan harian, volume pesanan, dan rekap pencairan dana QRIS Stan 01.</p>
            </div>

            <!-- Buttons: On mobile displayed below description, on desktop aligned to the right -->
            <div class="flex items-center gap-2.5 shrink-0 pt-1 sm:pt-0">
                <button type="button" 
                        onclick="window.print()" 
                        class="flex-1 sm:flex-none h-10 py-2.5 px-4 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                    <i data-lucide="printer" class="w-4 h-4 text-slate-900"></i>
                    <span>Cetak Rekap Kasir</span>
                </button>
                <button type="button" 
                        @click="exportModal = true" 
                        class="flex-1 sm:flex-none h-10 py-2.5 px-4 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-700 text-white shadow-sm shadow-brand-500/20 transition">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    <span>Unduh Excel / CSV</span>
                </button>
            </div>
        </div>

        <!-- Micro Summary Counters (Hitam Semua, Semi Bold & Rata Tengah) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t border-slate-100">
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Total Omset Bersih</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5" x-text="period === 'hari_ini' ? 'Rp 485.000' : (period === '7_hari' ? 'Rp 3.420.000' : 'Rp 14.850.000')">Rp 485.000</span>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Pesanan Berhasil</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5" x-text="period === 'hari_ini' ? '32 Pesanan' : (period === '7_hari' ? '228 Pesanan' : '990 Pesanan')">32 Pesanan</span>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Porsi Masakan Laku</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5" x-text="period === 'hari_ini' ? '46 Porsi' : (period === '7_hari' ? '312 Porsi' : '1.340 Porsi')">46 Porsi</span>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Rata-rata Nilai Nota</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5">Rp 15.150</span>
            </div>
        </div>
    </div>

    <!-- Period Filter Toolbar (Gaya Pills Halaman Menu) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs">
        <div class="flex items-center gap-2 overflow-x-auto thin-scrollbar">
            <button type="button" 
                    @click="period = 'hari_ini'" 
                    class="h-9 px-3.5 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center transition"
                    :class="period === 'hari_ini' ? 'bg-brand-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                Hari Ini (21 Okt 2026)
            </button>
            <button type="button" 
                    @click="period = '7_hari'" 
                    class="h-9 px-3.5 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center transition"
                    :class="period === '7_hari' ? 'bg-brand-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                7 Hari Terakhir
            </button>
            <button type="button" 
                    @click="period = 'bulan_ini'" 
                    class="h-9 px-3.5 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center transition"
                    :class="period === 'bulan_ini' ? 'bg-brand-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                Bulan Ini (Oktober 2026)
            </button>
            <button type="button" 
                    @click="period = 'semester'" 
                    class="h-9 px-3.5 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center transition"
                    :class="period === 'semester' ? 'bg-brand-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                Semester Ganjil 2026/2027
            </button>
        </div>
    </div>

    <!-- Chart & Top Selling Layout (2 columns) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Revenue Trend Bar Chart (8 cols) -->
        <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <i data-lucide="bar-chart-2" class="w-4 h-4 text-slate-900"></i>
                    <h2 class="text-sm font-bold text-slate-900">Tren Pendapatan Harian (Senin - Minggu)</h2>
                </div>
                <span class="text-xs text-slate-400">Oktober 2026</span>
            </div>

            <!-- CSS Bar Chart -->
            <div class="pt-4">
                <div class="h-48 flex items-end justify-between gap-2 sm:gap-4 px-2">
                    <!-- Senin -->
                    <div class="flex-1 flex flex-col items-center gap-1.5 h-full justify-end group">
                        <span class="text-[10px] font-bold text-slate-600 opacity-0 group-hover:opacity-100 transition">390k</span>
                        <div class="w-full max-w-[42px] bg-brand-100 rounded-t-lg group-hover:bg-brand-500 transition-all" style="height: 65%;"></div>
                        <span class="text-[11px] font-semibold text-slate-500">Sen</span>
                    </div>

                    <!-- Selasa -->
                    <div class="flex-1 flex flex-col items-center gap-1.5 h-full justify-end group">
                        <span class="text-[10px] font-bold text-slate-600 opacity-0 group-hover:opacity-100 transition">430k</span>
                        <div class="w-full max-w-[42px] bg-brand-200 rounded-t-lg group-hover:bg-brand-500 transition-all" style="height: 72%;"></div>
                        <span class="text-[11px] font-semibold text-slate-500">Sel</span>
                    </div>

                    <!-- Rabu (Hari Ini - Active) -->
                    <div class="flex-1 flex flex-col items-center gap-1.5 h-full justify-end group">
                        <span class="text-[10px] font-bold text-brand-700">485k</span>
                        <div class="w-full max-w-[42px] bg-brand-600 rounded-t-lg shadow-sm shadow-brand-500/30 transition-all" style="height: 85%;"></div>
                        <span class="text-[11px] font-bold text-brand-700">Rab</span>
                    </div>

                    <!-- Kamis -->
                    <div class="flex-1 flex flex-col items-center gap-1.5 h-full justify-end group">
                        <span class="text-[10px] font-bold text-slate-600 opacity-0 group-hover:opacity-100 transition">410k</span>
                        <div class="w-full max-w-[42px] bg-brand-100 rounded-t-lg group-hover:bg-brand-500 transition-all" style="height: 68%;"></div>
                        <span class="text-[11px] font-semibold text-slate-500">Kam</span>
                    </div>

                    <!-- Jumat -->
                    <div class="flex-1 flex flex-col items-center gap-1.5 h-full justify-end group">
                        <span class="text-[10px] font-bold text-slate-600 opacity-0 group-hover:opacity-100 transition">360k</span>
                        <div class="w-full max-w-[42px] bg-brand-100 rounded-t-lg group-hover:bg-brand-500 transition-all" style="height: 60%;"></div>
                        <span class="text-[11px] font-semibold text-slate-500">Jum</span>
                    </div>

                    <!-- Sabtu -->
                    <div class="flex-1 flex flex-col items-center gap-1.5 h-full justify-end group">
                        <span class="text-[10px] font-bold text-slate-600 opacity-0 group-hover:opacity-100 transition">240k</span>
                        <div class="w-full max-w-[42px] bg-slate-200 rounded-t-lg group-hover:bg-brand-400 transition-all" style="height: 40%;"></div>
                        <span class="text-[11px] font-semibold text-slate-400">Sab</span>
                    </div>

                    <!-- Minggu (Kantin Tutup) -->
                    <div class="flex-1 flex flex-col items-center gap-1.5 h-full justify-end group">
                        <span class="text-[10px] font-bold text-slate-400 opacity-0 group-hover:opacity-100 transition">0</span>
                        <div class="w-full max-w-[42px] bg-slate-100 rounded-t-lg" style="height: 6%;"></div>
                        <span class="text-[11px] font-semibold text-slate-400">Min</span>
                    </div>
                </div>
            </div>

            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 text-xs text-slate-600 flex items-center justify-between">
                <span>Puncak Keramaian Pengunjung: <strong>Rabu & Selasa (11.30 - 13.00 WIB)</strong></span>
                <span class="text-slate-900 font-semibold">Jam Istirahat Kuliah</span>
            </div>
        </div>

        <!-- Top Selling Menus of Stan (4 cols) -->
        <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <i data-lucide="award" class="w-4 h-4 text-slate-900"></i>
                    <h2 class="text-sm font-bold text-slate-900">Menu Terlaris Hari Ini</h2>
                </div>
                <span class="text-xs text-slate-400">Stan 01</span>
            </div>

            <div class="divide-y divide-slate-100">
                <!-- Top 1 -->
                <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <img src="https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?auto=format&fit=crop&w=120&q=80" 
                             alt="Nasi Geprek" 
                             class="w-11 h-11 rounded-xl object-cover ring-1 ring-slate-200 shrink-0">
                        <div class="min-w-0">
                            <span class="text-xs font-bold text-slate-900 block truncate">Paket Nasi Geprek Bawang</span>
                            <span class="text-[11px] text-slate-500">22 porsi terjual</span>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="text-xs font-extrabold text-slate-900 block">Rp 330.000</span>
                        <span class="text-[10px] text-emerald-600 font-semibold">Peringkat 1</span>
                    </div>
                </div>

                <!-- Top 2 -->
                <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <img src="https://images.unsplash.com/photo-1556679343-c7306c1976bc?auto=format&fit=crop&w=120&q=80" 
                             alt="Es Teh Jumbo" 
                             class="w-11 h-11 rounded-xl object-cover ring-1 ring-slate-200 shrink-0">
                        <div class="min-w-0">
                            <span class="text-xs font-bold text-slate-900 block truncate">Es Teh Manis Jumbo</span>
                            <span class="text-[11px] text-slate-500">30 gelas terjual</span>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="text-xs font-extrabold text-slate-900 block">Rp 90.000</span>
                        <span class="text-[10px] text-slate-500 font-semibold">Peringkat 2</span>
                    </div>
                </div>

                <!-- Top 3 -->
                <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <img src="https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?auto=format&fit=crop&w=120&q=80" 
                             alt="Ayam Penyet" 
                             class="w-11 h-11 rounded-xl object-cover ring-1 ring-slate-200 shrink-0">
                        <div class="min-w-0">
                            <span class="text-xs font-bold text-slate-900 block truncate">Ayam Penyet Terasi</span>
                            <span class="text-[11px] text-slate-500">8 porsi terjual</span>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="text-xs font-extrabold text-slate-900 block">Rp 128.000</span>
                        <span class="text-[10px] text-slate-500 font-semibold">Peringkat 3</span>
                    </div>
                </div>
            </div>

            <a href="{{ url('/penjual/prediksi') }}" 
               class="w-full h-10 py-2.5 px-4 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                <i data-lucide="sparkles" class="w-4 h-4 text-slate-900"></i>
                <span>Lihat Prediksi Porsi Besok</span>
            </a>
        </div>
    </div>

    <!-- Detailed Sales Transactions Log Table (Tabel Identik Halaman Menu Stan, Scrollable Desktop & Mobile) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="history" class="w-4 h-4 text-slate-900"></i>
                <h2 class="text-sm font-bold text-slate-900">Rincian Transaksi Selesai Hari Ini</h2>
                <span class="text-xs text-slate-400 font-normal">(5 transaksi terakhir)</span>
            </div>
            <span class="text-xs text-slate-400">Stan 01 — Counter Barat</span>
        </div>

        <!-- Table View (Desktop & Mobile Horizontal Scroll) -->
        <div class="overflow-x-auto thin-scrollbar">
            <table class="w-full min-w-[750px] text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4">Antrean & Pemesan</th>
                        <th class="py-3 px-4">Kode Transaksi</th>
                        <th class="py-3 px-4">Menu Dipesan</th>
                        <th class="py-3 px-4">Metode Bayar</th>
                        <th class="py-3 px-4 text-right">Total Bersih</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    <!-- Transaksi 1 -->
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center shrink-0">
                                    DP
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-bold text-slate-900">Dimas Prasetyo</span>
                                        <span class="text-slate-400">&bull;</span>
                                        <span class="text-[11px] font-semibold text-slate-500">Antrean 08</span>
                                    </div>
                                    <span class="text-[11px] text-slate-400">11:42 WIB</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 font-semibold text-slate-600 whitespace-nowrap">NC-20261021-8842</td>
                        <td class="py-3.5 px-4 text-slate-700 font-medium">Nasi Geprek, Es Teh Jumbo</td>
                        <td class="py-3.5 px-4 text-slate-600 whitespace-nowrap">QRIS Bank Jatim</td>
                        <td class="py-3.5 px-4 text-right font-extrabold text-slate-900 whitespace-nowrap">Rp 18.000</td>
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-[11px] font-semibold bg-emerald-600 text-white border border-emerald-600 shadow-2xs">
                                <span class="relative flex h-1.5 w-1.5 shrink-0">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-white"></span>
                                </span>
                                <span>Lunas</span>
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            <a href="{{ url('/penjual/pesanan/detail') }}?id=NC-20261021-8842" 
                               class="p-2 rounded-xl text-slate-500 hover:text-brand-600 hover:bg-brand-50 transition inline-flex items-center" 
                               title="Lihat Detail Transaksi">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                        </td>
                    </tr>

                    <!-- Transaksi 2 -->
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center shrink-0">
                                    DL
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-bold text-slate-900">Dewi Lestari</span>
                                        <span class="text-slate-400">&bull;</span>
                                        <span class="text-[11px] font-semibold text-slate-500">Antrean 07</span>
                                    </div>
                                    <span class="text-[11px] text-slate-400">11:35 WIB</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 font-semibold text-slate-600 whitespace-nowrap">NC-20261021-8840</td>
                        <td class="py-3.5 px-4 text-slate-700 font-medium">Nasi Telur Krispi, Es Jeruk Peras</td>
                        <td class="py-3.5 px-4 text-slate-600 whitespace-nowrap">QRIS BCA</td>
                        <td class="py-3.5 px-4 text-right font-extrabold text-slate-900 whitespace-nowrap">Rp 15.000</td>
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-[11px] font-semibold bg-emerald-600 text-white border border-emerald-600 shadow-2xs">
                                <span class="relative flex h-1.5 w-1.5 shrink-0">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-white"></span>
                                </span>
                                <span>Lunas</span>
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            <a href="{{ url('/penjual/pesanan/detail') }}?id=NC-20261021-8840" 
                               class="p-2 rounded-xl text-slate-500 hover:text-brand-600 hover:bg-brand-50 transition inline-flex items-center" 
                               title="Lihat Detail Transaksi">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                        </td>
                    </tr>

                    <!-- Transaksi 3 -->
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center shrink-0">
                                    RP
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-bold text-slate-900">Rian Pratama</span>
                                        <span class="text-slate-400">&bull;</span>
                                        <span class="text-[11px] font-semibold text-slate-500">Antrean 06</span>
                                    </div>
                                    <span class="text-[11px] text-slate-400">11:30 WIB</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 font-semibold text-slate-600 whitespace-nowrap">NC-20261021-8839</td>
                        <td class="py-3.5 px-4 text-slate-700 font-medium">Nasi Geprek, Tempe Mendoan</td>
                        <td class="py-3.5 px-4 text-slate-600 whitespace-nowrap">QRIS Mandiri</td>
                        <td class="py-3.5 px-4 text-right font-extrabold text-slate-900 whitespace-nowrap">Rp 21.000</td>
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-[11px] font-semibold bg-emerald-600 text-white border border-emerald-600 shadow-2xs">
                                <span class="relative flex h-1.5 w-1.5 shrink-0">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-white"></span>
                                </span>
                                <span>Lunas</span>
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            <a href="{{ url('/penjual/pesanan/detail') }}?id=NC-20261021-8839" 
                               class="p-2 rounded-xl text-slate-500 hover:text-brand-600 hover:bg-brand-50 transition inline-flex items-center" 
                               title="Lihat Detail Transaksi">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                        </td>
                    </tr>

                    <!-- Transaksi 4 -->
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center shrink-0">
                                    BS
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-bold text-slate-900">Bima Santoso</span>
                                        <span class="text-slate-400">&bull;</span>
                                        <span class="text-[11px] font-semibold text-slate-500">Antrean 04</span>
                                    </div>
                                    <span class="text-[11px] text-slate-400">11:15 WIB</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 font-semibold text-slate-600 whitespace-nowrap">NC-20261021-8835</td>
                        <td class="py-3.5 px-4 text-slate-700 font-medium">Nasi Geprek, Kopi Susu Aren</td>
                        <td class="py-3.5 px-4 text-slate-600 whitespace-nowrap">QRIS OVO</td>
                        <td class="py-3.5 px-4 text-right font-extrabold text-slate-900 whitespace-nowrap">Rp 23.000</td>
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-[11px] font-semibold bg-emerald-600 text-white border border-emerald-600 shadow-2xs">
                                <span class="relative flex h-1.5 w-1.5 shrink-0">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-white"></span>
                                </span>
                                <span>Lunas</span>
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            <a href="{{ url('/penjual/pesanan/detail') }}?id=NC-20261021-8835" 
                               class="p-2 rounded-xl text-slate-500 hover:text-brand-600 hover:bg-brand-50 transition inline-flex items-center" 
                               title="Lihat Detail Transaksi">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                        </td>
                    </tr>

                    <!-- Transaksi 5 -->
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center shrink-0">
                                    FH
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-bold text-slate-900">Farhan Hidayat</span>
                                        <span class="text-slate-400">&bull;</span>
                                        <span class="text-[11px] font-semibold text-slate-500">Antrean 02</span>
                                    </div>
                                    <span class="text-[11px] text-slate-400">10:55 WIB</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 font-semibold text-slate-600 whitespace-nowrap">NC-20261021-8831</td>
                        <td class="py-3.5 px-4 text-slate-700 font-medium">Nasi Geprek, Es Teh Jumbo</td>
                        <td class="py-3.5 px-4 text-slate-600 whitespace-nowrap">QRIS Dana</td>
                        <td class="py-3.5 px-4 text-right font-extrabold text-slate-900 whitespace-nowrap">Rp 18.000</td>
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-[11px] font-semibold bg-emerald-600 text-white border border-emerald-600 shadow-2xs">
                                <span class="relative flex h-1.5 w-1.5 shrink-0">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-white"></span>
                                </span>
                                <span>Lunas</span>
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            <a href="{{ url('/penjual/pesanan/detail') }}?id=NC-20261021-8831" 
                               class="p-2 rounded-xl text-slate-500 hover:text-brand-600 hover:bg-brand-50 transition inline-flex items-center" 
                               title="Lihat Detail Transaksi">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Export Modal -->
    <div x-show="exportModal" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
         @keydown.escape.window="exportModal = false">
        <div class="bg-white rounded-2xl max-w-sm w-full p-6 space-y-4 shadow-xl border border-slate-200"
             @click.away="exportModal = false">
            <div class="w-11 h-11 rounded-2xl bg-slate-100 text-slate-900 flex items-center justify-center">
                <i data-lucide="file-spreadsheet" class="w-5 h-5 text-slate-900"></i>
            </div>
            <div class="space-y-1">
                <h3 class="text-base font-bold text-slate-900">Unduh Rekap Penjualan</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Pilih format data laporan yang Anda inginkan untuk pembukuan stan Anda:
                </p>
            </div>
            <div class="space-y-2 pt-2">
                <button type="button" 
                        @click="exportModal = false; alert('Mengunduh file Excel (.xlsx)...')"
                        class="w-full h-10 py-2.5 px-4 rounded-xl text-xs font-semibold text-slate-800 bg-slate-100 hover:bg-slate-200 inline-flex items-center justify-between transition">
                    <span>Microsoft Excel (.xlsx)</span>
                    <i data-lucide="download" class="w-4 h-4 text-slate-900"></i>
                </button>
                <button type="button" 
                        @click="exportModal = false; alert('Mengunduh file CSV (.csv)...')"
                        class="w-full h-10 py-2.5 px-4 rounded-xl text-xs font-semibold text-slate-800 bg-slate-100 hover:bg-slate-200 inline-flex items-center justify-between transition">
                    <span>File CSV Kompatibel (.csv)</span>
                    <i data-lucide="download" class="w-4 h-4 text-slate-900"></i>
                </button>
            </div>
            <div class="flex items-center justify-end pt-2">
                <button type="button" 
                        @click="exportModal = false"
                        class="h-9 px-3 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });
</script>
@endsection
