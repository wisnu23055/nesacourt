@extends('layouts.admin')

@section('title', 'Monitoring Pesanan — NesaCourt')

@section('content')
<div class="space-y-6" x-data="{
    activeTab: 'semua',
    stallFilter: 'semua',
    searchQuery: '',
    orders: [
        {
            id: 'NC-20261025-9921',
            queueNo: 'A-09',
            date: '25 Okt 2026, 12:45 WIB',
            buyerName: 'Aditya Pratama',
            buyerNim: '23055310001',
            buyerPhone: '081234567890',
            buyerImage: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=120&q=80',
            stallName: 'Stan 01 - Barokah Rasa',
            stallKey: 'stan_01',
            menuName: 'Ayam Geprek Sambal Bawang Level 3',
            menuImage: 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?auto=format&fit=crop&w=300&q=80',
            portions: 1,
            total: 13000,
            table: 'Meja 08',
            serviceType: 'Makan di Tempat',
            paymentMethod: 'QRIS Statis (Terverifikasi Lunas)',
            status: 'Sedang Dimasak',
            statusCode: 'proses'
        },
        {
            id: 'NC-20261025-9920',
            queueNo: 'B-04',
            date: '25 Okt 2026, 12:42 WIB',
            buyerName: 'Nadia Salsabila',
            buyerNim: '23055310034',
            buyerPhone: '081234567895',
            buyerImage: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=120&q=80',
            stallName: 'Stan 02 - Soto Bu Siti',
            stallKey: 'stan_02',
            menuName: 'Soto Ayam Lamongan Lengkap Koya',
            menuImage: 'https://images.unsplash.com/photo-1541832676-9b763b0239ab?auto=format&fit=crop&w=300&q=80',
            portions: 1,
            total: 14000,
            table: 'Meja 12',
            serviceType: 'Makan di Tempat',
            paymentMethod: 'QRIS Statis (Terverifikasi Lunas)',
            status: 'Siap Diambil di Counter',
            statusCode: 'siap'
        },
        {
            id: 'NC-20261025-9919',
            queueNo: 'D-12',
            date: '25 Okt 2026, 12:35 WIB',
            buyerName: 'Rizky Kurniawan',
            buyerNim: '23055310018',
            buyerPhone: '081234567896',
            buyerImage: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=120&q=80',
            stallName: 'Stan 04 - Aneka Jus Vokasi',
            stallKey: 'stan_04',
            menuName: 'Jus Alpukat Kental Susu Cokelat',
            menuImage: 'https://images.unsplash.com/photo-1553530666-ba11a7da3888?auto=format&fit=crop&w=300&q=80',
            portions: 2,
            total: 20000,
            table: 'Bawa Pulang',
            serviceType: 'Bungkus',
            paymentMethod: 'QRIS Statis (Terverifikasi Lunas)',
            status: 'Pesanan Selesai',
            statusCode: 'selesai'
        },
        {
            id: 'NC-20261025-9918',
            queueNo: 'C-07',
            date: '25 Okt 2026, 12:28 WIB',
            buyerName: 'Dewi Lestari',
            buyerNim: '23055310045',
            buyerPhone: '081234567897',
            buyerImage: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=120&q=80',
            stallName: 'Stan 03 - Geprek Mas Bro',
            stallKey: 'stan_03',
            menuName: 'Kentang Goreng Krispi French Fries',
            menuImage: 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?auto=format&fit=crop&w=300&q=80',
            portions: 1,
            total: 8000,
            table: 'Meja 04',
            serviceType: 'Makan di Tempat',
            paymentMethod: 'QRIS Statis (Terverifikasi Lunas)',
            status: 'Pesanan Selesai',
            statusCode: 'selesai'
        }
    ],

    formatRupiah(num) {
        return 'Rp ' + Number(num).toLocaleString('id-ID');
    },

    get filteredOrders() {
        return this.orders.filter(o => {
            const matchStatus = this.activeTab === 'semua' || o.statusCode === this.activeTab;
            const matchStall = this.stallFilter === 'semua' || o.stallKey === this.stallFilter;
            const matchSearch = this.searchQuery === '' || 
                                o.id.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                                o.buyerName.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                                o.menuName.toLowerCase().includes(this.searchQuery.toLowerCase());
            return matchStatus && matchStall && matchSearch;
        });
    }
}">

    <!-- Page Title Header (Semi-Bold with Black Icon) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200/80">
        <div>
            <div class="flex items-center gap-2.5">
                <i data-lucide="shopping-bag" class="w-6 h-6 text-slate-900 shrink-0"></i>
                <h1 class="text-xl sm:text-2xl font-semibold text-slate-900 tracking-tight">Monitoring Pesanan Food Court</h1>
            </div>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Pemantauan langsung arus pesanan seluruh stan kantin Ketintang, nomor antrean, verifikasi QRIS statis, dan status penyiapan di dapur.</p>
        </div>
        <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-xs font-semibold text-slate-700 shadow-2xs">
            <span class="relative flex h-2 w-2 shrink-0">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            <span>Live Monitor Aktif</span>
        </div>
    </div>

    <!-- Filter Tabs & Controls Bar -->
    <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs space-y-4">
        <!-- Status Tabs Row -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2 overflow-x-auto pb-1.5 sm:pb-0 thin-scrollbar text-xs">
                <button type="button" 
                        @click="activeTab = 'semua'" 
                        :class="activeTab === 'semua' ? 'bg-slate-900 text-white font-bold' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200'"
                        class="whitespace-nowrap px-3.5 py-1.5 rounded-xl transition">
                    Semua Pesanan (164)
                </button>
                <button type="button" 
                        @click="activeTab = 'proses'" 
                        :class="activeTab === 'proses' ? 'bg-slate-900 text-white font-bold' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200'"
                        class="whitespace-nowrap px-3.5 py-1.5 rounded-xl transition">
                    Sedang Dimasak (8)
                </button>
                <button type="button" 
                        @click="activeTab = 'siap'" 
                        :class="activeTab === 'siap' ? 'bg-slate-900 text-white font-bold' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200'"
                        class="whitespace-nowrap px-3.5 py-1.5 rounded-xl transition">
                    Siap Diambil (4)
                </button>
                <button type="button" 
                        @click="activeTab = 'selesai'" 
                        :class="activeTab === 'selesai' ? 'bg-slate-900 text-white font-bold' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200'"
                        class="whitespace-nowrap px-3.5 py-1.5 rounded-xl transition">
                    Pesanan Selesai (152)
                </button>
            </div>

            <!-- Stan Filter Dropdown -->
            <div class="relative">
                <select x-model="stallFilter" class="appearance-none text-[11px] sm:text-xs font-semibold py-1.5 pl-3 pr-8 sm:pl-3.5 sm:pr-10 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-brand-500 cursor-pointer w-full sm:w-auto">
                    <option value="semua">Semua Stan</option>
                    <option value="stan_01">Stan 01 - Barokah</option>
                    <option value="stan_02">Stan 02 - Soto Bu Siti</option>
                    <option value="stan_03">Stan 03 - Geprek Mas Bro</option>
                    <option value="stan_04">Stan 04 - Jus Vokasi</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-2.5 sm:right-3 flex items-center text-slate-400">
                    <i data-lucide="chevron-down" class="w-3 h-3 sm:w-3.5 sm:h-3.5" stroke-width="1.5"></i>
                </div>
            </div>
        </div>

        <!-- Search Input -->
        <div class="relative">
            <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
            <input type="text" 
                   x-model="searchQuery" 
                   placeholder="Cari ID pesanan, nama mahasiswa, menu makanan..." 
                   class="w-full pl-9 pr-3.5 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-brand-500">
        </div>
    </div>

    <!-- Orders Directory Table / Card Feed -->
    <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-xs divide-y divide-slate-100">
        <template x-for="order in filteredOrders" :key="order.id">
            <div class="p-4 sm:p-5 space-y-3.5 hover:bg-slate-50/40 transition">
                <!-- Header Card: Order ID, Queue, Date & Status -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2.5 border-b border-slate-100 text-xs">
                    <div class="flex items-center gap-2.5">
                        <span class="font-bold text-slate-900" x-text="order.id"></span>
                        <span class="text-slate-300 font-normal">|</span>
                        <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-800 font-bold" x-text="'Antrean: ' + order.queueNo"></span>
                        <span class="text-slate-300 font-normal">|</span>
                        <span class="text-slate-500 text-[11px]" x-text="order.date"></span>
                    </div>

                    <!-- Status Tanpa Container Pembungkus -->
                    <div class="flex items-center gap-2">
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

                <!-- Main Content: Food Photo, Buyer, Stall, Amount -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                    <!-- Column 1: Food Photo & Menu -->
                    <div class="flex items-center gap-3.5 min-w-0">
                        <img :src="order.menuImage" :alt="order.menuName" class="w-14 h-14 rounded-xl object-cover ring-1 ring-slate-200 shrink-0 shadow-2xs">
                        <div class="min-w-0">
                            <h4 class="text-xs sm:text-sm font-bold text-slate-900 truncate" x-text="order.portions + 'x ' + order.menuName"></h4>
                            <p class="text-xs text-brand-700 font-semibold mt-0.5" x-text="order.stallName"></p>
                            <span class="text-[11px] text-slate-400" x-text="'Layanan: ' + order.serviceType + ' (' + order.table + ')'"></span>
                        </div>
                    </div>

                    <!-- Column 2: Buyer Profile Info (Hanya Foto dan Nama Pemesan) -->
                    <div class="flex items-center gap-2.5 border-y md:border-y-0 md:border-x border-slate-100 py-2 md:py-0 md:px-4">
                        <img :src="order.buyerImage" :alt="order.buyerName" class="w-8 h-8 rounded-full object-cover ring-1 ring-slate-200 shrink-0">
                        <span class="text-xs sm:text-sm font-semibold text-slate-900 truncate" x-text="order.buyerName"></span>
                    </div>

                    <!-- Column 3: Payment & Action -->
                    <div class="flex items-center justify-between md:justify-end gap-4 text-xs">
                        <div class="text-left md:text-right">
                            <span class="text-[11px] text-slate-400 block">Total Tagihan</span>
                            <span class="text-sm font-bold text-slate-900 block" x-text="formatRupiah(order.total)"></span>
                            <span class="text-[10px] text-emerald-600 font-semibold" x-text="order.paymentMethod"></span>
                        </div>

                        <a :href="'{{ url('/admin/pesanan/detail') }}?id=' + order.id" class="whitespace-nowrap inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl bg-slate-900 text-white font-semibold hover:bg-slate-800 transition shadow-2xs">
                            <i data-lucide="file-check" class="w-3.5 h-3.5 text-white"></i>
                            <span>Audit Detail</span>
                        </a>
                    </div>
                </div>
            </div>
        </template>
    </div>

</div>
@endsection
