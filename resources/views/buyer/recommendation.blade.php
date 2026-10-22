@extends('layouts.buyer')

@section('title', 'Rekomendasi Menu — NesaCourt')

@section('content')
<div class="space-y-6" x-data="{
    budget: 15000,
    selectedTaste: 'pedas',
    selectedTime: '10',
    selectedCategory: 'semua',
    isSearching: false,
    hasSearched: true,

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
            aiReason: 'Masuk dalam budget Rp 15.000, rasa pedas nagih, dan waktu penyajian cepat 7 menit saat jeda kuliah.',
            matchScore: 98
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
            aiReason: 'Solusi paling hemat, sudah termasuk nasi komplit dan minuman jumbo pas dengan batas anggaran Anda.',
            matchScore: 95
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
            aiReason: 'Kuah kaldu kuning hangat gurih dan cepat disajikan hanya dalam waktu 5 menit.',
            matchScore: 91
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
            aiReason: 'Harga Rp 12.000 menyisakan Rp 3.000 dari budget Anda, porsi mengenyangkan.',
            matchScore: 88
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
            aiReason: 'Kaya nutrisi buah asli, cocok sebagai pengganjal lapar manis di sela-sela praktikum.',
            matchScore: 84
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
            aiReason: 'Harga sangat murah, porsi ekstra jumbo siap minum dalam 2 menit.',
            matchScore: 80
        }
    ],

    get recommendedResults() {
        return this.menusDatabase.filter(m => {
            const matchBudget = m.price <= this.budget;
            const matchCategory = this.selectedCategory === 'semua' || m.category === this.selectedCategory;
            const matchTime = m.prepTime <= parseInt(this.selectedTime);
            return matchBudget && matchCategory && matchTime;
        }).sort((a, b) => b.matchScore - a.matchScore);
    },

    triggerSearch() {
        this.isSearching = true;
        setTimeout(() => {
            this.isSearching = false;
            this.hasSearched = true;
        }, 300);
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
                        <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">Rekomendasi Menu Kantin</h1>
                        <span class="text-slate-300 font-normal">|</span>
                        <span class="text-xs font-semibold text-brand-600">Sesuai Uang Saku Mahasiswa</span>
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

        <!-- Quick Budget Filter Pills (Kuning Transparan Halus) -->
        <div class="space-y-2">
            <span class="text-xs font-bold text-slate-800 block">Pilihan Cepat Batas Anggaran:</span>
            <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
                <button type="button" 
                        @click="budget = 5000; triggerSearch()" 
                        :class="budget === 5000 ? 'bg-amber-50/90 text-amber-900 font-bold border-amber-300 ring-1 ring-amber-300/40 shadow-2xs' : 'bg-slate-50 border border-slate-200 text-slate-700 hover:bg-amber-50/70 hover:border-amber-200 hover:text-amber-800'"
                        class="whitespace-nowrap px-3.5 py-1.5 rounded-xl text-xs font-semibold transition">
                    Budget &le; Rp 5.000 (Minuman Segar)
                </button>
                <button type="button" 
                        @click="budget = 10000; triggerSearch()" 
                        :class="budget === 10000 ? 'bg-amber-50/90 text-amber-900 font-bold border-amber-300 ring-1 ring-amber-300/40 shadow-2xs' : 'bg-slate-50 border border-slate-200 text-slate-700 hover:bg-amber-50/70 hover:border-amber-200 hover:text-amber-800'"
                        class="whitespace-nowrap px-3.5 py-1.5 rounded-xl text-xs font-semibold transition">
                    Budget &le; Rp 10.000 (Camilan & Jus)
                </button>
                <button type="button" 
                        @click="budget = 15000; triggerSearch()" 
                        :class="budget === 15000 ? 'bg-amber-50/90 text-amber-900 font-bold border-amber-300 ring-1 ring-amber-300/40 shadow-2xs' : 'bg-slate-50 border border-slate-200 text-slate-700 hover:bg-amber-50/70 hover:border-amber-200 hover:text-amber-800'"
                        class="whitespace-nowrap px-3.5 py-1.5 rounded-xl text-xs font-semibold transition">
                    Budget &le; Rp 15.000 (Nasi Ayam Komplit)
                </button>
                <button type="button" 
                        @click="budget = 20000; triggerSearch()" 
                        :class="budget === 20000 ? 'bg-amber-50/90 text-amber-900 font-bold border-amber-300 ring-1 ring-amber-300/40 shadow-2xs' : 'bg-slate-50 border border-slate-200 text-slate-700 hover:bg-amber-50/70 hover:border-amber-200 hover:text-amber-800'"
                        class="whitespace-nowrap px-3.5 py-1.5 rounded-xl text-xs font-semibold transition">
                    Budget &le; Rp 20.000 (Paket Kenyang)
                </button>
            </div>
        </div>
    </div>

    <!-- Criteria Filter Form Card (Tema Biru, Kontainer Anggaran di Tengah & Lebih Panjang) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-7 shadow-xs space-y-5">
        <div>
            <h2 class="text-sm sm:text-base font-bold text-slate-900">Tentukan Kriteria Menu Pilihan Anda</h2>
            <p class="text-xs text-slate-500 mt-0.5">Sesuaikan batas nominal uang saku, selera rasa, dan maksimal waktu tunggu persiapan menu di kantin.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-10 gap-5 pt-1">
            
            <!-- 1. Preferensi Rasa (Sisi Kiri, 3 Kolom) -->
            <div class="lg:col-span-3 p-4 sm:p-5 rounded-2xl bg-slate-50/70 border border-slate-200/70 space-y-3.5 flex flex-col justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-brand-50 border border-brand-200/80 flex items-center justify-center text-brand-600 shrink-0 shadow-2xs">
                        <i data-lucide="utensils" class="w-4 h-4 text-brand-600"></i>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-900 block leading-tight">Preferensi Rasa</label>
                        <span class="text-[10px] text-slate-500 block">Karakter cita rasa menu</span>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-1.5 pt-1">
                    <button type="button" 
                            @click="selectedTaste = 'pedas'; triggerSearch()" 
                            :class="selectedTaste === 'pedas' ? 'bg-brand-600 text-white font-bold border-brand-600 shadow-2xs' : 'bg-white border border-slate-200 text-slate-700 hover:bg-brand-50 hover:border-brand-200 hover:text-brand-700'"
                            class="whitespace-nowrap py-1.5 px-2 rounded-xl text-xs font-semibold text-center transition">
                        Pedas
                    </button>
                    <button type="button" 
                            @click="selectedTaste = 'gurih'; triggerSearch()" 
                            :class="selectedTaste === 'gurih' ? 'bg-brand-600 text-white font-bold border-brand-600 shadow-2xs' : 'bg-white border border-slate-200 text-slate-700 hover:bg-brand-50 hover:border-brand-200 hover:text-brand-700'"
                            class="whitespace-nowrap py-1.5 px-2 rounded-xl text-xs font-semibold text-center transition">
                        Gurih
                    </button>
                    <button type="button" 
                            @click="selectedTaste = 'manis'; triggerSearch()" 
                            :class="selectedTaste === 'manis' ? 'bg-brand-600 text-white font-bold border-brand-600 shadow-2xs' : 'bg-white border border-slate-200 text-slate-700 hover:bg-brand-50 hover:border-brand-200 hover:text-brand-700'"
                            class="whitespace-nowrap py-1.5 px-2 rounded-xl text-xs font-semibold text-center transition">
                        Manis
                    </button>
                    <button type="button" 
                            @click="selectedTaste = 'berkuah'; triggerSearch()" 
                            :class="selectedTaste === 'berkuah' ? 'bg-brand-600 text-white font-bold border-brand-600 shadow-2xs' : 'bg-white border border-slate-200 text-slate-700 hover:bg-brand-50 hover:border-brand-200 hover:text-brand-700'"
                            class="whitespace-nowrap py-1.5 px-2 rounded-xl text-xs font-semibold text-center transition">
                        Berkuah
                    </button>
                    <button type="button" 
                            @click="selectedTaste = 'segar'; triggerSearch()" 
                            :class="selectedTaste === 'segar' ? 'bg-brand-600 text-white font-bold border-brand-600 shadow-2xs' : 'bg-white border border-slate-200 text-slate-700 hover:bg-brand-50 hover:border-brand-200 hover:text-brand-700'"
                            class="whitespace-nowrap py-1.5 px-2 rounded-xl text-xs font-semibold text-center transition">
                        Segar
                    </button>
                    <button type="button" 
                            @click="selectedTaste = 'semua'; triggerSearch()" 
                            :class="selectedTaste === 'semua' ? 'bg-brand-600 text-white font-bold border-brand-600 shadow-2xs' : 'bg-white border border-slate-200 text-slate-700 hover:bg-brand-50 hover:border-brand-200 hover:text-brand-700'"
                            class="whitespace-nowrap py-1.5 px-2 rounded-xl text-xs font-semibold text-center transition">
                        Bebas
                    </button>
                </div>
            </div>

            <!-- 2. Batas Anggaran Mahasiswa (Di Tengah & Lebih Panjang: 4 Kolom) -->
            <div class="lg:col-span-4 p-4 sm:p-5 rounded-2xl bg-slate-50/70 border border-slate-200/70 space-y-3.5 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-brand-50 border border-brand-200/80 flex items-center justify-center text-brand-600 shrink-0 shadow-2xs">
                            <i data-lucide="wallet" class="w-4 h-4 text-brand-600"></i>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-900 block leading-tight">Batas Anggaran Mahasiswa</label>
                            <span class="text-[10px] text-slate-500 block">Uang saku maksimal</span>
                        </div>
                    </div>
                    <span class="text-xs font-extrabold text-brand-700 bg-brand-50 border border-brand-200 px-3 py-1 rounded-lg" x-text="formatRupiah(budget)">Rp 15.000</span>
                </div>

                <div class="space-y-1.5 pt-1">
                    <input type="range" 
                           min="4000" 
                           max="30000" 
                           step="1000" 
                           x-model.number="budget" 
                           @input="triggerSearch()"
                           class="w-full accent-brand-600 cursor-pointer">
                    <div class="flex justify-between text-[10px] text-slate-400 font-medium">
                        <span>Rp 4.000</span>
                        <span>Rp 15.000</span>
                        <span>Rp 30.000</span>
                    </div>
                </div>
            </div>

            <!-- 3. Maksimal Waktu Tunggu (Sisi Kanan, 3 Kolom) -->
            <div class="lg:col-span-3 p-4 sm:p-5 rounded-2xl bg-slate-50/70 border border-slate-200/70 space-y-3.5 flex flex-col justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-brand-50 border border-brand-200/80 flex items-center justify-center text-brand-600 shrink-0 shadow-2xs">
                        <i data-lucide="clock" class="w-4 h-4 text-brand-600"></i>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-900 block leading-tight">Maksimal Waktu Tunggu</label>
                        <span class="text-[10px] text-slate-500 block">Durasi persiapan dapur</span>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-1.5 pt-1">
                    <button type="button" 
                            @click="selectedTime = '5'; triggerSearch()" 
                            :class="selectedTime === '5' ? 'bg-brand-600 text-white font-bold border-brand-600 shadow-2xs' : 'bg-white border border-slate-200 text-slate-700 hover:bg-brand-50 hover:border-brand-200 hover:text-brand-700'"
                            class="whitespace-nowrap py-1.5 px-2 rounded-xl text-xs font-semibold text-center transition">
                        &le; 5 Mnt
                    </button>
                    <button type="button" 
                            @click="selectedTime = '10'; triggerSearch()" 
                            :class="selectedTime === '10' ? 'bg-brand-600 text-white font-bold border-brand-600 shadow-2xs' : 'bg-white border border-slate-200 text-slate-700 hover:bg-brand-50 hover:border-brand-200 hover:text-brand-700'"
                            class="whitespace-nowrap py-1.5 px-2 rounded-xl text-xs font-semibold text-center transition">
                        &le; 10 Mnt
                    </button>
                    <button type="button" 
                            @click="selectedTime = '20'; triggerSearch()" 
                            :class="selectedTime === '20' ? 'bg-brand-600 text-white font-bold border-brand-600 shadow-2xs' : 'bg-white border border-slate-200 text-slate-700 hover:bg-brand-50 hover:border-brand-200 hover:text-brand-700'"
                            class="whitespace-nowrap py-1.5 px-2 rounded-xl text-xs font-semibold text-center transition">
                        Santai (&gt; 15 Mnt)
                    </button>
                </div>
            </div>

        </div>

        <!-- Submit Button Row (Solid Brand Blue) -->
        <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="flex items-center gap-2 text-xs text-slate-500">
                <i data-lucide="info" class="w-4 h-4 text-brand-600"></i>
                <span>Sistem secara otomatis menyesuaikan rekomendasi dengan kondisi ketersediaan menu di stan.</span>
            </div>
            <button type="button" 
                    @click="triggerSearch()" 
                    class="whitespace-nowrap w-full sm:w-auto h-10 inline-flex items-center justify-center gap-1.5 py-2.5 px-4 rounded-xl bg-brand-600 text-white text-xs font-semibold hover:bg-brand-700 shadow-sm transition">
                <i data-lucide="refresh-cw" class="w-3.5 h-3.5" :class="isSearching ? 'animate-spin' : ''"></i>
                <span>Terapkan Kriteria Rekomendasi</span>
            </button>
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
                <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden hover:shadow-md transition flex flex-col justify-between group">
                    <div>
                        <!-- Photo & Badges -->
                        <div class="relative h-44 w-full bg-slate-100 overflow-hidden">
                            <img :src="item.image" :alt="item.name" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            
                            <!-- Stan Name Badge -->
                            <span class="absolute top-3 left-3 px-2 py-0.5 rounded-md bg-white/90 backdrop-blur-xs text-[10px] font-bold text-slate-800" x-text="item.seller"></span>
                            
                            <!-- Match Badge Clean -->
                            <span class="absolute top-3 right-3 px-2 py-0.5 rounded-md bg-brand-50 text-brand-700 text-[10px] font-bold border border-brand-200 shadow-2xs" x-text="'Kecocokan ' + item.matchScore + '%'"></span>

                            <!-- Portion Stock Badge -->
                            <span class="absolute bottom-3 left-3 px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 text-[10px] font-semibold border border-emerald-200" x-text="'Tersedia ' + item.stock + ' Porsi'"></span>
                        </div>

                        <!-- Info & Reason -->
                        <div class="p-4 space-y-2.5">
                            <div>
                                <h3 class="text-sm font-bold text-slate-900" x-text="item.name"></h3>
                                <div class="flex items-center justify-between mt-1">
                                    <span class="text-sm font-extrabold text-brand-700" x-text="formatRupiah(item.price)"></span>
                                    <span class="text-[11px] text-slate-400" x-text="'Masak ~' + item.prepTime + ' Menit'"></span>
                                </div>
                            </div>

                            <!-- Clean Reason Text (No AI gimmick quote box) -->
                            <p class="text-xs text-slate-600 leading-relaxed" x-text="item.aiReason"></p>
                        </div>
                    </div>

                    <!-- Add Button (Single Line Solid Brand Blue) -->
                    <div class="p-4 pt-0">
                        <button type="button" 
                                @click="addToCart({ id: item.id, name: item.name, price: item.price, seller: item.seller })" 
                                class="whitespace-nowrap w-full inline-flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-xl bg-brand-600 text-white text-xs font-semibold hover:bg-brand-700 shadow-sm transition">
                            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                            <span>Tambah ke Keranjang</span>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty State When Budget Is Too Low -->
        <div x-show="recommendedResults.length === 0" class="text-center py-16 bg-white rounded-2xl border border-slate-200/80 p-8 space-y-3">
            <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center mx-auto">
                <i data-lucide="alert-circle" class="w-6 h-6"></i>
            </div>
            <h4 class="text-sm font-bold text-slate-900">Belum Ada Menu yang Sesuai Kriteria</h4>
            <p class="text-xs text-slate-500 max-w-sm mx-auto leading-relaxed">
                Budget yang Anda tentukan terlalu rendah atau waktu tunggu terlalu singkat untuk menu yang tersedia. Coba tingkatkan slider anggaran di atas Rp 10.000.
            </p>
            <button type="button" 
                    @click="budget = 15000; selectedTime = '10'; selectedCategory = 'semua'; triggerSearch()" 
                    class="whitespace-nowrap inline-flex items-center justify-center px-4 py-2 rounded-xl text-xs font-semibold text-brand-700 bg-brand-50 hover:bg-brand-100 border border-brand-200 transition">
                Reset ke Rekomendasi Standar
            </button>
        </div>
    </div>

</div>
@endsection
