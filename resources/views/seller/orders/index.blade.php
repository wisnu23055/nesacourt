@extends('layouts.seller')

@section('title', 'Pesanan Masuk Dapur — NesaCourt')

@section('content')
<div class="space-y-6" x-data="{
    activeTab: 'all',
    searchQuery: '',
    autoRefresh: true,
    soundEnabled: true,
    orders: [
        {
            id: 'NC-20261021-8842',
            queueNo: '08',
            customerName: 'Dimas Prasetyo',
            customerType: 'Mahasiswa D4 TI',
            diningType: 'Makan di Tempat (Meja 14)',
            time: '11:42 WIB',
            status: 'menunggu_konfirmasi',
            statusLabel: 'Menunggu Verifikasi',
            paymentMethod: 'QRIS Bank Jatim',
            isPaid: true,
            totalPrice: 18000,
            notes: 'Sambal bawang jangan terlalu pedas, es batu sedikit saja.',
            items: [
                { name: 'Paket Nasi Geprek Sambal Bawang', qty: 1, price: 15000, note: 'Level 2 pedas sedang' },
                { name: 'Es Teh Manis Jumbo', qty: 1, price: 3000, note: 'Sedikit es' }
            ]
        },
        {
            id: 'NC-20261021-8845',
            queueNo: '09',
            customerName: 'Nadia Putri',
            customerType: 'Mahasiswi FEB',
            diningType: 'Bungkus / Bawa Pulang',
            time: '11:46 WIB',
            status: 'menunggu_konfirmasi',
            statusLabel: 'Menunggu Verifikasi',
            paymentMethod: 'QRIS GoPay',
            isPaid: true,
            totalPrice: 16000,
            notes: 'Bungkus terpisah ya kak, kuah lalapan dipisah.',
            items: [
                { name: 'Nasi Ayam Penyet Sambal Terasi', qty: 1, price: 16000, note: 'Sambal dipisah' }
            ]
        },
        {
            id: 'NC-20261021-8839',
            queueNo: '06',
            customerName: 'Rian Pratama',
            customerType: 'Mahasiswa Teknik Mesin',
            diningType: 'Makan di Tempat (Meja 08)',
            time: '11:30 WIB',
            status: 'memasak',
            statusLabel: 'Sedang Dimasak',
            paymentMethod: 'QRIS Mandiri',
            isPaid: true,
            totalPrice: 21000,
            notes: 'Ayam digoreng garing krispi ya pak.',
            items: [
                { name: 'Paket Nasi Geprek Sambal Bawang', qty: 1, price: 15000, note: 'Level 4 sangat pedas' },
                { name: 'Tempe Mendoan Hangat (Isi 3)', qty: 1, price: 6000, note: 'Kecap rawit banyak' }
            ]
        },
        {
            id: 'NC-20261021-8840',
            queueNo: '07',
            customerName: 'Dewi Lestari',
            customerType: 'Dosen Vokasi',
            diningType: 'Makan di Tempat (Meja 02)',
            time: '11:35 WIB',
            status: 'memasak',
            statusLabel: 'Sedang Dimasak',
            paymentMethod: 'QRIS BCA',
            isPaid: true,
            totalPrice: 15000,
            notes: 'Tanpa MSG, nasi setengah porsi saja.',
            items: [
                { name: 'Nasi Telur Krispi Pontianak', qty: 1, price: 10000, note: 'Kecap manis sedikit' },
                { name: 'Es Jeruk Peras Asli', qty: 1, price: 5000, note: 'Murni tanpa gula tambahan' }
            ]
        },
        {
            id: 'NC-20261021-8835',
            queueNo: '04',
            customerName: 'Bima Santoso',
            customerType: 'Mahasiswa FBS',
            diningType: 'Makan di Tempat (Meja 11)',
            time: '11:15 WIB',
            status: 'siap_ambil',
            statusLabel: 'Siap Diambil',
            paymentMethod: 'QRIS OVO',
            isPaid: true,
            totalPrice: 23000,
            notes: 'Sudah dipanggil di display kantin.',
            items: [
                { name: 'Paket Nasi Geprek Sambal Bawang', qty: 1, price: 15000, note: 'Pedas sedang' },
                { name: 'Kopi Susu Gula Aren Ketintang', qty: 1, price: 8000, note: 'Dingin' }
            ]
        },
        {
            id: 'NC-20261021-8831',
            queueNo: '02',
            customerName: 'Farhan Hidayat',
            customerType: 'Mahasiswa FIP',
            diningType: 'Bungkus / Bawa Pulang',
            time: '10:55 WIB',
            status: 'selesai',
            statusLabel: 'Pesanan Selesai',
            paymentMethod: 'QRIS Dana',
            isPaid: true,
            totalPrice: 18000,
            notes: 'Pesanan telah diambil pembeli.',
            items: [
                { name: 'Paket Nasi Geprek Sambal Bawang', qty: 1, price: 15000, note: 'Pedas' },
                { name: 'Es Teh Manis Jumbo', qty: 1, price: 3000, note: 'Normal' }
            ]
        }
    ],
    filteredOrders() {
        return this.orders.filter(order => {
            const matchesSearch = order.id.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                                  order.customerName.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                                  order.queueNo.includes(this.searchQuery);
            const matchesTab = this.activeTab === 'all' || order.status === this.activeTab;
            return matchesSearch && matchesTab;
        });
    },
    countByStatus(status) {
        if (status === 'all') return this.orders.length;
        return this.orders.filter(o => o.status === status).length;
    },
    advanceStatus(order, nextStatus, nextLabel) {
        order.status = nextStatus;
        order.statusLabel = nextLabel;
    }
}">

    <!-- Page Header & Action Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-7 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ url('/penjual/dashboard') }}" class="hover:text-brand-600">Portal Stan</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-400"></i>
                <span class="text-slate-800 font-semibold">Pesanan Masuk</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-semibold text-slate-900 tracking-tight">Antrean Pesanan Masuk Dapur</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-2 max-w-2xl">Pantau pesanan yang masuk, verifikasi status pembayaran QRIS, dan perbarui proses masak secara langsung.</p>
        </div>

        <!-- Filter Tabs Toolbar (Angka Tanpa Container) -->
        <div class="flex items-center gap-2 overflow-x-auto thin-scrollbar pt-6 mt-6 border-t border-slate-100 pb-1">
            <button type="button" 
                    @click="activeTab = 'all'" 
                    class="h-9 px-3.5 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center gap-2 transition"
                    :class="activeTab === 'all' ? 'bg-brand-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                <span>Semua Pesanan</span>
                <span class="font-bold text-xs" :class="activeTab === 'all' ? 'text-white' : 'text-slate-400'" x-text="countByStatus('all')"></span>
            </button>

            <button type="button" 
                    @click="activeTab = 'menunggu_konfirmasi'" 
                    class="h-9 px-3.5 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center gap-2 transition"
                    :class="activeTab === 'menunggu_konfirmasi' ? 'bg-amber-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                <span>Perlu Verifikasi</span>
                <span class="font-bold text-xs" :class="activeTab === 'menunggu_konfirmasi' ? 'text-white' : 'text-amber-600'" x-text="countByStatus('menunggu_konfirmasi')"></span>
            </button>

            <button type="button" 
                    @click="activeTab = 'memasak'" 
                    class="h-9 px-3.5 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center gap-2 transition"
                    :class="activeTab === 'memasak' ? 'bg-brand-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                <span>Sedang Dimasak</span>
                <span class="font-bold text-xs" :class="activeTab === 'memasak' ? 'text-white' : 'text-brand-600'" x-text="countByStatus('memasak')"></span>
            </button>

            <button type="button" 
                    @click="activeTab = 'siap_ambil'" 
                    class="h-9 px-3.5 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center gap-2 transition"
                    :class="activeTab === 'siap_ambil' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                <span>Siap Diambil</span>
                <span class="font-bold text-xs" :class="activeTab === 'siap_ambil' ? 'text-white' : 'text-emerald-600'" x-text="countByStatus('siap_ambil')"></span>
            </button>

            <button type="button" 
                    @click="activeTab = 'selesai'" 
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
                   placeholder="Cari berdasarkan nomor antrean (08), kode pesanan, atau nama pembeli..." 
                   class="w-full h-10 pl-10 pr-4 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
        </div>
    </div>

    <!-- Orders Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <template x-for="order in filteredOrders()" :key="order.id">
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex flex-col justify-between space-y-4 hover:border-slate-300 transition">
                <!-- Top Row: Customer with Plain Initial Avatar, & Status Tanpa Container -->
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <!-- Foto Profil Sementara / Inisial Polos -->
                        <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center shrink-0 uppercase" 
                             x-text="order.customerName ? order.customerName.charAt(0) : 'U'">
                        </div>
                        <div>
                            <div class="flex items-center gap-1.5">
                                <span class="font-bold text-slate-900 text-sm" x-text="order.customerName"></span>
                                <span class="text-slate-300">|</span>
                                <span class="text-[11px] text-slate-500" x-text="order.customerType"></span>
                            </div>
                            <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
                                <span class="text-[11px] font-semibold text-slate-600" x-text="order.id"></span>
                                <span class="text-slate-300">&bull;</span>
                                <span x-text="order.time"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Status Tanpa Container & Titik Kedap-Kedip -->
                    <div class="shrink-0 pt-0.5">
                        <div x-show="order.status === 'menunggu_konfirmasi'" class="flex items-center gap-1.5 text-xs font-bold text-amber-600">
                            <span class="relative flex h-2 w-2 shrink-0">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                            </span>
                            <span>Verifikasi Bayar</span>
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
                            <span>Selesai</span>
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
                    <span class="text-xs font-semibold text-slate-500 block">Item Masakan Dapur:</span>
                    <div class="space-y-1">
                        <template x-for="item in order.items" :key="item.name">
                            <div class="flex items-start justify-between">
                                <div>
                                    <span class="font-medium text-slate-500" x-text="item.qty + 'x ' + item.name"></span>
                                    <span x-show="item.note" class="text-[10px] text-slate-400 block" x-text="'(' + item.note + ')'"></span>
                                </div>
                                <span class="font-medium text-slate-500 shrink-0" x-text="'Rp ' + (item.price * item.qty).toLocaleString('id-ID')"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Total & Action Buttons Footer -->
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

                    <!-- Stage Step Action Buttons (Tombol Detail Teks Center) -->
                    <div class="flex items-center gap-2 pt-1">
                        <!-- If Menunggu Konfirmasi -->
                        <template x-if="order.status === 'menunggu_konfirmasi'">
                            <div class="flex items-center gap-2 w-full">
                                <a :href="'{{ url('/penjual/pesanan/detail') }}?id=' + order.id" 
                                   class="h-10 py-2.5 px-4 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center text-center bg-slate-100 hover:bg-slate-200 text-slate-700 transition w-24">
                                    <span>Detail</span>
                                </a>
                                <button type="button" 
                                        @click="advanceStatus(order, 'memasak', 'Sedang Dimasak')"
                                        class="flex-1 h-10 py-2.5 px-4 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-700 text-white shadow-sm shadow-brand-500/20 transition">
                                    <i data-lucide="flame" class="w-4 h-4"></i>
                                    <span>Verifikasi & Mulai Masak</span>
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
                                        class="flex-1 h-10 py-2.5 px-4 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm shadow-emerald-500/20 transition">
                                    <i data-lucide="bell" class="w-4 h-4"></i>
                                    <span>Tandai Siap Diambil</span>
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
                                        class="flex-1 h-10 py-2.5 px-4 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center gap-2 bg-slate-900 hover:bg-slate-800 text-white shadow-sm transition">
                                    <i data-lucide="check" class="w-4 h-4"></i>
                                    <span>Serahkan & Selesaikan</span>
                                </button>
                            </div>
                        </template>

                        <!-- If Selesai -->
                        <template x-if="order.status === 'selesai'">
                            <div class="flex items-center justify-between w-full">
                                <span class="text-xs text-slate-400 font-medium">Pesanan telah diserahkan</span>
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

    <!-- Empty State -->
    <div x-show="filteredOrders().length === 0" class="bg-white rounded-2xl border border-slate-200/80 p-12 text-center shadow-xs">
        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
            <i data-lucide="inbox" class="w-6 h-6"></i>
        </div>
        <h3 class="text-sm font-bold text-slate-800">Tidak ada antrean pesanan pada kategori ini</h3>
        <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Pesanan baru yang dibuat oleh mahasiswa di kantin akan langsung muncul di halaman ini secara real-time.</p>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });
</script>
@endsection
