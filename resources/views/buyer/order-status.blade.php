@extends('layouts.buyer')

@section('title', 'Status Pesanan — NesaCourt')

@section('content')
<div class="space-y-6" x-data="{
    currentStep: {{ $currentStep }},
    orderId: '{{ $order?->order_number ?? 'NC-20261021-8842' }}',
    queueNo: '{{ $order ? 'A-' . str_pad($order->id % 100, 2, '0', STR_PAD_LEFT) : 'A-09' }}',
    stallName: '{{ addslashes($order?->seller?->stall_name ?? 'Stan 01 - Barokah Rasa') }}',
    stallLocation: '{{ addslashes($order?->seller?->location ?? 'Counter Barat No. 01 | Food Court Ketintang') }}',
    etaMinutes: {{ $order?->items?->first()?->menu?->estimated_time_minutes ?? 7 }},
    stallImage: '{{ $order?->seller?->logo_path ?: 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=120&q=80' }}',

    steps: [
        { id: 1, title: 'Pesanan Dibuat', desc: 'Data pesanan berhasil dicatat sistem' },
        { id: 2, title: 'Menunggu Bayar', desc: 'Menunggu proses transfer QRIS' },
        { id: 3, title: 'Dikonfirmasi', desc: 'Penjual memverifikasi transfer' },
        { id: 4, title: 'Sedang Dimasak', desc: 'Dapur stan menyiapkan porsi pesanan' },
        { id: 5, title: 'Siap Diambil', desc: 'Pesanan siap di counter stan' },
        { id: 6, title: 'Selesai', desc: 'Pesanan telah diterima mahasiswa' }
    ]
}">

    <!-- Top Identity Card (Identik dengan Tema Dashboard & Halaman Buyer Lainnya) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-7 shadow-xs space-y-5">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3.5">
                <img :src="stallImage" 
                     :alt="stallName" 
                     class="w-12 h-12 rounded-xl object-cover ring-1 ring-slate-200 shrink-0 shadow-2xs">
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">Status Pesanan Real-time</h1>
                        <span class="text-slate-300 font-normal">|</span>
                        <span class="text-xs font-semibold text-brand-600">Pelacakan Langsung</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">D4 Manajemen Informatika | Vokasi UNESA Ketintang (NIM: {{ auth()->user()->phone ?? '23055310001' }})</p>
                </div>
            </div>

            <!-- Simulation Dropdown in Header for Easy Testing -->
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600 shrink-0 self-start md:self-auto">
                <span class="relative flex h-2 w-2 shrink-0">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <span class="font-medium">Simulasi Status:</span>
                <div class="relative">
                    <select x-model.number="currentStep" 
                            class="appearance-none pl-3 pr-8 py-1 rounded-lg border border-slate-200 bg-white text-slate-800 text-xs font-semibold hover:border-slate-300 focus:outline-none focus:ring-1 focus:ring-brand-500 cursor-pointer shadow-2xs transition">
                        <option value="1">1. Dibuat</option>
                        <option value="2">2. Menunggu Bayar</option>
                        <option value="3">3. Dikonfirmasi</option>
                        <option value="4">4. Sedang Dimasak</option>
                        <option value="5">5. Siap Diambil</option>
                        <option value="6">6. Selesai</option>
                    </select>
                    <div class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Ongoing Summary Card (Center Tengah, Rapi & Estetik) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- 1. Nomor ID Pesanan -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/70 flex flex-col items-center justify-center text-center space-y-1">
                <span class="text-[11px] text-slate-500 block font-medium">Nomor ID Pesanan</span>
                <div class="flex items-center justify-center gap-1.5 flex-wrap">
                    <span class="text-xs sm:text-sm font-bold text-slate-900" x-text="orderId">NC-20261021-8842</span>
                    <span class="text-slate-300 font-normal">|</span>
                    <span class="text-xs font-semibold text-emerald-600">QRIS Lunas</span>
                </div>
            </div>

            <!-- 2. Stan Kantin & Lokasi -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/70 flex flex-col items-center justify-center text-center space-y-1">
                <span class="text-[11px] text-slate-500 block font-medium">Stan Kantin & Lokasi</span>
                <span class="text-xs sm:text-sm font-bold text-slate-900 block truncate max-w-full" x-text="stallName">Stan 01 - Barokah Rasa</span>
                <span class="text-[11px] text-slate-500 block truncate max-w-full" x-text="stallLocation">Counter Barat No. 01 | Food Court Ketintang</span>
            </div>

            <!-- 3. Nomor Antrean Pengambilan -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/70 flex flex-col items-center justify-center text-center space-y-0.5">
                <span class="text-[11px] text-slate-500 block font-medium">Nomor Antrean Pengambilan</span>
                <span class="text-2xl sm:text-3xl font-black text-slate-900" x-text="queueNo">A-09</span>
            </div>

            <!-- 4. Estimasi Siap -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/70 flex flex-col items-center justify-center text-center space-y-1">
                <span class="text-[11px] text-slate-500 block font-medium">Estimasi Siap</span>
                <span class="text-base sm:text-lg font-bold text-slate-900 block" x-text="currentStep === 5 ? 'Siap Ambil!' : '~' + etaMinutes + ' Menit'">~7 Menit</span>
                <span class="text-[10px] text-slate-400 block">Di counter stan</span>
            </div>
        </div>
    </div>

    <!-- Real-time Stepper Progress Container -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-7 shadow-xs space-y-6">
        
        <!-- Status Terkini Banner yang Rapi & Proporsional -->
        <div class="p-4 sm:p-5 rounded-2xl bg-slate-50/70 border border-slate-200/70 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-brand-600 shrink-0 shadow-2xs">
                    <svg class="w-5 h-5 text-brand-600" x-show="currentStep === 4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 13.87A4 4 0 0 1 7.41 6a5.11 5.11 0 0 1 1.05-1.54 5 5 0 0 1 7.08 0A5.11 5.11 0 0 1 16.59 6 4 4 0 0 1 18 13.87V21H6Z"/><line x1="6" y1="17" x2="18" y2="17"/></svg>
                    <svg class="w-5 h-5 text-brand-600" x-show="currentStep === 1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    <svg class="w-5 h-5 text-brand-600" x-show="currentStep === 2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M7 7h.01"/><path d="M17 7h.01"/><path d="M7 17h.01"/><path d="M17 17h.01"/></svg>
                    <svg class="w-5 h-5 text-brand-600" x-show="currentStep === 3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <svg class="w-5 h-5 text-brand-600" x-show="currentStep === 5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                    <svg class="w-5 h-5 text-slate-700" x-show="currentStep === 6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-bold text-slate-900" x-text="steps[currentStep - 1].title">Sedang Dimasak</span>
                        <span class="text-slate-300 font-normal">|</span>
                        <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-brand-700">
                            <span class="relative flex h-2 w-2 shrink-0">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-brand-500"></span>
                            </span>
                            <span x-text="currentStep === 6 ? 'Pesanan Selesai' : (currentStep === 5 ? 'Siap Diambil di Counter' : 'Sedang Diproses')">Sedang Diproses</span>
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5" x-text="steps[currentStep - 1].desc">Dapur stan menyiapkan porsi pesanan</p>
                </div>
            </div>

            <div class="flex items-center gap-2 self-start sm:self-auto shrink-0">
                <span class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-xs font-normal text-slate-600 shadow-2xs">
                    Tahapan <span class="font-normal text-slate-600" x-text="currentStep">4</span> dari 6
                </span>
            </div>
        </div>

        <!-- Stepper Tracker Bar (Garis Center Presisi, Lurus & Dapat Di-scroll di Mobile & Tab) -->
        <div class="space-y-2">
            <div class="overflow-x-auto pb-2 pt-2 thin-scrollbar -mx-1 px-1">
                <div class="min-w-[620px] flex items-start">
                    <template x-for="(step, idx) in steps" :key="step.id">
                        <div class="flex-1 relative flex flex-col items-center text-center group">
                            
                            <!-- Connector Line to Previous Step (Tepat Center di Tengah Lingkaran top-4) -->
                            <div x-show="idx > 0" 
                                 class="absolute top-4 right-1/2 left-[-50%] h-1 -translate-y-1/2 z-0 transition-all duration-500"
                                 :class="step.id <= currentStep ? 'bg-brand-600' : 'bg-slate-100'"></div>

                            <!-- Circle Indicator (w-8 h-8, Center Y = 16px) -->
                            <div class="relative z-10 w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all shadow-xs shrink-0"
                                 :class="step.id < currentStep ? 'bg-brand-600 text-white shadow-2xs' : (step.id === currentStep ? 'bg-brand-600 text-white ring-4 ring-brand-100 shadow-2xs' : 'bg-white border-2 border-slate-200 text-slate-400')">
                                <template x-if="step.id < currentStep">
                                    <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                </template>
                                <template x-if="step.id >= currentStep">
                                    <span x-text="step.id"></span>
                                </template>
                            </div>

                            <!-- Title Text -->
                            <div class="mt-2.5 px-1 max-w-[100px]">
                                <span class="text-[11px] leading-tight block whitespace-normal"
                                      :class="step.id <= currentStep ? 'text-slate-900 font-bold' : 'text-slate-400 font-medium'"
                                      x-text="step.title">
                                </span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Petunjuk Geser Tipis Khusus Mobile & Tablet (Minimalis & Rapi) -->
            <div class="flex items-center justify-center gap-1.5 text-[10px] text-slate-400 font-medium sm:hidden">
                <svg class="w-3 h-3 text-slate-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
                <span>Geser ke kanan / kiri</span>
                <svg class="w-3 h-3 text-slate-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </div>
        </div>

        <!-- Ready Alert Banner (Shown on Step 5) -->
        <div x-show="currentStep === 5" x-transition class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center gap-3 text-emerald-900">
            <div class="w-10 h-10 rounded-lg bg-emerald-600 text-white flex items-center justify-center shrink-0">
                <i data-lucide="bell-ring" class="w-5 h-5 animate-bounce"></i>
            </div>
            <div>
                <h4 class="text-xs font-bold">Pesanan Sudah Siap Diambil!</h4>
                <p class="text-[11px] text-emerald-700 mt-0.5">Silakan menuju ke <strong x-text="stallName"></strong> di counter kantin dan tunjukkan nomor antrean <strong x-text="queueNo"></strong>.</p>
            </div>
        </div>

    </div>

    <!-- Order Items Detail Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 space-y-4 shadow-xs">
        <h3 class="text-sm font-bold text-slate-900 pb-3 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="receipt" class="w-4 h-4 text-slate-900"></i>
                <span>Rincian Menu yang Dipesan</span>
            </div>
            <span class="text-xs font-semibold text-slate-500" x-text="stallName">Stan 01 - Barokah Rasa</span>
        </h3>

        @if($order && $order->items->isNotEmpty())
            <div class="divide-y divide-slate-100 text-xs">
                @foreach($order->items as $item)
                    <div class="py-2.5 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-slate-800">{{ $item->quantity }}x {{ $item->menu_name_snapshot }}</span>
                            @if($item->note)
                                <p class="text-[11px] text-slate-400">Catatan: {{ $item->note }}</p>
                            @endif
                        </div>
                        <span class="font-bold text-slate-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-slate-900">
                <span>Total Pembayaran ({{ ucfirst($order->payment_status) }} via QRIS)</span>
                <span class="text-sm text-brand-700">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
        @else
            <div class="divide-y divide-slate-100 text-xs">
                <div class="py-2.5 flex items-center justify-between">
                    <div>
                        <span class="font-bold text-slate-800">1x Ayam Geprek Sambal Korek</span>
                        <p class="text-[11px] text-slate-400">Catatan: Sambal pedas dipisah</p>
                    </div>
                    <span class="font-bold text-slate-900">Rp 13.000</span>
                </div>
                <div class="py-2.5 flex items-center justify-between">
                    <div>
                        <span class="font-bold text-slate-800">1x Es Teh Manis Jumbo Vokasi</span>
                        <p class="text-[11px] text-slate-400">Catatan: Sedikit gula</p>
                    </div>
                    <span class="font-bold text-slate-900">Rp 4.000</span>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-slate-900">
                <span>Total Pembayaran (Lunas via QRIS)</span>
                <span class="text-sm text-brand-700">Rp 17.000</span>
            </div>
        @endif
    </div>

    <!-- Bottom Actions (Unified Height Matching Add to Cart) -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2">
        <a href="{{ url('/riwayat') }}" class="whitespace-nowrap w-full sm:w-auto h-10 inline-flex items-center justify-center gap-1.5 py-2.5 px-4 rounded-xl border border-slate-200 bg-white text-slate-700 text-xs font-semibold hover:bg-slate-50 transition shadow-2xs">
            <i data-lucide="receipt" class="w-3.5 h-3.5"></i>
            <span>Lihat Struk Digital</span>
        </a>

        <div class="flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ url('/katalog') }}" class="whitespace-nowrap w-full sm:w-auto h-10 inline-flex items-center justify-center gap-1.5 py-2.5 px-4 rounded-xl bg-brand-600 text-white text-xs font-semibold hover:bg-brand-700 shadow-sm transition">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                <span>Pesan Menu Lain</span>
            </a>
        </div>
    </div>

</div>
@endsection
