@extends('layouts.seller')

@section('title', 'AI Demand Forecasting — NesaCourt')

@section('content')
<div class="space-y-6" x-data="{
    appliedSuccess: false,
    forecastDate: 'Kamis, 22 Oktober 2026',
    forecasts: [
        {
            id: 1,
            name: 'Paket Nasi Geprek Sambal Bawang',
            category: 'Makanan Utama',
            image: 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?auto=format&fit=crop&w=300&q=80',
            lastWeekPortions: 24,
            recommendedPortions: 30,
            trendPercent: '+25%',
            trendPositive: true,
            confidence: '96%',
            keyFactor: 'Ada perkuliahan luring gabungan 4 kelas di Gedung K4 Vokasi jam 11.00 - 13.00 WIB.'
        },
        {
            id: 2,
            name: 'Nasi Ayam Penyet Sambal Terasi',
            category: 'Makanan Utama',
            image: 'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?auto=format&fit=crop&w=300&q=80',
            lastWeekPortions: 10,
            recommendedPortions: 12,
            trendPercent: '+20%',
            trendPositive: true,
            confidence: '92%',
            keyFactor: 'Permintaan stabil saat makan siang mahasiswa tingkat akhir.'
        },
        {
            id: 3,
            name: 'Nasi Telur Krispi Pontianak',
            category: 'Makanan Utama',
            image: 'https://images.unsplash.com/photo-1525351484163-7529414344d8?auto=format&fit=crop&w=300&q=80',
            lastWeekPortions: 18,
            recommendedPortions: 20,
            trendPercent: '+11%',
            trendPositive: true,
            confidence: '94%',
            keyFactor: 'Pilihan hemat favorit mahasiswa saat jeda praktikum pagi.'
        },
        {
            id: 4,
            name: 'Es Teh Manis Jumbo',
            category: 'Minuman Segar',
            image: 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?auto=format&fit=crop&w=300&q=80',
            lastWeekPortions: 38,
            recommendedPortions: 48,
            trendPercent: '+26%',
            trendPositive: true,
            confidence: '97%',
            keyFactor: 'Perkiraan cuaca Surabaya cerah terik mencapai 33°C pada siang hari.'
        },
        {
            id: 5,
            name: 'Tempe Mendoan Hangat (Isi 3)',
            category: 'Camilan',
            image: 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?auto=format&fit=crop&w=300&q=80',
            lastWeekPortions: 14,
            recommendedPortions: 16,
            trendPercent: '+14%',
            trendPositive: true,
            confidence: '89%',
            keyFactor: 'Paling sering dipesan mendampingi menu geprek saat jam istirahat.'
        }
    ],
    applyAllForecasts() {
        this.appliedSuccess = true;
        setTimeout(() => {
            this.appliedSuccess = false;
        }, 3500);
    }
}">

    <!-- Page Header & Action Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-7 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                    <a href="{{ url('/penjual/dashboard') }}" class="hover:text-brand-600">Portal Stan</a>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-900"></i>
                    <span class="text-slate-800 font-semibold">AI Demand Forecasting</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-semibold text-slate-900 tracking-tight">AI Demand Forecasting</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-2 max-w-xl">Estimasi porsi masakan dan rekomendasi belanja bahan baku esok hari (<span class="font-semibold text-slate-700" x-text="forecastDate"></span>) untuk meminimalkan sisa porsi dan lonjakan kehabisan stok.</p>
            </div>

            <!-- Buttons: On mobile displayed below description, on desktop aligned to the right -->
            <div class="flex items-center gap-2.5 shrink-0 pt-1 sm:pt-0">
                <button type="button" 
                        @click="applyAllForecasts()" 
                        class="flex-1 sm:flex-none h-10 py-2.5 px-4 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-700 text-white shadow-sm shadow-brand-500/20 transition">
                    <i data-lucide="check-check" class="w-4 h-4 text-white"></i>
                    <span>Terapkan Semua ke Stok Besok</span>
                </button>
            </div>
        </div>

        <!-- Micro Summary Counters (Hitam Semua, Semi Bold & Rata Tengah) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-6 pt-6 border-t border-slate-100">
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Tingkat Akurasi Model</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5">94.2% Akurat</span>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Dasar Analisis Tren</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5">45 Hari Transaksi</span>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Faktor Eksternal Terdeteksi</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5">Jadwal Kuliah + Cuaca</span>
            </div>
        </div>
    </div>

    <!-- Success Toast Alert -->
    <div x-show="appliedSuccess" 
         x-cloak 
         x-transition 
         class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between gap-2.5">
        <div class="flex items-center gap-2">
            <i data-lucide="check-circle-2" class="w-5 h-5 text-slate-900 shrink-0"></i>
            <span>Rekomendasi porsi esok hari berhasil dijadwalkan dan akan diterapkan saat jam buka stan!</span>
        </div>
        <button type="button" @click="appliedSuccess = false" class="text-slate-700 hover:text-slate-900">
            <i data-lucide="x" class="w-4 h-4 text-slate-900"></i>
        </button>
    </div>

    <!-- Recommendations Table Section (Tabel Identik Halaman Laporan & Menu, Scrollable Desktop & Mobile) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="target" class="w-4 h-4 text-slate-900"></i>
                <h2 class="text-sm font-bold text-slate-900">Rekomendasi Porsi Masakan per Menu</h2>
                <span class="text-xs text-slate-400 font-normal">(5 menu dianalisis)</span>
            </div>
            <span class="text-xs text-slate-400">Target Hari: Kamis (Besok)</span>
        </div>

        <!-- Table View (Desktop & Mobile Horizontal Scroll) -->
        <div class="overflow-x-auto thin-scrollbar">
            <table class="w-full min-w-[750px] text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4">Menu & Faktor Utama AI</th>
                        <th class="py-3 px-4 text-center">Kamis Lalu</th>
                        <th class="py-3 px-4 text-center">Saran Porsi Esok</th>
                        <th class="py-3 px-4 text-center">Tingkat Keyakinan</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    <template x-for="item in forecasts" :key="item.id">
                        <tr class="hover:bg-slate-50/70 transition">
                            <!-- Menu & Key Factor -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <img :src="item.image" :alt="item.name" class="w-12 h-12 rounded-xl object-cover ring-1 ring-slate-200 shrink-0">
                                    <div class="min-w-0 max-w-xs sm:max-w-md">
                                        <span class="font-bold text-slate-900 block truncate" x-text="item.name"></span>
                                        <div class="mt-0.5 flex items-center gap-1.5 text-[11px] text-slate-500 line-clamp-1">
                                            <i data-lucide="lightbulb" class="w-3.5 h-3.5 text-slate-900 shrink-0"></i>
                                            <span class="truncate" x-text="item.keyFactor"></span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Kamis Lalu -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="font-bold text-slate-700" x-text="item.lastWeekPortions + ' porsi'"></span>
                            </td>

                            <!-- Saran Porsi Esok -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="font-bold text-slate-700" x-text="item.recommendedPortions + ' porsi ' + item.trendPercent"></span>
                            </td>

                            <!-- Tingkat Keyakinan -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="font-bold text-slate-700" x-text="item.confidence + ' Akurat'"></span>
                            </td>

                            <!-- Aksi Button -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <button type="button" 
                                        @click="appliedSuccess = true" 
                                        class="h-8 px-3 rounded-xl text-[11px] font-semibold whitespace-nowrap inline-flex items-center justify-center gap-1.5 bg-brand-600 hover:bg-brand-700 text-white shadow-2xs transition">
                                    <i data-lucide="check" class="w-3.5 h-3.5 text-white"></i>
                                    <span>Terapkan</span>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Suggested Raw Ingredients Checklist Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <i data-lucide="shopping-cart" class="w-4 h-4 text-slate-900"></i>
                <h2 class="text-sm font-bold text-slate-900">Daftar Estimasi Belanja Bahan Baku Dapur (Persiapan Esok)</h2>
            </div>
            <span class="text-xs text-slate-400">Untuk 30 porsi geprek & 48 porsi minuman</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Daging Ayam Fillet / Segar</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5">12 - 14 kg</span>
                <span class="text-[10px] text-slate-400 block mt-1">Potong 40-45 bagian siap ungkep</span>
            </div>

            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Beras Pulen Lokal</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5">8 kg</span>
                <span class="text-[10px] text-slate-400 block mt-1">Cukup untuk 50 porsi nasi putih</span>
            </div>

            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Cabai Rawit & Bawang Merah</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5">2.5 kg</span>
                <span class="text-[10px] text-slate-400 block mt-1">Kebutuhan ulek sambal bawang</span>
            </div>

            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Teh Melati & Gula Pasir</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5">2 kg Gula + 4 Pak</span>
                <span class="text-[10px] text-slate-400 block mt-1">Persiapan es teh jumbo dingin</span>
            </div>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-xs text-slate-600 flex items-start gap-2.5">
            <i data-lucide="info" class="w-4 h-4 text-slate-900 shrink-0 mt-0.5"></i>
            <p class="leading-relaxed">
                Estimasi belanja ini disesuaikan berdasarkan resep standar porsi Stan 01 Barokah Rasa guna mempermudah Anda berbelanja ke pasar induk sebelum dapur beroperasi.
            </p>
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });
</script>
@endsection
