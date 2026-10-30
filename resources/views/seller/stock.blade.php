@extends('layouts.seller')

@section('title', 'Kelola Stok Porsi — NesaCourt')

@section('content')
<div class="space-y-6" x-data="{
    saveSuccess: false,
    items: {{ json_encode($items) }},
    increment(item, amount = 1) {
        item.stock = Math.max(0, item.stock + amount);
    },
    decrement(item, amount = 1) {
        item.stock = Math.max(0, item.stock - amount);
    },
    toggleAvailability(item) {
        if (item.stock > 0) {
            item.stock = 0;
        } else {
            item.stock = 10;
        }
    },
    async saveAllStock() {
        try {
            const payload = this.items.map(i => ({ id: i.id, stock: i.stock }));
            const res = await fetch('{{ route('seller.stock.update') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ stocks: payload })
            });
            if (res.ok) {
                this.saveSuccess = true;
                setTimeout(() => { this.saveSuccess = false; }, 3000);
            }
        } catch(e) {
            console.error(e);
        }
    },
    resetStandard() {
        this.items.forEach(i => {
            if (i.category === 'minuman') i.stock = 40;
            else if (i.category === 'makanan') i.stock = 25;
            else i.stock = 20;
        });
        this.saveAllStock();
    }
}">

    <!-- Page Header & Action Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-7 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                    <a href="{{ url('/penjual/dashboard') }}" class="hover:text-brand-600">Portal Stan</a>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-400"></i>
                    <span class="text-slate-800 font-semibold">Kelola Stok Porsi</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-semibold text-slate-900 tracking-tight">Kelola Stok Porsi Harian</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-2 max-w-xl">Perbarui jumlah porsi siap jual di dapur hari ini. Menu dengan stok 0 otomatis berstatus porsi habis di katalog pembeli.</p>
            </div>

            <!-- Buttons: On mobile displayed below description, on desktop aligned to the right -->
            <div class="flex items-center gap-2.5 shrink-0 pt-1 sm:pt-0">
                <button type="button" 
                        @click="resetStandard()" 
                        class="flex-1 sm:flex-none h-10 py-2.5 px-4 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                    <i data-lucide="rotate-ccw" class="w-4 h-4 text-slate-500"></i>
                    <span>Set Standar Pagi</span>
                </button>
                <button type="button" 
                        @click="saveAllStock()" 
                        class="flex-1 sm:flex-none h-10 py-2.5 px-4 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-700 text-white shadow-sm shadow-brand-500/20 transition">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Simpan Perubahan Stok</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Success Toast Alert -->
    <div x-show="saveSuccess" 
         x-cloak 
         x-transition 
         class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between gap-2.5">
        <div class="flex items-center gap-2">
            <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 shrink-0"></i>
            <span>Seluruh data stok porsi harian berhasil diperbarui dan disinkronkan ke katalog pembeli!</span>
        </div>
        <button type="button" @click="saveSuccess = false" class="text-emerald-700 hover:text-emerald-900">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    </div>

    <!-- Stock Management Table Section -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="layers" class="w-4 h-4 text-slate-900"></i>
                <h2 class="text-sm font-bold text-slate-900">Daftar Stok Menu Stan</h2>
                <span class="text-xs text-slate-400 font-normal" x-text="'(' + items.length + ' menu)'"></span>
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
                        <th class="py-3 px-4 text-center">Status Porsi</th>
                        <th class="py-3 px-4 text-center">Kelola Stok Porsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    <template x-for="item in items" :key="item.id">
                        <tr class="hover:bg-slate-50/70 transition">
                            <!-- Menu Name & Image -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <img :src="item.image" :alt="item.name" class="w-12 h-12 rounded-xl object-cover shrink-0 ring-1 ring-slate-200">
                                    <div class="min-w-0 max-w-xs sm:max-w-sm">
                                        <span class="font-bold text-slate-900 block truncate" x-text="item.name"></span>
                                        <p class="text-[11px] text-slate-500 line-clamp-1 mt-0.5" x-text="item.desc"></p>
                                    </div>
                                </div>
                            </td>

                            <!-- Category -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-slate-100 text-slate-700" x-text="item.categoryLabel"></span>
                            </td>

                            <!-- Price -->
                            <td class="py-3.5 px-4 whitespace-nowrap font-extrabold text-slate-900" x-text="'Rp ' + item.price.toLocaleString('id-ID')"></td>

                            <!-- Status Porsi Toggle (Warna Hijau Kontras Font Putih & Titik Kedap-Kedip) -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <button type="button" 
                                        @click="toggleAvailability(item)"
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-[11px] font-semibold border transition shadow-2xs"
                                        :class="item.stock > 0 ? 'bg-emerald-600 text-white border-emerald-600 hover:bg-emerald-700' : 'bg-slate-100 text-slate-600 border-slate-200 hover:bg-slate-200'">
                                    <span class="relative flex h-1.5 w-1.5 shrink-0">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" :class="item.stock > 0 ? 'bg-white' : 'bg-slate-400'"></span>
                                        <span class="relative inline-flex rounded-full h-1.5 w-1.5" :class="item.stock > 0 ? 'bg-white' : 'bg-slate-400'"></span>
                                    </span>
                                    <span x-text="item.stock > 0 ? 'Tersedia (' + item.stock + ')' : 'Habis (0)'"></span>
                                </button>
                            </td>

                            <!-- Counter Controls -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <div class="inline-flex items-center justify-center rounded-xl bg-slate-50 border border-slate-200 p-1">
                                    <button type="button" 
                                            @click="decrement(item)" 
                                            :disabled="item.stock <= 0"
                                            class="w-7 h-7 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 flex items-center justify-center disabled:opacity-40 transition shrink-0">
                                        <i data-lucide="minus" class="w-3.5 h-3.5 text-slate-900"></i>
                                    </button>

                                    <input type="number" 
                                           x-model.number="item.stock" 
                                           min="0" 
                                           max="500" 
                                           style="-moz-appearance: textfield; text-align: center;"
                                           class="w-12 text-center font-bold text-xs sm:text-sm text-slate-900 bg-transparent focus:outline-none p-0 m-0 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">

                                    <button type="button" 
                                            @click="increment(item)" 
                                            class="w-7 h-7 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 flex items-center justify-center transition shrink-0">
                                        <i data-lucide="plus" class="w-3.5 h-3.5 text-slate-900"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });
</script>
@endsection
