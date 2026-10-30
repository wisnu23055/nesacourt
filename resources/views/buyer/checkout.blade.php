@extends('layouts.buyer')

@section('title', 'Checkout Pesanan — NesaCourt')

@section('content')
<div class="space-y-6" x-data="{
    serviceType: 'dine_in', // dine_in | takeaway
    tableNumber: 'Meja 08',
    customerName: '{{ addslashes(auth()->user()->name) }}',
    customerNim: '{{ addslashes(auth()->user()->phone ?? '23055310001') }}',
    customerPhone: '{{ addslashes(auth()->user()->phone ?? '081234567890') }}',
    orderNotes: '',
    isSubmitting: false,

    async submitOrder() {
        if (this.serviceType === 'dine_in' && !this.tableNumber.trim()) {
            alert('Silakan masukkan nomor meja Anda di Food Court Ketintang');
            return;
        }
        if (!this.customerName.trim()) {
            alert('Silakan masukkan nama pemesan');
            return;
        }
        if (!this.items || this.items.length === 0) {
            alert('Keranjang belanja Anda masih kosong.');
            return;
        }

        this.isSubmitting = true;

        try {
            const payload = {
                service_type: this.serviceType,
                table_number: this.tableNumber,
                customer_name: this.customerName,
                customer_phone: this.customerPhone,
                order_notes: this.orderNotes,
                items: this.items.map(i => ({ id: i.id, qty: i.qty }))
            };

            const res = await fetch('{{ route('buyer.checkout.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            });

            const data = await res.json();
            if (!res.ok || !data.success) {
                throw new Error(data.message || 'Terjadi kesalahan saat memproses pesanan.');
            }

            this.clearCart();
            window.location.href = data.redirect;
        } catch (err) {
            this.isSubmitting = false;
            alert(err.message);
        }
    }
}">

    <!-- Breadcrumb & Header -->
    <div class="pb-4 border-b border-slate-200/80">
        <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
            <a href="{{ url('/dashboard') }}" class="hover:text-brand-600 transition">Beranda</a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
            <a href="{{ url('/keranjang') }}" class="hover:text-brand-600 transition">Keranjang</a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
            <span class="text-slate-900 font-semibold">Konfirmasi Checkout</span>
        </div>
        <div class="flex items-center gap-2.5">
            <i data-lucide="clipboard-check" class="w-6 h-6 text-slate-900 shrink-0"></i>
            <h1 class="text-xl sm:text-2xl font-semibold text-slate-900 tracking-tight">Konfirmasi Pesanan</h1>
        </div>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">Lengkapi data meja atau bungkus sebelum melakukan transfer QRIS ke stan penjual.</p>
    </div>

    <!-- Main Form Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left 2 Cols: Form Options -->
        <div class="lg:col-span-2 space-y-6">

            <!-- 1. Opsi Tipe Layanan (Makan di Tempat / Bawa Pulang) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 space-y-4 shadow-xs">
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <i data-lucide="utensils-crossed" class="w-4 h-4 text-slate-900"></i>
                    <span>Tipe Pemesanan Makanan</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <button type="button" 
                            @click="serviceType = 'dine_in'" 
                            :class="serviceType === 'dine_in' ? 'border-brand-600 bg-brand-50/50 ring-1 ring-brand-600' : 'border-slate-200 bg-white hover:bg-slate-50'"
                            class="p-4 rounded-xl border text-left flex items-start gap-3 transition">
                        <div class="p-2 rounded-lg bg-white shadow-xs border border-slate-100 text-slate-900 shrink-0">
                            <i data-lucide="coffee" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-slate-900 block">Makan di Tempat (Dine-in)</span>
                            <span class="text-[11px] text-slate-500 mt-0.5 block">Disajikan di piring/gelas stan untuk dinikmati di kantin</span>
                        </div>
                    </button>

                    <button type="button" 
                            @click="serviceType = 'takeaway'" 
                            :class="serviceType === 'takeaway' ? 'border-brand-600 bg-brand-50/50 ring-1 ring-brand-600' : 'border-slate-200 bg-white hover:bg-slate-50'"
                            class="p-4 rounded-xl border text-left flex items-start gap-3 transition">
                        <div class="p-2 rounded-lg bg-white shadow-xs border border-slate-100 text-slate-900 shrink-0">
                            <i data-lucide="package-check" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-slate-900 block">Bawa Pulang (Takeaway)</span>
                            <span class="text-[11px] text-slate-500 mt-0.5 block">Dikemas rapi dalam kotak/cup untuk dibawa ke kelas</span>
                        </div>
                    </button>
                </div>

                <!-- Input Nomor Meja (Tampil bila dine_in) -->
                <div x-show="serviceType === 'dine_in'" x-transition class="space-y-1.5 pt-2">
                    <label class="block text-xs font-semibold text-slate-700">Nomor Meja Kantin Ketintang <span class="text-rose-500">*</span></label>
                    <input type="text" 
                           x-model="tableNumber" 
                           placeholder="Contoh: Meja 08, Meja C-12, Gazebo 2" 
                           class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <p class="text-[11px] text-slate-400">Nomor tertera pada stiker akrilik di atas meja food court.</p>
                </div>
            </div>

            <!-- 2. Data Pemesan Mahasiswa -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 space-y-4 shadow-xs">
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <i data-lucide="user" class="w-4 h-4 text-slate-900"></i>
                    <span>Informasi Pemesan Mahasiswa</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs font-medium text-slate-600">Nama Lengkap</label>
                        <input type="text" x-model="customerName" readonly class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-700 font-semibold cursor-not-allowed">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-medium text-slate-600">NIM UNESA</label>
                        <input type="text" x-model="customerNim" readonly class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-700 font-semibold cursor-not-allowed">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-medium text-slate-600">No. WhatsApp</label>
                        <input type="text" x-model="customerPhone" class="w-full px-3 py-2 text-xs rounded-xl bg-white border border-slate-200 text-slate-900 font-medium focus:ring-2 focus:ring-brand-500">
                    </div>
                </div>

                <!-- Catatan untuk Penjual -->
                <div class="space-y-1.5 pt-2 border-t border-slate-100">
                    <label class="block text-xs font-semibold text-slate-700">Catatan Khusus untuk Stan Penjual (Opsional)</label>
                    <textarea x-model="orderNotes" 
                              rows="2" 
                              placeholder="Misal: Sambal dipisah, es teh sedikit gula, jangan pakai seledri..." 
                              class="w-full p-3 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-brand-500"></textarea>
                </div>
            </div>

            <!-- 3. Ketentuan QRIS Statis -->
            <div class="p-4 rounded-2xl bg-amber-50/70 border border-amber-200 space-y-2 text-xs text-amber-900">
                <div class="flex items-center gap-2 font-bold">
                    <i data-lucide="qr-code" class="w-4 h-4 text-slate-900"></i>
                    <span>Ketentuan Pembayaran QRIS Statis Stan</span>
                </div>
                <p class="leading-relaxed text-[11px] text-amber-800">
                    NesaCourt menggunakan QRIS Statis resmi masing-masing stan penjual. Setelah konfirmasi, layar Anda akan menampilkan kode QRIS. Silakan lakukan pembayaran via aplikasi bank atau e-wallet (BCA, Mandiri, BRI, GoPay, OVO, ShopeePay, DANA) lalu simpan bukti bayar untuk ditunjukkan ke kasir stan.
                </p>
            </div>

        </div>

        <!-- Right 1 Col: Summary & Checkout Button -->
        <div class="space-y-4">
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 space-y-4 shadow-xs">
                <h3 class="text-sm font-bold text-slate-900 pb-3 border-b border-slate-100 flex items-center gap-2">
                    <i data-lucide="receipt" class="w-4 h-4 text-slate-900"></i>
                    <span>Detail Pesanan Stan</span>
                </h3>

                <!-- Stan Name -->
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-xs text-slate-700 flex items-center gap-2">
                    <i data-lucide="store" class="w-4 h-4 text-slate-900 shrink-0"></i>
                    <span class="truncate">Stan: <strong x-text="currentSeller || 'Stan 01 - Barokah Rasa'"></strong></span>
                </div>

                <!-- Items Preview -->
                <div class="space-y-2 text-xs">
                    <template x-if="items.length > 0">
                        <div class="space-y-2">
                            <template x-for="item in items" :key="item.id">
                                <div class="flex items-center justify-between text-slate-700">
                                    <span class="truncate pr-2" x-text="item.qty + 'x ' + item.name"></span>
                                    <span class="font-semibold shrink-0" x-text="formatRupiah(item.price * item.qty)"></span>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- Mock fallback if visited directly without cart -->
                    <template x-if="items.length === 0">
                        <div class="space-y-2 text-slate-600">
                            <div class="flex justify-between">
                                <span>1x Ayam Geprek Sambal Korek</span>
                                <span class="font-semibold">Rp 13.000</span>
                            </div>
                            <div class="flex justify-between">
                                <span>1x Es Teh Manis Jumbo Vokasi</span>
                                <span class="font-semibold">Rp 4.000</span>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="pt-3 border-t border-slate-100 space-y-2 text-xs text-slate-600">
                    <div class="flex justify-between">
                        <span>Biaya Layanan</span>
                        <span class="font-semibold text-emerald-600">Gratis (Rp 0)</span>
                    </div>
                    <div class="flex justify-between font-bold text-slate-900 text-sm pt-1 border-t border-slate-100">
                        <span>Total yang Harus Dibayar</span>
                        <span class="text-base text-brand-700" x-text="items.length > 0 ? formatRupiah(subtotal) : 'Rp 17.000'"></span>
                    </div>
                </div>

                <button type="button" 
                        @click="submitOrder()" 
                        :disabled="isSubmitting"
                        class="whitespace-nowrap w-full h-10 inline-flex items-center justify-center gap-1.5 py-2.5 px-4 rounded-xl bg-brand-600 text-white text-xs font-semibold hover:bg-brand-700 shadow-sm transition">
                    <i data-lucide="qr-code" class="w-3.5 h-3.5"></i>
                    <span x-text="isSubmitting ? 'Memproses Pesanan...' : 'Buat Pesanan & Bayar QRIS'"></span>
                </button>

                <a href="{{ url('/keranjang') }}" class="whitespace-nowrap w-full inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-50 transition">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    <span>Kembali ke Keranjang</span>
                </a>
            </div>
        </div>

    </div>

</div>
@endsection
