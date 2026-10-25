@extends('layouts.admin')

@section('title', 'Monitoring Menu — NesaCourt')

@section('content')
<div class="space-y-6" x-data="{
    selectedStall: 'semua',
    selectedCategory: 'semua',
    searchQuery: '',
    menus: [
        {
            id: 1,
            name: 'Ayam Geprek Sambal Bawang Level 3',
            category: 'makanan',
            categoryLabel: 'Makanan Berat',
            stall: 'Stan 01 - Barokah Rasa',
            stallKey: 'stan_01',
            price: 13000,
            stock: 14,
            prepTime: '7 Menit',
            image: 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?auto=format&fit=crop&w=600&q=80'
        },
        {
            id: 2,
            name: 'Ayam Penyet Sambal Ijo Padang',
            category: 'makanan',
            categoryLabel: 'Makanan Berat',
            stall: 'Stan 01 - Barokah Rasa',
            stallKey: 'stan_01',
            price: 14000,
            stock: 9,
            prepTime: '8 Menit',
            image: 'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?auto=format&fit=crop&w=600&q=80'
        },
        {
            id: 3,
            name: 'Soto Ayam Lamongan Lengkap Koya',
            category: 'makanan',
            categoryLabel: 'Makanan Berat',
            stall: 'Stan 02 - Soto Bu Siti',
            stallKey: 'stan_02',
            price: 14000,
            stock: 11,
            prepTime: '5 Menit',
            image: 'https://images.unsplash.com/photo-1541832676-9b763b0239ab?auto=format&fit=crop&w=600&q=80'
        },
        {
            id: 4,
            name: 'Kentang Goreng Krispi French Fries',
            category: 'camilan',
            categoryLabel: 'Camilan',
            stall: 'Stan 03 - Geprek Mas Bro',
            stallKey: 'stan_03',
            price: 8000,
            stock: 15,
            prepTime: '4 Menit',
            image: 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?auto=format&fit=crop&w=600&q=80'
        },
        {
            id: 5,
            name: 'Es Teh Manis Jumbo Vokasi',
            category: 'minuman',
            categoryLabel: 'Minuman',
            stall: 'Stan 04 - Aneka Jus Vokasi',
            stallKey: 'stan_04',
            price: 4000,
            stock: 40,
            prepTime: '2 Menit',
            image: 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?auto=format&fit=crop&w=600&q=80'
        },
        {
            id: 6,
            name: 'Jus Alpukat Kental Susu Cokelat',
            category: 'minuman',
            categoryLabel: 'Minuman',
            stall: 'Stan 04 - Aneka Jus Vokasi',
            stallKey: 'stan_04',
            price: 10000,
            stock: 8,
            prepTime: '4 Menit',
            image: 'https://images.unsplash.com/photo-1553530666-ba11a7da3888?auto=format&fit=crop&w=600&q=80'
        },
        {
            id: 7,
            name: 'Kopi Susu Gula Aren Ketintang',
            category: 'minuman',
            categoryLabel: 'Minuman',
            stall: 'Stan 04 - Aneka Jus Vokasi',
            stallKey: 'stan_04',
            price: 9000,
            stock: 0,
            prepTime: '3 Menit',
            image: 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?auto=format&fit=crop&w=600&q=80'
        },
        {
            id: 8,
            name: 'Paket Kenyang Hemat: Nasi Ayam + Es Teh',
            category: 'makanan',
            categoryLabel: 'Paket Hemat',
            stall: 'Stan 01 - Barokah Rasa',
            stallKey: 'stan_01',
            price: 15000,
            stock: 12,
            prepTime: '6 Menit',
            image: 'https://images.unsplash.com/photo-1569058242253-92a9c755a0ec?auto=format&fit=crop&w=600&q=80'
        }
    ],

    formatRupiah(num) {
        return 'Rp ' + Number(num).toLocaleString('id-ID');
    },

    get filteredMenus() {
        return this.menus.filter(m => {
            const matchStall = this.selectedStall === 'semua' || m.stallKey === this.selectedStall;
            const matchCat = this.selectedCategory === 'semua' || m.category === this.selectedCategory;
            const matchSearch = this.searchQuery === '' || 
                                m.name.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                                m.stall.toLowerCase().includes(this.searchQuery.toLowerCase());
            return matchStall && matchCat && matchSearch;
        });
    }
}">

    <!-- Page Title Header (Semi-Bold with Black Icon) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200/80">
        <div>
            <div class="flex items-center gap-2.5">
                <i data-lucide="utensils" class="w-6 h-6 text-slate-900 shrink-0"></i>
                <h1 class="text-xl sm:text-2xl font-semibold text-slate-900 tracking-tight">Monitoring Menu Seluruh Stan</h1>
            </div>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Pengawasan kualitas hidangan, batas wajar harga makanan mahasiswa, dan ketersediaan stok porsi seluruh stan food court.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs font-semibold text-slate-700 bg-white border border-slate-200 px-3 py-1.5 rounded-xl shadow-2xs">
                Total <span class="text-slate-900" x-text="filteredMenus.length"></span> Menu Terdaftar
            </span>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs space-y-3.5">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <!-- Filter Stan -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Filter Stan Kantin</label>
                <div class="relative">
                    <select x-model="selectedStall" class="appearance-none w-full text-xs font-semibold py-2 pl-3.5 pr-11 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-brand-500 cursor-pointer">
                        <option value="semua">Semua Stan Kantin (4)</option>
                        <option value="stan_01">Stan 01 - Barokah Rasa</option>
                        <option value="stan_02">Stan 02 - Soto Bu Siti</option>
                        <option value="stan_03">Stan 03 - Geprek Mas Bro</option>
                        <option value="stan_04">Stan 04 - Aneka Jus Vokasi</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-3.5 sm:right-4 flex items-center text-slate-400">
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5" stroke-width="1.5"></i>
                    </div>
                </div>
            </div>

            <!-- Filter Kategori -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Kategori Menu</label>
                <div class="relative">
                    <select x-model="selectedCategory" class="appearance-none w-full text-xs font-semibold py-2 pl-3.5 pr-11 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-brand-500 cursor-pointer">
                        <option value="semua">Semua Kategori</option>
                        <option value="makanan">Makanan Berat</option>
                        <option value="camilan">Camilan</option>
                        <option value="minuman">Minuman Dingin / Hangat</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-3.5 sm:right-4 flex items-center text-slate-400">
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5" stroke-width="1.5"></i>
                    </div>
                </div>
            </div>

            <!-- Search -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Pencarian Menu</label>
                <div class="relative">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                    <input type="text" 
                           x-model="searchQuery" 
                           placeholder="Cari nama makanan / stan..." 
                           class="w-full pl-9 pr-3.5 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-brand-500">
                </div>
            </div>
        </div>
    </div>

    <!-- Menus Monitoring Table / List -->
    <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-xs">
        <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="layers" class="w-4 h-4 text-slate-900"></i>
                <h3 class="text-sm font-bold text-slate-900">Katalog Menu & Verifikasi Harga Mahasiswa</h3>
            </div>
            <span class="text-xs text-slate-500">Rentang Harga Standar: Rp 4.000 - Rp 15.000</span>
        </div>

        <div class="divide-y divide-slate-100">
            <template x-for="menu in filteredMenus" :key="menu.id">
                <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50/50 transition">
                    <div class="flex items-center gap-3.5 min-w-0">
                        <!-- Food Photo -->
                        <img :src="menu.image" :alt="menu.name" class="w-14 h-14 sm:w-16 sm:h-16 rounded-xl object-cover ring-1 ring-slate-200 shrink-0 shadow-2xs">
                        
                        <div class="min-w-0 space-y-1">
                            <h4 class="text-xs sm:text-sm font-bold text-slate-900 truncate" x-text="menu.name"></h4>
                            <div class="flex flex-wrap items-center gap-2 text-xs">
                                <span class="font-semibold text-brand-700" x-text="menu.stall"></span>
                                <span class="text-slate-300 font-normal">|</span>
                                <span class="text-[11px] text-slate-500" x-text="menu.categoryLabel"></span>
                                <span class="text-slate-300 font-normal">|</span>
                                <span class="text-[11px] text-slate-500" x-text="'Masak ' + menu.prepTime"></span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between sm:justify-end gap-5 shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                        <div class="text-left sm:text-right">
                            <span class="text-sm font-bold text-slate-900 block" x-text="formatRupiah(menu.price)"></span>
                            <span class="text-[11px] font-medium" 
                                  :class="menu.stock > 0 ? 'text-slate-500' : 'text-rose-600 font-bold'" 
                                  x-text="menu.stock > 0 ? 'Sisa Stok: ' + menu.stock + ' Porsi' : 'Stok Habis'"></span>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

</div>
@endsection
