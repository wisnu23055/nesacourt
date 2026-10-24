@extends('layouts.seller')

@section('title', 'Detail Pesanan & Verifikasi — NesaCourt')

@section('content')
<div class="space-y-6" x-data="{
    status: 'memasak',
    statusLabel: 'Sedang Dimasak',
    step: 2,
    advanceStep(nextStep, nextStatus, nextLabel) {
        this.step = nextStep;
        this.status = nextStatus;
        this.statusLabel = nextLabel;
        this.$nextTick(() => {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    }
}">

    <!-- Page Header & Action Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-7 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                    <a href="{{ url('/penjual/dashboard') }}" class="hover:text-brand-600">Portal Stan</a>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-400"></i>
                    <a href="{{ url('/penjual/pesanan') }}" class="hover:text-brand-600">Pesanan Masuk</a>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-400"></i>
                    <span class="text-slate-800 font-semibold">Detail Transaksi</span>
                </div>
                <div class="flex items-center gap-2.5 flex-wrap">
                    <h1 class="text-xl sm:text-2xl font-semibold text-slate-900 tracking-tight">Detail Pesanan #08</h1>
                    <span class="text-xs px-2.5 py-0.5 rounded-lg bg-slate-100 text-slate-700 font-semibold border border-slate-200/80">NC-20261021-8842</span>
                </div>
                <p class="text-xs sm:text-sm text-slate-500 mt-2 max-w-xl">Dipesan pada 21 Oktober 2026 pukul 11:42 WIB &bull; Stan 01 Barokah Rasa.</p>
            </div>

            <!-- Buttons: On mobile displayed below description, on desktop aligned to the right -->
            <div class="flex items-center gap-2.5 shrink-0 pt-1 sm:pt-0">
                <button type="button" 
                        onclick="window.print()" 
                        class="flex-1 sm:flex-none h-10 py-2.5 px-4 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                    <i data-lucide="printer" class="w-4 h-4 text-slate-900"></i>
                    <span>Cetak Struk Dapur</span>
                </button>
                <a href="{{ url('/penjual/pesanan') }}" 
                   class="flex-1 sm:flex-none h-10 py-2.5 px-4 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                    <i data-lucide="arrow-left" class="w-4 h-4 text-slate-900"></i>
                    <span>Kembali ke Daftar</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Live Status Tracker (Consistent Stepper) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs">
        <div class="flex items-center gap-2 pb-3 border-b border-slate-100 mb-5">
            <i data-lucide="git-commit" class="w-4 h-4 text-slate-900"></i>
            <h2 class="text-sm font-bold text-slate-900">Perjalanan Status Pesanan</h2>
        </div>
        
        <div class="relative overflow-x-auto thin-scrollbar pb-2">
            <div class="flex items-center justify-between min-w-[550px] relative">
                <!-- Connector Line Behind Circles -->
                <div class="absolute left-6 right-6 top-4 -translate-y-1/2 h-0.5 bg-slate-200 z-0"></div>
                <div class="absolute left-6 top-4 -translate-y-1/2 h-0.5 bg-brand-600 transition-all duration-300 z-0"
                     :style="'width: ' + ((step - 1) / 3 * 100) + '%'"></div>

                <!-- Step 1: Menunggu Verifikasi -->
                <div class="relative z-10 flex flex-col items-center text-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all shadow-xs relative"
                         :class="step >= 1 ? 'bg-brand-600 text-white' : 'bg-slate-100 text-slate-400 border border-slate-200'">
                        <i data-lucide="check" class="w-4 h-4 text-white"></i>
                        <span class="absolute -top-1 -right-1 flex h-2.5 w-2.5" x-show="step === 1">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-brand-500"></span>
                        </span>
                    </div>
                    <span class="text-xs font-bold mt-2 text-slate-900">Menunggu Verifikasi</span>
                    <span class="text-[10px] text-slate-400">11:42 WIB</span>
                </div>

                <!-- Step 2: Dimasak -->
                <div class="relative z-10 flex flex-col items-center text-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all shadow-xs relative"
                         :class="step >= 2 ? 'bg-brand-600 text-white' : 'bg-slate-100 text-slate-400 border border-slate-200'">
                        <i data-lucide="flame" class="w-4 h-4" :class="step >= 2 ? 'text-white' : 'text-slate-400'"></i>
                        <span class="absolute -top-1 -right-1 flex h-2.5 w-2.5" x-show="step === 2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-brand-500"></span>
                        </span>
                    </div>
                    <span class="text-xs font-bold mt-2" :class="step >= 2 ? 'text-slate-900' : 'text-slate-400'">Sedang Dimasak</span>
                    <span class="text-[10px] text-slate-400">11:43 WIB</span>
                </div>

                <!-- Step 3: Siap Diambil -->
                <div class="relative z-10 flex flex-col items-center text-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all shadow-xs relative"
                         :class="step >= 3 ? 'bg-brand-600 text-white' : 'bg-slate-100 text-slate-400 border border-slate-200'">
                        <i data-lucide="bell" class="w-4 h-4" :class="step >= 3 ? 'text-white' : 'text-slate-400'"></i>
                        <span class="absolute -top-1 -right-1 flex h-2.5 w-2.5" x-show="step === 3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-brand-500"></span>
                        </span>
                    </div>
                    <span class="text-xs font-bold mt-2" :class="step >= 3 ? 'text-slate-900' : 'text-slate-400'">Siap Diambil</span>
                    <span class="text-[10px] text-slate-400">Estimasi 11:53 WIB</span>
                </div>

                <!-- Step 4: Selesai -->
                <div class="relative z-10 flex flex-col items-center text-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all shadow-xs relative"
                         :class="step >= 4 ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-400 border border-slate-200'">
                        <i data-lucide="check-check" class="w-4 h-4" :class="step >= 4 ? 'text-white' : 'text-slate-400'"></i>
                        <span class="absolute -top-1 -right-1 flex h-2.5 w-2.5" x-show="step === 4">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                        </span>
                    </div>
                    <span class="text-xs font-bold mt-2" :class="step >= 4 ? 'text-emerald-700' : 'text-slate-400'">Pesanan Selesai</span>
                    <span class="text-[10px] text-slate-400">Diserahkan</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2 Column Details: Items & Verification -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Left: Customer Details & Ordered Menu Items (7 cols) -->
        <div class="lg:col-span-7 space-y-6">
            <!-- Customer Information Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs space-y-3.5">
                <div class="flex items-center gap-2 pb-2.5 border-b border-slate-100">
                    <i data-lucide="user" class="w-4 h-4 text-slate-900"></i>
                    <h2 class="text-sm font-bold text-slate-900">Informasi Pemesan</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 block text-[11px]">Nama Pembeli:</span>
                        <span class="font-bold text-slate-900 block mt-0.5">Dimas Prasetyo</span>
                        <span class="text-slate-500 text-[11px]">D4 Manajemen Informatika - FT</span>
                    </div>

                    <div>
                        <span class="text-slate-400 block text-[11px]">Metode Pengambilan:</span>
                        <span class="font-bold text-slate-900 block mt-0.5">Makan di Tempat (Dine In)</span>
                        <span class="text-brand-600 text-[11px] font-semibold">Nomor Meja: Meja 14 Kantin Barat</span>
                    </div>

                    <div>
                        <span class="text-slate-400 block text-[11px]">Kontak WhatsApp:</span>
                        <span class="font-semibold text-slate-800 block mt-0.5">0812-3456-7890</span>
                    </div>

                    <div>
                        <span class="text-slate-400 block text-[11px]">Waktu Pemesanan:</span>
                        <span class="font-semibold text-slate-800 block mt-0.5">21 Okt 2026, 11:42:18 WIB</span>
                    </div>
                </div>

                <!-- Special Customer Kitchen Note -->
                <div class="p-3.5 rounded-xl bg-amber-50/60 border border-amber-200 text-xs text-amber-900 flex items-start gap-2 mt-2">
                    <i data-lucide="message-square" class="w-4 h-4 text-slate-900 shrink-0 mt-0.5"></i>
                    <div>
                        <span class="font-bold text-slate-900 block">Catatan Khusus untuk Dapur:</span>
                        <p class="mt-0.5 text-amber-900">"Sambal bawang jangan terlalu pedas, tolong es teh manisnya es batu sedikit saja ya pak."</p>
                    </div>
                </div>
            </div>

            <!-- Ordered Menu Items Breakdown -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs space-y-4">
                <div class="flex items-center gap-2 pb-2.5 border-b border-slate-100">
                    <i data-lucide="utensils" class="w-4 h-4 text-slate-900"></i>
                    <h2 class="text-sm font-bold text-slate-900">Rincian Menu Masakan</h2>
                </div>

                <div class="divide-y divide-slate-100 text-xs">
                    <!-- Item 1 -->
                    <div class="py-3 flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3">
                            <img src="https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?auto=format&fit=crop&w=150&q=80" 
                                 alt="Ayam Geprek" 
                                 class="w-12 h-12 rounded-xl object-cover shrink-0 ring-1 ring-slate-200">
                            <div>
                                <span class="font-bold text-slate-900 block">Paket Nasi Geprek Sambal Bawang</span>
                                <span class="text-[11px] text-slate-500">1 porsi &bull; Level 2 (Pedas Sedang)</span>
                                <span class="text-[10px] text-brand-600 font-semibold block mt-0.5">Lalapan timun lengkap</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-slate-900 block">Rp 15.000</span>
                            <span class="text-[11px] text-slate-400">@ Rp 15.000</span>
                        </div>
                    </div>

                    <!-- Item 2 -->
                    <div class="py-3 flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3">
                            <img src="https://images.unsplash.com/photo-1556679343-c7306c1976bc?auto=format&fit=crop&w=150&q=80" 
                                 alt="Es Teh Jumbo" 
                                 class="w-12 h-12 rounded-xl object-cover shrink-0 ring-1 ring-slate-200">
                            <div>
                                <span class="font-bold text-slate-900 block">Es Teh Manis Jumbo</span>
                                <span class="text-[11px] text-slate-500">1 porsi &bull; Gelas 22oz</span>
                                <span class="text-[10px] text-amber-700 font-semibold block mt-0.5">Permintaan: Sedikit es batu</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-slate-900 block">Rp 3.000</span>
                            <span class="text-[11px] text-slate-400">@ Rp 3.000</span>
                        </div>
                    </div>
                </div>

                <!-- Cost Calculation Summary -->
                <div class="pt-3 border-t border-slate-100 space-y-2 text-xs">
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal Makanan & Minuman</span>
                        <span class="font-bold text-slate-800">Rp 18.000</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Biaya Platform Aplikasi Kampus</span>
                        <span class="font-semibold text-slate-500">Rp 0 (Gratis)</span>
                    </div>
                    <div class="flex justify-between text-sm font-bold text-slate-900 pt-2 border-t border-slate-200/60">
                        <span>Total Bersih Diterima Stan</span>
                        <span class="text-brand-600 font-bold">Rp 18.000</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: QRIS Payment Verification & Fast Action (5 cols) -->
        <div class="lg:col-span-5 space-y-6">
            <!-- Payment Verification Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <i data-lucide="shield-check" class="w-4 h-4 text-slate-900"></i>
                        <h2 class="text-sm font-bold text-slate-900">Verifikasi Pembayaran QRIS</h2>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="relative flex h-1.5 w-1.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
                        </span>
                        Lunas
                    </span>
                </div>

                <div class="space-y-2.5 text-xs text-slate-600">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Metode Bayar:</span>
                        <span class="font-bold text-slate-900">QRIS Bank Jatim</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Nomor Transaksi (RRN):</span>
                        <span class="font-semibold text-slate-800">20261021884200192</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Waktu Bayar:</span>
                        <span class="text-slate-800">21 Okt 2026, 11:42:30 WIB</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Status Settlement:</span>
                        <span class="text-emerald-700 font-semibold">Terselesaikan Otomatis</span>
                    </div>
                </div>

                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 text-[11px] text-slate-500 space-y-1">
                    <span class="font-bold text-slate-700 block">Jaminan Keamanan:</span>
                    <p class="leading-relaxed">Nominal Rp 18.000 telah masuk ke saldo rekening penampung Stan 01 Barokah Rasa.</p>
                </div>
            </div>

            <!-- Kitchen Action Control Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs space-y-3.5">
                <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                    <i data-lucide="sliders" class="w-4 h-4 text-slate-900"></i>
                    <h2 class="text-sm font-bold text-slate-900">Kendali Status Dapur</h2>
                </div>

                <p class="text-xs text-slate-500">Pilih status berikutnya untuk memberitahukan posisi pesanan ke layar smartphone mahasiswa:</p>

                <div class="space-y-2 pt-1">
                    <button type="button" 
                            @click="advanceStep(2, 'memasak', 'Sedang Dimasak')" 
                            class="w-full h-10 py-2.5 px-4 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center gap-2 border transition"
                            :class="step === 2 ? 'bg-brand-600 text-white border-brand-600 shadow-xs' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100'">
                        <i data-lucide="flame" class="w-4 h-4" :class="step === 2 ? 'text-white' : 'text-slate-900'"></i>
                        <span>1. Sedang Dimasak di Dapur</span>
                    </button>

                    <button type="button" 
                            @click="advanceStep(3, 'siap_ambil', 'Siap Diambil')" 
                            class="w-full h-10 py-2.5 px-4 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center gap-2 border transition"
                            :class="step === 3 ? 'bg-emerald-600 text-white border-emerald-600 shadow-xs' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100'">
                        <i data-lucide="bell" class="w-4 h-4" :class="step === 3 ? 'text-white' : 'text-slate-900'"></i>
                        <span>2. Makanan Siap Diambil (Kirim Notifikasi)</span>
                    </button>

                    <button type="button" 
                            @click="advanceStep(4, 'selesai', 'Pesanan Selesai')" 
                            class="w-full h-10 py-2.5 px-4 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center gap-2 border transition"
                            :class="step === 4 ? 'bg-slate-900 text-white border-slate-900 shadow-xs' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100'">
                        <i data-lucide="check-check" class="w-4 h-4" :class="step === 4 ? 'text-white' : 'text-slate-900'"></i>
                        <span>3. Serahkan Makanan & Selesaikan Pesanan</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) {
            window.lucide.createIcons();
        }
    });
</script>
@endsection
