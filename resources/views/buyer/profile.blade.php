@extends('layouts.buyer')

@section('title', 'Profil Mahasiswa — NesaCourt')

@section('content')
<div class="space-y-6" x-data="{
    savedSuccess: {{ session('profile_success') ? 'true' : 'false' }},
    passwordSuccess: {{ session('password_success') ? 'true' : 'false' }},
    name: '{{ addslashes($user->name) }}',
    email: '{{ addslashes($user->email) }}',
    nim: '{{ addslashes($user->phone ?? '23055310001') }}',
    faculty: 'Fakultas Vokasi - D4 Manajemen Informatika',
    phone: '{{ addslashes($user->phone ?? '081234567890') }}',
}">

    <!-- Top Identity Card (Identik dengan Tema Dashboard & Halaman Buyer Lainnya) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-7 shadow-xs space-y-5">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3.5">
                <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=120&q=80" 
                     alt="Foto Mahasiswa" 
                     class="w-14 h-14 rounded-2xl object-cover ring-1 ring-slate-200 shrink-0">
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight" x-text="name">Aditya Pratama</h1>
                        <span class="text-slate-300 font-normal">|</span>
                        <span class="text-xs font-semibold text-brand-600">Mahasiswa Aktif</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">D4 Manajemen Informatika | Vokasi UNESA Ketintang (NIM: 23055310001)</p>
                </div>
            </div>

            <!-- SSO Status Badge with Blinking Indicator -->
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600 shrink-0 self-start md:self-auto">
                <span class="relative flex h-2 w-2 shrink-0">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <span>Akun Terverifikasi SSO UNESA</span>
            </div>
        </div>

        <!-- Quick Activity Stats (Center Tengah & Ikon Hitam) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/70 flex flex-col items-center justify-center text-center space-y-1.5">
                <div class="w-9 h-9 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-900 shrink-0 shadow-2xs">
                    <i data-lucide="shopping-bag" class="w-4 h-4 text-slate-900"></i>
                </div>
                <div>
                    <span class="text-[11px] text-slate-500 block font-medium">Total Pesanan Selesai</span>
                    <span class="text-sm font-bold text-slate-900 block mt-0.5">{{ $totalCompleted }} Transaksi</span>
                </div>
            </div>

            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/70 flex flex-col items-center justify-center text-center space-y-1.5">
                <div class="w-9 h-9 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-900 shrink-0 shadow-2xs">
                    <i data-lucide="store" class="w-4 h-4 text-slate-900"></i>
                </div>
                <div>
                    <span class="text-[11px] text-slate-500 block font-medium">Stan Langganan Utama</span>
                    <span class="text-sm font-bold text-slate-900 block mt-0.5">{{ $favStall }}</span>
                </div>
            </div>

            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/70 flex flex-col items-center justify-center text-center space-y-1.5">
                <div class="w-9 h-9 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-900 shrink-0 shadow-2xs">
                    <i data-lucide="qr-code" class="w-4 h-4 text-slate-900"></i>
                </div>
                <div>
                    <span class="text-[11px] text-slate-500 block font-medium">Metode Pembayaran</span>
                    <span class="text-sm font-bold text-slate-900 block mt-0.5">QRIS Statis Kantin</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Notifications -->
    <div x-show="savedSuccess" x-transition class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs font-semibold text-emerald-800 flex items-center gap-2">
        <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
        <span>Data profil berhasil diperbarui.</span>
    </div>

    <div x-show="passwordSuccess" x-transition class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs font-semibold text-emerald-800 flex items-center gap-2">
        <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
        <span>Kata sandi akun Anda berhasil diganti.</span>
    </div>

    <!-- Grid Forms: Profile Info & Security -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Form 1: Biodata Mahasiswa -->
        <form action="{{ route('buyer.profile.update') }}" method="POST" class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 space-y-4 shadow-xs flex flex-col justify-between">
            @csrf
            <div>
                <h3 class="text-sm font-bold text-slate-900 pb-3 border-b border-slate-100 flex items-center gap-2">
                    <i data-lucide="user-check" class="w-4 h-4 text-slate-900"></i>
                    <span>Informasi Pribadi Mahasiswa</span>
                </h3>

                <div class="space-y-3 pt-3">
                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-slate-700">Nama Lengkap</label>
                        <input type="text" name="name" x-model="name" required class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-slate-700">Nomor Induk Mahasiswa (NIM)</label>
                        <input type="text" x-model="nim" readonly class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50 text-slate-500 cursor-not-allowed font-medium">
                        <p class="text-[10px] text-slate-400">NIM terikat secara permanen dengan akun SSO UNESA.</p>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-slate-700">Alamat Email Kampus</label>
                        <input type="email" x-model="email" readonly class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50 text-slate-500 cursor-not-allowed font-medium">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-slate-700">Nomor WhatsApp Aktif</label>
                        <input type="text" name="phone" x-model="phone" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                        <p class="text-[10px] text-slate-400">Digunakan penjual stan untuk konfirmasi pesanan siap ambil.</p>
                    </div>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" 
                        class="whitespace-nowrap w-full h-10 inline-flex items-center justify-center gap-1.5 py-2.5 px-4 rounded-xl bg-brand-600 text-white text-xs font-semibold hover:bg-brand-700 shadow-sm transition">
                    <i data-lucide="save" class="w-3.5 h-3.5"></i>
                    <span>Simpan Perubahan Data</span>
                </button>
            </div>
        </form>

        <!-- Form 2: Keamanan & Kata Sandi -->
        <form action="{{ route('buyer.profile.password') }}" method="POST" class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 space-y-4 shadow-xs flex flex-col justify-between">
            @csrf
            <div>
                <h3 class="text-sm font-bold text-slate-900 pb-3 border-b border-slate-100 flex items-center gap-2">
                    <i data-lucide="shield-check" class="w-4 h-4 text-slate-900"></i>
                    <span>Keamanan & Kata Sandi</span>
                </h3>

                @if ($errors->has('current_password') || $errors->has('password'))
                    <div class="mt-2 p-3 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-700">
                        {{ $errors->first('current_password') ?: $errors->first('password') }}
                    </div>
                @endif

                <div class="space-y-3 pt-3">
                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-slate-700">Kata Sandi Saat Ini</label>
                        <input type="password" name="current_password" required placeholder="Masukkan kata sandi lama" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-slate-700">Kata Sandi Baru</label>
                        <input type="password" name="password" required placeholder="Minimal 8 karakter" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-slate-700">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" name="password_confirmation" required placeholder="Ulangi kata sandi baru" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </div>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" 
                        class="whitespace-nowrap w-full h-10 inline-flex items-center justify-center gap-1.5 py-2.5 px-4 rounded-xl bg-slate-800 text-white text-xs font-semibold hover:bg-slate-900 shadow-sm transition">
                    <i data-lucide="key-round" class="w-3.5 h-3.5"></i>
                    <span>Perbarui Kata Sandi</span>
                </button>
            </div>
        </form>

    </div>

    <!-- Logout Area -->
    <div class="p-5 rounded-2xl bg-white border border-rose-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h4 class="text-xs font-bold text-slate-900">Keluar dari Akun NesaCourt</h4>
            <p class="text-[11px] text-slate-500 mt-0.5">Sesi Anda di perangkat ini akan diakhiri dan keranjang belanja lokal akan dibersihkan.</p>
        </div>
        <a href="{{ route('logout') }}" class="whitespace-nowrap h-10 inline-flex items-center justify-center gap-1.5 py-2.5 px-4 rounded-xl text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition">
            <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
            <span>Keluar Akun</span>
        </a>
    </div>

</div>
@endsection
