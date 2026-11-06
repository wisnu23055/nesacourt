@extends('layouts.seller')

@section('title', 'Profil Stan & Jam Operasional — NesaCourt')

@section('content')
<div class="space-y-6" x-data="{
    saveSuccess: {{ (session('profile_success') || session('success')) ? 'true' : 'false' }},
    storePhoto: '{{ $seller->logo_url ?: 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=600&q=80' }}',
    hasNewPhoto: false,
    handlePhotoUpload(e) {
        const file = e.target.files[0];
        if (file) {
            this.storePhoto = URL.createObjectURL(file);
            this.hasNewPhoto = true;
        }
    }
}">

    <!-- Page Header -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-7 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ url('/penjual/dashboard') }}" class="hover:text-brand-600">Portal Stan</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-900"></i>
                <span class="text-slate-800 font-semibold">Profil Stan</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-semibold text-slate-900 tracking-tight">Profil Stan & Jam Operasional</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-2 max-w-xl">Kelola data resmi stan, lokasi counter di food court UNESA Ketintang, nomor WhatsApp kasir, dan jadwal operasional.</p>
        </div>
    </div>

    <!-- Success Toast Alert -->
    <div x-show="saveSuccess" 
         x-cloak 
         x-transition 
         class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between gap-2.5">
        <div class="flex items-center gap-2">
            <i data-lucide="check-circle-2" class="w-5 h-5 text-slate-900 shrink-0"></i>
            <span>Informasi profil stan dan jam operasional berhasil diperbarui!</span>
        </div>
        <button type="button" @click="saveSuccess = false" class="text-slate-700 hover:text-slate-900">
            <i data-lucide="x" class="w-4 h-4 text-slate-900"></i>
        </button>
    </div>

    <!-- Form 2-Column Grid -->
    <form action="{{ route('seller.profile.update') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        @csrf
        <!-- Left Column: Business Info & Operating Schedule (7 cols) -->
        <div class="lg:col-span-7 space-y-6">
            <!-- Stan Identity Form -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs space-y-4">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                    <i data-lucide="store" class="w-4 h-4 text-slate-900"></i>
                    <h2 class="text-sm font-bold text-slate-900">Informasi Stan & Kontak</h2>
                </div>

                <div class="space-y-4">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700">Nama Stan Resmi di Food Court <span class="text-rose-500">*</span></label>
                        <input type="text" 
                               name="stall_name"
                               value="{{ $seller->stall_name }}" 
                               required 
                               class="w-full h-10 px-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700">Slogan / Keterangan Spesialisasi</label>
                        <input type="text" 
                               name="description"
                               value="{{ $seller->description }}" 
                               class="w-full h-10 px-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700">Lokasi Counter Stan <span class="text-rose-500">*</span></label>
                        <input type="text" 
                               name="location"
                               value="{{ $seller->location }}" 
                               required 
                               class="w-full h-10 px-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700">Nomor WhatsApp Pengelola <span class="text-rose-500">*</span></label>
                        <input type="text" 
                               name="phone"
                               value="{{ auth()->user()->phone ?? '0812-3456-7890' }}" 
                               required 
                               class="w-full h-10 px-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700">Deskripsi Singkat untuk Mahasiswa</label>
                        <textarea name="description" rows="3" 
                                  class="w-full p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">{{ $seller->description }}</textarea>
                    </div>

                    <!-- Tombol Simpan Data di Card Informasi Stan & Kontak -->
                    <div class="pt-3 border-t border-slate-100 flex justify-end">
                        <button type="submit" 
                                class="w-full sm:w-auto h-10 py-2.5 px-5 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-700 text-white shadow-sm shadow-brand-500/20 transition">
                            <i data-lucide="check" class="w-4 h-4 text-white"></i>
                            <span>Simpan Data</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Banner Image (5 cols) -->
        <div class="space-y-6 lg:col-span-5">
            <!-- Foto Etalase Stan Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs space-y-4">
                <div class="flex items-center gap-2 pb-2.5 border-b border-slate-100">
                    <i data-lucide="image" class="w-4 h-4 text-slate-900"></i>
                    <h2 class="text-sm font-bold text-slate-900">Foto Counter Stan</h2>
                </div>

                <div class="relative w-full aspect-16/10 rounded-xl overflow-hidden bg-slate-100 border border-slate-200">
                    <img :src="storePhoto" alt="Counter Stan 01 Barokah" class="w-full h-full object-cover">
                </div>

                <label class="w-full h-10 py-2.5 px-4 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 cursor-pointer transition">
                    <i data-lucide="camera" class="w-4 h-4 text-slate-900"></i>
                    <span>Ganti Foto Counter Stan</span>
                    <input type="file" name="logo" accept="image/*" @change="handlePhotoUpload($event)" class="hidden">
                </label>

                <!-- Tombol Simpan Profil (Hanya muncul jika baru upload / ganti foto) -->
                <div x-show="hasNewPhoto" x-cloak class="pt-2">
                    <button type="submit" 
                            class="w-full h-10 py-2.5 px-4 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm shadow-emerald-500/20 transition">
                        <i data-lucide="upload" class="w-4 h-4 text-white"></i>
                        <span>Simpan Profil</span>
                    </button>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-2.5">
                <a href="{{ route('logout') }}" 
                   class="w-full h-10 py-2.5 px-4 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center gap-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 transition">
                    <i data-lucide="log-out" class="w-4 h-4 text-rose-700"></i>
                    <span>Keluar Akun Stan</span>
                </a>
            </div>
        </div>
    </form>

</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });
</script>
@endsection
