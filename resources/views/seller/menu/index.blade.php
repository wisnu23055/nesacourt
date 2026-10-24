@extends('layouts.seller')

@section('title', 'Kelola Menu Stan — NesaCourt')

@section('content')
<div class="space-y-6" x-data="{
    searchQuery: '',
    selectedCategory: 'all',
    selectedStatus: 'all',
    deleteModalOpen: false,
    menuToDelete: null,
    menus: [
        {
            id: 1,
            name: 'Paket Nasi Geprek Sambal Bawang',
            category: 'makanan',
            categoryLabel: 'Makanan',
            price: 15000,
            stock: 14,
            isAvailable: true,
            isTopSeller: true,
            image: 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?auto=format&fit=crop&w=400&q=80',
            desc: 'Nasi putih pulen, ayam goreng krispi geprek pedas gurih level 1-5, lalapan timun segar.'
        },
        {
            id: 2,
            name: 'Nasi Ayam Penyet Sambal Terasi',
            category: 'makanan',
            categoryLabel: 'Makanan',
            price: 16000,
            stock: 8,
            isAvailable: true,
            isTopSeller: false,
            image: 'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?auto=format&fit=crop&w=400&q=80',
            desc: 'Ayam goreng bumbu rempah kuning dipenyet dengan sambal terasi matang dan kemangi.'
        },
        {
            id: 3,
            name: 'Nasi Telur Krispi Pontianak',
            category: 'makanan',
            categoryLabel: 'Makanan',
            price: 10000,
            stock: 22,
            isAvailable: true,
            isTopSeller: false,
            image: 'https://images.unsplash.com/photo-1525351484163-7529414344d8?auto=format&fit=crop&w=400&q=80',
            desc: 'Dua butir telur ceplok krispi disiram kecap racik gurih manis daun bawang dan cabai rawit.'
        },
        {
            id: 4,
            name: 'Ayam Bakar Madu Solo + Nasi',
            category: 'makanan',
            categoryLabel: 'Makanan',
            price: 18000,
            stock: 0,
            isAvailable: false,
            isTopSeller: false,
            image: 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=400&q=80',
            desc: 'Ayam ungkep bumbu rempah manis legit dibakar arang wangi, disajikan bersama sambal korek.'
        },
        {
            id: 5,
            name: 'Es Teh Manis Jumbo',
            category: 'minuman',
            categoryLabel: 'Minuman',
            price: 3000,
            stock: 45,
            isAvailable: true,
            isTopSeller: true,
            image: 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?auto=format&fit=crop&w=400&q=80',
            desc: 'Teh melati seduh pekat harum, gula tebu murni dengan porsi gelas ukuran 22oz.'
        },
        {
            id: 6,
            name: 'Es Jeruk Peras Asli',
            category: 'minuman',
            categoryLabel: 'Minuman',
            price: 5000,
            stock: 19,
            isAvailable: true,
            isTopSeller: false,
            image: 'https://images.unsplash.com/photo-1613478223719-2ab802602423?auto=format&fit=crop&w=400&q=80',
            desc: 'Jeruk peras segar alami kaya vitamin C tanpa pemanis buatan, dingin dan menyegarkan.'
        },
        {
            id: 7,
            name: 'Kopi Susu Gula Aren Ketintang',
            category: 'minuman',
            categoryLabel: 'Minuman',
            price: 8000,
            stock: 12,
            isAvailable: true,
            isTopSeller: false,
            image: 'https://images.unsplash.com/photo-1517701550927-30cf4ba1dba5?auto=format&fit=crop&w=400&q=80',
            desc: 'Espresso robusta lokal dipadu susu kental dan sirup gula aren legit khas kantin kampus.'
        },
        {
            id: 8,
            name: 'Tempe Mendoan Hangat (Isi 3)',
            category: 'camilan',
            categoryLabel: 'Camilan',
            price: 6000,
            stock: 15,
            isAvailable: true,
            isTopSeller: false,
            image: 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?auto=format&fit=crop&w=400&q=80',
            desc: 'Tempe daun kedelai digoreng setengah matang renyah lembut dengan cocolan kecap rawit.'
        },
        {
            id: 9,
            name: 'Tahu Bakso Goreng Krispi (Isi 4)',
            category: 'camilan',
            categoryLabel: 'Camilan',
            price: 8000,
            stock: 10,
            isAvailable: true,
            isTopSeller: false,
            image: 'https://images.unsplash.com/photo-1541592106381-b31e9677c0e5?auto=format&fit=crop&w=400&q=80',
            desc: 'Tahu pong isi adonan bakso sapi gurih digoreng garing renyah disajikan dengan cabai rawit.'
        }
    ],
    filteredMenus() {
        return this.menus.filter(item => {
            const matchesSearch = item.name.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                                  item.desc.toLowerCase().includes(this.searchQuery.toLowerCase());
            const matchesCategory = this.selectedCategory === 'all' || item.category === this.selectedCategory;
            const matchesStatus = this.selectedStatus === 'all' || 
                                  (this.selectedStatus === 'available' && item.isAvailable) || 
                                  (this.selectedStatus === 'empty' && !item.isAvailable);
            return matchesSearch && matchesCategory && matchesStatus;
        });
    },
    confirmDelete(menu) {
        this.menuToDelete = menu;
        this.deleteModalOpen = true;
    },
    executeDelete() {
        if (this.menuToDelete) {
            this.menus = this.menus.filter(m => m.id !== this.menuToDelete.id);
            this.deleteModalOpen = false;
            this.menuToDelete = null;
        }
    },
    toggleAvailability(menu) {
        menu.isAvailable = !menu.isAvailable;
        if (!menu.isAvailable) {
            menu.stock = 0;
        } else if (menu.stock === 0) {
            menu.stock = 10;
        }
    }
}">

    <!-- Page Header & Action Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-7 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                    <a href="{{ url('/penjual/dashboard') }}" class="hover:text-brand-600">Portal Stan</a>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-400"></i>
                    <span class="text-slate-800 font-semibold">Kelola Menu</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-semibold text-slate-900 tracking-tight">Katalog Menu Stan 01</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-2 max-w-xl">Kelola daftar menu makanan, minuman, harga porsi, serta status aktif di kantin.</p>
            </div>

            <!-- Buttons: On mobile displayed below description, on desktop aligned to the right -->
            <div class="flex items-center gap-2.5 shrink-0 pt-1 sm:pt-0">
                <a href="{{ url('/penjual/stok') }}" 
                   class="flex-1 sm:flex-none h-10 py-2.5 px-4 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                    <i data-lucide="layers" class="w-4 h-4 text-slate-500"></i>
                    <span>Cepat Kelola Stok</span>
                </a>
                <a href="{{ url('/penjual/menu/create') }}" 
                   class="flex-1 sm:flex-none h-10 py-2.5 px-4 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-700 text-white shadow-sm shadow-brand-500/20 transition">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span>Tambah Menu Baru</span>
                </a>
            </div>
        </div>

        <!-- Micro Summary Counters (Hitam Semua, Semi Bold & Rata Tengah) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t border-slate-100">
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Total Item Menu</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5" x-text="menus.length + ' Item'"></span>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Status Siap Saji</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5" x-text="menus.filter(m => m.isAvailable).length + ' Tersedia'"></span>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Menu Porsi Habis</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5" x-text="menus.filter(m => !m.isAvailable || m.stock === 0).length + ' Habis'"></span>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Kategori Terbanyak</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5">Makanan (4)</span>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs space-y-3.5">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
            <!-- Search Field -->
            <div class="relative flex-1 max-w-md">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                <input type="text" 
                       x-model="searchQuery" 
                       placeholder="Cari nama menu atau deskripsi masakan..." 
                       class="w-full h-10 pl-10 pr-4 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
            </div>

            <!-- Status Filter Dropdown -->
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-500 font-medium whitespace-nowrap">Filter:</span>
                <div class="relative">
                    <select x-model="selectedStatus" 
                            class="h-10 pl-3.5 pr-10 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 appearance-none focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition cursor-pointer">
                        <option value="all">Semua Ketersediaan</option>
                        <option value="available">Tersedia (Siap Saji)</option>
                        <option value="empty">Porsi Habis</option>
                    </select>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                </div>
            </div>
        </div>

        <!-- Category Pills Bar -->
        <div class="flex items-center gap-2 overflow-x-auto thin-scrollbar pb-1 pt-1 border-t border-slate-100">
            <button type="button" 
                    @click="selectedCategory = 'all'" 
                    class="h-9 px-3.5 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center transition"
                    :class="selectedCategory === 'all' ? 'bg-brand-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                Semua Kategori
            </button>
            <button type="button" 
                    @click="selectedCategory = 'makanan'" 
                    class="h-9 px-3.5 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center transition"
                    :class="selectedCategory === 'makanan' ? 'bg-brand-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                Makanan Utama
            </button>
            <button type="button" 
                    @click="selectedCategory = 'minuman'" 
                    class="h-9 px-3.5 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center transition"
                    :class="selectedCategory === 'minuman' ? 'bg-brand-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                Minuman Segar
            </button>
            <button type="button" 
                    @click="selectedCategory = 'camilan'" 
                    class="h-9 px-3.5 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center transition"
                    :class="selectedCategory === 'camilan' ? 'bg-brand-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                Camilan & Pelengkap
            </button>
        </div>
    </div>

    <!-- Menus List Section (Table for Desktop, Cards for Mobile) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="utensils-crossed" class="w-4 h-4 text-slate-900"></i>
                <h2 class="text-sm font-bold text-slate-900">Daftar Menu Stan</h2>
                <span class="text-xs text-slate-400 font-normal" x-text="'(' + filteredMenus().length + ' menu ditemukan)'"></span>
            </div>
            <span class="text-xs text-slate-400">Stan 01 — Counter Barat</span>
        </div>

        <!-- Table View (Desktop & Mobile Horizontal Scroll) -->
        <div class="overflow-x-auto thin-scrollbar">
            <table class="w-full min-w-[700px] text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4">Menu & Deskripsi</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4">Harga Satuan</th>
                        <th class="py-3 px-4">Stok Porsi</th>
                        <th class="py-3 px-4 text-center">Status Jual</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    <template x-for="menu in filteredMenus()" :key="menu.id">
                        <tr class="hover:bg-slate-50/70 transition">
                            <!-- Menu Name & Image -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <img :src="menu.image" :alt="menu.name" class="w-12 h-12 rounded-xl object-cover shrink-0 ring-1 ring-slate-200">
                                    <div class="min-w-0 max-w-xs sm:max-w-sm">
                                        <span class="font-bold text-slate-900 block truncate" x-text="menu.name"></span>
                                        <p class="text-[11px] text-slate-500 line-clamp-1 mt-0.5" x-text="menu.desc"></p>
                                    </div>
                                </div>
                            </td>

                            <!-- Category -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-slate-100 text-slate-700" x-text="menu.categoryLabel"></span>
                            </td>

                            <!-- Price -->
                            <td class="py-3.5 px-4 whitespace-nowrap font-extrabold text-slate-900" x-text="'Rp ' + menu.price.toLocaleString('id-ID')"></td>

                            <!-- Stock -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="font-bold" :class="menu.stock > 3 ? 'text-slate-800' : (menu.stock > 0 ? 'text-amber-600' : 'text-rose-600')" x-text="menu.stock + ' porsi'"></span>
                            </td>

                            <!-- Status Toggle (Warna Hijau Kontras Font Putih & Titik Kedap-Kedip) -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <button type="button" 
                                        @click="toggleAvailability(menu)"
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-[11px] font-semibold border transition shadow-2xs"
                                        :class="menu.isAvailable ? 'bg-emerald-600 text-white border-emerald-600 hover:bg-emerald-700' : 'bg-slate-100 text-slate-600 border-slate-200 hover:bg-slate-200'">
                                    <span class="relative flex h-1.5 w-1.5 shrink-0">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" :class="menu.isAvailable ? 'bg-white' : 'bg-slate-400'"></span>
                                        <span class="relative inline-flex rounded-full h-1.5 w-1.5" :class="menu.isAvailable ? 'bg-white' : 'bg-slate-400'"></span>
                                    </span>
                                    <span x-text="menu.isAvailable ? 'Tersedia' : 'Habis'"></span>
                                </button>
                            </td>

                            <!-- Actions (Center) -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <div class="inline-flex items-center justify-center gap-1.5">
                                    <a :href="'{{ url('/penjual/menu/create') }}?id=' + menu.id" 
                                       class="p-2 rounded-xl text-slate-500 hover:text-brand-600 hover:bg-brand-50 transition"
                                       title="Ubah Menu">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </a>
                                    <button type="button" 
                                            @click="confirmDelete(menu)"
                                            class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition"
                                            title="Hapus Menu">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Empty State if no menu matches search -->
        <div x-show="filteredMenus().length === 0" class="py-12 px-4 text-center">
            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                <i data-lucide="search-x" class="w-6 h-6"></i>
            </div>
            <h3 class="text-sm font-bold text-slate-800">Tidak ada menu yang sesuai</h3>
            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Silakan ubah kata kunci pencarian atau ganti filter kategori menu Anda.</p>
            <button type="button" 
                    @click="searchQuery = ''; selectedCategory = 'all'; selectedStatus = 'all'" 
                    class="mt-3 h-8 px-3 rounded-lg bg-slate-100 text-slate-700 text-xs font-semibold inline-flex items-center gap-1.5 hover:bg-slate-200">
                <span>Reset Filter</span>
            </button>
        </div>
    </div>

    <!-- Delete Confirmation Modal (Alpine.js) -->
    <div x-show="deleteModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
         @keydown.escape.window="deleteModalOpen = false">
        <div class="bg-white rounded-2xl max-w-sm w-full p-6 space-y-4 shadow-xl border border-slate-200"
             @click.away="deleteModalOpen = false">
            <div class="w-11 h-11 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center">
                <i data-lucide="alert-triangle" class="w-5 h-5"></i>
            </div>
            <div class="space-y-1">
                <h3 class="text-base font-bold text-slate-900">Hapus Menu Ini?</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Menu <span class="font-bold text-slate-800" x-text="menuToDelete ? menuToDelete.name : ''"></span> akan dihapus dari daftar katalog stan Anda.
                </p>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" 
                        @click="deleteModalOpen = false"
                        class="h-10 py-2.5 px-4 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                    Batal
                </button>
                <button type="button" 
                        @click="executeDelete()"
                        class="h-10 py-2.5 px-4 rounded-xl text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 transition">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });
</script>
@endsection
