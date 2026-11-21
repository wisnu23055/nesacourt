@extends('layouts.buyer')

@section('title', 'Rekomendasi Menu — NesaCourt')

@section('content')
<div class="space-y-6" x-data="{
    budget: 'semua',
    selectedTaste: 'semua',
    selectedTime: 'semua',
    loading: false,
    aiOnline: {{ $aiOnline ? 'true' : 'false' }},
    aiSource: '{{ $aiSource }}',
    aiMessage: @js($aiMessage),
    recommendedResults: @js($menus),

    async fetchRecommendations() {
        this.loading = true;
        try {
            const resp = await fetch('{{ route('buyer.recommendation.ai') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    budget: this.budget,
                    taste: this.selectedTaste,
                    max_wait: this.selectedTime
                })
            });
            if (resp.ok) {
                const data = await resp.json();
                this.recommendedResults = data.menus || [];
                this.aiOnline = Boolean(data.ai_online);
                this.aiSource = data.ai_source || 'local_fallback';
                this.aiMessage = data.message || '';
            }
        } catch (e) {
            console.error('AI recommendation error:', e);
        } finally {
            this.loading = false;
        }
    },

    setBudget(val) {
        this.budget = val;
        this.fetchRecommendations();
    },

    setTaste(val) {
        this.selectedTaste = val;
        this.fetchRecommendations();
    },

    setTime(val) {
        this.selectedTime = val;
        this.fetchRecommendations();
    },

    resetFilters() {
        this.budget = 'semua';
        this.selectedTaste = 'semua';
        this.selectedTime = 'semua';
        this.fetchRecommendations();
    }
}">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-slate-200/80">
        <div>
            <h1 class="text-xl sm:text-2xl font-semibold text-slate-900 tracking-tight">Rekomendasi Menu Kantin</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Pilihan menu cerdas berdasarkan uang saku, selera rasa, antrean stan, dan preferensi mahasiswa Food Court Baseball UNESA 1 Ketintang.</p>
        </div>
        <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-xs text-slate-600 shrink-0 self-start md:self-auto shadow-2xs">
            <span class="relative flex h-2 w-2 shrink-0">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75"
                      :class="aiOnline ? 'bg-emerald-400' : 'bg-amber-400'"></span>
                <span class="relative inline-flex rounded-full h-2 w-2"
                      :class="aiOnline ? 'bg-emerald-500' : 'bg-amber-500'"></span>
            </span>
            <span x-text="aiOnline ? 'Smart AI FastAPI Aktif' : 'Mode Fallback Lokal'"></span>
        </div>
    </div>

    <!-- Notice bila layanan AI Python sedang offline -->
    <div x-show="!aiOnline" x-cloak class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-xs flex items-center gap-2.5">
        <i data-lucide="alert-triangle" class="w-4 h-4 shrink-0 text-amber-600"></i>
        <span>Layanan AI Python (port 8001) sedang tidak aktif. Sistem otomatis menampilkan pencocokan fallback lokal agar pencarian tetap berjalan.</span>
    </div>

    <!-- Filter Rekomendasi Menu Kantin -->
    <div class="p-5 sm:p-6 rounded-2xl bg-white border border-slate-200/80 shadow-xs space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <i data-lucide="sliders-horizontal" class="w-4 h-4 text-slate-900"></i>
                    <h2 class="text-sm sm:text-base font-bold text-slate-900">Filter & Kriteria Rekomendasi AI</h2>
                </div>
                <p class="text-xs text-slate-500">Sesuaikan batas anggaran uang saku, selera rasa, dan maksimal waktu tunggu persiapan di kantin</p>
            </div>
            <button type="button"
                    x-show="budget !== 'semua' || selectedTaste !== 'semua' || selectedTime !== 'semua'"
                    @click="resetFilters()"
                    class="whitespace-nowrap self-start sm:self-auto inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition cursor-pointer">
                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                <span>Reset Kriteria</span>
            </button>
        </div>

        <!-- 1. Batas Anggaran Mahasiswa -->
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
                        @click="setBudget('semua')"
                        class="p-3 rounded-xl border text-center transition select-none flex flex-col items-center justify-center gap-1 cursor-pointer"
                        :class="budget === 'semua'
                            ? 'border-brand-600 bg-brand-50/70 ring-1 ring-brand-500/20 shadow-2xs'
                            : 'border-slate-200 bg-slate-50 hover:bg-slate-100/80'">
                    <span class="text-[10px] font-semibold text-slate-500">Semua Harga</span>
                    <span class="text-xs font-bold text-slate-900 block">Semua Budget</span>
                    <span class="text-[10px] text-slate-500 block">Semua Menu Kantin</span>
                </button>

                <button type="button"
                        @click="setBudget('5000')"
                        class="p-3 rounded-xl border text-center transition select-none flex flex-col items-center justify-center gap-1 cursor-pointer"
                        :class="budget === '5000'
                            ? 'border-brand-600 bg-brand-50/70 ring-1 ring-brand-500/20 shadow-2xs'
                            : 'border-slate-200 bg-slate-50 hover:bg-slate-100/80'">
                    <span class="text-[10px] font-semibold text-slate-500">Maks. 5 Ribu</span>
                    <span class="text-xs font-bold text-slate-900 block">&le; Rp 5.000</span>
                    <span class="text-[10px] text-slate-500 block">Minuman Segar</span>
                </button>

                <button type="button"
                        @click="setBudget('10000')"
                        class="p-3 rounded-xl border text-center transition select-none flex flex-col items-center justify-center gap-1 cursor-pointer"
                        :class="budget === '10000'
                            ? 'border-brand-600 bg-brand-50/70 ring-1 ring-brand-500/20 shadow-2xs'
                            : 'border-slate-200 bg-slate-50 hover:bg-slate-100/80'">
                    <span class="text-[10px] font-semibold text-slate-500">Maks. 10 Ribu</span>
                    <span class="text-xs font-bold text-slate-900 block">&le; Rp 10.000</span>
                    <span class="text-[10px] text-slate-500 block">Menu Hemat</span>
                </button>

                <button type="button"
                        @click="setBudget('13000')"
                        class="p-3 rounded-xl border text-center transition select-none flex flex-col items-center justify-center gap-1 cursor-pointer"
                        :class="budget === '13000'
                            ? 'border-brand-600 bg-brand-50/70 ring-1 ring-brand-500/20 shadow-2xs'
                            : 'border-slate-200 bg-slate-50 hover:bg-slate-100/80'">
                    <span class="text-[10px] font-semibold text-slate-500">Maks. 13 Ribu</span>
                    <span class="text-xs font-bold text-slate-900 block">&le; Rp 13.000</span>
                    <span class="text-[10px] text-slate-500 block">Geprek &amp; Campur</span>
                </button>

                <button type="button"
                        @click="setBudget('15000')"
                        class="col-span-2 sm:col-span-1 p-3 rounded-xl border text-center transition select-none flex flex-col items-center justify-center gap-1 cursor-pointer"
                        :class="budget === '15000'
                            ? 'border-brand-600 bg-brand-50/70 ring-1 ring-brand-500/20 shadow-2xs'
                            : 'border-slate-200 bg-slate-50 hover:bg-slate-100/80'">
                    <span class="text-[10px] font-semibold text-slate-500">Maks. 15 Ribu</span>
                    <span class="text-xs font-bold text-slate-900 block">&le; Rp 15.000</span>
                    <span class="text-[10px] text-slate-500 block">Semua Lauk Utama</span>
                </button>
            </div>
        </div>

        <!-- 2 & 3. Preferensi Karakter Rasa dan Maksimal Waktu Tunggu -->
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
                    @foreach (['semua' => 'Bebas', 'pedas' => 'Pedas', 'gurih' => 'Gurih', 'manis' => 'Manis', 'berkuah' => 'Berkuah', 'segar' => 'Segar'] as $val => $label)
                        <button type="button"
                                @click="setTaste('{{ $val }}')"
                                :class="selectedTaste === '{{ $val }}'
                                    ? 'border-brand-600 bg-brand-50/80 text-brand-700 font-bold ring-1 ring-brand-500/20 shadow-2xs'
                                    : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-100'"
                                class="whitespace-nowrap py-1.5 px-2 rounded-xl border text-xs font-semibold text-center transition cursor-pointer">
                            {{ $label }}
                        </button>
                    @endforeach
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
                        <span class="text-[10px] text-slate-500 block">Sudah memperhitungkan estimasi antrean aktif stan</span>
                    </div>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-1.5 pt-0.5">
                    <button type="button"
                            @click="setTime('semua')"
                            :class="selectedTime === 'semua'
                                ? 'border-brand-600 bg-brand-50/80 text-brand-700 font-bold ring-1 ring-brand-500/20 shadow-2xs'
                                : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-100'"
                            class="whitespace-nowrap py-1.5 px-2 rounded-xl border text-xs font-semibold text-center transition cursor-pointer">
                        Semua
                    </button>
                    <button type="button"
                            @click="setTime('5')"
                            :class="selectedTime === '5'
                                ? 'border-brand-600 bg-brand-50/80 text-brand-700 font-bold ring-1 ring-brand-500/20 shadow-2xs'
                                : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-100'"
                            class="whitespace-nowrap py-1.5 px-2 rounded-xl border text-xs font-semibold text-center transition cursor-pointer">
                        &le; 5 Mnt
                    </button>
                    <button type="button"
                            @click="setTime('10')"
                            :class="selectedTime === '10'
                                ? 'border-brand-600 bg-brand-50/80 text-brand-700 font-bold ring-1 ring-brand-500/20 shadow-2xs'
                                : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-100'"
                            class="whitespace-nowrap py-1.5 px-2 rounded-xl border text-xs font-semibold text-center transition cursor-pointer">
                        &le; 10 Mnt
                    </button>
                    <button type="button"
                            @click="setTime('20')"
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

    <!-- Recommendation Results Grid -->
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h2 class="text-base sm:text-lg font-bold text-slate-900">Hasil Rekomendasi Menu Terpilih</h2>
                <p class="text-xs text-slate-500" x-text="aiMessage || 'Diurutkan oleh AI berdasarkan skor kecocokan rasa, budget, antrean, dan popularitas UNESA'"></p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <span x-show="loading" x-cloak class="text-xs font-semibold text-brand-600 animate-pulse">Menghitung skor AI...</span>
                <span class="text-xs font-semibold text-slate-500" x-text="recommendedResults.length + ' Menu Tersedia'"></span>
            </div>
        </div>

        <!-- Grid Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5" :class="loading ? 'opacity-60 pointer-events-none transition' : ''">
            <template x-for="item in recommendedResults" :key="item.id">
                <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <div class="relative h-44 w-full bg-slate-100 overflow-hidden">
                            <img :src="item.image"
                                 :alt="item.name"
                                 class="w-full h-full object-cover transition duration-300">
                            <template x-if="item.matchScore !== null && item.matchScore !== undefined">
                                <span class="absolute top-3 right-3 px-2.5 py-1 rounded-lg bg-slate-900/85 text-white text-[11px] font-bold backdrop-blur-xs shadow-xs"
                                      x-text="'Cocok ' + Math.round(item.matchScore) + '%'"></span>
                            </template>
                        </div>

                        <div class="p-4 space-y-1.5">
                            <h3 class="text-sm font-bold text-slate-900" x-text="item.name"></h3>
                            <p class="text-[11px] font-semibold text-slate-500 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7" />
                                    <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8" />
                                    <path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4" />
                                    <path d="M2 7h20" />
                                </svg>
                                <span x-text="item.seller"></span>
                            </p>
                            <p class="text-xs text-slate-500 line-clamp-2 pt-0.5" x-text="item.aiReason"></p>
                            <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                                <div>
                                    <span class="text-sm font-extrabold text-brand-700 block" x-text="formatRupiah(item.price)"></span>
                                    <span class="text-[10px] text-slate-400"
                                          x-text="item.activeQueue > 0 ? ('Est. ~' + item.prepTime + ' Mnt (' + item.activeQueue + ' antrean)') : ('Masak ~' + item.prepTime + ' Menit')"></span>
                                </div>
                                <span class="text-[11px] font-semibold"
                                      :class="item.stock > 5 ? 'text-slate-500' : (item.stock > 0 ? 'text-amber-600' : 'text-rose-600')"
                                      x-text="item.stock > 0 ? 'Sisa ' + item.stock + ' porsi' : 'Porsi Habis'"></span>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 pt-0">
                        <button type="button"
                                :disabled="item.stock === 0"
                                @click="addToCart({ id: item.id, name: item.name, price: item.price, seller: item.seller, image: item.image })"
                                :class="item.stock === 0 ? 'bg-slate-100 text-slate-400 cursor-not-allowed border-slate-200' : 'bg-brand-600 text-white hover:bg-brand-700 shadow-sm'"
                                class="whitespace-nowrap w-full inline-flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-xl text-xs font-semibold transition cursor-pointer">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                            <span x-text="item.stock === 0 ? 'Porsi Habis' : 'Tambah ke Keranjang'"></span>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty State When No Menu Matches Criteria (Kasus E) -->
        <div x-show="!loading && recommendedResults.length === 0" x-cloak class="text-center py-16 bg-white rounded-2xl border border-slate-200/80 p-8 space-y-3">
            <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                <i data-lucide="search-x" class="w-6 h-6"></i>
            </div>
            <h4 class="text-sm font-bold text-slate-900">Belum Ada Menu yang Sesuai Kriteria</h4>
            <p class="text-xs text-slate-500 max-w-md mx-auto leading-relaxed"
               x-text="aiMessage || 'Kombinasi batas anggaran, preferensi rasa, atau waktu tunggu tidak menemukan menu yang cocok. Coba sesuaikan pilihan filter di atas.'"></p>
            <button type="button"
                    @click="resetFilters()"
                    class="whitespace-nowrap inline-flex items-center justify-center px-4 py-2 rounded-xl text-xs font-semibold text-brand-700 bg-brand-50 hover:bg-brand-100 border border-brand-200 transition cursor-pointer">
                Reset Semua Filter
            </button>
        </div>
    </div>

</div>
@endsection
