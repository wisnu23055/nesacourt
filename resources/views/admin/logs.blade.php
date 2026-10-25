@extends('layouts.admin')

@section('title', 'Log Aktivitas Stan Penjual — NesaCourt')

@section('content')
<div class="space-y-6" x-data="{
    activityFilter: 'semua',
    searchQuery: '',
    logs: [
        {
            id: 1,
            time: '25 Okt 2026, 12:45:10 WIB',
            actorName: 'Pak Bambang',
            stallName: 'Stan 01 - Barokah Rasa',
            actorImage: 'https://images.unsplash.com/photo-1556157382-97eda2d62296?auto=format&fit=crop&w=120&q=80',
            action: 'Update Stok Porsi',
            category: 'stok',
            description: 'Menambah stok porsi Ayam Geprek Sambal Bawang sebanyak +20 porsi untuk jam istirahat siang.',
            module: 'Stok Menu',
            ip: '10.20.44.12',
            device: 'Android Chrome (Stan 01)',
            status: 'Sukses'
        },
        {
            id: 2,
            time: '25 Okt 2026, 09:30:15 WIB',
            actorName: 'Bu Siti Rahayu',
            stallName: 'Stan 02 - Soto Bu Siti',
            actorImage: 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=120&q=80',
            action: 'Update Stok Porsi',
            category: 'stok',
            description: 'Memperbarui ketersediaan sisa porsi Soto Ayam Lamongan menjadi 15 porsi kuah segar.',
            module: 'Stok Menu',
            ip: '10.20.44.15',
            device: 'Android Chrome (Stan 02)',
            status: 'Sukses'
        },
        {
            id: 3,
            time: '25 Okt 2026, 08:05:40 WIB',
            actorName: 'Bu Dian Fitri',
            stallName: 'Stan 04 - Aneka Jus Vokasi',
            actorImage: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=120&q=80',
            action: 'Login Akun Stan',
            category: 'login',
            description: 'Autentikasi login perangkat kasir stan berhasil terhubung di jaringan Food Court Ketintang.',
            module: 'Autentikasi',
            ip: '10.20.44.22',
            device: 'iPad Safari (Stan 04)',
            status: 'Sukses'
        },
        {
            id: 4,
            time: '25 Okt 2026, 07:45:22 WIB',
            actorName: 'Pak Bambang',
            stallName: 'Stan 01 - Barokah Rasa',
            actorImage: 'https://images.unsplash.com/photo-1556157382-97eda2d62296?auto=format&fit=crop&w=120&q=80',
            action: 'Login Akun Stan',
            category: 'login',
            description: 'Berhasil login ke dashboard dapur stan melalui verifikasi nomor WhatsApp pedagang.',
            module: 'Autentikasi',
            ip: '10.20.44.12',
            device: 'Android Chrome (Stan 01)',
            status: 'Sukses'
        },
        {
            id: 5,
            time: '25 Okt 2026, 07:40:05 WIB',
            actorName: 'Mas Hendra',
            stallName: 'Stan 03 - Geprek Mas Bro',
            actorImage: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=120&q=80',
            action: 'Login Akun Stan',
            category: 'login',
            description: 'Berhasil login ke portal penjual NesaCourt untuk persiapan operasional pagi.',
            module: 'Autentikasi',
            ip: '10.20.44.18',
            device: 'Android Chrome (Stan 03)',
            status: 'Sukses'
        },
        {
            id: 6,
            time: '24 Okt 2026, 16:30:40 WIB',
            actorName: 'Bu Siti Rahayu',
            stallName: 'Stan 02 - Soto Bu Siti',
            actorImage: 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=120&q=80',
            action: 'Update Stok Porsi',
            category: 'stok',
            description: 'Memperbarui ketersediaan sisa porsi Soto Ayam Lamongan menjadi 0 (Stok Habis hari ini).',
            module: 'Stok Menu',
            ip: '10.20.44.15',
            device: 'Android Chrome (Stan 02)',
            status: 'Sukses'
        },
        {
            id: 7,
            time: '24 Okt 2026, 14:10:02 WIB',
            actorName: 'Mas Hendra',
            stallName: 'Stan 03 - Geprek Mas Bro',
            actorImage: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=120&q=80',
            action: 'Perubahan Jam Buka Stan',
            category: 'perubahan',
            description: 'Mengubah jadwal tutup sementara stan untuk persiapan pengadaan stok bahan baku ayam segar.',
            module: 'Operasional Stan',
            ip: '10.20.44.18',
            device: 'Android Chrome (Stan 03)',
            status: 'Pemberitahuan'
        },
        {
            id: 8,
            time: '24 Okt 2026, 10:15:20 WIB',
            actorName: 'Bu Dian Fitri',
            stallName: 'Stan 04 - Aneka Jus Vokasi',
            actorImage: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=120&q=80',
            action: 'Perubahan Ketersediaan Menu',
            category: 'perubahan',
            description: 'Menonaktifkan sementara menu Jus Alpukat karena stok buah matang sedang restock.',
            module: 'Kelola Menu',
            ip: '10.20.44.22',
            device: 'iPad Safari (Stan 04)',
            status: 'Pemberitahuan'
        }
    ],

    get filteredLogs() {
        return this.logs.filter(log => {
            const matchCategory = this.activityFilter === 'semua' || log.category === this.activityFilter;
            const matchSearch = this.searchQuery === '' || 
                                log.action.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                                log.actorName.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                                log.stallName.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                                log.description.toLowerCase().includes(this.searchQuery.toLowerCase());
            return matchCategory && matchSearch;
        });
    }
}">

    <!-- Page Title Header (Semi-Bold with Black Icon) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200/80">
        <div>
            <div class="flex items-center gap-2.5">
                <i data-lucide="shield-alert" class="w-6 h-6 text-slate-900 shrink-0"></i>
                <h1 class="text-xl sm:text-2xl font-semibold text-slate-900 tracking-tight">Log Aktivitas Stan Penjual</h1>
            </div>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Audit riwayat login akun stan, pembaharuan ketersediaan stok porsi, dan perubahan jadwal operasional stan kantin Ketintang.</p>
        </div>

        <div class="flex items-center gap-2">
            <span class="text-xs font-semibold text-slate-700 bg-white border border-slate-200 px-3 py-1.5 rounded-xl shadow-2xs">
                Audit Trail Penjual Aktif
            </span>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <!-- Filter Kategori Aktivitas Penjual -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1.5 sm:pb-0 thin-scrollbar text-xs">
            <button type="button" 
                    @click="activityFilter = 'semua'" 
                    :class="activityFilter === 'semua' ? 'bg-slate-900 text-white font-bold' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200'"
                    class="whitespace-nowrap px-3.5 py-1.5 rounded-xl transition">
                Semua Aktivitas
            </button>
            <button type="button" 
                    @click="activityFilter = 'login'" 
                    :class="activityFilter === 'login' ? 'bg-slate-900 text-white font-bold' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200'"
                    class="whitespace-nowrap px-3.5 py-1.5 rounded-xl transition">
                Login Akun Stan
            </button>
            <button type="button" 
                    @click="activityFilter = 'stok'" 
                    :class="activityFilter === 'stok' ? 'bg-slate-900 text-white font-bold' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200'"
                    class="whitespace-nowrap px-3.5 py-1.5 rounded-xl transition">
                Update Stok Porsi
            </button>
            <button type="button" 
                    @click="activityFilter = 'perubahan'" 
                    :class="activityFilter === 'perubahan' ? 'bg-slate-900 text-white font-bold' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200'"
                    class="whitespace-nowrap px-3.5 py-1.5 rounded-xl transition">
                Perubahan Jam & Menu
            </button>
        </div>

        <div class="relative w-full sm:w-64">
            <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
            <input type="text" 
                   x-model="searchQuery" 
                   placeholder="Cari aktivitas, pemilik, atau stan..." 
                   class="w-full pl-9 pr-3.5 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500">
        </div>
    </div>

    <!-- Activity Log List -->
    <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-xs divide-y divide-slate-100">
        <template x-for="log in filteredLogs" :key="log.id">
            <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-start justify-between gap-4 hover:bg-slate-50/50 transition">
                <div class="flex items-start gap-3.5 min-w-0">
                    <!-- Actor Photo -->
                    <img :src="log.actorImage" :alt="log.actorName" class="w-10 h-10 rounded-xl object-cover ring-1 ring-slate-200 shrink-0 mt-0.5">

                    <div class="space-y-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs font-bold text-slate-900" x-text="log.actorName"></span>
                            <span class="text-slate-300 font-normal">|</span>
                            <span class="text-xs font-semibold text-brand-700" x-text="log.stallName"></span>
                            <span class="text-slate-300 font-normal">|</span>
                            <span class="text-xs font-bold text-slate-800" x-text="log.action"></span>
                        </div>

                        <p class="text-xs text-slate-600 leading-relaxed" x-text="log.description"></p>

                        <div class="flex flex-wrap items-center gap-3 text-[11px] text-slate-400 pt-0.5">
                            <span class="font-medium text-slate-500" x-text="'Modul: ' + log.module"></span>
                            <span class="text-slate-300 font-normal">|</span>
                            <span x-text="'IP: ' + log.ip"></span>
                            <span class="text-slate-300 font-normal">|</span>
                            <span x-text="log.device"></span>
                        </div>
                    </div>
                </div>

                <!-- Status Tanpa Container Pembungkus -->
                <div class="flex items-center sm:flex-col sm:items-end justify-between sm:justify-start gap-1 shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100 text-xs">
                    <span class="text-[11px] text-slate-400 font-medium" x-text="log.time"></span>
                    <span class="text-xs font-semibold shrink-0"
                          :class="log.status === 'Sukses' ? 'text-emerald-600' : 'text-amber-600'"
                          x-text="log.status">
                    </span>
                </div>
            </div>
        </template>
    </div>

</div>
@endsection
