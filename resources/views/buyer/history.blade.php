@extends('layouts.buyer')

@section('title', 'Riwayat Pesanan — NesaCourt')

@section('content')
<div class="space-y-6" x-data="{
    activeTab: 'semua',
    receiptModalOpen: false,
    selectedOrder: null,

    orders: {{ json_encode($orders) }},

    openReceipt(order) {
        this.selectedOrder = order;
        this.receiptModalOpen = true;
    },

    get filteredOrders() {
        if (this.activeTab === 'semua') return this.orders;
        return this.orders.filter(o => o.statusCode === this.activeTab);
    }
}">

    <!-- Top Header Card (Identik dengan Dashboard) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-7 shadow-xs space-y-5">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3.5">
                <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=120&q=80" 
                     alt="Foto Mahasiswa" 
                     class="w-12 h-12 rounded-xl object-cover ring-1 ring-slate-200 shrink-0">
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">Riwayat Transaksi Pesanan</h1>
                        <span class="text-slate-300 font-normal">|</span>
                        <span class="text-xs font-semibold text-brand-600">Aditya Pratama</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">D4 Manajemen Informatika | Vokasi UNESA Ketintang (NIM: 23055310001)</p>
                </div>
            </div>

            <!-- Operating Hours Badge with Blinking Dot -->
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600 shrink-0 self-start md:self-auto">
                <span class="relative flex h-2 w-2 shrink-0">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <span>Kantin Ketintang Buka | 08.00 - 17.00 WIB</span>
            </div>
        </div>

        <!-- Filter Tabs & New Order CTA Row -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-1">
            <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
                <button type="button" 
                        @click="activeTab = 'semua'" 
                        :class="activeTab === 'semua' ? 'bg-brand-600 text-white font-bold shadow-2xs' : 'bg-slate-50 border border-slate-200 text-slate-700 hover:bg-slate-100'"
                        class="whitespace-nowrap px-4 py-2 rounded-xl text-xs font-semibold transition">
                    Semua Pesanan
                </button>
                <button type="button" 
                        @click="activeTab = 'diproses'" 
                        :class="activeTab === 'diproses' ? 'bg-brand-600 text-white font-bold shadow-2xs' : 'bg-slate-50 border border-slate-200 text-slate-700 hover:bg-slate-100'"
                        class="whitespace-nowrap px-4 py-2 rounded-xl text-xs font-semibold transition">
                    Sedang Berjalan
                </button>
                <button type="button" 
                        @click="activeTab = 'selesai'" 
                        :class="activeTab === 'selesai' ? 'bg-brand-600 text-white font-bold shadow-2xs' : 'bg-slate-50 border border-slate-200 text-slate-700 hover:bg-slate-100'"
                        class="whitespace-nowrap px-4 py-2 rounded-xl text-xs font-semibold transition">
                    Selesai
                </button>
            </div>

            <a href="{{ url('/katalog') }}" class="whitespace-nowrap inline-flex items-center justify-center gap-1.5 py-2 px-4 rounded-xl bg-brand-600 text-white text-xs sm:text-sm font-semibold hover:bg-brand-700 shadow-sm transition">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Pesan Makanan Baru</span>
            </a>
        </div>
    </div>

    <!-- List Order Cards (Identik dengan Card Pelacakan di Dashboard) -->
    <div class="space-y-4">
        <template x-for="order in filteredOrders" :key="order.id">
            <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs space-y-4 hover:shadow-md transition">
                
                <!-- Card Header -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-3 min-w-0">
                        <img :src="order.stallImage" 
                             :alt="order.stall" 
                             class="w-11 h-11 rounded-xl object-cover ring-1 ring-slate-200 shrink-0 shadow-2xs">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-xs font-bold text-slate-900" x-text="order.id"></span>
                                <span class="text-slate-300 font-normal">|</span>
                                <span class="text-xs font-bold text-slate-800" x-text="order.stall"></span>
                                <span class="text-slate-300 font-normal">|</span>
                                
                                <!-- Status tanpa container box -->
                                <span x-show="order.statusCode === 'diproses'" class="text-xs font-bold text-amber-600" x-text="order.status"></span>
                                <span x-show="order.statusCode === 'selesai'" class="text-xs font-bold text-emerald-600" x-text="order.status"></span>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5" x-text="order.date + ' | ' + order.type + (order.table !== '-' ? ' (' + order.table + ')' : '')"></p>
                        </div>
                    </div>

                    <!-- Right Quick Tracker Action if Ongoing -->
                    <div class="flex items-center gap-2 shrink-0">
                        <a x-show="order.statusCode === 'diproses'" 
                           href="{{ url('/pesanan/status') }}" 
                           class="whitespace-nowrap inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-brand-600 bg-brand-50 hover:bg-brand-100 transition">
                            <i data-lucide="radio" class="w-3.5 h-3.5 text-brand-600"></i>
                            <span>Pantau Real-time</span>
                        </a>
                    </div>
                </div>

                <!-- Card Body Item List -->
                <div class="space-y-1.5 text-xs">
                    <template x-for="it in order.items" :key="it.name">
                        <div class="flex items-center justify-between text-slate-600">
                            <span class="font-medium text-slate-800" x-text="it.qty + 'x ' + it.name"></span>
                            <span class="font-bold text-slate-900" x-text="formatRupiah(it.price * it.qty)"></span>
                        </div>
                    </template>
                </div>

                <!-- Card Footer & Single Line Action Buttons -->
                <div class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-2 text-xs">
                        <span class="text-slate-500">Total Transaksi:</span>
                        <span class="text-sm font-extrabold text-brand-700" x-text="formatRupiah(order.total)"></span>
                        <span class="text-slate-300">|</span>
                        <span class="text-[11px] text-emerald-700 font-semibold" x-text="order.paymentMethod"></span>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" 
                                @click="openReceipt(order)" 
                                class="whitespace-nowrap inline-flex items-center justify-center gap-1.5 py-2 px-3.5 rounded-xl border border-slate-200 bg-white text-slate-700 text-xs font-semibold hover:bg-slate-50 transition shadow-2xs">
                            <i data-lucide="receipt" class="w-3.5 h-3.5"></i>
                            <span>Lihat Struk Digital</span>
                        </button>
                        
                        <button x-show="order.statusCode === 'selesai'" 
                                type="button" 
                                @click="addToCart({ id: 1, name: order.items[0].name, price: order.items[0].price, seller: order.stall })" 
                                class="whitespace-nowrap inline-flex items-center justify-center gap-1.5 py-2 px-3.5 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold hover:bg-slate-200 transition">
                            <i data-lucide="rotate-cw" class="w-3.5 h-3.5"></i>
                            <span>Pesan Lagi</span>
                        </button>
                    </div>
                </div>

            </div>
        </template>
    </div>

    <!-- Empty State -->
    <div x-show="filteredOrders.length === 0" class="text-center py-16 bg-white rounded-2xl border border-slate-200/80 p-8 space-y-3">
        <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
            <i data-lucide="receipt" class="w-6 h-6"></i>
        </div>
        <h4 class="text-sm font-bold text-slate-900">Tidak Ada Riwayat Transaksi</h4>
        <p class="text-xs text-slate-500 max-w-sm mx-auto">Anda belum memiliki transaksi pesanan pada kategori status ini.</p>
    </div>

    <!-- Digital Receipt Modal -->
    <template x-teleport="body">
        <div x-show="receiptModalOpen" 
             x-cloak 
             class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-5 shadow-2xl overflow-y-auto max-h-[90vh]" @click.outside="receiptModalOpen = false">
                
                <!-- Receipt Header -->
                <div class="text-center space-y-1 pb-4 border-b border-dashed border-slate-300">
                    <div class="w-10 h-10 rounded-xl bg-brand-600 text-white flex items-center justify-center font-bold text-lg mx-auto shadow-xs">
                        N
                    </div>
                    <h3 class="text-base font-bold text-slate-900 pt-1">NesaCourt UNESA</h3>
                    <p class="text-xs text-slate-500" x-text="selectedOrder?.stall"></p>
                    <p class="text-[10px] text-slate-400">Food Court Vokasi Ketintang Surabaya</p>
                </div>

                <!-- Receipt Metadata -->
                <div class="text-xs text-slate-600 space-y-1.5 font-mono">
                    <div class="flex justify-between">
                        <span>No. Nota:</span>
                        <strong x-text="selectedOrder?.id" class="text-slate-900"></strong>
                    </div>
                    <div class="flex justify-between">
                        <span>Tanggal:</span>
                        <span x-text="selectedOrder?.date"></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Pelanggan:</span>
                        <span>Aditya Pratama (NIM: 23055310001)</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Tipe Layanan:</span>
                        <span x-text="selectedOrder?.type + ' (' + selectedOrder?.table + ')'"></span>
                    </div>
                </div>

                <!-- Receipt Itemized Table -->
                <div class="pt-3 border-t border-dashed border-slate-300 space-y-2 text-xs">
                    <template x-for="item in (selectedOrder?.items || [])" :key="item.name">
                        <div class="flex justify-between">
                            <div>
                                <span x-text="item.qty + 'x ' + item.name" class="font-medium text-slate-800"></span>
                                <span class="block text-[10px] text-slate-400" x-text="'@ ' + formatRupiah(item.price)"></span>
                            </div>
                            <span class="font-bold text-slate-900" x-text="formatRupiah(item.price * item.qty)"></span>
                        </div>
                    </template>
                </div>

                <!-- Receipt Totals -->
                <div class="pt-3 border-t border-dashed border-slate-300 space-y-1.5 text-xs">
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal:</span>
                        <span x-text="formatRupiah(selectedOrder?.total || 0)"></span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Biaya Platform:</span>
                        <span class="text-emerald-600 font-semibold">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-sm font-bold text-slate-900 pt-1 border-t border-slate-200">
                        <span>Total Lunas:</span>
                        <span class="text-brand-700" x-text="formatRupiah(selectedOrder?.total || 0)"></span>
                    </div>
                    <div class="flex justify-between text-[11px] text-emerald-700 font-semibold pt-1">
                        <span>Metode:</span>
                        <span x-text="selectedOrder?.paymentMethod"></span>
                    </div>
                </div>

                <!-- Footer Message -->
                <div class="text-center text-[10px] text-slate-400 pt-2 border-t border-dashed border-slate-300">
                    Terima kasih atas pesanan Anda.<br>Selamat menikmati hidangan kantin UNESA Ketintang!
                </div>

                <!-- Actions Modal -->
                <div class="grid grid-cols-2 gap-2 pt-2">
                    <button type="button" 
                            onclick="window.print()" 
                            class="whitespace-nowrap py-2 px-3 rounded-xl border border-slate-200 bg-white text-slate-700 text-xs font-semibold hover:bg-slate-50 transition flex items-center justify-center gap-1.5">
                        <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                        <span>Cetak Struk</span>
                    </button>
                    <button type="button" 
                            @click="receiptModalOpen = false" 
                            class="whitespace-nowrap py-2 px-3 rounded-xl bg-slate-900 text-white text-xs font-semibold hover:bg-slate-800 transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </template>

</div>
@endsection
