@extends('layouts.buyer')

@section('title', 'Keranjang Belanja — NesaCourt')

@section('content')
<div class="space-y-6">

    <!-- Breadcrumb & Header -->
    <div class="pb-4 border-b border-slate-200/80">
        <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
            <a href="{{ url('/dashboard') }}" class="hover:text-brand-600 transition">Beranda</a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
            <a href="{{ url('/katalog') }}" class="hover:text-brand-600 transition">Katalog</a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
            <span class="text-slate-900 font-semibold">Keranjang Belanja</span>
        </div>
        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Keranjang Pesanan</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">Periksa kembali rincian porsi makanan dan minuman Anda sebelum melakukan konfirmasi meja dan pembayaran.</p>
    </div>

    <!-- Empty State -->
    <template x-if="items.length === 0">
        <div class="text-center py-20 bg-white rounded-2xl border border-slate-200/80 p-8 space-y-4">
            <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                <i data-lucide="shopping-bag" class="w-8 h-8"></i>
            </div>
            <h3 class="text-base font-bold text-slate-900">Keranjang Belanja Anda Masih Kosong</h3>
            <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto">
                Anda belum memilih menu makanan atau minuman dari kantin UNESA Ketintang. Silakan telusuri katalog atau gunakan rekomendasi AI.
            </p>
            <div class="flex items-center justify-center gap-3 pt-2">
                <a href="{{ url('/katalog') }}" class="whitespace-nowrap inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-brand-600 text-white text-xs font-bold hover:bg-brand-700 shadow-sm transition">
                    <i data-lucide="utensils" class="w-4 h-4"></i>
                    <span>Buka Katalog Menu</span>
                </a>
                <a href="{{ url('/rekomendasi') }}" class="whitespace-nowrap inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 text-xs font-semibold hover:bg-slate-50 transition">
                    <i data-lucide="sparkles" class="w-4 h-4 text-amber-500"></i>
                    <span>Rekomendasi Cerdas</span>
                </a>
            </div>
        </div>
    </template>

    <!-- Cart With Items Layout -->
    <template x-if="items.length > 0">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Left 2 Cols: Items List -->
            <div class="lg:col-span-2 space-y-4">
                
                <!-- Single Seller Notice Banner -->
                <div class="p-3.5 rounded-2xl bg-brand-50 border border-brand-200 flex items-center justify-between text-xs text-brand-900">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-brand-600 text-white flex items-center justify-center shrink-0">
                            <i data-lucide="store" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <span class="block font-bold">Pesanan dari <span x-text="currentSeller"></span></span>
                            <span class="text-[11px] text-brand-700">QRIS Statis pembayaran ditujukan langsung ke stan ini</span>
                        </div>
                    </div>
                    <span class="text-[11px] font-semibold text-brand-700 bg-white px-2.5 py-1 rounded-md border border-brand-200">
                        1 Stan / Transaksi
                    </span>
                </div>

                <!-- Table / Item Cards -->
                <div class="bg-white rounded-2xl border border-slate-200/80 divide-y divide-slate-100 overflow-hidden shadow-xs">
                    <template x-for="item in items" :key="item.id">
                        <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 shrink-0">
                                    <i data-lucide="utensils" class="w-5 h-5 text-brand-600"></i>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-sm font-bold text-slate-900 truncate" x-text="item.name"></h4>
                                    <p class="text-xs text-brand-700 font-semibold mt-0.5" x-text="formatRupiah(item.price) + ' / porsi'"></p>
                                </div>
                            </div>

                            <div class="flex items-center justify-between sm:justify-end gap-5 shrink-0">
                                <!-- Qty Counter -->
                                <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-xl p-1">
                                    <button type="button" @click="updateQty(item.id, -1)" class="w-7 h-7 rounded-lg bg-white border border-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center hover:bg-slate-100 transition">-</button>
                                    <span class="text-xs font-bold w-6 text-center text-slate-900" x-text="item.qty"></span>
                                    <button type="button" @click="updateQty(item.id, 1)" class="w-7 h-7 rounded-lg bg-white border border-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center hover:bg-slate-100 transition">+</button>
                                </div>

                                <!-- Item Subtotal -->
                                <div class="text-right min-w-[80px]">
                                    <span class="text-xs text-slate-400 block sm:hidden">Subtotal:</span>
                                    <span class="text-sm font-bold text-slate-900" x-text="formatRupiah(item.price * item.qty)"></span>
                                </div>

                                <!-- Delete Single Item -->
                                <button type="button" @click="updateQty(item.id, -item.qty)" class="p-1.5 text-slate-400 hover:text-rose-600 transition" title="Hapus menu">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Action Bar Bottom -->
                <div class="flex items-center justify-between pt-2">
                    <a href="{{ url('/katalog') }}" class="whitespace-nowrap text-xs font-semibold text-brand-600 hover:text-brand-700 flex items-center gap-1.5">
                        <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                        <span>Tambah Menu Lainnya</span>
                    </a>
                    <button type="button" @click="clearCart()" class="whitespace-nowrap text-xs font-semibold text-rose-600 hover:text-rose-700 flex items-center gap-1.5">
                        <i data-lucide="trash" class="w-3.5 h-3.5"></i>
                        <span>Kosongkan Seluruh Keranjang</span>
                    </button>
                </div>
            </div>

            <!-- Right 1 Col: Order Summary & Checkout CTA -->
            <div class="space-y-4">
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 space-y-4 shadow-xs">
                    <h3 class="text-sm font-bold text-slate-900 pb-3 border-b border-slate-100">Ringkasan Pembayaran</h3>

                    <div class="space-y-2.5 text-xs text-slate-600">
                        <div class="flex items-center justify-between">
                            <span>Total Jumlah Porsi</span>
                            <span class="font-bold text-slate-800" x-text="totalCount + ' Porsi'"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Subtotal Menu</span>
                            <span class="font-bold text-slate-800" x-text="formatRupiah(subtotal)"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-1">
                                <span>Biaya Layanan Kantin</span>
                                <i data-lucide="info" class="w-3.5 h-3.5 text-slate-400" title="Bebas biaya pemesanan digital NesaCourt"></i>
                            </div>
                            <span class="font-semibold text-emerald-600">Gratis (Rp 0)</span>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-900">Total Tagihan</span>
                        <span class="text-base sm:text-lg font-extrabold text-brand-700" x-text="formatRupiah(subtotal)"></span>
                    </div>

                    <a href="{{ url('/checkout') }}" class="whitespace-nowrap w-full h-10 inline-flex items-center justify-center gap-1.5 py-2.5 px-4 rounded-xl bg-brand-600 text-white text-xs font-semibold hover:bg-brand-700 shadow-sm transition">
                        <span>Lanjut Konfirmasi Pesanan</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </a>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-start gap-2 text-[11px] text-slate-500 leading-relaxed">
                        <i data-lucide="shield-check" class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5"></i>
                        <span>Pembayaran aman via QRIS Statis langsung ditransfer ke rekening pemilik stan.</span>
                    </div>
                </div>
            </div>

        </div>
    </template>

</div>
@endsection
