@extends('layouts.seller')

@section('title', 'Dashboard Stan Penjual — NesaCourt')

@section('content')
<div class="space-y-6" x-data="{
    filterPeriod: 'hari_ini'
}">

    <!-- Welcome & Operational Header Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-7 shadow-xs space-y-5">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3.5">
                <img src="https://images.unsplash.com/photo-1556157382-97eda2d62296?auto=format&fit=crop&w=120&q=80" 
                     alt="Foto Pemilik Stan" 
                     class="w-12 h-12 rounded-xl object-cover ring-1 ring-slate-200 shrink-0">
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">Halo, Pak Bambang</h1>
                        <span class="text-slate-300 font-normal">|</span>
                        <span class="text-xs font-semibold text-brand-600">Stan 01 Barokah Rasa</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Spesialis Ayam Geprek & Nasi Penyet | Counter Barat No. 01</p>
                </div>
            </div>

            <!-- Operating Status Indicator (Tanpa Container) -->
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
            <a href="{{ url('/penjual/pesanan') }}" class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 hover:border-brand-200 hover:bg-brand-50/40 transition flex items-center gap-2.5 group">
                <div class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-700 group-hover:text-brand-600 shrink-0 shadow-2xs">
                    <i data-lucide="cooking-pot" class="w-4 h-4"></i>
                </div>
                <div class="min-w-0">
                    <span class="text-xs font-bold text-slate-900 block truncate group-hover:text-brand-600">Pesanan Masuk</span>
                    <span class="text-[10px] text-amber-600 font-semibold block truncate">3 Perlu Diproses</span>
                </div>
            </a>

            <a href="{{ url('/penjual/menu/create') }}" class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 hover:border-brand-200 hover:bg-brand-50/40 transition flex items-center gap-2.5 group">
                <div class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-700 group-hover:text-brand-600 shrink-0 shadow-2xs">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                </div>
                <div class="min-w-0">
                    <span class="text-xs font-bold text-slate-900 block truncate group-hover:text-brand-600">Tambah Menu</span>
                    <span class="text-[10px] text-slate-400 block truncate">Menu Baru Dapur</span>
                </div>
            </a>

            <a href="{{ url('/penjual/stok') }}" class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 hover:border-brand-200 hover:bg-brand-50/40 transition flex items-center gap-2.5 group">
                <div class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-700 group-hover:text-brand-600 shrink-0 shadow-2xs">
                    <i data-lucide="layers" class="w-4 h-4"></i>
                </div>
                <div class="min-w-0">
                    <span class="text-xs font-bold text-slate-900 block truncate group-hover:text-brand-600">Kelola Stok</span>
                    <span class="text-[10px] text-slate-400 block truncate">Update Porsi Harian</span>
                </div>
            </a>

            <a href="{{ url('/penjual/qris') }}" class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 hover:border-brand-200 hover:bg-brand-50/40 transition flex items-center gap-2.5 group">
                <div class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-700 group-hover:text-brand-600 shrink-0 shadow-2xs">
                    <i data-lucide="qr-code" class="w-4 h-4"></i>
                </div>
                <div class="min-w-0">
                    <span class="text-xs font-bold text-slate-900 block truncate group-hover:text-brand-600">QRIS Stan</span>
                    <span class="text-[10px] text-slate-400 block truncate">Rekening Penjual</span>
                </div>
            </a>
        </div>
    </div>

    <!-- 4 Key Statistics Cards (Gaya Identik Halaman Menu dengan Container Putih) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 shadow-xs">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <!-- Card 1: Omset Hari Ini -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Total Omset Hari Ini</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5">Rp 485.000</span>
                <span class="text-[10px] text-slate-500 block mt-0.5">+12.5% dari kemarin</span>
            </div>

            <!-- Card 2: Pesanan Masuk -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Pesanan Selesai / Masuk</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5">28 / 31</span>
                <span class="text-[10px] text-slate-500 block mt-0.5">3 pesanan sedang diproses</span>
            </div>

            <!-- Card 3: Porsi Terjual -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Porsi Terjual Hari Ini</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5">34 Porsi</span>
                <span class="text-[10px] text-slate-500 block mt-0.5">Ayam geprek paling diminati</span>
            </div>

            <!-- Card 4: Stok Porsi Menipis -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Stok Menu Menipis</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5">2 Menu</span>
                <span class="text-[10px] text-slate-500 block mt-0.5">Sisa porsi &le; 3 porsi</span>
            </div>
        </div>
    </div>

    <!-- Active Kitchen Orders & Popular Menu Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Col 1 & 2: Pesanan Masuk Dapur Real-time -->
        <div class="lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base sm:text-lg font-bold text-slate-900">Pesanan Masuk Perlu Diproses</h2>
                    <p class="text-xs text-slate-500">Antrean pesanan mahasiswa yang siap dimasak atau diserahkan</p>
                </div>
                <a href="{{ url('/penjual/pesanan') }}" class="whitespace-nowrap text-xs font-semibold text-brand-600 hover:text-brand-700 flex items-center gap-1">
                    <span>Lihat Semua</span>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>

            <div class="space-y-3">
                <!-- Order Item 1 -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex flex-col justify-between space-y-4 hover:border-slate-300 transition">
                    <!-- Top Row: Customer Info & Status di Samping Kanan -->
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center shrink-0 uppercase">
                                AP
                            </div>
                            <div>
                                <div class="flex items-center gap-1.5">
                                    <span class="font-bold text-slate-900 text-sm">Aditya Pratama</span>
                                    <span class="text-slate-300">|</span>
                                    <span class="text-[11px] text-slate-500">Mahasiswa FT</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
                                    <span class="text-[11px] font-semibold text-slate-600">NC-20261021-8839</span>
                                    <span class="text-slate-300">&bull;</span>
                                    <span>11:42 WIB</span>
                                </div>
                            </div>
                        </div>

                        <!-- Status Tanpa Container & Titik Kedap-Kedip -->
                        <div class="shrink-0 pt-0.5">
                            <div class="flex items-center gap-1.5 text-xs font-bold text-amber-600">
                                <span class="relative flex h-2 w-2 shrink-0">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                                </span>
                                <span>Sedang Dimasak</span>
                            </div>
                        </div>
                    </div>

                    <!-- Ordered Items List (Tanpa Bagian Layanan & Catatan) -->
                    <div class="space-y-1.5 text-xs">
                        <span class="text-xs font-semibold text-slate-500 block">Item Masakan Dapur:</span>
                        <div class="space-y-1">
                            <div class="flex items-start justify-between">
                                <div>
                                    <span class="font-medium text-slate-500">1x Ayam Geprek Sambal Korek</span>
                                    <span class="text-[10px] text-slate-400 block">(Sambal dipisah)</span>
                                </div>
                                <span class="font-medium text-slate-500 shrink-0">Rp 13.000</span>
                            </div>
                            <div class="flex items-start justify-between">
                                <div>
                                    <span class="font-medium text-slate-500">1x Es Teh Manis Jumbo Vokasi</span>
                                    <span class="text-[10px] text-slate-400 block">(Sedikit gula)</span>
                                </div>
                                <span class="font-medium text-slate-500 shrink-0">Rp 4.000</span>
                            </div>
                        </div>
                    </div>

                    <!-- Total Tagihan, Metode Pembayaran & Action Buttons -->
                    <div class="pt-3 border-t border-slate-100 space-y-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-[10px] text-slate-400 block">Total Tagihan</span>
                                <span class="text-sm font-bold text-slate-700">Rp 17.000</span>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] text-slate-400 block">Metode Pembayaran</span>
                                <span class="text-xs font-semibold text-emerald-700 flex items-center gap-1 justify-end">
                                    <i data-lucide="check-circle" class="w-3 h-3 text-emerald-600"></i>
                                    <span>QRIS ShopeePay</span>
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 pt-1">
                            <a href="{{ url('/penjual/pesanan/detail') }}" 
                               class="h-10 py-2.5 px-4 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center text-center bg-slate-100 hover:bg-slate-200 text-slate-700 transition w-24">
                                <span>Detail</span>
                            </a>
                            <a href="{{ url('/penjual/pesanan/detail') }}" 
                               class="flex-1 h-10 py-2.5 px-4 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm shadow-emerald-500/20 transition">
                                <i data-lucide="bell" class="w-4 h-4"></i>
                                <span>Tandai Siap Diambil</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Order Item 2 -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex flex-col justify-between space-y-4 hover:border-slate-300 transition">
                    <!-- Top Row: Customer Info & Status di Samping Kanan -->
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center shrink-0 uppercase">
                                RM
                            </div>
                            <div>
                                <div class="flex items-center gap-1.5">
                                    <span class="font-bold text-slate-900 text-sm">Rizky Maulana</span>
                                    <span class="text-slate-300">|</span>
                                    <span class="text-[11px] text-slate-500">Mahasiswa FEB</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
                                    <span class="text-[11px] font-semibold text-slate-600">NC-20261021-8837</span>
                                    <span class="text-slate-300">&bull;</span>
                                    <span>11:35 WIB</span>
                                </div>
                            </div>
                        </div>

                        <!-- Status Tanpa Container & Titik Kedap-Kedip -->
                        <div class="shrink-0 pt-0.5">
                            <div class="flex items-center gap-1.5 text-xs font-bold text-amber-600">
                                <span class="relative flex h-2 w-2 shrink-0">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                                </span>
                                <span>Verifikasi Bayar</span>
                            </div>
                        </div>
                    </div>

                    <!-- Ordered Items List (Tanpa Bagian Layanan & Catatan) -->
                    <div class="space-y-1.5 text-xs">
                        <span class="text-xs font-semibold text-slate-500 block">Item Masakan Dapur:</span>
                        <div class="space-y-1">
                            <div class="flex items-start justify-between">
                                <div>
                                    <span class="font-medium text-slate-500">2x Ayam Penyet Sambal Terasi</span>
                                    <span class="text-[10px] text-slate-400 block">(Pedas mantap)</span>
                                </div>
                                <span class="font-medium text-slate-500 shrink-0">Rp 26.000</span>
                            </div>
                        </div>
                    </div>

                    <!-- Total Tagihan, Metode Pembayaran & Action Buttons -->
                    <div class="pt-3 border-t border-slate-100 space-y-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-[10px] text-slate-400 block">Total Tagihan</span>
                                <span class="text-sm font-bold text-slate-700">Rp 26.000</span>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] text-slate-400 block">Metode Pembayaran</span>
                                <span class="text-xs font-semibold text-emerald-700 flex items-center gap-1 justify-end">
                                    <i data-lucide="check-circle" class="w-3 h-3 text-emerald-600"></i>
                                    <span>QRIS BCA</span>
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 pt-1">
                            <a href="{{ url('/penjual/pesanan/detail') }}" 
                               class="h-10 py-2.5 px-4 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center text-center bg-slate-100 hover:bg-slate-200 text-slate-700 transition w-24">
                                <span>Detail</span>
                            </a>
                            <a href="{{ url('/penjual/pesanan/detail') }}" 
                               class="flex-1 h-10 py-2.5 px-4 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-700 text-white shadow-sm shadow-brand-500/20 transition">
                                <i data-lucide="flame" class="w-4 h-4"></i>
                                <span>Verifikasi & Mulai Masak</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Col 3: Menu Terlaris Stan Hari Ini -->
        <div class="space-y-4">
            <div>
                <h2 class="text-base sm:text-lg font-bold text-slate-900">Menu Terlaris Stan</h2>
                <p class="text-xs text-slate-500">Pantau performa penjualan produk stan Anda</p>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 space-y-4 shadow-xs">
                <div class="space-y-3">
                    <div class="space-y-1.5">
                        <div class="flex justify-between text-xs">
                            <span class="font-bold text-slate-800 truncate">Ayam Geprek Sambal Korek</span>
                            <span class="font-bold text-slate-900">18 Porsi</span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full bg-brand-600 rounded-full" style="width: 75%"></div>
                        </div>
                        <span class="text-[10px] text-slate-400 block">Sisa porsi tersedia: 12 porsi</span>
                    </div>

                    <div class="space-y-1.5 pt-2 border-t border-slate-100">
                        <div class="flex justify-between text-xs">
                            <span class="font-bold text-slate-800 truncate">Paket Nasi Penyet Komplit</span>
                            <span class="font-bold text-slate-900">10 Porsi</span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full bg-amber-500 rounded-full" style="width: 50%"></div>
                        </div>
                        <span class="text-[10px] text-slate-400 block">Sisa porsi tersedia: 10 porsi</span>
                    </div>

                    <div class="space-y-1.5 pt-2 border-t border-slate-100">
                        <div class="flex justify-between text-xs">
                            <span class="font-bold text-slate-800 truncate">Ayam Krispi Sambal Bawang</span>
                            <span class="font-bold text-slate-900">6 Porsi</span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full" style="width: 30%"></div>
                        </div>
                        <span class="text-[10px] text-rose-500 font-semibold block">Sisa porsi menipis: 2 porsi</span>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100">
                    <a href="{{ url('/penjual/stok') }}" class="whitespace-nowrap w-full h-10 inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl border border-slate-200 bg-white text-slate-700 text-xs font-semibold hover:bg-slate-50 transition shadow-2xs">
                        <i data-lucide="layers" class="w-3.5 h-3.5"></i>
                        <span>Perbarui Stok Porsi Stan</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
