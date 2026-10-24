@extends('layouts.seller')

@section('title', 'Pengaturan QRIS Stan — NesaCourt')

@section('content')
<div class="space-y-6" x-data="{
    saveSuccess: false,
    qrisPreview: '{{ asset('images/qris.jpeg') }}',
    handleQrisUpload(e) {
        const file = e.target.files[0];
        if (file) {
            this.qrisPreview = URL.createObjectURL(file);
        }
    },
    saveSettings() {
        this.saveSuccess = true;
        setTimeout(() => {
            this.saveSuccess = false;
        }, 3000);
    }
}">

    <!-- Page Header & Action Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-7 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                    <a href="{{ url('/penjual/dashboard') }}" class="hover:text-brand-600">Portal Stan</a>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-900"></i>
                    <span class="text-slate-800 font-semibold">Pengaturan QRIS Stan</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-semibold text-slate-900 tracking-tight">Pengaturan QRIS & Rekening Stan</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-2 max-w-xl">Kelola gambar kode QRIS statis dan data rekening stan untuk menerima pembayaran pesanan langsung dari mahasiswa.</p>
            </div>

            <!-- Buttons: On mobile displayed below description, on desktop aligned to the right -->
            <div class="flex items-center gap-2.5 shrink-0 pt-1 sm:pt-0">
                <button type="button" 
                        onclick="window.print()" 
                        class="flex-1 sm:flex-none h-10 py-2.5 px-4 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                    <i data-lucide="printer" class="w-4 h-4 text-slate-900"></i>
                    <span>Cetak Stiker Meja</span>
                </button>
                <button type="button" 
                        @click="saveSettings()" 
                        class="flex-1 sm:flex-none h-10 py-2.5 px-4 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-700 text-white shadow-sm shadow-brand-500/20 transition">
                    <i data-lucide="check" class="w-4 h-4 text-white"></i>
                    <span>Simpan Pengaturan</span>
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
            <i data-lucide="check-circle-2" class="w-5 h-5 text-slate-900 shrink-0"></i>
            <span>Pengaturan QRIS dan informasi rekening penampung stan berhasil disimpan!</span>
        </div>
        <button type="button" @click="saveSuccess = false" class="text-slate-700 hover:text-slate-900">
            <i data-lucide="x" class="w-4 h-4 text-slate-900"></i>
        </button>
    </div>

    <!-- 2 Column Layout: QRIS Display & Bank Account Details -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Left: QRIS Card & Preview (5 cols) -->
        <div class="lg:col-span-5 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <i data-lucide="qr-code" class="w-4 h-4 text-slate-900"></i>
                        <h2 class="text-sm font-bold text-slate-900">Kode QRIS Aktif</h2>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="relative flex h-1.5 w-1.5 shrink-0">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-500 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
                        </span>
                        Aktif
                    </span>
                </div>

                <!-- QRIS Box with White Frame -->
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-center space-y-3">
                    <div class="max-w-xs mx-auto bg-white p-3 rounded-xl shadow-xs border border-slate-200">
                        <img :src="qrisPreview" 
                             alt="Kode QRIS Stan 01 Barokah" 
                             class="w-full h-auto object-contain rounded-lg">
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs font-bold text-slate-900 block">Stan 01 - Barokah Rasa</span>
                        <p class="text-[11px] text-slate-500 font-medium">NMID: ID1020039281920</p>
                        <p class="text-[10px] text-slate-400">Kantin Vokasi Ketintang UNESA Surabaya</p>
                    </div>
                </div>

                <!-- File Upload Trigger -->
                <div class="space-y-2">
                    <label class="block text-xs font-semibold text-slate-700">Unggah Gambar QRIS Baru</label>
                    <label class="w-full h-10 py-2.5 px-4 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 cursor-pointer transition">
                        <i data-lucide="upload" class="w-4 h-4 text-slate-900"></i>
                        <span>Pilih File Gambar QRIS (JPEG/PNG)</span>
                        <input type="file" accept="image/*" @change="handleQrisUpload($event)" class="hidden">
                    </label>
                    <p class="text-[11px] text-slate-400 leading-relaxed">
                        Pastikan gambar barcode tidak buram agar aplikasi mobile banking dan e-wallet mahasiswa dapat mendeteksi dengan cepat.
                    </p>
                </div>
            </div>
        </div>

        <!-- Right: Merchant & Settlement Details (7 cols) -->
        <div class="lg:col-span-7 space-y-6">
            <!-- Merchant Account Credentials -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs space-y-4">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                    <i data-lucide="building-2" class="w-4 h-4 text-slate-900"></i>
                    <h2 class="text-sm font-bold text-slate-900">Identitas Merchant & Acquirer</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700">Nama Merchant Resmi QRIS</label>
                        <input type="text" 
                               value="Stan 01 - Barokah Rasa" 
                               class="w-full h-10 px-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 font-semibold focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700">National Merchant ID (NMID)</label>
                        <input type="text" 
                               value="ID1020039281920" 
                               class="w-full h-10 px-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 font-semibold focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700">Lembaga Penyelenggara (Acquirer)</label>
                        <input type="text" 
                               value="Bank Jatim (QRIS Interoperable)" 
                               readonly
                               class="w-full h-10 px-3.5 rounded-xl bg-slate-100 border border-slate-200 text-xs text-slate-600 font-medium">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700">Status Registrasi Bank</label>
                        <div class="h-10 px-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 font-semibold flex items-center gap-2">
                            <i data-lucide="shield-check" class="w-4 h-4 text-slate-900"></i>
                            <span>Terdaftar Resmi BI & ASPI</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settlement Bank Account -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs space-y-4">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                    <i data-lucide="wallet-cards" class="w-4 h-4 text-slate-900"></i>
                    <h2 class="text-sm font-bold text-slate-900">Rekening Penampung Penjualan (Pencairan)</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700">Nama Bank Penerima</label>
                        <select class="w-full h-10 px-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                            <option value="bank_jatim" selected>Bank Jatim</option>
                            <option value="mandiri">Bank Mandiri</option>
                            <option value="bri">Bank Rakyat Indonesia (BRI)</option>
                            <option value="bca">Bank Central Asia (BCA)</option>
                            <option value="bni">Bank Negara Indonesia (BNI)</option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700">Nomor Rekening Bank</label>
                        <input type="text" 
                               value="0123-8899-4411-00" 
                               class="w-full h-10 px-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    </div>

                    <div class="sm:col-span-2 space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700">Nama Pemilik Rekening (Sesuai Buku Tabungan)</label>
                        <input type="text" 
                               value="BAMBANG SUPRAYITNO" 
                               class="w-full h-10 px-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-900 uppercase focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    </div>
                </div>

                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 text-xs text-slate-600 flex items-start gap-2.5">
                    <i data-lucide="info" class="w-4 h-4 text-slate-900 shrink-0 mt-0.5"></i>
                    <p class="leading-relaxed">
                        Dana hasil pembayaran pesanan mahasiswa via QRIS masuk langsung ke rekening bank penampung Anda secara otomatis (H+0 atau real-time sesuai ketentuan Bank Jatim / BI-FAST).
                    </p>
                </div>
            </div>

            <!-- Bottom Action Row -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" 
                        @click="saveSettings()" 
                        class="h-10 py-2.5 px-5 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-700 text-white shadow-sm shadow-brand-500/20 transition">
                    <i data-lucide="check" class="w-4 h-4 text-white"></i>
                    <span>Simpan Seluruh Pengaturan QRIS</span>
                </button>
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
