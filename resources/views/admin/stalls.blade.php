@extends('layouts.admin')

@section('title', 'Manajemen Stan Kantin — NesaCourt')

@section('content')
<div class="space-y-6" x-data="{
    filterLocation: 'semua',
    searchQuery: '',
    modalOpen: false,
    newStall: {
        name: '',
        owner: '',
        phone: '',
        location: 'Counter Barat',
        number: '05'
    },
    stalls: [
        {
            id: 1,
            number: '01',
            name: 'Stan 01 - Barokah Rasa',
            locationArea: 'barat',
            location: 'Counter Barat No. 01',
            owner: 'Pak Bambang',
            phone: '081234567891',
            ownerImage: 'https://images.unsplash.com/photo-1556157382-97eda2d62296?auto=format&fit=crop&w=120&q=80',
            stallImage: 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=600&q=80',
            menuCount: 8,
            rentStatus: 'Lunas (Okt 2026)',
            isOpen: true
        },
        {
            id: 2,
            number: '02',
            name: 'Stan 02 - Soto Bu Siti',
            locationArea: 'barat',
            location: 'Counter Barat No. 02',
            owner: 'Bu Siti Rahayu',
            phone: '081234567892',
            ownerImage: 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=120&q=80',
            stallImage: 'https://images.unsplash.com/photo-1541832676-9b763b0239ab?auto=format&fit=crop&w=600&q=80',
            menuCount: 6,
            rentStatus: 'Lunas (Okt 2026)',
            isOpen: true
        },
        {
            id: 3,
            number: '03',
            name: 'Stan 03 - Geprek Mas Bro',
            locationArea: 'timur',
            location: 'Counter Timur No. 01',
            owner: 'Mas Hendra',
            phone: '081234567893',
            ownerImage: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=120&q=80',
            stallImage: 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?auto=format&fit=crop&w=600&q=80',
            menuCount: 7,
            rentStatus: 'Lunas (Okt 2026)',
            isOpen: true
        },
        {
            id: 4,
            number: '04',
            name: 'Stan 04 - Aneka Jus Vokasi',
            locationArea: 'timur',
            location: 'Counter Timur No. 02',
            owner: 'Bu Dian Fitri',
            phone: '081234567894',
            ownerImage: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=120&q=80',
            stallImage: 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?auto=format&fit=crop&w=600&q=80',
            menuCount: 9,
            rentStatus: 'Lunas (Okt 2026)',
            isOpen: true
        }
    ],

    get filteredStalls() {
        return this.stalls.filter(s => {
            const matchLoc = this.filterLocation === 'semua' || s.locationArea === this.filterLocation;
            const matchSearch = this.searchQuery === '' || 
                                s.name.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                                s.owner.toLowerCase().includes(this.searchQuery.toLowerCase());
            return matchLoc && matchSearch;
        });
    },

    toggleStall(id) {
        const stall = this.stalls.find(s => s.id === id);
        if (stall) stall.isOpen = !stall.isOpen;
    }
}">

    <!-- Page Title Header (Semi-Bold with Black Icon) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200/80">
        <div>
            <div class="flex items-center gap-2.5">
                <i data-lucide="store" class="w-6 h-6 text-slate-900 shrink-0"></i>
                <h1 class="text-xl sm:text-2xl font-semibold text-slate-900 tracking-tight">Manajemen Stan Kantin</h1>
            </div>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Kelola data pendaftaran stan, penetapan counter food court Ketintang, profil pemilik, dan status sewa operasional.</p>
        </div>
        <button type="button" 
                @click="modalOpen = true" 
                class="whitespace-nowrap inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-semibold hover:bg-slate-800 shadow-sm transition">
            <i data-lucide="plus" class="w-4 h-4 text-white"></i>
            <span>Tambah Stan Baru</span>
        </button>
    </div>

    <!-- 4 Key Statistics Cards (Gaya Identik Halaman Penjual dengan Container Putih) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 shadow-xs">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <!-- Card 1: Total Stan Terdaftar -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Total Stan Terdaftar</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5">4 Stan</span>
                <span class="text-[10px] text-slate-500 block mt-0.5">Kapasitas 8 counter</span>
            </div>

            <!-- Card 2: Stan Beroperasi Buka -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Stan Beroperasi Buka</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5">4 Stan</span>
                <span class="text-[10px] text-slate-500 block mt-0.5">100% aktif melayani</span>
            </div>

            <!-- Card 3: Counter Tersedia -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Counter Tersedia</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5">4 Counter</span>
                <span class="text-[10px] text-slate-500 block mt-0.5">Siap disewa mitra baru</span>
            </div>

            <!-- Card 4: Status Retribusi Sewa -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Status Retribusi Sewa</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5">100% Tertib</span>
                <span class="text-[10px] text-slate-500 block mt-0.5">Lunas periode berjalan</span>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0 scrollbar-none">
            <button type="button" 
                    @click="filterLocation = 'semua'" 
                    :class="filterLocation === 'semua' ? 'bg-slate-900 text-white font-bold' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200'" 
                    class="whitespace-nowrap px-3.5 py-1.5 rounded-xl text-xs transition">
                Semua Counter (4)
            </button>
            <button type="button" 
                    @click="filterLocation = 'barat'" 
                    :class="filterLocation === 'barat' ? 'bg-slate-900 text-white font-bold' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200'" 
                    class="whitespace-nowrap px-3.5 py-1.5 rounded-xl text-xs transition">
                Counter Barat (2)
            </button>
            <button type="button" 
                    @click="filterLocation = 'timur'" 
                    :class="filterLocation === 'timur' ? 'bg-slate-900 text-white font-bold' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200'" 
                    class="whitespace-nowrap px-3.5 py-1.5 rounded-xl text-xs transition">
                Counter Timur (2)
            </button>
        </div>

        <div class="relative w-full sm:w-64">
            <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
            <input type="text" 
                   x-model="searchQuery" 
                   placeholder="Cari stan atau pemilik..." 
                   class="w-full pl-9 pr-3.5 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500">
        </div>
    </div>

    <!-- Stalls Directory Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <template x-for="stall in filteredStalls" :key="stall.id">
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 space-y-4 shadow-xs hover:border-slate-300 transition">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3.5 min-w-0">
                        <img :src="stall.stallImage" :alt="stall.name" class="w-16 h-16 rounded-xl object-cover ring-1 ring-slate-200 shrink-0 shadow-2xs">
                        <div class="min-w-0">
                            <h3 class="text-sm font-bold text-slate-900 truncate" x-text="stall.name"></h3>
                            <p class="text-xs text-slate-500 mt-0.5" x-text="stall.location"></p>
                            <span class="text-[11px] font-semibold text-brand-700 block mt-0.5" x-text="stall.menuCount + ' Menu Aktif'"></span>
                        </div>
                    </div>

                    <!-- Operational Switch -->
                    <button type="button" 
                            @click="toggleStall(stall.id)" 
                            :class="stall.isOpen ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-600'"
                            class="whitespace-nowrap px-2.5 py-1 rounded-lg text-[11px] font-bold shadow-2xs transition">
                        <span x-text="stall.isOpen ? 'Buka' : 'Tutup'"></span>
                    </button>
                </div>

                <!-- Owner Details Row -->
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2.5">
                        <img :src="stall.ownerImage" :alt="stall.owner" class="w-8 h-8 rounded-lg object-cover ring-1 ring-slate-200 shrink-0">
                        <div>
                            <span class="font-bold text-slate-800 block truncate" x-text="stall.owner"></span>
                            <span class="text-[10px] text-slate-500" x-text="stall.phone"></span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] text-slate-400 block">Status Retribusi</span>
                        <span class="font-semibold text-emerald-600 text-[11px]" x-text="stall.rentStatus"></span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-2 pt-1 border-t border-slate-100 text-xs">
                    <a :href="'{{ url('/admin/menu') }}?stan=' + stall.id" class="whitespace-nowrap flex-1 inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 font-semibold hover:bg-slate-100 transition">
                        <i data-lucide="utensils" class="w-3.5 h-3.5 text-slate-700"></i>
                        <span>Katalog Menu</span>
                    </a>
                    <a :href="'{{ url('/admin/pesanan') }}?stan=' + stall.id" class="whitespace-nowrap flex-1 inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 font-semibold hover:bg-slate-100 transition">
                        <i data-lucide="receipt" class="w-3.5 h-3.5 text-slate-700"></i>
                        <span>Arus Pesanan</span>
                    </a>
                </div>
            </div>
        </template>
    </div>

    <!-- Modal Form: Tambah Stan Baru -->
    <div x-show="modalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl border border-slate-100">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <i data-lucide="store" class="w-5 h-5 text-slate-900"></i>
                    <h3 class="text-base font-bold text-slate-900">Registrasi Stan Baru</h3>
                </div>
                <button type="button" @click="modalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <div class="space-y-3 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama Stan Kantin</label>
                    <input type="text" x-model="newStall.name" placeholder="Contoh: Stan 05 - Nasi Goreng Juara" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama Pemilik Stan</label>
                    <input type="text" x-model="newStall.owner" placeholder="Contoh: Bu Sri Mulyani" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Area Counter</label>
                        <select x-model="newStall.location" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white">
                            <option value="Counter Barat">Counter Barat</option>
                            <option value="Counter Timur">Counter Timur</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nomor Counter</label>
                        <input type="text" x-model="newStall.number" placeholder="Contoh: 03" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white">
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nomor WhatsApp Aktif</label>
                    <input type="text" x-model="newStall.phone" placeholder="08xxxxxxxxxx" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white">
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" @click="modalOpen = false" class="whitespace-nowrap px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                    Batal
                </button>
                <button type="button" 
                        @click="modalOpen = false; alert('Data pendaftaran stan berhasil disimpan ke sistem.')" 
                        class="whitespace-nowrap px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-semibold hover:bg-slate-800">
                    Simpan Stan
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
