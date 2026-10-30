@extends('layouts.buyer')

@section('title', 'Dashboard Pembeli — NesaCourt')

@section('content')
<div class="space-y-6" x-data="{
    activeOrder: {{ $activeOrder ? json_encode($activeOrder) : 'null' }},
    budgetFilter: 'semua',
    popularMenus: {{ json_encode($popularMenus) }},
    get filteredMenus() {
        if (this.budgetFilter === 'semua') return this.popularMenus;
        const maxPrice = parseInt(this.budgetFilter);
        return this.popularMenus.filter(m => m.price <= maxPrice);
    }
}">

    <!-- Student Welcome Card & Fast Search Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-7 shadow-xs space-y-5">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3.5">
                <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=120&q=80" 
                     alt="Foto Mahasiswa" 
                     class="w-12 h-12 rounded-xl object-cover ring-1 ring-slate-200 shrink-0">
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">Halo, {{ auth()->user()->name }}</h1>
                        <span class="text-slate-300 font-normal">|</span>
                        <span class="text-xs font-semibold text-brand-600">Mahasiswa</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">D4 Manajemen Informatika | Vokasi UNESA Ketintang (NIM: {{ auth()->user()->phone ?? '23055310001' }})</p>
                </div>
            </div>

            <!-- Operating Hours Badge -->
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600 shrink-0 self-start md:self-auto">
                <span class="relative flex h-2 w-2 shrink-0">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <span>Kantin Ketintang Buka | 08.00 - 17.00 WIB</span>
            </div>
        </div>

        <!-- Direct Fast Search Input -->
        <form action="{{ url('/katalog') }}" method="GET" class="relative">
            <div class="flex flex-col sm:flex-row items-center gap-2.5">
                <div class="relative w-full">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                    <input type="text" 
                           name="q" 
                           placeholder="Cari makanan, soto ayam, geprek, jus buah, atau stan kantin..." 
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 text-xs sm:text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition">
                </div>
                <button type="submit" 
                        class="whitespace-nowrap w-full sm:w-auto inline-flex items-center justify-center gap-1.5 py-2.5 px-5 rounded-xl bg-brand-600 text-white text-xs sm:text-sm font-semibold hover:bg-brand-700 shadow-sm transition">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    <span>Cari Menu</span>
                </button>
            </div>
        </form>

        <!-- Category Shortcuts (Horizontal di Samping Icon, Center Container & Ramping) -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5 pt-1">
            <a href="{{ url('/katalog?kategori=makanan') }}" 
               class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/80 hover:border-brand-300 hover:bg-brand-50/40 transition flex items-center justify-center gap-2.5 group">
                <div class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-700 group-hover:text-brand-600 shrink-0 shadow-2xs">
                    <i data-lucide="utensils" class="w-4 h-4"></i>
                </div>
                <div class="min-w-0">
                    <span class="text-xs font-bold text-slate-900 block truncate group-hover:text-brand-600">Makanan</span>
                    <span class="text-[10px] text-slate-400 block truncate">Nasi & Ayam</span>
                </div>
            </a>

            <a href="{{ url('/katalog?kategori=berkuah') }}" 
               class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/80 hover:border-brand-300 hover:bg-brand-50/40 transition flex items-center justify-center gap-2.5 group">
                <div class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-700 group-hover:text-brand-600 shrink-0 shadow-2xs">
                    <i data-lucide="flame" class="w-4 h-4"></i>
                </div>
                <div class="min-w-0">
                    <span class="text-xs font-bold text-slate-900 block truncate group-hover:text-brand-600">Soto & Kuah</span>
                    <span class="text-[10px] text-slate-400 block truncate">Soto & Rawon</span>
                </div>
            </a>

            <a href="{{ url('/katalog?kategori=minuman') }}" 
               class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/80 hover:border-brand-300 hover:bg-brand-50/40 transition flex items-center justify-center gap-2.5 group">
                <div class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-700 group-hover:text-brand-600 shrink-0 shadow-2xs">
                    <i data-lucide="coffee" class="w-4 h-4"></i>
                </div>
                <div class="min-w-0">
                    <span class="text-xs font-bold text-slate-900 block truncate group-hover:text-brand-600">Minuman</span>
                    <span class="text-[10px] text-slate-400 block truncate">Es Teh & Jus</span>
                </div>
            </a>

            <a href="{{ url('/katalog?kategori=camilan') }}" 
               class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/80 hover:border-brand-300 hover:bg-brand-50/40 transition flex items-center justify-center gap-2.5 group">
                <div class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-700 group-hover:text-brand-600 shrink-0 shadow-2xs">
                    <i data-lucide="cookie" class="w-4 h-4"></i>
                </div>
                <div class="min-w-0">
                    <span class="text-xs font-bold text-slate-900 block truncate group-hover:text-brand-600">Camilan</span>
                    <span class="text-[10px] text-slate-400 block truncate">Kentang & Roti</span>
                </div>
            </a>

            <a href="{{ url('/katalog?kategori=paket') }}" 
               class="col-span-2 sm:col-span-1 p-2.5 rounded-xl bg-slate-50 border border-slate-200/80 hover:border-brand-300 hover:bg-brand-50/40 transition flex items-center justify-center gap-2.5 group">
                <div class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-700 group-hover:text-brand-600 shrink-0 shadow-2xs">
                    <i data-lucide="badge-percent" class="w-4 h-4"></i>
                </div>
                <div class="min-w-0">
                    <span class="text-xs font-bold text-slate-900 block truncate group-hover:text-brand-600">Paket Hemat</span>
                    <span class="text-[10px] text-slate-400 block truncate">Komplit Murah</span>
                </div>
            </a>
        </div>
    </div>

    <!-- Active Ongoing Order Tracker Card (Dengan Foto Pesanan) -->
    <div x-show="activeOrder" class="p-5 sm:p-6 rounded-2xl bg-white border border-slate-200/80 shadow-xs space-y-4">
        <!-- Section Title & Description -->
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <i data-lucide="activity" class="w-4 h-4 text-slate-900"></i>
                <h2 class="text-sm font-bold text-slate-900">Pesanan Aktif Sedang Diproses</h2>
            </div>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-800 border border-amber-200 text-[11px] font-bold">
                <span class="relative flex h-1.5 w-1.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-amber-500"></span>
                </span>
                Proses Dapur
            </span>
        </div>
        <p class="text-xs text-slate-500 -mt-1">Pantau kemajuan pesanan makanan Anda yang sedang dimasak di dapur stan kantin secara real-time.</p>

        <!-- Order Information Box dengan Foto Makanan -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3.5 p-4 rounded-xl bg-slate-50 border border-slate-100">
            <div class="flex items-center gap-3.5">
                <img :src="activeOrder.image" 
                     alt="Foto Pesanan Aktif" 
                     class="w-12 h-12 rounded-xl object-cover ring-1 ring-slate-200 shrink-0 shadow-2xs">
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-bold text-slate-900" x-text="activeOrder.id">NC-20261021-8842</span>
                        <span class="text-slate-300 font-normal">|</span>
                        <span class="text-xs font-bold text-amber-600" x-text="activeOrder.status">Sedang Dimasak di Dapur</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5" x-text="activeOrder.stall + ' | ' + activeOrder.statusDesc">Stan 01 - Barokah Rasa | Pesanan nomor antrean A-09 sedang disiapkan penjual</p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ url('/pesanan/status') }}" class="whitespace-nowrap inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold border border-slate-200 bg-white text-slate-700 hover:bg-slate-100 transition shadow-2xs">
                    <i data-lucide="radio" class="w-3.5 h-3.5 text-slate-900"></i>
                    <span>Pantau Status Real-time</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5 text-slate-400"></i>
                </a>
            </div>
        </div>

        <!-- Progress Step Bar -->
        <div class="space-y-1.5 pt-1">
            <div class="flex items-center justify-between text-xs text-slate-500">
                <span class="font-medium text-slate-700">Estimasi siap ambil: <strong class="text-slate-900" x-text="activeOrder.eta">~7 Menit</strong></span>
                <span class="font-bold text-brand-600" x-text="activeOrder.progress + '%'">65%</span>
            </div>
            <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden">
                <div class="h-full bg-brand-600 rounded-full transition-all duration-500" :style="'width: ' + activeOrder.progress + '%'"></div>
            </div>
        </div>
    </div>

    <!-- Rekomendasi Menu Sesuai Uang Saku Mahasiswa (Header Rata Kiri) -->
    <div class="p-5 sm:p-6 rounded-2xl bg-white border border-slate-200/80 shadow-xs space-y-4">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <i data-lucide="wallet" class="w-4 h-4 text-slate-900"></i>
                <h2 class="text-sm sm:text-base font-bold text-slate-900">Rekomendasi Menu Sesuai Uang Saku Mahasiswa</h2>
            </div>
            <p class="text-xs text-slate-500">Pilih batas pengeluaran jajan Anda untuk menampilkan pilihan menu yang pas di kantin</p>
        </div>

        <!-- Budget Selection Cards Grid (Center Tengah & Rapi) -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5 pt-1">
            <button type="button" 
                    @click="budgetFilter = 'semua'" 
                    class="p-3 rounded-xl border text-center transition select-none flex flex-col items-center justify-center gap-1"
                    :class="budgetFilter === 'semua' 
                        ? 'border-brand-600 bg-brand-50/70 ring-1 ring-brand-500/20 shadow-2xs' 
                        : 'border-slate-200 bg-slate-50 hover:bg-slate-100/80'">
                <span class="text-[10px] font-semibold text-slate-500">Semua Harga</span>
                <span class="text-xs font-bold text-slate-900 block">Semua Budget</span>
                <span class="text-[10px] text-slate-500 block">Semua Menu Kantin</span>
            </button>

            <button type="button" 
                    @click="budgetFilter = '5000'" 
                    class="p-3 rounded-xl border text-center transition select-none flex flex-col items-center justify-center gap-1"
                    :class="budgetFilter === '5000' 
                        ? 'border-brand-600 bg-brand-50/70 ring-1 ring-brand-500/20 shadow-2xs' 
                        : 'border-slate-200 bg-slate-50 hover:bg-slate-100/80'">
                <span class="text-[10px] font-semibold text-slate-500">Maks. 5 Ribu</span>
                <span class="text-xs font-bold text-slate-900 block">&le; Rp 5.000</span>
                <span class="text-[10px] text-slate-500 block">Minuman Dingin</span>
            </button>

            <button type="button" 
                    @click="budgetFilter = '10000'" 
                    class="p-3 rounded-xl border text-center transition select-none flex flex-col items-center justify-center gap-1"
                    :class="budgetFilter === '10000' 
                        ? 'border-brand-600 bg-brand-50/70 ring-1 ring-brand-500/20 shadow-2xs' 
                        : 'border-slate-200 bg-slate-50 hover:bg-slate-100/80'">
                <span class="text-[10px] font-semibold text-slate-500">Maks. 10 Ribu</span>
                <span class="text-xs font-bold text-slate-900 block">&le; Rp 10.000</span>
                <span class="text-[10px] text-slate-500 block">Camilan & Aneka Jus</span>
            </button>

            <button type="button" 
                    @click="budgetFilter = '15000'" 
                    class="p-3 rounded-xl border text-center transition select-none flex flex-col items-center justify-center gap-1"
                    :class="budgetFilter === '15000' 
                        ? 'border-brand-600 bg-brand-50/70 ring-1 ring-brand-500/20 shadow-2xs' 
                        : 'border-slate-200 bg-slate-50 hover:bg-slate-100/80'">
                <span class="text-[10px] font-semibold text-slate-500">Maks. 15 Ribu</span>
                <span class="text-xs font-bold text-slate-900 block">&le; Rp 15.000</span>
                <span class="text-[10px] text-slate-500 block">Nasi & Lauk Utama</span>
            </button>

            <button type="button" 
                    @click="budgetFilter = '20000'" 
                    class="p-3 rounded-xl border text-center transition select-none flex flex-col items-center justify-center gap-1"
                    :class="budgetFilter === '20000' 
                        ? 'border-brand-600 bg-brand-50/70 ring-1 ring-brand-500/20 shadow-2xs' 
                        : 'border-slate-200 bg-slate-50 hover:bg-slate-100/80'">
                <span class="text-[10px] font-semibold text-slate-500">Maks. 20 Ribu</span>
                <span class="text-xs font-bold text-slate-900 block">&le; Rp 20.000</span>
                <span class="text-[10px] text-slate-500 block">Paket Komplit Kenyang</span>
            </button>
        </div>
    </div>

    <!-- Menu Terlaris Mahasiswa UNESA Hari Ini (Tanpa Tulisan Budget di Samping Judul) -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base sm:text-lg font-bold text-slate-900">Menu Terlaris Hari Ini</h2>
                <p class="text-xs text-slate-500">Paling banyak dipesan mahasiswa di jam istirahat kuliah</p>
            </div>
            <a href="{{ url('/katalog') }}" class="whitespace-nowrap text-xs font-semibold text-brand-600 hover:text-brand-700 flex items-center gap-1">
                <span>Katalog Lengkap</span>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <template x-for="item in filteredMenus" :key="item.id">
                <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <!-- Food Photo without floating badges -->
                        <div class="relative h-44 w-full bg-slate-100 overflow-hidden">
                            <img :src="item.image" 
                                 :alt="item.name" 
                                 class="w-full h-full object-cover">
                        </div>
                        
                        <!-- Content: Food Name, Stan Name Below It, Desc, Price -->
                        <div class="p-4 space-y-1.5">
                            <h3 class="text-sm font-bold text-slate-900" x-text="item.name"></h3>
                            <!-- Nama Stan di bawah nama menu masakan -->
                            <p class="text-[11px] font-semibold text-slate-500 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7" />
                                    <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8" />
                                    <path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4" />
                                    <path d="M2 7h20" />
                                    <path d="M22 7v3a2 2 0 0 1-2 2v0a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 16 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 12 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 8 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 4 12v0a2 2 0 0 1-2-2V7" />
                                </svg>
                                <span x-text="item.stall"></span>
                            </p>
                            <p class="text-xs text-slate-500 line-clamp-2 pt-0.5" x-text="item.desc"></p>
                            <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                                <span class="text-sm font-extrabold text-brand-700" x-text="formatRupiah(item.price)"></span>
                                <span class="text-[11px] text-slate-400" x-text="item.prepTime"></span>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 pt-0">
                        <button type="button" 
                                @click="addToCart({ id: item.id, name: item.name, price: item.price, seller: item.sellerName })" 
                                class="whitespace-nowrap w-full inline-flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-xl bg-brand-600 text-white text-xs font-semibold hover:bg-brand-700 shadow-sm transition">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                            <span>Tambah ke Keranjang</span>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty State jika tidak ada menu -->
        <div x-show="filteredMenus.length === 0" class="text-center py-12 bg-white rounded-2xl border border-slate-200/80 p-6 space-y-2">
            <p class="text-xs font-semibold text-slate-700">Tidak ada menu terlaris dalam rentang harga ini.</p>
            <button type="button" @click="budgetFilter = 'semua'" class="text-xs font-semibold text-brand-600 hover:text-brand-700">Tampilkan Semua Menu</button>
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
