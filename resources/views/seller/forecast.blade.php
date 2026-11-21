@extends('layouts.seller')

@section('title', 'AI Demand Forecasting — NesaCourt')

@section('content')
<div class="space-y-6" x-data="{
    appliedSuccess: false,
    appliedMessage: 'Rekomendasi porsi berhasil diterapkan ke stok stan!',
    forecastDate: @js($summary['targetDateHuman']),
    targetDateIso: @js($summary['targetDateIso']),
    weather: @js($filters['weather']),
    hasEvent: {{ $filters['event'] ? 'true' : 'false' }},
    forecasts: @js($forecasts),

    hourlyModalOpen: false,
    hourlyLoading: false,
    hourlyData: null,

    async applyAllForecasts() {
        try {
            const payload = this.forecasts.map(f => ({ id: f.id, stock: f.recommendedPortions }));
            await fetch('{{ route('seller.stock.update') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ stocks: payload })
            });
            this.appliedMessage = 'Seluruh rekomendasi porsi AI (' + payload.length + ' menu) berhasil diterapkan ke stok stan!';
            this.appliedSuccess = true;
            setTimeout(() => { this.appliedSuccess = false; }, 3500);
        } catch(e) {
            console.error(e);
        }
    },

    async applySingleForecast(item) {
        try {
            await fetch('{{ route('seller.stock.update') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ stocks: [{ id: item.id, stock: item.recommendedPortions }] })
            });
            this.appliedMessage = 'Stok menu ' + item.name + ' diperbarui menjadi ' + item.recommendedPortions + ' porsi sesuai saran AI!';
            this.appliedSuccess = true;
            setTimeout(() => { this.appliedSuccess = false; }, 3500);
        } catch(e) {
            console.error(e);
        }
    },

    async openHourlyModal(item) {
        this.hourlyModalOpen = true;
        this.hourlyLoading = true;
        this.hourlyData = { menu_name: item.name, hourly_predictions: [] };
        try {
            const url = '{{ url('/penjual/prediksi/hourly') }}/' + item.id
                + '?date=' + encodeURIComponent(this.targetDateIso)
                + '&weather=' + encodeURIComponent(this.weather)
                + '&event=' + (this.hasEvent ? '1' : '0');
            const resp = await fetch(url, { headers: { 'Accept': 'application/json' } });
            this.hourlyData = await resp.json();
        } catch(e) {
            this.hourlyData = { status: 'error', message: 'Gagal memuat pola prediksi per jam.', hourly_predictions: [] };
        } finally {
            this.hourlyLoading = false;
        }
    },

    maxHourlyQty() {
        if (!this.hourlyData || !this.hourlyData.hourly_predictions || this.hourlyData.hourly_predictions.length === 0) {
            return 1;
        }
        return Math.max(1, ...this.hourlyData.hourly_predictions.map(h => h.predicted_quantity || 0));
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
                <div class="flex flex-wrap items-center gap-2.5">
                    <h1 class="text-xl sm:text-2xl font-semibold text-slate-900 tracking-tight">AI Demand Forecasting</h1>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-semibold border {{ $aiMeta['online'] ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $aiMeta['online'] ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                        {{ $aiMeta['online'] ? 'Python AI Aktif (' . $aiMeta['modelName'] . ')' : 'Fallback Lokal MySQL' }}
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-slate-500 mt-2 max-w-2xl">
                    Perkiraan porsi terjual untuk <span class="font-semibold text-slate-700">{{ $seller->stall_name }}</span> pada
                    <span class="font-semibold text-slate-700" x-text="forecastDate"></span> sebagai bantuan perencanaan stok dapur.
                </p>
            </div>

            <div class="flex items-center gap-2.5 shrink-0 pt-1 sm:pt-0">
                <button type="button"
                        @click="applyAllForecasts()"
                        class="flex-1 sm:flex-none h-10 py-2.5 px-4 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-700 text-white shadow-sm shadow-brand-500/20 transition cursor-pointer">
                    <i data-lucide="check-check" class="w-4 h-4 text-white"></i>
                    <span>Terapkan Semua ke Stok</span>
                </button>
            </div>
        </div>

        <!-- Parameter Simulasi AI (Tanggal, Cuaca, Acara Kampus - Kasus B & E) -->
        <form method="GET" action="{{ route('seller.forecast') }}" class="mt-5 pt-5 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Tanggal Target Prediksi</label>
                <input type="date" name="date" value="{{ $filters['date'] }}"
                       class="w-full h-9 px-3 rounded-xl border border-slate-200 text-xs text-slate-800 bg-slate-50 focus:bg-white focus:border-brand-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Perkiraan Cuaca</label>
                <select name="weather"
                        class="w-full h-9 px-3 rounded-xl border border-slate-200 text-xs text-slate-800 bg-slate-50 focus:bg-white focus:border-brand-500 focus:outline-none">
                    <option value="cerah" {{ $filters['weather'] === 'cerah' ? 'selected' : '' }}>Cerah (Normal)</option>
                    <option value="hujan" {{ $filters['weather'] === 'hujan' ? 'selected' : '' }}>Hujan (Kunjungan Turun)</option>
                </select>
            </div>
            <div class="flex items-center h-9 px-3 rounded-xl border border-slate-200 bg-slate-50">
                <label class="inline-flex items-center gap-2 text-xs font-medium text-slate-700 cursor-pointer select-none">
                    <input type="checkbox" name="event" value="1" {{ $filters['event'] ? 'checked' : '' }}
                           class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                    <span>Ada Acara Kampus (Lonjakan)</span>
                </label>
            </div>
            <div>
                <button type="submit"
                        class="w-full h-9 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold inline-flex items-center justify-center gap-1.5 transition cursor-pointer">
                    <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                    <span>Hitung Prediksi AI</span>
                </button>
            </div>
        </form>

        <!-- Micro Summary Counters (Data Riil dari Model Evaluasi & Prediksi AI) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-5 pt-5 border-t border-slate-100">
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Performa Model (Evaluasi Test)</span>
                <span class="text-base sm:text-lg font-semibold text-slate-900 block mt-0.5">{{ $aiMeta['accuracyLabel'] }}</span>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Total Prediksi Stan ({{ $summary['targetDayName'] }})</span>
                <span class="text-base sm:text-lg font-semibold text-slate-900 block mt-0.5">{{ $summary['totalFood'] }} Makanan &middot; {{ $summary['totalDrink'] }} Minuman</span>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Dasar Data &amp; Faktor Aktif</span>
                <span class="text-base sm:text-lg font-semibold text-slate-900 block mt-0.5">{{ $aiMeta['operatingDaysLabel'] }} ({{ $aiMeta['factorsLabel'] }})</span>
            </div>
        </div>
    </div>

    @if (!$aiMeta['online'])
        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-xs flex items-start gap-2.5">
            <i data-lucide="alert-triangle" class="w-4 h-4 shrink-0 text-amber-600 mt-0.5"></i>
            <div>
                <span class="font-bold">Layanan Python AI (http://127.0.0.1:8001) sedang tidak aktif.</span>
                Halaman menampilkan perkiraan fallback rata-rata 7 hari terakhir dari database MySQL. Nyalakan server FastAPI (`uvicorn app:app --port 8001`) untuk mengaktifkan prediksi penuh Random Forest &amp; pola per jam.
            </div>
        </div>
    @endif

    @if ($summary['isSunday'])
        <div class="p-4 rounded-2xl bg-slate-100 border border-slate-200 text-slate-700 text-xs flex items-center gap-2.5">
            <i data-lucide="calendar-off" class="w-4 h-4 shrink-0 text-slate-600"></i>
            <span>Tanggal yang dipilih adalah <strong>Hari Minggu</strong> (operasional Foodcourt Baseball UNESA libur/tutup), sehingga saran porsi bernilai 0.</span>
        </div>
    @endif

    <!-- Success Toast Alert -->
    <div x-show="appliedSuccess"
         x-cloak
         x-transition
         class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between gap-2.5">
        <div class="flex items-center gap-2">
            <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-700 shrink-0"></i>
            <span x-text="appliedMessage"></span>
        </div>
        <button type="button" @click="appliedSuccess = false" class="text-slate-700 hover:text-slate-900 cursor-pointer">
            <i data-lucide="x" class="w-4 h-4 text-slate-900"></i>
        </button>
    </div>

    <!-- Recommendations Table Section -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <i data-lucide="target" class="w-4 h-4 text-slate-900"></i>
                <h2 class="text-sm font-bold text-slate-900">Rekomendasi Porsi per Menu</h2>
                <span class="text-xs text-slate-400 font-normal">({{ $summary['totalMenus'] }} menu aktif dianalisis)</span>
            </div>
            <span class="text-xs text-slate-500">Target: <strong class="text-slate-700">{{ $summary['targetDateHuman'] }}</strong></span>
        </div>

        <div class="overflow-x-auto thin-scrollbar">
            <table class="w-full min-w-[820px] text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4">Menu &amp; Penjelasan AI</th>
                        <th class="py-3 px-4 text-center">{{ $summary['targetDayName'] }} Lalu</th>
                        <th class="py-3 px-4 text-center">Prediksi &amp; Rentang</th>
                        <th class="py-3 px-4 text-center">Keyakinan</th>
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
                                        <div class="flex items-center gap-1.5">
                                            <span class="font-bold text-slate-900 truncate" x-text="item.name"></span>
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600"
                                                  x-text="item.itemType === 'minuman' ? 'Minuman' : 'Makanan'"></span>
                                            <template x-if="item.isTemporary">
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-100 text-amber-800">Sementara</span>
                                            </template>
                                        </div>
                                        <div class="mt-0.5 flex items-start gap-1.5 text-[11px] text-slate-500">
                                            <i data-lucide="lightbulb" class="w-3.5 h-3.5 text-slate-700 shrink-0 mt-0.5"></i>
                                            <span class="line-clamp-2" x-text="item.keyFactor"></span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Minggu Lalu -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="font-bold text-slate-700 block" x-text="item.lastWeekPortions + ' porsi'"></span>
                                <span class="text-[10px] text-slate-400" x-text="'Stok kini: ' + item.currentStock"></span>
                            </td>

                            <!-- Saran Porsi & Rentang Kehati-hatian -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="font-bold text-slate-900 text-sm block" x-text="item.recommendedPortions + ' porsi'"></span>
                                <span class="text-[10px] font-semibold"
                                      :class="item.trendPositive ? 'text-emerald-600' : 'text-amber-600'"
                                      x-text="'Rentang ' + item.rangeMin + '–' + item.rangeMax + ' (' + item.trendPercent + ')'"></span>
                            </td>

                            <!-- Tingkat Keyakinan -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="inline-flex px-2.5 py-1 rounded-lg text-[11px] font-bold"
                                      :class="item.confidenceLevel === 'tinggi' ? 'bg-emerald-50 text-emerald-700' : (item.confidenceLevel === 'rendah' ? 'bg-amber-50 text-amber-700' : 'bg-slate-100 text-slate-700')"
                                      x-text="item.confidence"></span>
                            </td>

                            <!-- Aksi -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <button type="button"
                                            @click="openHourlyModal(item)"
                                            class="h-8 px-2.5 rounded-xl text-[11px] font-semibold whitespace-nowrap inline-flex items-center justify-center gap-1 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 transition cursor-pointer">
                                        <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                        <span>Pola Jam</span>
                                    </button>
                                    <button type="button"
                                            @click="applySingleForecast(item)"
                                            class="h-8 px-3 rounded-xl text-[11px] font-semibold whitespace-nowrap inline-flex items-center justify-center gap-1 bg-brand-600 hover:bg-brand-700 text-white shadow-2xs transition cursor-pointer">
                                        <i data-lucide="check" class="w-3.5 h-3.5 text-white"></i>
                                        <span>Terapkan</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Dynamic Kitchen Prep Summary Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-2 pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <i data-lucide="shopping-cart" class="w-4 h-4 text-slate-900"></i>
                <h2 class="text-sm font-bold text-slate-900">Ringkasan Persiapan Bahan Baku ({{ $seller->stall_name }})</h2>
            </div>
            <span class="text-xs text-slate-500">Untuk {{ $summary['totalFood'] }} porsi makanan &amp; {{ $summary['totalDrink'] }} cup minuman</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Estimasi Beras</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5">{{ number_format($summary['totalFood'] * 0.12, 1) }} kg</span>
                <span class="text-[10px] text-slate-400 block mt-1">±120g beras per porsi nasi</span>
            </div>

            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Estimasi Lauk Utama</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5">{{ $summary['totalFood'] }} porsi</span>
                <span class="text-[10px] text-slate-400 block mt-1">Siapkan cadangan ±5% saat jam sibuk</span>
            </div>

            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Kemasan &amp; Cup Minuman</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5">{{ $summary['totalDrink'] }} cup/botol</span>
                <span class="text-[10px] text-slate-400 block mt-1">Termasuk air mineral &amp; es batu</span>
            </div>

            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Jam Puncak Operasional</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5">11.00 - 13.30 WIB</span>
                <span class="text-[10px] text-slate-400 block mt-1">±55% permintaan terjadi saat istirahat kuliah</span>
            </div>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-xs text-slate-600 flex items-start gap-2.5">
            <i data-lucide="info" class="w-4 h-4 text-slate-900 shrink-0 mt-0.5"></i>
            <p class="leading-relaxed">
                Perkiraan ini adalah <strong>bantuan perencanaan stok</strong> berdasarkan pola historis transaksi selesai di {{ $seller->stall_name }} (transaksi dibatalkan otomatis tidak dihitung), bukan jaminan penjualan pasti. Penjual tetap disarankan menyesuaikan dengan pengalaman harian di lapangan.
            </p>
        </div>
    </div>

    <!-- Modal Pola Prediksi Per Jam (08:00 - 16:00) -->
    <div x-show="hourlyModalOpen"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs"
         @keydown.escape.window="hourlyModalOpen = false">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl w-full max-w-lg p-5 sm:p-6 space-y-4" @click.away="hourlyModalOpen = false">
            <div class="flex items-start justify-between gap-3 border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-sm sm:text-base font-bold text-slate-900" x-text="'Pola Permintaan Per Jam — ' + (hourlyData?.menu_name || '')"></h3>
                    <p class="text-[11px] text-slate-500 mt-0.5" x-text="'Target: ' + forecastDate"></p>
                </div>
                <button type="button" @click="hourlyModalOpen = false" class="p-1 rounded-lg hover:bg-slate-100 text-slate-500 cursor-pointer">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <div x-show="hourlyLoading" class="py-10 text-center text-xs text-slate-500">
                Memuat prediksi per jam dari layanan AI...
            </div>

            <div x-show="!hourlyLoading && hourlyData && hourlyData.status !== 'ok'" class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800">
                <span x-text="hourlyData?.message || hourlyData?.explanation || 'Data per jam belum tersedia.'"></span>
            </div>

            <div x-show="!hourlyLoading && hourlyData && hourlyData.status === 'ok'" class="space-y-3">
                <div class="flex items-center justify-between text-xs bg-slate-50 rounded-xl px-3.5 py-2.5 border border-slate-200/70">
                    <span class="text-slate-600">Jam Puncak: <strong class="text-brand-700" x-text="hourlyData?.peak_hour"></strong></span>
                    <span class="text-slate-600">Total Prediksi Jam: <strong class="text-slate-900" x-text="(hourlyData?.total_predicted_daily || 0) + ' porsi'"></strong></span>
                </div>

                <div class="space-y-2">
                    <template x-for="slot in (hourlyData?.hourly_predictions || [])" :key="slot.hour">
                        <div class="flex items-center gap-3 text-xs">
                            <span class="w-12 font-semibold text-slate-600 shrink-0" x-text="slot.hour"></span>
                            <div class="flex-1 h-5 rounded-lg bg-slate-100 overflow-hidden">
                                <div class="h-full rounded-lg transition-all"
                                     :class="slot.is_peak_hour ? 'bg-brand-600' : 'bg-slate-400'"
                                     :style="'width: ' + Math.max(4, Math.round((slot.predicted_quantity / maxHourlyQty()) * 100)) + '%'"></div>
                            </div>
                            <span class="w-16 text-right font-bold text-slate-800 shrink-0" x-text="slot.predicted_quantity + ' porsi'"></span>
                        </div>
                    </template>
                </div>

                <p class="text-[11px] text-slate-500 pt-1" x-text="hourlyData?.explanation"></p>
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
