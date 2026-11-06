@extends('layouts.seller')

@section('title', 'Dashboard Stan Penjual — NesaCourt')

@section('content')
<div class="space-y-6" x-data="{
    filterPeriod: 'hari_ini',
    orders: {{ json_encode($activeOrdersData) }},
    async advanceStatus(order, nextStatus, nextLabel) {
        order.status = nextStatus;
        order.statusLabel = nextLabel;
        try {
            await fetch('{{ url('/penjual/pesanan') }}/' + order.id + '/status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ order_status: nextStatus })
            });
        } catch(e) {
            console.error(e);
        }
    }
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
                        <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">Halo, {{ auth()->user()->name }}</h1>
                        <span class="text-slate-300 font-normal">|</span>
                        <span class="text-xs font-semibold text-brand-600">{{ $seller->stall_name }}</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $seller->description ?: 'Spesialis Kuliner Food Court' }} | {{ $seller->location }}</p>
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
                    <span class="text-[10px] text-amber-600 font-semibold block truncate">{{ $activeOrders->count() }} Perlu Diproses</span>
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
                <span class="text-lg font-semibold text-slate-900 block mt-0.5">Rp {{ number_format($omsetToday, 0, ',', '.') }}</span>
                <span class="text-[10px] text-slate-500 block mt-0.5">Pesanan berhasil</span>
            </div>

            <!-- Card 2: Pesanan Masuk -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Pesanan Selesai / Masuk</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5">{{ $completedCountToday }} / {{ $ordersCountToday }}</span>
                <span class="text-[10px] text-slate-500 block mt-0.5">{{ $activeOrders->count() }} pesanan aktif</span>
            </div>

            <!-- Card 3: Porsi Terjual -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Porsi Terjual Hari Ini</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5">{{ $portionsSoldToday }} Porsi</span>
                <span class="text-[10px] text-slate-500 block mt-0.5">Total item masakan</span>
            </div>

            <!-- Card 4: Stok Porsi Menipis -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Stok Menu Menipis</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5">{{ $lowStockCount }} Menu</span>
                <span class="text-[10px] text-slate-500 block mt-0.5">Sisa porsi &le; 3 porsi</span>
            </div>
        </div>
    </div>

    <!-- Pesanan Masuk Dapur Real-time (Format Dua-Dua Grid) -->
    <div class="space-y-4">
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

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <template x-for="order in orders" :key="order.id">
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex flex-col justify-between space-y-4 hover:border-slate-300 transition">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center shrink-0 uppercase"
                                 x-text="order.initials">
                            </div>
                            <div>
                                <div class="flex items-center gap-1.5">
                                    <span class="font-bold text-slate-900 text-sm" x-text="order.customerName"></span>
                                    <span class="text-slate-300">|</span>
                                    <span class="text-[11px] text-slate-500" x-text="'Antrean #' + order.queueNo"></span>
                                </div>
                                <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
                                    <span class="text-[11px] font-semibold text-slate-600" x-text="order.id"></span>
                                    <span class="text-slate-300">&bull;</span>
                                    <span x-text="order.time"></span>
                                </div>
                            </div>
                        </div>

                        <div class="shrink-0 pt-0.5">
                            <div x-show="order.status === 'menunggu_konfirmasi'" class="flex items-center gap-1.5 text-xs font-bold text-amber-600">
                                <span class="relative flex h-2 w-2 shrink-0">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                                </span>
                                <span>Menunggu Verifikasi</span>
                            </div>

                            <div x-show="order.status === 'memasak'" class="flex items-center gap-1.5 text-xs font-bold text-brand-600">
                                <span class="relative flex h-2 w-2 shrink-0">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-brand-500"></span>
                                </span>
                                <span>Sedang Dimasak</span>
                            </div>

                            <div x-show="order.status === 'siap_ambil'" class="flex items-center gap-1.5 text-xs font-bold text-emerald-600">
                                <span class="relative flex h-2 w-2 shrink-0">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                </span>
                                <span>Siap Diambil</span>
                            </div>

                            <div x-show="order.status === 'selesai'" class="flex items-center gap-1.5 text-xs font-bold text-slate-500">
                                <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                <span>Pesanan Selesai</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-1.5 text-xs">
                        <span class="text-xs font-semibold text-slate-500 block">Item Masakan Dapur:</span>
                        <div class="space-y-1">
                            <template x-for="it in order.items" :key="it.name">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <span class="font-medium text-slate-600" x-text="it.qty + 'x ' + it.name"></span>
                                        <span x-show="it.note" class="text-[10px] text-slate-400 block" x-text="'(' + it.note + ')'"></span>
                                    </div>
                                    <span class="font-medium text-slate-500 shrink-0" x-text="'Rp ' + it.subtotal.toLocaleString('id-ID')"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 space-y-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-[10px] text-slate-400 block">Total Tagihan</span>
                                <span class="text-sm font-bold text-slate-700" x-text="'Rp ' + order.totalPrice.toLocaleString('id-ID')"></span>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] text-slate-400 block">Metode Pembayaran</span>
                                <span class="text-xs font-semibold text-emerald-700 flex items-center gap-1 justify-end">
                                    <i data-lucide="check-circle" class="w-3 h-3 text-emerald-600"></i>
                                    <span x-text="order.paymentMethod"></span>
                                </span>
                            </div>
                        </div>

                        <!-- Tombol Berantai Interaktif -->
                        <div class="flex items-center gap-2 pt-1">
                            <!-- If Menunggu Verifikasi -->
                            <template x-if="order.status === 'menunggu_konfirmasi'">
                                <div class="flex items-center gap-2 w-full">
                                    <a :href="'{{ url('/penjual/pesanan/detail') }}?id=' + order.id" 
                                       class="h-10 py-2.5 px-4 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center text-center bg-slate-100 hover:bg-slate-200 text-slate-700 transition w-24">
                                        <span>Detail</span>
                                    </a>
                                    <button type="button" 
                                            @click="advanceStatus(order, 'memasak', 'Sedang Dimasak')"
                                            class="flex-1 h-10 py-2.5 px-4 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-700 text-white shadow-sm shadow-brand-500/20 transition cursor-pointer active:scale-95">
                                        <svg class="w-4 h-4 text-white shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/></svg>
                                        <span>Lanjut Masak</span>
                                    </button>
                                </div>
                            </template>

                            <!-- If Memasak -->
                            <template x-if="order.status === 'memasak'">
                                <div class="flex items-center gap-2 w-full">
                                    <a :href="'{{ url('/penjual/pesanan/detail') }}?id=' + order.id" 
                                       class="h-10 py-2.5 px-4 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center text-center bg-slate-100 hover:bg-slate-200 text-slate-700 transition w-24">
                                        <span>Detail</span>
                                    </a>
                                    <button type="button" 
                                            @click="advanceStatus(order, 'siap_ambil', 'Siap Diambil')"
                                            class="flex-1 h-10 py-2.5 px-4 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm shadow-emerald-500/20 transition cursor-pointer active:scale-95">
                                        <svg class="w-4 h-4 text-white shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                                        <span>Lanjut Siap Diambil</span>
                                    </button>
                                </div>
                            </template>

                            <!-- If Siap Diambil -->
                            <template x-if="order.status === 'siap_ambil'">
                                <div class="flex items-center gap-2 w-full">
                                    <a :href="'{{ url('/penjual/pesanan/detail') }}?id=' + order.id" 
                                       class="h-10 py-2.5 px-4 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center text-center bg-slate-100 hover:bg-slate-200 text-slate-700 transition w-24">
                                        <span>Detail</span>
                                    </a>
                                    <button type="button" 
                                            @click="advanceStatus(order, 'selesai', 'Pesanan Selesai')"
                                            class="flex-1 h-10 py-2.5 px-4 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center gap-2 bg-slate-900 hover:bg-slate-800 text-white shadow-sm transition cursor-pointer active:scale-95">
                                        <svg class="w-4 h-4 text-white shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                        <span>Lanjut Selesai</span>
                                    </button>
                                </div>
                            </template>

                            <!-- If Selesai -->
                            <template x-if="order.status === 'selesai'">
                                <div class="flex items-center justify-between w-full">
                                    <span class="text-xs text-slate-400 font-medium flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-emerald-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                        <span>Pesanan Selesai</span>
                                    </span>
                                    <a :href="'{{ url('/penjual/pesanan/detail') }}?id=' + order.id" 
                                       class="h-10 py-2.5 px-4 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center text-center bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                                        <span>Lihat Bukti Transaksi</span>
                                    </a>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <div x-show="orders.length === 0" class="bg-white rounded-2xl border border-slate-200/80 p-8 text-center text-slate-500 text-xs">
            Tidak ada antrean pesanan yang memerlukan proses saat ini.
        </div>
    </div>

</div>
@endsection
