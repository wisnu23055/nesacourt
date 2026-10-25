@extends('layouts.buyer')

@section('title', 'Katalog Menu & Stan — NesaCourt')

@section('content')
<div class="space-y-6" x-data="{
    searchQuery: '',
    selectedCategory: 'semua',
    selectedStall: 'semua',
    detailModalOpen: false,
    selectedMenu: null,
    itemNotes: '',
    detailQty: 1,

    categories: [
        { id: 'semua', label: 'Semua Menu' },
        { id: 'makanan', label: 'Makanan Berat' },
        { id: 'camilan', label: 'Camilan & Gorengan' },
        { id: 'minuman', label: 'Minuman Dingin' },
        { id: 'kopi', label: 'Kopi & Teh' },
        { id: 'paket', label: 'Paket Hemat Mahasiswa' }
    ],

    menus: [
        {
            id: 101,
            name: 'Ayam Geprek Sambal Bawang Level 3',
            category: 'makanan',
            seller: 'Stan 01 - Barokah Rasa',
            sellerKey: 'barokah',
            price: 13000,
            stock: 14,
            prepTime: '7 Menit',
            image: 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?auto=format&fit=crop&w=600&q=80',
            description: 'Daging ayam segar renyah dengan balutan tepung bumbu khas, diulek langsung bersama cabai rawit merah dan bawang putih segar.',
            tags: ['Pedas', 'Gurih', 'Nasi Putih']
        },
        {
            id: 102,
            name: 'Ayam Penyet Sambal Ijo Padang',
            category: 'makanan',
            seller: 'Stan 01 - Barokah Rasa',
            sellerKey: 'barokah',
            price: 14000,
            stock: 9,
            prepTime: '8 Menit',
            image: 'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?auto=format&fit=crop&w=600&q=80',
            description: 'Ayam ungkep bumbu rempah kuning digoreng garing dengan siraman sambal cabe ijo khas Minang yang gurih pedas nikmat.',
            tags: ['Pedas Segar', 'Ayam Ungkep']
        },
        {
            id: 103,
            name: 'Soto Ayam Lamongan Lengkap Koya',
            category: 'makanan',
            seller: 'Stan 02 - Soto Bu Siti',
            sellerKey: 'soto',
            price: 14000,
            stock: 11,
            prepTime: '5 Menit',
            image: 'https://images.unsplash.com/photo-1541832676-9b763b0239ab?auto=format&fit=crop&w=600&q=80',
            description: 'Kuah soto kuning gurih beraroma kaldu ayam kampung, suwiran daging ayam, soun lembut, tauge, daun seledri, dan bubuk koya gurih.',
            tags: ['Berkuah Hangat', 'Koya Gurih']
        },
        {
            id: 104,
            name: 'Rawon Daging Sapi Kuah Hitam',
            category: 'makanan',
            seller: 'Stan 02 - Soto Bu Siti',
            sellerKey: 'soto',
            price: 18000,
            stock: 6,
            prepTime: '6 Menit',
            image: 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=600&q=80',
            description: 'Potongan daging sapi empuk dalam kuah kluwek hitam gurih khas Jawa Timur, disajikan lengkap bersama tauge pendek segar dan sambal terasi.',
            tags: ['Daging Sapi', 'Khas Jatim']
        },
        {
            id: 105,
            name: 'Nasi Goreng Spesial Telur Ceplok',
            category: 'makanan',
            seller: 'Stan 03 - Geprek Mas Bro',
            sellerKey: 'geprek',
            price: 12000,
            stock: 18,
            prepTime: '7 Menit',
            image: 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?auto=format&fit=crop&w=600&q=80',
            description: 'Nasi goreng harum beraroma wok dengan kecap manis bango, bumbu bawang, irisan baso, suwiran ayam, dan telur ceplok setengah matang.',
            tags: ['Wok Aroma', 'Porsi Pas']
        },
        {
            id: 106,
            name: 'Kentang Goreng Krispi French Fries',
            category: 'camilan',
            seller: 'Stan 03 - Geprek Mas Bro',
            sellerKey: 'geprek',
            price: 8000,
            stock: 15,
            prepTime: '4 Menit',
            image: 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?auto=format&fit=crop&w=600&q=80',
            description: 'Kentang potong garing bertabur bumbu keju manis atau balado gurih, cocok untuk camilan santai saat menunggu jadwal kuliah.',
            tags: ['Camilan Garing', 'Tabur Bumbu']
        },
        {
            id: 107,
            name: 'Es Teh Manis Jumbo Vokasi',
            category: 'minuman',
            seller: 'Stan 04 - Aneka Jus Vokasi',
            sellerKey: 'minuman',
            price: 4000,
            stock: 40,
            prepTime: '2 Menit',
            image: 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?auto=format&fit=crop&w=600&q=80',
            description: 'Teh melati seduh segar dengan gula asli dan es batu melimpah. Ukuran cup jumbo 22 oz yang bikin dahaga langsung hilang.',
            tags: ['Dingin Segar', 'Paling Murah']
        },
        {
            id: 108,
            name: 'Jus Alpukat Kental Susu Cokelat',
            category: 'minuman',
            seller: 'Stan 04 - Aneka Jus Vokasi',
            sellerKey: 'minuman',
            price: 10000,
            stock: 8,
            prepTime: '4 Menit',
            image: 'https://images.unsplash.com/photo-1553530666-ba11a7da3888?auto=format&fit=crop&w=600&q=80',
            description: 'Alpukat mentega matang pilihan diblender lembut dengan kental manis cokelat di sekeliling gelas.',
            tags: ['Kental', 'Buah Segar']
        },
        {
            id: 109,
            name: 'Kopi Susu Gula Aren Ketintang',
            category: 'kopi',
            seller: 'Stan 04 - Aneka Jus Vokasi',
            sellerKey: 'minuman',
            price: 9000,
            stock: 0,
            prepTime: '3 Menit',
            image: 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?auto=format&fit=crop&w=600&q=80',
            description: 'Perpaduan biji kopi robusta nusantara, susu segar creamy, dan manisnya sirup aren murni asli Tuban.',
            tags: ['Kopi Hits', 'Stok Habis']
        },
        {
            id: 110,
            name: 'Paket Kenyang Hemat: Nasi Ayam + Es Teh',
            category: 'paket',
            seller: 'Stan 01 - Barokah Rasa',
            sellerKey: 'barokah',
            price: 15000,
            stock: 12,
            prepTime: '6 Menit',
            image: 'https://images.unsplash.com/photo-1569058242253-92a9c755a0ec?auto=format&fit=crop&w=600&q=80',
            description: 'Solusi tanggal tua mahasiswa: 1 porsi Nasi Ayam Geprek / Penyet komplit dengan 1 cup Es Teh Manis Jumbo.',
            tags: ['Paling Hemat', 'Komplit']
        }
    ],

    openDetail(menu) {
        this.selectedMenu = menu;
        this.detailQty = 1;
        this.itemNotes = '';
        this.detailModalOpen = true;
    },

    get filteredMenus() {
        return this.menus.filter(m => {
            const matchSearch = this.searchQuery === '' || 
                m.name.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                m.seller.toLowerCase().includes(this.searchQuery.toLowerCase());
            const matchCat = this.selectedCategory === 'semua' || m.category === this.selectedCategory;
            const matchStall = this.selectedStall === 'semua' || m.sellerKey === this.selectedStall;
            return matchSearch && matchCat && matchStall;
        });
    }
}">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-slate-200/80">
        <div>
            <h1 class="text-xl sm:text-2xl font-semibold text-slate-900 tracking-tight">Katalog Menu & Stan</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Pilih aneka kuliner halal, higienis, dan ramah kantong mahasiswa di Food Court UNESA Ketintang.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs text-slate-500 font-medium">Filter Stan:</span>
            <div class="relative inline-block">
                <select x-model="selectedStall" class="appearance-none text-xs font-semibold py-2 pl-3.5 pr-9 rounded-xl border border-slate-200 bg-white text-slate-700 hover:border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 shadow-2xs cursor-pointer transition">
                    <option value="semua">Semua Stan Kantin</option>
                    <option value="barokah">Stan 01 - Barokah Rasa</option>
                    <option value="soto">Stan 02 - Soto Bu Siti</option>
                    <option value="geprek">Stan 03 - Geprek Mas Bro</option>
                    <option value="minuman">Stan 04 - Aneka Jus Vokasi</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
                    <i data-lucide="chevron-down" class="w-4 h-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Bar & Category Chips -->
    <div class="space-y-4">
        <!-- Search Input -->
        <div class="relative w-full">
            <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
            <input type="text" 
                   x-model="searchQuery" 
                   placeholder="Cari nama makanan, minuman, atau stan kantin..." 
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs sm:text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 shadow-xs">
        </div>

        <!-- Category Horizontal Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
            <template x-for="cat in categories" :key="cat.id">
                <button type="button" 
                        @click="selectedCategory = cat.id" 
                        :class="selectedCategory === cat.id ? 'bg-brand-600 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'"
                        class="whitespace-nowrap px-3.5 py-2 rounded-xl text-xs font-semibold transition"
                        x-text="cat.label">
                </button>
            </template>
        </div>
    </div>

    <!-- Menu Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <template x-for="item in filteredMenus" :key="item.id">
            <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden hover:shadow-md transition flex flex-col justify-between">
                <!-- Top Part -->
                <div>
                    <!-- Food Photo without floating badges -->
                    <div class="relative h-44 w-full bg-slate-100 overflow-hidden cursor-pointer" @click="openDetail(item)">
                        <img :src="item.image" 
                             :alt="item.name" 
                             class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    </div>

                    <!-- Content: Food Name, Stan Name Below It, Desc, Price -->
                    <div class="p-4 space-y-1.5">
                        <h3 class="text-sm font-bold text-slate-900 hover:text-brand-600 transition cursor-pointer" @click="openDetail(item)" x-text="item.name"></h3>
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
                        <p class="text-xs text-slate-500 line-clamp-2 pt-0.5" x-text="item.description"></p>
                        <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                            <span class="text-sm font-extrabold text-brand-700" x-text="formatRupiah(item.price)"></span>
                            <span class="text-[11px] text-slate-400" x-text="'Masak ' + item.prepTime"></span>
                        </div>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="p-4 pt-0">
                    <button type="button" 
                            :disabled="item.stock === 0"
                            @click="addToCart({ id: item.id, name: item.name, price: item.price, seller: item.seller })" 
                            :class="item.stock === 0 ? 'bg-slate-100 text-slate-400 cursor-not-allowed border-slate-200' : 'bg-brand-600 text-white hover:bg-brand-700 shadow-sm'"
                            class="whitespace-nowrap w-full inline-flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-xl text-xs font-semibold transition">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span x-text="item.stock === 0 ? 'Porsi Habis' : 'Tambah ke Keranjang'"></span>
                    </button>
                </div>
            </div>
        </template>
    </div>

    <!-- Empty State Filter -->
    <div x-show="filteredMenus.length === 0" class="text-center py-16 bg-white rounded-2xl border border-slate-200/80 p-8 space-y-3">
        <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
            <i data-lucide="search-x" class="w-6 h-6"></i>
        </div>
        <h4 class="text-sm font-bold text-slate-800">Menu Tidak Ditemukan</h4>
        <p class="text-xs text-slate-500 max-w-sm mx-auto">Tidak ada menu yang cocok dengan kata kunci atau filter yang Anda pilih. Coba gunakan filter lain.</p>
        <button type="button" @click="searchQuery = ''; selectedCategory = 'semua'; selectedStall = 'semua'" class="whitespace-nowrap inline-flex items-center justify-center px-4 py-2 rounded-xl text-xs font-semibold text-brand-600 bg-brand-50 hover:bg-brand-100 transition">
            Reset Semua Filter
        </button>
    </div>

    <!-- F-07: Detail Menu Pop-up Modal Quick View -->
    <template x-teleport="body">
        <div x-show="detailModalOpen" 
             x-cloak 
             class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white rounded-2xl max-w-lg w-full overflow-hidden shadow-2xl flex flex-col max-h-[90vh]" @click.outside="detailModalOpen = false">
                <!-- Modal Header Image -->
                <div class="relative h-48 w-full bg-slate-100 shrink-0">
                    <img :src="selectedMenu?.image" :alt="selectedMenu?.name" class="w-full h-full object-cover">
                    <button type="button" @click="detailModalOpen = false" class="absolute top-3 right-3 w-8 h-8 rounded-full bg-white/80 hover:bg-white text-slate-700 flex items-center justify-center shadow-xs transition">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                    <span class="absolute bottom-3 left-3 px-2.5 py-1 rounded-md bg-white/90 backdrop-blur-sm text-xs font-bold text-slate-900" x-text="selectedMenu?.seller"></span>
                </div>

                <!-- Modal Content Body -->
                <div class="p-5 space-y-4 overflow-y-auto flex-1">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-base sm:text-lg font-bold text-slate-900" x-text="selectedMenu?.name"></span>
                            <span class="text-base font-extrabold text-brand-700" x-text="formatRupiah(selectedMenu?.price || 0)"></span>
                        </div>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-xs text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200" x-text="'Tersedia: ' + (selectedMenu?.stock || 0) + ' Porsi'"></span>
                            <span class="text-xs text-slate-500" x-text="'Estimasi: ' + selectedMenu?.prepTime"></span>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <h5 class="text-xs font-bold text-slate-800">Deskripsi Menu</h5>
                        <p class="text-xs text-slate-600 leading-relaxed" x-text="selectedMenu?.description"></p>
                    </div>

                    <!-- Tags -->
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <template x-for="tag in (selectedMenu?.tags || [])" :key="tag">
                            <span class="text-[10px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded" x-text="tag"></span>
                        </template>
                    </div>

                    <!-- Input Catatan Pesanan Khusus -->
                    <div class="space-y-1.5 pt-2 border-t border-slate-100">
                        <label class="block text-xs font-semibold text-slate-700">Catatan Khusus Penjual (Opsional)</label>
                        <input type="text" 
                               x-model="itemNotes" 
                               placeholder="Contoh: Sambal dipisah, tidak pakai daun bawang, kuah banyak" 
                               class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </div>
                </div>

                <!-- Modal Footer Action -->
                <div class="p-4 border-t border-slate-100 bg-slate-50 flex items-center justify-between gap-3 shrink-0">
                    <div class="flex items-center gap-2">
                        <button type="button" @click="detailQty = Math.max(1, detailQty - 1)" class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-700 font-bold text-sm flex items-center justify-center hover:bg-slate-100">-</button>
                        <span class="text-xs font-bold w-6 text-center" x-text="detailQty"></span>
                        <button type="button" @click="detailQty = Math.min((selectedMenu?.stock || 1), detailQty + 1)" class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-700 font-bold text-sm flex items-center justify-center hover:bg-slate-100">+</button>
                    </div>

                    <button type="button" 
                            :disabled="selectedMenu?.stock === 0"
                            @click="for(let i=0; i<detailQty; i++) { addToCart({ id: selectedMenu.id, name: selectedMenu.name, price: selectedMenu.price, seller: selectedMenu.seller }); } detailModalOpen = false;" 
                            :class="selectedMenu?.stock === 0 ? 'bg-slate-200 text-slate-400 cursor-not-allowed' : 'bg-brand-600 text-white hover:bg-brand-700 shadow-sm'"
                            class="whitespace-nowrap flex-1 inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-xs font-bold transition">
                        <i data-lucide="shopping-bag" class="w-3.5 h-3.5"></i>
                        <span x-text="selectedMenu?.stock === 0 ? 'Porsi Habis' : 'Tambah ke Keranjang | ' + formatRupiah((selectedMenu?.price || 0) * detailQty)"></span>
                    </button>
                </div>
            </div>
        </div>
    </template>

</div>
@endsection
