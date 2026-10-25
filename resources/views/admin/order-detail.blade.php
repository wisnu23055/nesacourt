@extends('layouts.admin')

@section('title', 'Audit Detail Pesanan — NesaCourt')

@section('content')
<div class="space-y-6" x-data="{
    orderId: 'NC-20261021-8842',
    queueNo: 'A-09',
    auditStatus: 'Terverifikasi Sesuai',
    isVerifying: false,

    printAudit() {
        window.print();
    }
}">

    <!-- Breadcrumb & Header (Semi-Bold Title with Black Icon) -->
    <div class="pb-4 border-b border-slate-200/80">
        <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
            <a href="{{ url('/admin/dashboard') }}" class="hover:text-brand-600 transition">Dashboard</a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
            <a href="{{ url('/admin/pesanan') }}" class="hover:text-brand-600 transition">Monitoring Pesanan</a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
            <span class="text-slate-900 font-semibold">Audit Transaksi</span>
        </div>
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
            <div>
                <div class="flex items-center gap-2.5">
                    <i data-lucide="file-check" class="w-6 h-6 text-slate-900 shrink-0"></i>
                    <h1 class="text-xl sm:text-2xl font-semibold text-slate-900 tracking-tight">Detail Pesanan & Audit Transaksi</h1>
                </div>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">Audit rincian transaksi nomor NC-20261021-8842, validasi pembayaran QRIS statis kantin, dan kepatuhan waktu pelayanan.</p>
            </div>

            <!-- Tombol Cetak: Di Mobile di Bawah Deskripsi, di Desktop di Kanan Atas -->
            <div class="pt-1 sm:pt-0 shrink-0">
                <button type="button" 
                        @click="printAudit()" 
                        class="w-full sm:w-auto whitespace-nowrap inline-flex items-center justify-center gap-1.5 py-2 px-3.5 rounded-xl border border-slate-200 bg-white text-slate-700 text-xs font-semibold hover:bg-slate-50 transition shadow-2xs">
                    <i data-lucide="printer" class="w-3.5 h-3.5 text-slate-900"></i>
                    <span>Cetak Lembar Audit</span>
                </button>
            </div>
        </div>
    </div>

    <!-- 4 Center Metadata Cards (Identik dengan Desain Status Pesanan) -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3.5">
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-2xs text-center flex flex-col justify-center">
            <span class="text-[11px] text-slate-400 font-medium">Nomor ID Pesanan</span>
            <span class="text-sm sm:text-base font-bold text-slate-900 tracking-tight mt-0.5" x-text="orderId"></span>
            <span class="text-[10px] font-semibold text-emerald-600 mt-0.5">QRIS Lunas Terverifikasi</span>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-2xs text-center flex flex-col justify-center">
            <span class="text-[11px] text-slate-400 font-medium">Stan Kantin & Lokasi</span>
            <span class="text-xs sm:text-sm font-bold text-slate-900 tracking-tight mt-0.5">Stan 01 - Barokah Rasa</span>
            <span class="text-[10px] text-slate-500 mt-0.5">Counter Barat No. 01</span>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-2xs text-center flex flex-col justify-center">
            <span class="text-[11px] text-slate-400 font-medium">Nomor Antrean Pesanan</span>
            <span class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight mt-0.5" x-text="queueNo"></span>
            <span class="text-[10px] text-slate-500 mt-0.5">Makan di Tempat (Meja 08)</span>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-2xs text-center flex flex-col justify-center">
            <span class="text-[11px] text-slate-400 font-medium">Durasi Pelayanan Dapur</span>
            <span class="text-sm sm:text-base font-bold text-slate-900 tracking-tight mt-0.5">~7 Menit</span>
            <span class="text-[10px] text-emerald-600 font-semibold mt-0.5">Tepat Waktu & Standar</span>
        </div>
    </div>

    <!-- Customer & Merchant Profile Information Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Customer Profile Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 space-y-4 shadow-xs">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <i data-lucide="user" class="w-4 h-4 text-slate-900"></i>
                <h3 class="text-sm font-bold text-slate-900">Identitas Pemesan Mahasiswa</h3>
            </div>

            <div class="flex items-center gap-3.5">
                <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=120&q=80" 
                     alt="Foto Mahasiswa" 
                     class="w-14 h-14 rounded-2xl object-cover ring-1 ring-slate-200 shrink-0">
                <div class="space-y-0.5">
                    <h4 class="text-sm font-bold text-slate-900">Aditya Pratama</h4>
                    <p class="text-xs text-slate-500">NIM: 23055310001 (Mahasiswa Aktif UNESA)</p>
                    <p class="text-xs text-slate-500">Program Studi: D4 Manajemen Informatika</p>
                    <p class="text-xs font-semibold text-brand-700 pt-0.5">WhatsApp: 081234567890</p>
                </div>
            </div>
        </div>

        <!-- Merchant Profile Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 space-y-4 shadow-xs">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <i data-lucide="store" class="w-4 h-4 text-slate-900"></i>
                <h3 class="text-sm font-bold text-slate-900">Informasi Pemilik Stan Penjual</h3>
            </div>

            <div class="flex items-center gap-3.5">
                <img src="https://images.unsplash.com/photo-1556157382-97eda2d62296?auto=format&fit=crop&w=120&q=80" 
                     alt="Foto Pemilik Stan" 
                     class="w-14 h-14 rounded-2xl object-cover ring-1 ring-slate-200 shrink-0">
                <div class="space-y-0.5">
                    <h4 class="text-sm font-bold text-slate-900">Pak Bambang</h4>
                    <p class="text-xs text-slate-500">Pengelola Stan 01 - Barokah Rasa</p>
                    <p class="text-xs text-slate-500">Lokasi: Counter Barat No. 01 | Food Court Ketintang</p>
                    <p class="text-xs font-semibold text-emerald-700 pt-0.5">Status Sewa: Lunas Terdaftar</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Items Detail Table with Food Photos -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 space-y-4 shadow-xs">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <i data-lucide="receipt" class="w-4 h-4 text-slate-900"></i>
                <h3 class="text-sm font-bold text-slate-900">Rincian Menu Yang Dipesan & Tera Harga</h3>
            </div>
            <span class="text-xs font-semibold text-slate-500">2 Jenis Menu</span>
        </div>

        <div class="divide-y divide-slate-100">
            <!-- Item 1 -->
            <div class="py-3 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3 min-w-0">
                    <img src="https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?auto=format&fit=crop&w=300&q=80" 
                         alt="Ayam Geprek Sambal Korek" 
                         class="w-12 h-12 rounded-xl object-cover ring-1 ring-slate-200 shrink-0 shadow-2xs">
                    <div>
                        <span class="text-xs sm:text-sm font-bold text-slate-900 block truncate">1x Ayam Geprek Sambal Korek</span>
                        <p class="text-[11px] text-slate-400">Catatan Pembeli: Sambal pedas dipisah</p>
                    </div>
                </div>
                <span class="text-xs sm:text-sm font-bold text-slate-900">Rp 13.000</span>
            </div>

            <!-- Item 2 -->
            <div class="py-3 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3 min-w-0">
                    <img src="https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?auto=format&fit=crop&w=300&q=80" 
                         alt="Es Teh Manis Jumbo Vokasi" 
                         class="w-12 h-12 rounded-xl object-cover ring-1 ring-slate-200 shrink-0 shadow-2xs">
                    <div>
                        <span class="text-xs sm:text-sm font-bold text-slate-900 block truncate">1x Es Teh Manis Jumbo Vokasi</span>
                        <p class="text-[11px] text-slate-400">Catatan Pembeli: Sedikit gula</p>
                    </div>
                </div>
                <span class="text-xs sm:text-sm font-bold text-slate-900">Rp 4.000</span>
            </div>
        </div>

        <!-- Totals & Payment Summary -->
        <div class="pt-4 border-t border-slate-100 space-y-2 text-xs">
            <div class="flex justify-between text-slate-600">
                <span>Subtotal Menu</span>
                <span class="font-bold text-slate-800">Rp 17.000</span>
            </div>
            <div class="flex justify-between text-slate-600">
                <span>Biaya Layanan Kampus</span>
                <span class="font-semibold text-emerald-600">Gratis (Rp 0)</span>
            </div>
            <div class="flex justify-between text-slate-900 font-bold text-sm pt-2 border-t border-slate-100">
                <span>Total Audit Pembayaran</span>
                <span class="text-base text-brand-700">Rp 17.000 (Lunas)</span>
            </div>
        </div>
    </div>

    <!-- QRIS Statis Verification Stamp & Compliance Box -->
    <div class="p-5 rounded-2xl bg-emerald-50/70 border border-emerald-200 text-emerald-950 space-y-3">
        <div class="flex items-center gap-2 font-bold text-sm">
            <i data-lucide="shield-check" class="w-5 h-5 text-slate-900"></i>
            <span>Verifikasi Pembayaran QRIS Statis Valid</span>
        </div>
        <p class="text-xs text-emerald-800 leading-relaxed">
            Transaksi ini telah tervalidasi menggunakan QRIS Statis resmi Stan 01 - Barokah Rasa. Dana masuk langsung ke rekening pedagang tanpa potongan biaya admin, dan diverifikasi langsung oleh kasir stan pada saat penyerahan pesanan di meja counter.
        </p>
    </div>

    <!-- Actions Row -->
    <div class="flex items-center justify-between pt-2">
        <a href="{{ url('/admin/pesanan') }}" class="whitespace-nowrap inline-flex items-center justify-center gap-1.5 py-2.5 px-4 rounded-xl border border-slate-200 bg-white text-slate-700 text-xs font-semibold hover:bg-slate-50 transition shadow-2xs">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5 text-slate-900"></i>
            <span>Kembali ke Monitoring Pesanan</span>
        </a>
        <a href="{{ url('/admin/laporan') }}" class="whitespace-nowrap inline-flex items-center justify-center gap-1.5 py-2.5 px-4 rounded-xl bg-slate-900 text-white text-xs font-semibold hover:bg-slate-800 transition shadow-sm">
            <i data-lucide="bar-chart-3" class="w-3.5 h-3.5 text-white"></i>
            <span>Lihat Rekap Keuangan</span>
        </a>
    </div>

</div>
@endsection
