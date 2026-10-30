@extends('layouts.buyer')

@section('title', 'Pembayaran QRIS — NesaCourt')

@section('content')
<div class="max-w-2xl mx-auto space-y-6" x-data="{
    orderId: '{{ $order->order_number }}',
    nominal: {{ (int) $order->total }},
    merchantName: '{{ addslashes($order->seller->stall_name ?? 'Stan Kantin') }}',
    copiedOrder: false,
    copiedNominal: false,

    copy(text, type) {
        navigator.clipboard.writeText(text);
        if (type === 'order') {
            this.copiedOrder = true;
            setTimeout(() => this.copiedOrder = false, 2000);
        } else {
            this.copiedNominal = true;
            setTimeout(() => this.copiedNominal = false, 2000);
        }
    }
}">

    <!-- Top Info Card (Identik dengan Tema Dashboard & Checkout) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs space-y-3">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 pb-3 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-900 shrink-0 shadow-2xs">
                    <i data-lucide="qr-code" class="w-5 h-5 text-slate-900"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">Pembayaran QRIS Statis Stan</h1>
                        <span class="text-slate-300 font-normal">|</span>
                        <span class="text-xs font-semibold text-brand-600">Kantin Ketintang</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Transfer langsung ke rekening pemilik stan kantin tanpa biaya admin</p>
                </div>
            </div>

            <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600 shrink-0 self-start md:self-auto">
                <span class="relative flex h-2 w-2 shrink-0">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <span>QRIS Aktif Siap Pindai</span>
            </div>
        </div>

        <!-- Order & Amount Info (Font Sama, Kompak & Seimbang) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-slate-500 block">Nomor Pesanan</span>
                    <span class="text-xs font-semibold text-slate-800" x-text="orderId">NC-20261021-8842</span>
                </div>
                <button type="button" 
                        @click="copy(orderId, 'order')" 
                        class="whitespace-nowrap px-2.5 py-1 rounded-lg border border-slate-200 bg-white text-slate-700 text-[11px] font-semibold hover:bg-slate-50 transition shadow-2xs">
                    <span x-text="copiedOrder ? 'Tersalin' : 'Salin'">Salin</span>
                </button>
            </div>

            <div class="p-3 rounded-xl bg-brand-50/60 border border-brand-200/70 flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-brand-700 block">Total Tagihan Pesanan</span>
                    <span class="text-xs font-semibold text-brand-700" x-text="formatRupiah(nominal)">Rp 17.000</span>
                </div>
                <button type="button" 
                        @click="copy(nominal, 'nominal')" 
                        class="whitespace-nowrap px-2.5 py-1 rounded-lg bg-brand-600 text-white text-[11px] font-semibold hover:bg-brand-700 transition shadow-2xs">
                    <span x-text="copiedNominal ? 'Tersalin' : 'Salin Nominal'">Salin Nominal</span>
                </button>
            </div>
        </div>
    </div>

    <!-- QRIS Card Container (Full Image, Bersih Tanpa Teks Stan & NMID) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-7 shadow-xs space-y-6 text-center">
        
        <!-- Full QRIS Image Container -->
        <div class="max-w-md mx-auto p-3 sm:p-4 rounded-2xl border border-slate-200 bg-slate-50/40">
            <div class="bg-white p-2 sm:p-3 rounded-xl border border-slate-200/80 shadow-xs">
                <img src="{{ $order->seller->qris?->image_path ? asset($order->seller->qris->image_path) : asset('images/qris.jpeg') }}" 
                     alt="Kode QRIS Pembayaran NesaCourt" 
                     class="w-full h-auto object-contain mx-auto rounded-lg">
            </div>
        </div>

        <!-- Download Action -->
        <div class="flex justify-center">
            <a href="{{ $order->seller->qris?->image_path ? asset($order->seller->qris->image_path) : asset('images/qris.jpeg') }}" 
               download="qris-stan-{{ $order->order_number }}.jpeg" 
               class="whitespace-nowrap inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 border border-slate-200 bg-white hover:bg-slate-50 transition shadow-2xs">
                <i data-lucide="download" class="w-3.5 h-3.5"></i>
                <span>Unduh Gambar QRIS</span>
            </a>
        </div>

        <!-- Payment Instructions -->
        <div class="text-left bg-slate-50/70 rounded-2xl p-4 sm:p-5 space-y-3 border border-slate-200/70">
            <h4 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                <i data-lucide="help-circle" class="w-4 h-4 text-slate-900"></i>
                <span>Cara Pembayaran QRIS:</span>
            </h4>
            <ol class="text-xs text-slate-600 space-y-2 list-decimal list-inside leading-relaxed">
                <li>Buka aplikasi Mobile Banking (BCA, BRI, BNI) atau E-Wallet (GoPay, OVO, ShopeePay, DANA).</li>
                <li>Pilih menu <strong>Pindai / Scan QRIS</strong> dan arahkan kamera ke barcode QRIS di atas.</li>
                <li>Pastikan nama merchant penerima dana sesuai.</li>
                <li>Masukkan nominal transfer persis sebesar <strong class="text-brand-700" x-text="formatRupiah(nominal)">Rp 17.000</strong> tanpa biaya transaksi.</li>
                <li>Setelah berhasil mentransfer, klik tombol konfirmasi di bawah.</li>
            </ol>
        </div>

        <!-- CTA Action Buttons -->
        <div class="space-y-3 pt-2">
            <form action="{{ route('buyer.payment.confirm', ['order_number' => $order->order_number]) }}" method="POST">
                @csrf
                <button type="submit" 
                        class="whitespace-nowrap w-full h-10 inline-flex items-center justify-center gap-1.5 py-2.5 px-4 rounded-xl bg-brand-600 text-white text-xs font-semibold hover:bg-brand-700 shadow-sm transition">
                    <i data-lucide="check" class="w-3.5 h-3.5"></i>
                    <span>Saya Sudah Membayar</span>
                </button>
            </form>

            <div class="flex items-center justify-center gap-3 text-xs text-slate-500">
                <a href="{{ url('/katalog') }}" class="hover:text-slate-800 transition">
                    Kembali ke Menu
                </a>
                <span class="text-slate-300">|</span>
                <form action="{{ route('buyer.order.cancel', ['order_number' => $order->order_number]) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini? Stok porsi menu akan dikembalikan.')" class="inline">
                    @csrf
                    <button type="submit" class="text-rose-600 hover:text-rose-700 font-semibold transition cursor-pointer">
                        Batalkan Pesanan
                    </button>
                </form>
            </div>
        </div>

    </div>

</div>
@endsection
