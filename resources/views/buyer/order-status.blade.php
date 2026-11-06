@extends('layouts.buyer')

@section('title', 'Status Pesanan - NesaCourt')

@section('content')
<div class="space-y-6" x-data="{
    activeTab: 'all',
    searchQuery: '',
    orders: {{ json_encode($orders) }},
    currentStep: {{ $currentStep }},
    orderId: '{{ $order?->order_number ?? '' }}',
    queueNo: '{{ $order ? 'A-' . str_pad($order->id % 100, 2, '0', STR_PAD_LEFT) : 'A-01' }}',
    stallName: '{{ addslashes($order?->seller?->stall_name ?? 'Stan Kantin') }}',
    stallLocation: '{{ addslashes($order?->seller?->location ?? 'Food Court UNESA') }}',
    etaMinutes: {{ $order?->items?->first()?->menu?->estimated_time_minutes ?? 7 }},
    stallImage: '{{ $order?->seller?->logo_url ?: 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=120&q=80' }}',

    steps: [
        { id: 1, title: 'Pesanan Dibuat', desc: 'Data pesanan berhasil dicatat sistem' },
        { id: 2, title: 'Menunggu Bayar', desc: 'Menunggu proses transfer QRIS' },
        { id: 3, title: 'Dikonfirmasi', desc: 'Penjual memverifikasi transfer' },
        { id: 4, title: 'Sedang Dimasak', desc: 'Dapur stan menyiapkan porsi pesanan' },
        { id: 5, title: 'Siap Diambil', desc: 'Pesanan siap di counter stan' },
        { id: 6, title: 'Selesai', desc: 'Pesanan telah diterima mahasiswa' }
    ],

    filteredOrders() {
        return this.orders.filter(order => {
            const matchesSearch = !this.searchQuery || 
                                  order.id.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                                  order.stallName.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                                  order.queueNo.toLowerCase().includes(this.searchQuery.toLowerCase());
            const matchesTab = this.activeTab === 'all' || order.status === this.activeTab;
            return matchesSearch && matchesTab;
        });
    },

    countByStatus(status) {
        if (status === 'all') return this.orders.length;
        return this.orders.filter(o => o.status === status).length;
    },

    init() {
        this.$watch('activeTab', () => {
            this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
        });
        this.$watch('searchQuery', () => {
            this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
        });
        @if($showDetail && $order && !in_array($order->order_status, ['selesai', 'dibatalkan']))
        setInterval(() => {
            window.location.reload();
        }, 12000);
        @endif
        this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
    }
}">

@if(! $showDetail)
    <!-- ========================================== -->
    <!-- MODE 1: DAFTAR KARTU PESANAN (TEMA PENJUAL) -->
    <!-- ========================================== -->

    <!-- Header & Filter Tabs Toolbar (Identik dengan penjual/pesanan) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-7 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ url('/katalog') }}" class="hover:text-brand-600">Kantin Mahasiswa</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-400"></i>
                <span class="text-slate-800 font-semibold">Status Pesanan</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-semibold text-slate-900 tracking-tight">Antrean & Status Pesanan Anda</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-2 max-w-2xl">Pantau pesanan makanan di kantin UNESA secara langsung. Klik "Lihat Detail" pada stan terkait untuk melihat tahap pemrosesan pesanan.</p>
        </div>

        <!-- Filter Tabs Toolbar (Tanpa Tab 'Semua Pesanan', Sesuai Status Detail) -->
        <div class="flex items-center gap-2 overflow-x-auto thin-scrollbar pt-6 mt-6 border-t border-slate-100 pb-1">
            <button type="button" 
                    @click="activeTab = activeTab === 'menunggu_bayar' ? 'all' : 'menunggu_bayar'" 
                    class="h-9 px-3.5 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center gap-2 transition"
                    :class="activeTab === 'menunggu_bayar' ? 'bg-amber-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                <span>Menunggu Bayar</span>
                <span class="font-bold text-xs" :class="activeTab === 'menunggu_bayar' ? 'text-white' : 'text-amber-600'" x-text="countByStatus('menunggu_bayar')"></span>
            </button>

            <button type="button" 
                    @click="activeTab = activeTab === 'dikonfirmasi' ? 'all' : 'dikonfirmasi'" 
                    class="h-9 px-3.5 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center gap-2 transition"
                    :class="activeTab === 'dikonfirmasi' ? 'bg-amber-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                <span>Dikonfirmasi</span>
                <span class="font-bold text-xs" :class="activeTab === 'dikonfirmasi' ? 'text-white' : 'text-amber-600'" x-text="countByStatus('dikonfirmasi')"></span>
            </button>

            <button type="button" 
                    @click="activeTab = activeTab === 'memasak' ? 'all' : 'memasak'" 
                    class="h-9 px-3.5 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center gap-2 transition"
                    :class="activeTab === 'memasak' ? 'bg-brand-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                <span>Sedang Dimasak</span>
                <span class="font-bold text-xs" :class="activeTab === 'memasak' ? 'text-white' : 'text-brand-600'" x-text="countByStatus('memasak')"></span>
            </button>

            <button type="button" 
                    @click="activeTab = activeTab === 'siap_ambil' ? 'all' : 'siap_ambil'" 
                    class="h-9 px-3.5 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center gap-2 transition"
                    :class="activeTab === 'siap_ambil' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                <span>Siap Diambil</span>
                <span class="font-bold text-xs" :class="activeTab === 'siap_ambil' ? 'text-white' : 'text-emerald-600'" x-text="countByStatus('siap_ambil')"></span>
            </button>

            <button type="button" 
                    @click="activeTab = activeTab === 'selesai' ? 'all' : 'selesai'" 
                    class="h-9 px-3.5 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center gap-2 transition"
                    :class="activeTab === 'selesai' ? 'bg-slate-800 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                <span>Selesai</span>
                <span class="font-bold text-xs" :class="activeTab === 'selesai' ? 'text-white' : 'text-slate-400'" x-text="countByStatus('selesai')"></span>
            </button>
        </div>
    </div>

    <!-- Search Toolbar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs">
        <div class="relative">
            <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
            <input type="text" 
                   x-model="searchQuery" 
                   placeholder="Cari berdasarkan nomor antrean (08), kode pesanan, atau nama stan..." 
                   class="w-full h-10 pl-10 pr-4 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
        </div>
    </div>

    <!-- Orders Cards Grid (Tema Identik Penjual) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <template x-for="order in filteredOrders()" :key="order.id">
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex flex-col justify-between space-y-4 hover:border-slate-300 transition">
                <!-- Top Row: Stall Avatar & Name & Status Tanpa Container -->
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <template x-if="order.stallImage">
                            <img :src="order.stallImage" :alt="order.stallName" class="w-10 h-10 rounded-xl object-cover ring-1 ring-slate-200 shrink-0">
                        </template>
                        <template x-if="!order.stallImage">
                            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center shrink-0 uppercase" 
                                 x-text="order.stallInitials || 'ST'">
                            </div>
                        </template>
                        <div>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="font-bold text-slate-900 text-sm" x-text="order.stallName"></span>
                            </div>
                            <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5 flex-wrap">
                                <span class="text-[11px] font-semibold text-slate-600" x-text="order.id"></span>
                                <span class="text-slate-300">&bull;</span>
                                <span x-text="order.time"></span>
                                <span class="text-slate-300">&bull;</span>
                                <span class="font-bold text-brand-600" x-text="'Antrean ' + order.queueNo"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Status Sesuai Detail & Titik Kedap-Kedip (Tanpa Waktu) -->
                    <div class="shrink-0 pt-0.5">
                        <div x-show="['pesanan_dibuat', 'menunggu_bayar', 'dikonfirmasi'].includes(order.status)" class="flex items-center gap-1.5 text-xs font-bold text-amber-600">
                            <span class="relative flex h-2 w-2 shrink-0">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                            </span>
                            <span x-text="order.statusLabel"></span>
                        </div>

                        <div x-show="order.status === 'memasak'" class="flex items-center gap-1.5 text-xs font-bold text-brand-600">
                            <span class="relative flex h-2 w-2 shrink-0">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-brand-500"></span>
                            </span>
                            <span x-text="order.statusLabel"></span>
                        </div>

                        <div x-show="order.status === 'siap_ambil'" class="flex items-center gap-1.5 text-xs font-bold text-emerald-600">
                            <span class="relative flex h-2 w-2 shrink-0">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                            </span>
                            <span x-text="order.statusLabel"></span>
                        </div>

                        <div x-show="order.status === 'selesai'" class="flex items-center gap-1.5 text-xs font-bold text-slate-500">
                            <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                            <span>Selesai</span>
                        </div>

                        <div x-show="order.status === 'dibatalkan'" class="flex items-center gap-1.5 text-xs font-bold text-rose-500">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            <span>Dibatalkan</span>
                        </div>
                    </div>
                </div>

                <!-- Dining Type & Notes -->
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 space-y-1.5 text-xs">
                    <div class="flex items-center justify-between text-[11px]">
                        <span class="text-slate-500">Layanan:</span>
                        <span class="font-bold text-slate-800 flex items-center gap-1">
                            <i data-lucide="utensils" class="w-3 h-3 text-brand-600"></i>
                            <span x-text="order.diningType"></span>
                        </span>
                    </div>
                    <div x-show="order.notes" class="pt-1.5 border-t border-slate-200/60 text-[11px] text-slate-600 flex items-start gap-1.5">
                        <i data-lucide="message-square" class="w-3 h-3 text-amber-600 shrink-0 mt-0.5"></i>
                        <span class="italic" x-text="'Catatan: ' + order.notes"></span>
                    </div>
                </div>

                <!-- Ordered Items List -->
                <div class="space-y-1.5 text-xs">
                    <span class="text-xs font-semibold text-slate-500 block">Menu yang Dipesan:</span>
                    <div class="space-y-1">
                        <template x-for="item in order.items" :key="item.name">
                            <div class="flex items-start justify-between">
                                <div>
                                    <span class="font-medium text-slate-700" x-text="item.qty + 'x ' + item.name"></span>
                                    <span x-show="item.note" class="text-[10px] text-slate-400 block" x-text="'(' + item.note + ')'"></span>
                                </div>
                                <span class="font-medium text-slate-600 shrink-0" x-text="'Rp ' + (item.price * item.qty).toLocaleString('id-ID')"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Total & Action Buttons Footer -->
                <div class="pt-3 border-t border-slate-100 space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-slate-400 block">Total Pembayaran</span>
                            <span class="text-sm font-bold text-slate-800" x-text="'Rp ' + order.totalPrice.toLocaleString('id-ID')"></span>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] text-slate-400 block">Metode Pembayaran</span>
                            <span class="text-xs font-semibold text-emerald-700 flex items-center gap-1 justify-end">
                                <i data-lucide="check-circle" class="w-3 h-3 text-emerald-600"></i>
                                <span x-text="order.paymentMethod"></span>
                            </span>
                        </div>
                    </div>

                    <!-- Tombol Lihat Detail: Membuka Halaman Proses Real-time -->
                    <div class="flex items-center gap-2 pt-1">
                        <a :href="'{{ url('/pesanan/status') }}?order=' + order.id" 
                           class="w-full h-10 py-2.5 px-4 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-700 text-white shadow-sm shadow-brand-500/20 transition">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                            <span>Lihat Detail</span>
                        </a>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Empty State -->
    <div x-show="filteredOrders().length === 0" class="bg-white rounded-2xl border border-slate-200/80 p-12 text-center shadow-xs">
        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
            <i data-lucide="inbox" class="w-6 h-6"></i>
        </div>
        <h3 class="text-sm font-bold text-slate-800">Tidak ada antrean pesanan pada kategori ini</h3>
        <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Pesanan yang Anda buat di kantin UNESA akan muncul di sini secara real-time dari verifikasi hingga siap diambil.</p>
        <div class="mt-4">
            <a href="{{ url('/katalog') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-brand-600 text-white text-xs font-semibold hover:bg-brand-700 transition">
                <i data-lucide="utensils" class="w-4 h-4"></i>
                <span>Pesan Menu Kantin</span>
            </a>
        </div>
    </div>

@else
    <!-- ========================================== -->
    <!-- MODE 2: TAMPILAN PROSES DETAIL PESANAN     -->
    <!-- ========================================== -->

    <!-- Tombol Navigasi Kembali ke Daftar Pesanan -->
    <div class="flex items-center justify-between gap-3">
        <a href="{{ url('/pesanan/status') }}" 
           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition shadow-2xs">
            <i data-lucide="arrow-left" class="w-4 h-4 text-slate-500"></i>
            <span>Kembali ke Daftar Pesanan</span>
        </a>

        <div class="flex items-center gap-2 text-xs">
            <span class="text-slate-500 hidden sm:inline font-medium">ID Pesanan:</span>
            <span class="font-bold text-slate-800 bg-slate-100 px-2.5 py-1 rounded-lg" x-text="orderId">{{ $order?->order_number }}</span>
        </div>
    </div>

    @if($order && $order->order_status === 'dibatalkan')
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-950 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                <i data-lucide="x-circle" class="w-5 h-5"></i>
            </div>
            <div>
                <span class="text-sm font-bold text-rose-900 block">Pesanan Telah Dibatalkan</span>
                <p class="text-xs text-rose-700 mt-0.5">Transaksi ini dibatalkan dan seluruh stok porsi menu telah otomatis dikembalikan ke etalase stan.</p>
            </div>
        </div>
        <a href="{{ url('/katalog') }}" class="whitespace-nowrap inline-flex items-center justify-center gap-1.5 py-2 px-3.5 rounded-xl bg-rose-600 text-white text-xs font-semibold hover:bg-rose-700 transition">
            <span>Pesan Menu Baru</span>
            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
        </a>
    </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-7 shadow-xs space-y-5">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3.5">
                <img :src="stallImage" 
                     :alt="stallName" 
                     class="w-12 h-12 rounded-xl object-cover ring-1 ring-slate-200 shrink-0 shadow-2xs">
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight" x-text="stallName"></h1>
                        <span class="text-slate-300 font-normal">|</span>
                        <span class="text-xs font-semibold text-brand-600">Pelacakan Proses</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5" x-text="stallLocation"></p>
                </div>
            </div>

            <!-- Live Tracker Badge -->
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-700 shrink-0 self-start md:self-auto font-medium">
                <span class="relative flex h-2 w-2 shrink-0">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <span>Pelacakan Real-time</span>
            </div>
        </div>

        <!-- Order Ongoing Summary Card (Center Tengah, Rapi & Estetik) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- 1. Nomor ID Pesanan -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/70 flex flex-col items-center justify-center text-center space-y-1">
                <span class="text-[11px] text-slate-500 block font-medium">Nomor ID Pesanan</span>
                <div class="flex items-center justify-center gap-1.5 flex-wrap">
                    <span class="text-xs sm:text-sm font-bold text-slate-900" x-text="orderId"></span>
                    <span class="text-slate-300 font-normal">|</span>
                    <span class="text-xs font-semibold text-emerald-600">QRIS</span>
                </div>
            </div>

            <!-- 2. Stan Kantin & Lokasi -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/70 flex flex-col items-center justify-center text-center space-y-1">
                <span class="text-[11px] text-slate-500 block font-medium">Stan Kantin & Lokasi</span>
                <span class="text-xs sm:text-sm font-bold text-slate-900 block truncate max-w-full" x-text="stallName"></span>
                <span class="text-[11px] text-slate-500 block truncate max-w-full" x-text="stallLocation"></span>
            </div>

            <!-- 3. Nomor Antrean Pengambilan -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/70 flex flex-col items-center justify-center text-center space-y-0.5">
                <span class="text-[11px] text-slate-500 block font-medium">Nomor Antrean Pengambilan</span>
                <span class="text-2xl sm:text-3xl font-black text-slate-900" x-text="queueNo"></span>
            </div>

            <!-- 4. Estimasi Siap -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/70 flex flex-col items-center justify-center text-center space-y-1">
                <span class="text-[11px] text-slate-500 block font-medium">Estimasi Siap</span>
                <span class="text-base sm:text-lg font-bold text-slate-900 block" x-text="currentStep === 5 ? 'Siap Ambil!' : '~' + etaMinutes + ' Menit'"></span>
                <span class="text-[10px] text-slate-400 block">Di counter stan</span>
            </div>
        </div>
    </div>

    <!-- Real-time Stepper Progress Container -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-7 shadow-xs space-y-6">
        
        <!-- Status Terkini Banner yang Rapi & Proporsional -->
        <div class="p-4 sm:p-5 rounded-2xl bg-slate-50/70 border border-slate-200/70 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-brand-600 shrink-0 shadow-2xs">
                    <svg class="w-5 h-5 text-brand-600" x-show="currentStep === 4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 13.87A4 4 0 0 1 7.41 6a5.11 5.11 0 0 1 1.05-1.54 5 5 0 0 1 7.08 0A5.11 5.11 0 0 1 16.59 6 4 4 0 0 1 18 13.87V21H6Z"/><line x1="6" y1="17" x2="18" y2="17"/></svg>
                    <svg class="w-5 h-5 text-brand-600" x-show="currentStep === 1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    <svg class="w-5 h-5 text-brand-600" x-show="currentStep === 2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M7 7h.01"/><path d="M17 7h.01"/><path d="M7 17h.01"/><path d="M17 17h.01"/></svg>
                    <svg class="w-5 h-5 text-brand-600" x-show="currentStep === 3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <svg class="w-5 h-5 text-brand-600" x-show="currentStep === 5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                    <svg class="w-5 h-5 text-slate-700" x-show="currentStep === 6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-bold text-slate-900" x-text="steps[currentStep - 1].title">Sedang Dimasak</span>
                        <span class="text-slate-300 font-normal">|</span>
                        <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-brand-700">
                            <span class="relative flex h-2 w-2 shrink-0">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-brand-500"></span>
                            </span>
                            <span x-text="currentStep === 6 ? 'Pesanan Selesai' : (currentStep === 5 ? 'Siap Diambil di Counter' : 'Sedang Diproses')"></span>
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5" x-text="steps[currentStep - 1].desc"></p>
                </div>
            </div>

            <div class="flex items-center gap-2 self-start sm:self-auto shrink-0">
                <span class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-xs font-normal text-slate-600 shadow-2xs">
                    Tahapan <span class="font-normal text-slate-600" x-text="currentStep"></span> dari 6
                </span>
            </div>
        </div>

        <!-- Stepper Tracker Bar (Garis Center Presisi, Lurus & Dapat Di-scroll di Mobile & Tab) -->
        <div class="space-y-2">
            <div class="overflow-x-auto pb-2 pt-2 thin-scrollbar -mx-1 px-1">
                <div class="min-w-[620px] flex items-start">
                    <template x-for="(step, idx) in steps" :key="step.id">
                        <div class="flex-1 relative flex flex-col items-center text-center group">
                            
                            <!-- Connector Line to Previous Step -->
                            <div x-show="idx > 0" 
                                 class="absolute top-4 right-1/2 left-[-50%] h-1 -translate-y-1/2 z-0 transition-all duration-500"
                                 :class="step.id <= currentStep ? 'bg-brand-600' : 'bg-slate-100'"></div>

                            <!-- Circle Indicator -->
                            <div class="relative z-10 w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all shadow-xs shrink-0"
                                 :class="step.id < currentStep ? 'bg-brand-600 text-white shadow-2xs' : (step.id === currentStep ? 'bg-brand-600 text-white ring-4 ring-brand-100 shadow-2xs' : 'bg-white border-2 border-slate-200 text-slate-400')">
                                <template x-if="step.id < currentStep">
                                    <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                </template>
                                <template x-if="step.id >= currentStep">
                                    <span x-text="step.id"></span>
                                </template>
                            </div>

                            <!-- Title Text -->
                            <div class="mt-2.5 px-1 max-w-[100px]">
                                <span class="text-[11px] leading-tight block whitespace-normal"
                                      :class="step.id <= currentStep ? 'text-slate-900 font-bold' : 'text-slate-400 font-medium'"
                                      x-text="step.title">
                                </span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Petunjuk Geser Tipis Khusus Mobile -->
            <div class="flex items-center justify-center gap-1.5 text-[10px] text-slate-400 font-medium sm:hidden">
                <svg class="w-3 h-3 text-slate-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
                <span>Geser ke kanan / kiri</span>
                <svg class="w-3 h-3 text-slate-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </div>
        </div>

        <!-- Ready Alert Banner (Shown on Step 5) -->
        <div x-show="currentStep === 5" x-transition class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center gap-3 text-emerald-900">
            <div class="w-10 h-10 rounded-lg bg-emerald-600 text-white flex items-center justify-center shrink-0">
                <i data-lucide="bell-ring" class="w-5 h-5 animate-bounce"></i>
            </div>
            <div>
                <h4 class="text-xs font-bold">Pesanan Sudah Siap Diambil!</h4>
                <p class="text-[11px] text-emerald-700 mt-0.5">Silakan menuju ke <strong x-text="stallName"></strong> di counter kantin dan tunjukkan nomor antrean <strong x-text="queueNo"></strong>.</p>
            </div>
        </div>

    </div>

    <!-- Order Items Detail Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 space-y-4 shadow-xs">
        <h3 class="text-sm font-bold text-slate-900 pb-3 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="receipt" class="w-4 h-4 text-slate-900"></i>
                <span>Rincian Menu yang Dipesan</span>
            </div>
            <span class="text-xs font-semibold text-slate-500" x-text="stallName"></span>
        </h3>

        @if($order && $order->items->isNotEmpty())
            <div class="divide-y divide-slate-100 text-xs">
                @foreach($order->items as $item)
                    <div class="py-2.5 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-slate-800">{{ $item->quantity }}x {{ $item->menu_name_snapshot }}</span>
                            @if($item->note)
                                <p class="text-[11px] text-slate-400">Catatan: {{ $item->note }}</p>
                            @endif
                        </div>
                        <span class="font-bold text-slate-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-slate-900">
                <span>Total Pembayaran</span>
                <span class="text-sm text-brand-700">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
        @else
            <div class="divide-y divide-slate-100 text-xs">
                <div class="py-2.5 flex items-center justify-between">
                    <div>
                        <span class="font-bold text-slate-800">1x Nasi Rendang Daging</span>
                    </div>
                    <span class="font-bold text-slate-900">Rp 18.000</span>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-slate-900">
                <span>Total Pembayaran</span>
                <span class="text-sm text-brand-700">Rp 18.000</span>
            </div>
        @endif
    </div>

    <!-- Bottom Actions -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2">
        <a href="{{ url('/riwayat') }}" 
           class="whitespace-nowrap w-full sm:w-auto h-10 inline-flex items-center justify-center gap-1.5 py-2.5 px-4 rounded-xl border border-slate-200 bg-white text-slate-700 text-xs font-semibold hover:bg-slate-50 transition shadow-2xs">
            <i data-lucide="receipt" class="w-3.5 h-3.5"></i>
            <span>Buka Riwayat & Struk Digital</span>
        </a>

        <div class="flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto">
            @if($order && $order->order_status === 'dibuat' && $order->payment_status === 'menunggu')
            <form action="{{ route('buyer.order.cancel', ['order_number' => $order->order_number]) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini? Stok menu akan dikembalikan.')" class="w-full sm:w-auto">
                @csrf
                <button type="submit" class="whitespace-nowrap w-full sm:w-auto h-10 inline-flex items-center justify-center gap-1.5 py-2.5 px-4 rounded-xl border border-rose-200 bg-rose-50 text-rose-700 text-xs font-semibold hover:bg-rose-100 transition cursor-pointer">
                    <i data-lucide="trash-2" class="w-3.5 h-3.5 text-rose-600"></i>
                    <span>Batalkan Pesanan</span>
                </button>
            </form>
            @endif

            <a href="{{ url('/katalog') }}" 
               class="whitespace-nowrap w-full sm:w-auto h-10 inline-flex items-center justify-center gap-1.5 py-2.5 px-4 rounded-xl bg-brand-600 text-white text-xs font-semibold hover:bg-brand-700 shadow-sm transition">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                <span>Pesan Menu Lain</span>
            </a>
        </div>
    </div>
@endif

</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) {
            window.lucide.createIcons();
        }
    });
</script>
@endsection
