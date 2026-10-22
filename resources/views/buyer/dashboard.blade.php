@extends('layouts.buyer')

@section('title', 'Dashboard Pembeli — NesaCourt')

@section('content')
<div class="space-y-6" x-data="{
    activeOrder: {
        id: 'NC-20261021-8842',
        stall: 'Stan 01 - Barokah Rasa',
        status: 'Sedang Dimasak di Dapur',
        statusDesc: 'Pesanan nomor antrean A-09 sedang disiapkan penjual',
        progress: 65,
        eta: '~7 Menit'
    },
    budgetFilter: 'semua',
    popularMenus: [
        {
            id: 1,
            name: 'Ayam Geprek Sambal Korek',
            price: 13000,
            stall: 'Stan 01 Barokah',
            sellerName: 'Stan 01 - Barokah Rasa',
            stock: 12,
            prepTime: 'Masak ~7 menit',
            image: 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?auto=format&fit=crop&w=600&q=80',
            desc: 'Ayam krispi gurih digeprek sambal pedas segar disajikan dengan nasi hangat dan lalapan mentimun.'
        },
        {
            id: 2,
            name: 'Soto Ayam Lamongan Koya',
            price: 14000,
            stall: 'Stan 02 Soto Bu Siti',
            sellerName: 'Stan 02 - Soto Bu Siti',
            stock: 8,
            prepTime: 'Masak ~5 menit',
            image: 'https://images.unsplash.com/photo-1541832676-9b763b0239ab?auto=format&fit=crop&w=600&q=80',
            desc: 'Soto ayam kuah kuning hangat dengan taburan koya gurih melimpah, bihun, irisan telur, dan jeruk nipis.'
        },
        {
            id: 3,
            name: 'Es Teh Manis Jumbo Vokasi',
            price: 4000,
            stall: 'Stan 04 Jus Vokasi',
            sellerName: 'Stan 04 - Jus Vokasi',
            stock: 35,
            prepTime: 'Siap ~2 menit',
            image: 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?auto=format&fit=crop&w=600&q=80',
            desc: 'Teh melati racikan khas disajikan dingin dengan gelas jumbo, sangat menyegarkan saat cuaca panas.'
        },
        {
            id: 4,
            name: 'Kentang Goreng Krispi Keju',
            price: 8000,
            stall: 'Stan 03 Geprek Mas Bro',
            sellerName: 'Stan 03 - Geprek Mas Bro',
            stock: 15,
            prepTime: 'Masak ~4 menit',
            image: 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?auto=format&fit=crop&w=600&q=80',
            desc: 'Kentang goreng renyah bertabur bumbu keju manis gurih untuk teman nugas santai.'
        },
        {
            id: 5,
            name: 'Jus Alpukat Kental Susu Cokelat',
            price: 10000,
            stall: 'Stan 04 Jus Vokasi',
            sellerName: 'Stan 04 - Jus Vokasi',
            stock: 20,
            prepTime: 'Siap ~3 menit',
            image: 'https://images.unsplash.com/photo-1553530666-ba11a7da3888?auto=format&fit=crop&w=600&q=80',
            desc: 'Kaya nutrisi buah alpukat segar dengan susu cokelat manis, pengganjal lapar praktis di kampus.'
        },
        {
            id: 6,
            name: 'Paket Nasi Penyet + Es Teh',
            price: 15000,
            stall: 'Stan 01 Barokah',
            sellerName: 'Stan 01 - Barokah Rasa',
            stock: 10,
            prepTime: 'Masak ~6 menit',
            image: 'https://images.unsplash.com/photo-1569058242253-92a9c755a0ec?auto=format&fit=crop&w=600&q=80',
            desc: 'Paket komplit hemat mahasiswa: Nasi hangat, ayam goreng penyet sambal terasi dan segelas es teh manis.'
        }
    ],
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
                        <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">Halo, Aditya Pratama</h1>
                        <span class="text-slate-300 font-normal">|</span>
                        <span class="text-xs font-semibold text-brand-600">Mahasiswa</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">D4 Manajemen Informatika | Vokasi UNESA Ketintang (NIM: 23055310001)</p>
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

        <!-- Category Shortcuts -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5 pt-1">
            <a href="{{ url('/katalog?kategori=makanan') }}" class="p-3 rounded-xl bg-slate-50 border border-slate-100 hover:border-brand-200 hover:bg-brand-50/40 transition flex items-center gap-2.5 group">
                <div class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-700 group-hover:text-brand-600 shrink-0 shadow-2xs">
                    <i data-lucide="utensils" class="w-4 h-4"></i>
                </div>
                <div class="min-w-0">
                    <span class="text-xs font-bold text-slate-900 block truncate group-hover:text-brand-600">Makanan</span>
                    <span class="text-[10px] text-slate-400 block truncate">Nasi & Ayam</span>
                </div>
            </a>

            <a href="{{ url('/katalog?kategori=berkuah') }}" class="p-3 rounded-xl bg-slate-50 border border-slate-100 hover:border-brand-200 hover:bg-brand-50/40 transition flex items-center gap-2.5 group">
                <div class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-700 group-hover:text-brand-600 shrink-0 shadow-2xs">
                    <i data-lucide="flame" class="w-4 h-4"></i>
                </div>
                <div class="min-w-0">
                    <span class="text-xs font-bold text-slate-900 block truncate group-hover:text-brand-600">Soto & Kuah</span>
                    <span class="text-[10px] text-slate-400 block truncate">Soto & Rawon</span>
                </div>
            </a>

            <a href="{{ url('/katalog?kategori=minuman') }}" class="p-3 rounded-xl bg-slate-50 border border-slate-100 hover:border-brand-200 hover:bg-brand-50/40 transition flex items-center gap-2.5 group">
                <div class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-700 group-hover:text-brand-600 shrink-0 shadow-2xs">
                    <i data-lucide="coffee" class="w-4 h-4"></i>
                </div>
                <div class="min-w-0">
                    <span class="text-xs font-bold text-slate-900 block truncate group-hover:text-brand-600">Minuman</span>
                    <span class="text-[10px] text-slate-400 block truncate">Es Teh & Jus</span>
                </div>
            </a>

            <a href="{{ url('/katalog?kategori=camilan') }}" class="p-3 rounded-xl bg-slate-50 border border-slate-100 hover:border-brand-200 hover:bg-brand-50/40 transition flex items-center gap-2.5 group">
                <div class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-700 group-hover:text-brand-600 shrink-0 shadow-2xs">
                    <i data-lucide="cookie" class="w-4 h-4"></i>
                </div>
                <div class="min-w-0">
                    <span class="text-xs font-bold text-slate-900 block truncate group-hover:text-brand-600">Camilan</span>
                    <span class="text-[10px] text-slate-400 block truncate">Kentang & Roti</span>
                </div>
            </a>

            <a href="{{ url('/katalog?kategori=paket') }}" class="col-span-2 sm:col-span-1 p-3 rounded-xl bg-slate-50 border border-slate-100 hover:border-brand-200 hover:bg-brand-50/40 transition flex items-center gap-2.5 group">
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

    <!-- Active Ongoing Order Tracker Card -->
    <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center shrink-0">
                    <i data-lucide="clock-3" class="w-5 h-5"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-slate-900" x-text="activeOrder.id">NC-20261021-8842</span>
                        <span class="text-slate-300 font-normal">|</span>
                        <span class="text-xs font-bold text-amber-600" x-text="activeOrder.status">Sedang Dimasak di Dapur</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5" x-text="activeOrder.stall + ' | ' + activeOrder.statusDesc"></p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ url('/pesanan/status') }}" class="whitespace-nowrap inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold border border-slate-200 bg-slate-50 text-slate-700 hover:bg-amber-50 hover:border-amber-300 hover:text-amber-800 active:bg-amber-600 active:text-white transition group shadow-2xs">
                    <i data-lucide="radio" class="w-3.5 h-3.5 text-amber-600"></i>
                    <span>Pantau Status Real-time</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5 text-slate-400 group-hover:text-amber-800 group-hover:translate-x-0.5 transition-all"></i>
                </a>
            </div>
        </div>

        <!-- Progress Step Bar -->
        <div class="space-y-1.5">
            <div class="flex items-center justify-between text-xs text-slate-500">
                <span class="font-medium text-slate-700">Estimasi siap ambil: <strong class="text-slate-900" x-text="activeOrder.eta">~7 Menit</strong></span>
                <span class="font-bold text-brand-600" x-text="activeOrder.progress + '%'">65%</span>
            </div>
            <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden">
                <div class="h-full bg-brand-600 rounded-full transition-all duration-500" :style="'width: ' + activeOrder.progress + '%'"></div>
            </div>
        </div>
    </div>

    <!-- Student Budget Menu Finder (Clean Practical Feature, No AI Gimmicks) -->
    <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs space-y-3.5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-sm sm:text-base font-bold text-slate-900">Rekomendasi Menu Sesuai Uang Saku Mahasiswa</h2>
                <p class="text-xs text-slate-500 mt-0.5">Pilih batas anggaran Anda untuk langsung melihat menu yang tersedia di kantin</p>
            </div>
            <a href="{{ url('/rekomendasi') }}" class="whitespace-nowrap inline-flex items-center justify-center gap-1.5 py-2 px-4 rounded-xl bg-amber-600 text-white text-xs font-semibold hover:bg-amber-700 shadow-sm transition">
                <span>Filter Kriteria Lengkap</span>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>

        <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
            <button type="button" 
                    @click="budgetFilter = 'semua'" 
                    :class="budgetFilter === 'semua' ? 'border-amber-300 bg-amber-50/90 text-amber-900 font-bold shadow-2xs' : 'border-slate-200 bg-slate-50 text-slate-700 font-semibold hover:bg-amber-50/70 hover:border-amber-200 hover:text-amber-800'" 
                    class="whitespace-nowrap px-3.5 py-1.5 rounded-xl border text-xs transition">
                Semua Anggaran
            </button>
            <button type="button" 
                    @click="budgetFilter = '5000'" 
                    :class="budgetFilter === '5000' ? 'border-amber-300 bg-amber-50/90 text-amber-900 font-bold shadow-2xs' : 'border-slate-200 bg-slate-50 text-slate-700 font-semibold hover:bg-amber-50/70 hover:border-amber-200 hover:text-amber-800'" 
                    class="whitespace-nowrap px-3.5 py-1.5 rounded-xl border text-xs transition">
                Budget &le; Rp 5.000 (Minuman Segar)
            </button>
            <button type="button" 
                    @click="budgetFilter = '10000'" 
                    :class="budgetFilter === '10000' ? 'border-amber-300 bg-amber-50/90 text-amber-900 font-bold shadow-2xs' : 'border-slate-200 bg-slate-50 text-slate-700 font-semibold hover:bg-amber-50/70 hover:border-amber-200 hover:text-amber-800'" 
                    class="whitespace-nowrap px-3.5 py-1.5 rounded-xl border text-xs transition">
                Budget &le; Rp 10.000 (Camilan & Jus)
            </button>
            <button type="button" 
                    @click="budgetFilter = '15000'" 
                    :class="budgetFilter === '15000' ? 'border-amber-300 bg-amber-50/90 text-amber-900 font-bold shadow-2xs' : 'border-slate-200 bg-slate-50 text-slate-700 font-semibold hover:bg-amber-50/70 hover:border-amber-200 hover:text-amber-800'" 
                    class="whitespace-nowrap px-3.5 py-1.5 rounded-xl border text-xs transition">
                Budget &le; Rp 15.000 (Nasi Ayam Komplit)
            </button>
            <button type="button" 
                    @click="budgetFilter = '20000'" 
                    :class="budgetFilter === '20000' ? 'border-amber-300 bg-amber-50/90 text-amber-900 font-bold shadow-2xs' : 'border-slate-200 bg-slate-50 text-slate-700 font-semibold hover:bg-amber-50/70 hover:border-amber-200 hover:text-amber-800'" 
                    class="whitespace-nowrap px-3.5 py-1.5 rounded-xl border text-xs transition">
                Budget &le; Rp 20.000 (Paket Kenyang)
            </button>
        </div>
    </div>

    <!-- Stan Kantin Populer Ketintang -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base sm:text-lg font-bold text-slate-900">Stan Kantin Ketintang</h2>
                <p class="text-xs text-slate-500">Pilih stan untuk melihat menu spesifik yang sedang aktif</p>
            </div>
            <a href="{{ url('/katalog') }}" class="whitespace-nowrap text-xs font-semibold text-brand-600 hover:text-brand-700 flex items-center gap-1">
                <span>Lihat Semua Stan</span>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Stan 1 -->
            <a href="{{ url('/katalog?stan=barokah') }}" class="group p-4 rounded-2xl bg-white border border-slate-200/80 hover:border-brand-300 hover:shadow-md transition space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold text-brand-700 bg-brand-50 px-2 py-0.5 rounded-md">Stan 01</span>
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-emerald-700">
                        <span class="relative flex h-2 w-2 shrink-0">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <span>Buka</span>
                    </span>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900 group-hover:text-brand-600 transition">Stan Barokah Rasa</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Spesialis Ayam Geprek & Nasi Penyet</p>
                </div>
                <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span>12 Menu Aktif</span>
                    <span class="font-semibold text-slate-700">Mulai Rp 10.000</span>
                </div>
            </a>

            <!-- Stan 2 -->
            <a href="{{ url('/katalog?stan=soto') }}" class="group p-4 rounded-2xl bg-white border border-slate-200/80 hover:border-brand-300 hover:shadow-md transition space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold text-brand-700 bg-brand-50 px-2 py-0.5 rounded-md">Stan 02</span>
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-emerald-700">
                        <span class="relative flex h-2 w-2 shrink-0">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <span>Buka</span>
                    </span>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900 group-hover:text-brand-600 transition">Soto Lamongan Bu Siti</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Kuah Koya Gurih & Nasi Rawon</p>
                </div>
                <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span>8 Menu Aktif</span>
                    <span class="font-semibold text-slate-700">Mulai Rp 12.000</span>
                </div>
            </a>

            <!-- Stan 3 -->
            <a href="{{ url('/katalog?stan=geprek') }}" class="group p-4 rounded-2xl bg-white border border-slate-200/80 hover:border-brand-300 hover:shadow-md transition space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold text-brand-700 bg-brand-50 px-2 py-0.5 rounded-md">Stan 03</span>
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-emerald-700">
                        <span class="relative flex h-2 w-2 shrink-0">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <span>Buka</span>
                    </span>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900 group-hover:text-brand-600 transition">Geprek Mas Bro</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Ayam Krispi Sambal Bawang & Keju</p>
                </div>
                <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span>15 Menu Aktif</span>
                    <span class="font-semibold text-slate-700">Mulai Rp 11.000</span>
                </div>
            </a>

            <!-- Stan 4 -->
            <a href="{{ url('/katalog?stan=minuman') }}" class="group p-4 rounded-2xl bg-white border border-slate-200/80 hover:border-brand-300 hover:shadow-md transition space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold text-brand-700 bg-brand-50 px-2 py-0.5 rounded-md">Stan 04</span>
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-emerald-700">
                        <span class="relative flex h-2 w-2 shrink-0">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <span>Buka</span>
                    </span>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900 group-hover:text-brand-600 transition">Aneka Jus & Es Vokasi</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Jus Buah Segar, Es Teh & Kopi Susu</p>
                </div>
                <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span>18 Menu Aktif</span>
                    <span class="font-semibold text-slate-700">Mulai Rp 3.000</span>
                </div>
            </a>
        </div>
    </div>

    <!-- Menu Terlaris Mahasiswa UNESA Hari Ini -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-base sm:text-lg font-bold text-slate-900">Menu Terlaris Hari Ini</h2>
                    <template x-if="budgetFilter !== 'semua'">
                        <span class="px-2 py-0.5 rounded-md bg-amber-50 text-amber-800 border border-amber-200 text-[10px] font-bold" x-text="'Budget ≤ ' + formatRupiah(parseInt(budgetFilter))"></span>
                    </template>
                </div>
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
                        <div class="relative h-44 w-full bg-slate-100 overflow-hidden">
                            <img :src="item.image" 
                                 :alt="item.name" 
                                 class="w-full h-full object-cover">
                            <span class="absolute top-3 left-3 px-2 py-0.5 rounded-md bg-white/90 backdrop-blur-xs text-[10px] font-bold text-slate-800" x-text="item.stall">
                            </span>
                            <span class="absolute bottom-3 left-3 px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 text-[10px] font-semibold border border-emerald-200" x-text="'Tersedia ' + item.stock + ' Porsi'">
                            </span>
                        </div>
                        <div class="p-4 space-y-2">
                            <h3 class="text-sm font-bold text-slate-900" x-text="item.name"></h3>
                            <p class="text-xs text-slate-500 line-clamp-2" x-text="item.desc"></p>
                            <div class="flex items-center justify-between pt-1">
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
@endsection
