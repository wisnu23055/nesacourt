@extends('layouts.buyer')

@section('title', 'Rekomendasi Menu — NesaCourt')

@section('content')
<div class="space-y-6" x-data="{
    budget: 'semua',
    selectedTaste: 'semua',
    selectedTime: 'semua',

    menusDatabase: [
        {
            id: 201,
            name: 'Ayam Geprek Sambal Bawang Level 2',
            price: 13000,
            seller: 'Stan 01 - Barokah Rasa',
            prepTime: 7,
            taste: 'pedas',
            category: 'makanan',
            stock: 14,
            image: 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?auto=format&fit=crop&w=600&q=80',
            aiReason: 'Ayam krispi gurih dengan sambal bawang pedas segar, cocok dinikmati saat jeda perkuliahan.'
        },
        {
            id: 202,
            name: 'Paket Hemat: Nasi Ayam + Es Teh Manis',
            price: 15000,
            seller: 'Stan 01 - Barokah Rasa',
            prepTime: 6,
            taste: 'gurih',
            category: 'makanan',
            stock: 12,
            image: 'https://images.unsplash.com/photo-1569058242253-92a9c755a0ec?auto=format&fit=crop&w=600&q=80',
            aiReason: 'Solusi paling hemat, sudah termasuk nasi komplit dan minuman jumbo pas dengan batas anggaran Anda.'
        },
        {
            id: 203,
            name: 'Soto Ayam Lamongan Kuah Koya',
            price: 14000,
            seller: 'Stan 02 - Soto Bu Siti',
            prepTime: 5,
            taste: 'berkuah',
            category: 'makanan',
            stock: 8,
            image: 'https://images.unsplash.com/photo-1541832676-9b763b0239ab?auto=format&fit=crop&w=600&q=80',
            aiReason: 'Kuah kaldu kuning hangat gurih dan cepat disajikan hanya dalam waktu 5 menit.'
        },
        {
            id: 204,
            name: 'Nasi Goreng Telur Ceplok Mas Bro',
            price: 12000,
            seller: 'Stan 03 - Geprek Mas Bro',
            prepTime: 8,
            taste: 'gurih',
            category: 'makanan',
            stock: 18,
            image: 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?auto=format&fit=crop&w=600&q=80',
            aiReason: 'Nasi goreng harum beraroma wok dengan telur ceplok setengah matang yang mengenyangkan.'
        },
        {
            id: 205,
            name: 'Jus Alpukat Kental Susu Cokelat',
            price: 10000,
            seller: 'Stan 04 - Aneka Jus Vokasi',
            prepTime: 4,
            taste: 'manis',
            category: 'minuman',
            stock: 10,
            image: 'https://images.unsplash.com/photo-1553530666-ba11a7da3888?auto=format&fit=crop&w=600&q=80',
            aiReason: 'Kaya nutrisi buah asli, cocok sebagai pengganjal lapar manis di sela-sela praktikum.'
        },
        {
            id: 206,
            name: 'Es Teh Manis Jumbo Vokasi',
            price: 4000,
            seller: 'Stan 04 - Aneka Jus Vokasi',
            prepTime: 2,
            taste: 'segar',
            category: 'minuman',
            stock: 40,
            image: 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?auto=format&fit=crop&w=600&q=80',
            aiReason: 'Harga sangat murah, porsi ekstra jumbo siap minum dalam 2 menit.'
        }
    ],

    get recommendedResults() {
        return this.menusDatabase.filter(m => {
            const matchBudget = this.budget === 'semua' || m.price <= parseInt(this.budget);
            const matchTaste = this.selectedTaste === 'semua' || m.taste === this.selectedTaste;
            const matchTime = this.selectedTime === 'semua' || (
                this.selectedTime === '20' ? true : m.prepTime <= parseInt(this.selectedTime)
            );
            return matchBudget && matchTaste && matchTime;
        });
    }
}">

    <!-- Header Section (Judul Semi-Bold) -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-slate-200/80">
        <div>
            <h1 class="text-xl sm:text-2xl font-semibold text-slate-900 tracking-tight">Rekomendasi Menu Kantin</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Pilihan menu terbaik yang disesuaikan dengan kriteria uang saku dan selera mahasiswa di Food Court UNESA Ketintang.</p>
        </div>
        <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-xs text-slate-600 shrink-0 self-start md:self-auto shadow-2xs">
            <span class="relative flex h-2 w-2 shrink-0">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            <span>Kantin Ketintang Buka | 08.00 - 17.00 WIB</span>
        </div>
    </div>

    <!-- Filter Rekomendasi Menu Kantin (Tema Dashboard Sesuai Desain & Fitur Utuh) -->
    <div class="p-5 sm:p-6 rounded-2xl bg-white border border-slate-200/80 shadow-xs space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <i data-lucide="sliders-horizontal" class="w-4 h-4 text-slate-900"></i>
                    <h2 class="text-sm sm:text-base font-bold text-slate-900">Filter & Kriteria Rekomendasi Menu</h2>
                </div>
                <p class="text-xs text-slate-500">Sesuaikan batas anggaran uang saku, selera rasa, dan maksimal waktu tunggu persiapan di kantin</p>
            </div>
            <!-- Tombol Reset jika filter sedang aktif -->
            <button type="button" 
                    x-show="budget !== 'semua' || selectedTaste !== 'semua' || selectedTime !== 'semua'"
                    @click="budget = 'semua'; selectedTaste = 'semua'; selectedTime = 'semua'"
                    class="whitespace-nowrap self-start sm:self-auto inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition cursor-pointer">
                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                <span>Reset Kriteria</span>
            </button>
        </div>

        <!-- 1. Batas Anggaran Mahasiswa (Budget Cards Grid Identik Dashboard) -->
        <div class="space-y-2.5">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-700 shrink-0 shadow-2xs">
                    <i data-lucide="wallet" class="w-3.5 h-3.5"></i>
                </div>
                <div>
                    <span class="text-xs font-bold text-slate-900 block leading-tight">1. Batas Anggaran Uang Saku</span>
                    <span class="text-[10px] text-slate-500 block">Pilih batas maksimal pengeluaran jajan per porsi</span>
                </div>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5">
                <button type="button" 
                        @click="budget = 'semua'" 
                        class="p-3 rounded-xl border text-center transition select-none flex flex-col items-center justify-center gap-1 cursor-pointer"
                        :class="budget === 'semua' 
                            ? 'border-brand-600 bg-brand-50/70 ring-1 ring-brand-500/20 shadow-2xs' 
                            : 'border-slate-200 bg-slate-50 hover:bg-slate-100/80'">
                    <span class="text-[10px] font-semibold text-slate-500">Semua Harga</span>
                    <span class="text-xs font-bold text-slate-900 block">Semua Budget</span>
                    <span class="text-[10px] text-slate-500 block">Semua Menu Kantin</span>
                </button>

                <button type="button" 
                        @click="budget = '5000'" 
                        class="p-3 rounded-xl border text-center transition select-none flex flex-col items-center justify-center gap-1 cursor-pointer"
                        :class="budget === '5000' 
                            ? 'border-brand-600 bg-brand-50/70 ring-1 ring-brand-500/20 shadow-2xs' 
                            : 'border-slate-200 bg-slate-50 hover:bg-slate-100/80'">
                    <span class="text-[10px] font-semibold text-slate-500">Maks. 5 Ribu</span>
                    <span class="text-xs font-bold text-slate-900 block">&le; Rp 5.000</span>
                    <span class="text-[10px] text-slate-500 block">Minuman Dingin</span>
                </button>

                <button type="button" 
                        @click="budget = '10000'" 
                        class="p-3 rounded-xl border text-center transition select-none flex flex-col items-center justify-center gap-1 cursor-pointer"
                        :class="budget === '10000' 
                            ? 'border-brand-600 bg-brand-50/70 ring-1 ring-brand-500/20 shadow-2xs' 
                            : 'border-slate-200 bg-slate-50 hover:bg-slate-100/80'">
                    <span class="text-[10px] font-semibold text-slate-500">Maks. 10 Ribu</span>
                    <span class="text-xs font-bold text-slate-900 block">&le; Rp 10.000</span>
                    <span class="text-[10px] text-slate-500 block">Camilan & Aneka Jus</span>
                </button>

                <button type="button" 
                        @click="budget = '15000'" 
                        class="p-3 rounded-xl border text-center transition select-none flex flex-col items-center justify-center gap-1 cursor-pointer"
                        :class="budget === '15000' 
                            ? 'border-brand-600 bg-brand-50/70 ring-1 ring-brand-500/20 shadow-2xs' 
                            : 'border-slate-200 bg-slate-50 hover:bg-slate-100/80'">
                    <span class="text-[10px] font-semibold text-slate-500">Maks. 15 Ribu</span>
                    <span class="text-xs font-bold text-slate-900 block">&le; Rp 15.000</span>
                    <span class="text-[10px] text-slate-500 block">Nasi & Lauk Utama</span>
                </button>

                <button type="button" 
                        @click="budget = '20000'" 
                        class="col-span-2 sm:col-span-1 p-3 rounded-xl border text-center transition select-none flex flex-col items-center justify-center gap-1 cursor-pointer"
                        :class="budget === '20000' 
                            ? 'border-brand-600 bg-brand-50/70 ring-1 ring-brand-500/20 shadow-2xs' 
                            : 'border-slate-200 bg-slate-50 hover:bg-slate-100/80'">
                    <span class="text-[10px] font-semibold text-slate-500">Maks. 20 Ribu</span>
                    <span class="text-xs font-bold text-slate-900 block">&le; Rp 20.000</span>
                    <span class="text-[10px] text-slate-500 block">Paket Komplit Kenyang</span>
                </button>
            </div>
        </div>

        <!-- 2 & 3. Preferensi Karakter Rasa dan Maksimal Waktu Tunggu (Dua Kolom Bertema Dashboard) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
            <!-- Preferensi Rasa -->
            <div class="p-4 rounded-xl bg-slate-50/70 border border-slate-200/70 space-y-2.5">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-700 shrink-0 shadow-2xs">
                        <i data-lucide="utensils" class="w-3.5 h-3.5"></i>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-slate-900 block leading-tight">2. Preferensi Karakter Rasa</span>
                        <span class="text-[10px] text-slate-500 block">Karakter cita rasa menu masakan</span>
                    </div>
                </div>
                <div class="grid grid-cols-3 sm:grid-cols-6 md:grid-cols-3 lg:grid-cols-6 gap-1.5 pt-0.5">
                    <button type="button" 
                            @click="selectedTaste = 'semua'" 
                            :class="selectedTaste === 'semua' 
                                ? 'border-brand-600 bg-brand-50/80 text-brand-700 font-bold ring-1 ring-brand-500/20 shadow-2xs' 
                                : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-100'"
                            class="whitespace-nowrap py-1.5 px-2 rounded-xl border text-xs font-semibold text-center transition cursor-pointer">
                        Bebas
                    </button>
                    <button type="button" 
                            @click="selectedTaste = 'pedas'" 
                            :class="selectedTaste === 'pedas' 
                                ? 'border-brand-600 bg-brand-50/80 text-brand-700 font-bold ring-1 ring-brand-500/20 shadow-2xs' 
                                : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-100'"
                            class="whitespace-nowrap py-1.5 px-2 rounded-xl border text-xs font-semibold text-center transition cursor-pointer">
                        Pedas
                    </button>
                    <button type="button" 
                            @click="selectedTaste = 'gurih'" 
                            :class="selectedTaste === 'gurih' 
                                ? 'border-brand-600 bg-brand-50/80 text-brand-700 font-bold ring-1 ring-brand-500/20 shadow-2xs' 
                                : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-100'"
                            class="whitespace-nowrap py-1.5 px-2 rounded-xl border text-xs font-semibold text-center transition cursor-pointer">
                        Gurih
                    </button>
                    <button type="button" 
                            @click="selectedTaste = 'manis'" 
                            :class="selectedTaste === 'manis' 
                                ? 'border-brand-600 bg-brand-50/80 text-brand-700 font-bold ring-1 ring-brand-500/20 shadow-2xs' 
                                : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-100'"
                            class="whitespace-nowrap py-1.5 px-2 rounded-xl border text-xs font-semibold text-center transition cursor-pointer">
                        Manis
                    </button>
                    <button type="button" 
                            @click="selectedTaste = 'berkuah'" 
                            :class="selectedTaste === 'berkuah' 
                                ? 'border-brand-600 bg-brand-50/80 text-brand-700 font-bold ring-1 ring-brand-500/20 shadow-2xs' 
                                : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-100'"
                            class="whitespace-nowrap py-1.5 px-2 rounded-xl border text-xs font-semibold text-center transition cursor-pointer">
                        Berkuah
                    </button>
                    <button type="button" 
                            @click="selectedTaste = 'segar'" 
                            :class="selectedTaste === 'segar' 
                                ? 'border-brand-600 bg-brand-50/80 text-brand-700 font-bold ring-1 ring-brand-500/20 shadow-2xs' 
                                : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-100'"
                            class="whitespace-nowrap py-1.5 px-2 rounded-xl border text-xs font-semibold text-center transition cursor-pointer">
                        Segar
                    </button>
                </div>
            </div>

            <!-- Maksimal Waktu Tunggu -->
            <div class="p-4 rounded-xl bg-slate-50/70 border border-slate-200/70 space-y-2.5">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-700 shrink-0 shadow-2xs">
                        <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-slate-900 block leading-tight">3. Maksimal Waktu Tunggu</span>
                        <span class="text-[10px] text-slate-500 block">Durasi persiapan dapur stan</span>
                    </div>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-1.5 pt-0.5">
                    <button type="button" 
                            @click="selectedTime = 'semua'" 
                            :class="selectedTime === 'semua' 
                                ? 'border-brand-600 bg-brand-50/80 text-brand-700 font-bold ring-1 ring-brand-500/20 shadow-2xs' 
                                : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-100'"
                            class="whitespace-nowrap py-1.5 px-2 rounded-xl border text-xs font-semibold text-center transition cursor-pointer">
                        Semua
                    </button>
                    <button type="button" 
                            @click="selectedTime = '5'" 
                            :class="selectedTime === '5' 
                                ? 'border-brand-600 bg-brand-50/80 text-brand-700 font-bold ring-1 ring-brand-500/20 shadow-2xs' 
                                : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-100'"
                            class="whitespace-nowrap py-1.5 px-2 rounded-xl border text-xs font-semibold text-center transition cursor-pointer">
                        &le; 5 Mnt
                    </button>
                    <button type="button" 
                            @click="selectedTime = '10'" 
                            :class="selectedTime === '10' 
                                ? 'border-brand-600 bg-brand-50/80 text-brand-700 font-bold ring-1 ring-brand-500/20 shadow-2xs' 
                                : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-100'"
                            class="whitespace-nowrap py-1.5 px-2 rounded-xl border text-xs font-semibold text-center transition cursor-pointer">
                        &le; 10 Mnt
                    </button>
                    <button type="button" 
                            @click="selectedTime = '20'" 
                            :class="selectedTime === '20' 
                                ? 'border-brand-600 bg-brand-50/80 text-brand-700 font-bold ring-1 ring-brand-500/20 shadow-2xs' 
                                : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-100'"
                            class="whitespace-nowrap py-1.5 px-2 rounded-xl border text-xs font-semibold text-center transition cursor-pointer">
                        Santai (&gt; 15 Mnt)
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Recommendation Results Grid (Identik dengan Menu Terlaris di Dashboard) -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base sm:text-lg font-bold text-slate-900">Hasil Rekomendasi Menu Terpilih</h2>
                <p class="text-xs text-slate-500">Diurutkan berdasarkan kecocokan budget dan kecepatan persiapan porsi</p>
            </div>
            <span class="text-xs font-semibold text-slate-500" x-text="recommendedResults.length + ' Menu Tersedia'"></span>
        </div>

        <!-- Grid Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <template x-for="item in recommendedResults" :key="item.id">
                <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <!-- Food Photo without floating badges -->
                        <div class="relative h-44 w-full bg-slate-100 overflow-hidden">
                            <img :src="item.image" 
                                 :alt="item.name" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
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
                                <span x-text="item.seller"></span>
                            </p>
                            <p class="text-xs text-slate-500 line-clamp-2 pt-0.5" x-text="item.aiReason"></p>
                            <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                                <span class="text-sm font-extrabold text-brand-700" x-text="formatRupiah(item.price)"></span>
                                <span class="text-[11px] text-slate-400" x-text="'Masak ~' + item.prepTime + ' Menit'"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Add Button -->
                    <div class="p-4 pt-0">
                        <button type="button" 
                                @click="addToCart({ id: item.id, name: item.name, price: item.price, seller: item.seller })" 
                                class="whitespace-nowrap w-full inline-flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-xl bg-brand-600 text-white text-xs font-semibold hover:bg-brand-700 shadow-sm transition">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                            <span>Tambah ke Keranjang</span>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty State When No Menu Matches Criteria -->
        <div x-show="recommendedResults.length === 0" class="text-center py-16 bg-white rounded-2xl border border-slate-200/80 p-8 space-y-3">
            <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                <i data-lucide="search-x" class="w-6 h-6"></i>
            </div>
            <h4 class="text-sm font-bold text-slate-900">Belum Ada Menu yang Sesuai Kriteria</h4>
            <p class="text-xs text-slate-500 max-w-sm mx-auto leading-relaxed">
                Kombinasi batas anggaran, preferensi rasa, atau waktu tunggu tidak menemukan menu yang cocok. Coba sesuaikan pilihan filter di atas.
            </p>
            <button type="button" 
                    @click="budget = 'semua'; selectedTaste = 'semua'; selectedTime = 'semua'" 
                    class="whitespace-nowrap inline-flex items-center justify-center px-4 py-2 rounded-xl text-xs font-semibold text-brand-700 bg-brand-50 hover:bg-brand-100 border border-brand-200 transition cursor-pointer">
                Reset Semua Filter
            </button>
        </div>
    </div>

</div>
@endsection
