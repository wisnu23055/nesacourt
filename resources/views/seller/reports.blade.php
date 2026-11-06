@extends('layouts.seller')

@section('title', 'Riwayat Penjualan & Laporan — NesaCourt')

@section('content')
<style>
@media print {
    /* Setup Page A4 Landscape agar seluruh kolom terlihat penuh tanpa terpotong */
    @page {
        size: A4 landscape;
        margin: 10mm 12mm;
    }

    /* Reset layout & backgrounds for clean print */
    body, html {
        background: #ffffff !important;
        color: #0f172a !important;
        font-size: 10pt !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    /* Hide sidebar, navigation, header, cards, and interactive elements */
    aside,
    header,
    nav,
    .print\:hidden,
    .no-print {
        display: none !important;
    }

    /* Full width content area */
    main {
        padding: 0 !important;
        margin: 0 !important;
        max-width: 100% !important;
        width: 100% !important;
    }

    /* Reset card wrapper for printing table */
    .print-card-reset {
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        margin: 0 !important;
        background: transparent !important;
        overflow: visible !important;
    }

    .thin-scrollbar {
        overflow: visible !important;
    }

    /* Clean, crisp table borders for PDF export */
    .print-table {
        width: 100% !important;
        min-width: 100% !important;
        max-width: 100% !important;
        border-collapse: collapse !important;
        font-size: 8.5pt !important;
        table-layout: auto !important;
    }

    .print-table th,
    .print-table td {
        border: 1px solid #cbd5e1 !important;
        padding: 6px 8px !important;
        color: #0f172a !important;
    }

    .print-table th {
        background-color: #f8fafc !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        font-size: 8pt !important;
        letter-spacing: 0.04em;
    }

    .print-table tr {
        page-break-inside: avoid !important;
    }

    tfoot.print-footer {
        display: table-footer-group !important;
    }
}
</style>

<div class="space-y-6" x-data="{
    period: '{{ $period }}'
}">

    <!-- Kop Cetak Laporan (Tampil Saat Cetak / Save to PDF) -->
    <div class="hidden print:block mb-5">
        <div class="flex items-center justify-between pb-3 border-b-2 border-slate-900">
            <div>
                <img src="{{ asset('images/logo/Logo.webp') }}" alt="Logo NesaCourt" class="h-12 w-auto object-contain">
            </div>
            <div class="text-right">
                <p class="text-xs text-slate-600 font-medium">Waktu Cetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
            </div>
        </div>

        <!-- Metadata Stan & Periode -->
        <div class="grid grid-cols-2 gap-4 mt-3 p-3 bg-slate-50 rounded-lg border border-slate-200 text-xs">
            <div class="space-y-1">
                <div class="flex"><span class="w-28 text-slate-500 font-medium">Nama Stan:</span> <span class="font-bold text-slate-900">{{ auth()->user()->sellerProfile ? auth()->user()->sellerProfile->stall_name : 'Stan Kantin' }}</span></div>
                <div class="flex"><span class="w-28 text-slate-500 font-medium">Lokasi Counter:</span> <span class="font-semibold text-slate-800">{{ auth()->user()->sellerProfile ? auth()->user()->sellerProfile->location : 'Food Court UNESA' }}</span></div>
                <div class="flex"><span class="w-28 text-slate-500 font-medium">Kasir / Penjual:</span> <span class="font-medium text-slate-800">{{ auth()->user()->name }}</span></div>
            </div>
            <div class="space-y-1 text-right">
                <div class="flex justify-end gap-2"><span class="text-slate-500 font-medium">Periode Laporan:</span> <span class="font-bold text-slate-900">{{ $period === 'hari_ini' ? 'Hari Ini (' . now()->translatedFormat('d F Y') . ')' : ($period === '7_hari' ? '7 Hari Terakhir' : ($period === 'bulan_ini' ? 'Bulan Ini (' . now()->translatedFormat('F Y') . ')' : 'Semester')) }}</span></div>
                <div class="flex justify-end gap-2"><span class="text-slate-500 font-medium">Total Pesanan:</span> <span class="font-bold text-slate-900">{{ $completedOrders }} Transaksi</span></div>
                <div class="flex justify-end gap-2"><span class="text-slate-500 font-medium">Total Porsi Terjual:</span> <span class="font-bold text-slate-900">{{ $portionsSold }} Porsi</span></div>
            </div>
        </div>
    </div>

    <!-- Page Header & Action Bar (Web Screen Only) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-7 shadow-xs print:hidden">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                    <a href="{{ url('/penjual/dashboard') }}" class="hover:text-brand-600">Portal Stan</a>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-900"></i>
                    <span class="text-slate-800 font-semibold">Riwayat Penjualan</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-semibold text-slate-900 tracking-tight">Laporan Penjualan & Rekap Omset</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-2 max-w-xl">Pantau performa penjualan masakan harian, volume pesanan, dan rekap pencairan dana kasir stan Anda.</p>
            </div>

            <!-- Action Buttons: Cetak Rekap Kasir (PDF) & Ekspor Excel -->
            <div class="flex items-center gap-2.5 shrink-0 pt-1 sm:pt-0">
                <button type="button" 
                        onclick="window.print()" 
                        class="flex-1 sm:flex-none h-10 py-2.5 px-4 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-700 text-white shadow-sm shadow-brand-500/20 transition active:scale-95 cursor-pointer">
                    <i data-lucide="printer" class="w-4 h-4 text-white"></i>
                    <span>Cetak Rekap Kasir (PDF)</span>
                </button>

                <a href="{{ route('seller.reports', ['export' => 'excel', 'period' => $period]) }}" 
                   class="flex-1 sm:flex-none h-10 py-2.5 px-4 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center gap-2 bg-slate-900 hover:bg-slate-800 text-white shadow-sm transition active:scale-95">
                    <i data-lucide="sheet" class="w-4 h-4 text-white"></i>
                    <span>Ekspor Excel</span>
                </a>
            </div>
        </div>

        <!-- Micro Summary Counters (Hitam Semua, Semi Bold & Rata Tengah) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t border-slate-100">
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Total Omset Bersih</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5">Rp {{ number_format($omsetTotal, 0, ',', '.') }}</span>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Pesanan Berhasil</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5">{{ $completedOrders }} Pesanan</span>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Porsi Masakan Laku</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5">{{ $portionsSold }} Porsi</span>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Rata-rata Nilai Nota</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5">Rp {{ number_format($avgOrderValue, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- Period Filter Toolbar (Web Screen Only) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs print:hidden">
        <div class="flex items-center gap-2 overflow-x-auto thin-scrollbar">
            <a href="{{ route('seller.reports', ['period' => 'hari_ini']) }}" 
               class="h-9 px-3.5 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center transition {{ $period === 'hari_ini' ? 'bg-brand-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Hari Ini ({{ now()->translatedFormat('d M Y') }})
            </a>
            <a href="{{ route('seller.reports', ['period' => '7_hari']) }}" 
               class="h-9 px-3.5 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center transition {{ $period === '7_hari' ? 'bg-brand-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                7 Hari Terakhir
            </a>
            <a href="{{ route('seller.reports', ['period' => 'bulan_ini']) }}" 
               class="h-9 px-3.5 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center transition {{ $period === 'bulan_ini' ? 'bg-brand-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Bulan Ini ({{ now()->translatedFormat('F Y') }})
            </a>
            <a href="{{ route('seller.reports', ['period' => 'semester']) }}" 
               class="h-9 px-3.5 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center transition {{ $period === 'semester' ? 'bg-brand-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Semester Ganjil 2026/2027
            </a>
        </div>
    </div>

    <!-- Chart & Top Selling Layout (Web Screen Only) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 print:hidden">
        <!-- Revenue Trend Bar Chart (8 cols) -->
        <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <i data-lucide="bar-chart-2" class="w-4 h-4 text-slate-900"></i>
                    <h2 class="text-sm font-bold text-slate-900">Tren Pendapatan Harian (Senin - Minggu)</h2>
                </div>
                <span class="text-xs text-slate-400">{{ now()->translatedFormat('F Y') }}</span>
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
                <span class="text-xs text-slate-400">{{ auth()->user()->sellerProfile ? auth()->user()->sellerProfile->stall_name : 'Stan 01' }}</span>
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

    <!-- Detailed Sales Transactions Log Table (Tabel Identik Halaman Menu Stan, Scrollable Desktop & Mobile, Rapi Saat PDF/Cetak) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden print-card-reset">
        <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between print:hidden">
            <div class="flex items-center gap-2">
                <i data-lucide="history" class="w-4 h-4 text-slate-900"></i>
                <h2 class="text-sm font-bold text-slate-900">Rincian Transaksi Selesai Kasir</h2>
                <span class="text-xs text-slate-400 font-normal">({{ $recentOrders->count() }} transaksi tercatat)</span>
            </div>
            <span class="text-xs text-slate-500 font-semibold">{{ auth()->user()->sellerProfile ? auth()->user()->sellerProfile->stall_name : 'Stan Kantin' }}</span>
        </div>

        <!-- Table View (Desktop & Mobile Horizontal Scroll, Rapi Pada Cetak PDF) -->
        <div class="overflow-x-auto thin-scrollbar">
            <table class="w-full min-w-[750px] text-left border-collapse print-table">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4 hidden print:table-cell text-center w-10">No</th>
                        <th class="py-3 px-4 whitespace-nowrap">Waktu</th>
                        <th class="py-3 px-4 whitespace-nowrap">Kode Pesanan</th>
                        <th class="py-3 px-4">Antrean & Pemesan</th>
                        <th class="py-3 px-4">Menu Dipesan</th>
                        <th class="py-3 px-4 whitespace-nowrap text-center">Metode Bayar</th>
                        <th class="py-3 px-4 text-right whitespace-nowrap">Total Bersih</th>
                        <th class="py-3 px-4 text-center whitespace-nowrap">Status</th>
                        <th class="py-3 px-4 text-center whitespace-nowrap print:hidden">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($recentOrders as $index => $order)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3.5 px-4 hidden print:table-cell text-center font-semibold text-slate-600">
                                {{ $index + 1 }}
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap font-medium text-slate-600">
                                {{ $order->created_at->format('H:i') }} WIB
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-slate-700 whitespace-nowrap font-mono text-[11px]">
                                {{ $order->order_number }}
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    @php
                                        $buyerAvatar = $order->buyer ? $order->buyer->avatar_url : null;
                                        $buyerInitials = $order->buyer ? $order->buyer->initials : strtoupper(substr($order->customer_name, 0, 2));
                                    @endphp
                                    <div class="print:hidden shrink-0">
                                        @if($buyerAvatar)
                                            <img src="{{ $buyerAvatar }}" 
                                                 alt="{{ $order->customer_name }}" 
                                                 class="w-9 h-9 rounded-xl object-cover ring-1 ring-slate-200">
                                        @else
                                            <div class="w-9 h-9 rounded-xl bg-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center uppercase">
                                                {{ $buyerInitials }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5">
                                            <span class="font-bold text-slate-900">{{ $order->customer_name }}</span>
                                            <span class="text-slate-400 print:hidden">&bull;</span>
                                            <span class="text-[11px] font-semibold text-slate-500">Antrean #{{ str_pad($order->id % 100, 2, '0', STR_PAD_LEFT) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-slate-700 font-medium">
                                {{ $order->items->pluck('menu_name_snapshot')->take(2)->join(', ') }}
                                @if($order->items->count() > 2)
                                    +{{ $order->items->count() - 2 }} item
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 whitespace-nowrap text-center font-semibold">
                                {{ strtoupper($order->payment_method) }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-extrabold text-slate-900 whitespace-nowrap">
                                Rp {{ number_format($order->total, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="font-bold text-emerald-600">Lunas</span>
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap print:hidden">
                                <a href="{{ url('/penjual/pesanan/detail') }}?id={{ $order->order_number }}" 
                                   class="p-2 rounded-xl text-slate-500 hover:text-brand-600 hover:bg-brand-50 transition inline-flex items-center" 
                                   title="Lihat Detail Transaksi">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-400">Belum ada riwayat pesanan selesai untuk stan ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                <!-- Footer Total Rekap Kasir (Hanya Tampil Saat Cetak / PDF) -->
                <tfoot class="hidden print:table-footer-group print-footer border-t-2 border-slate-300">
                    <tr class="bg-slate-50 font-bold text-slate-900">
                        <td colspan="6" class="py-3 px-4 text-right uppercase text-xs tracking-wider">
                            Total Penerimaan Bersih Kasir:
                        </td>
                        <td class="py-3 px-4 text-right text-xs font-black whitespace-nowrap text-slate-900">
                            Rp {{ number_format($omsetTotal, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-4 text-center text-xs text-emerald-700 font-bold">
                            Lunas
                        </td>
                        <td class="print:hidden"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) {
            lucide.createIcons();
        }
    });
</script>
@endsection
