@extends('layouts.auth')

@section('title', 'Daftar sebagai Mahasiswa')

@section('content')
<div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-slate-200 grid grid-cols-1 lg:grid-cols-12 min-h-[520px]">

    <!-- Left Side: Campus Identity & Informational -->
    <div class="lg:col-span-5 bg-slate-900 text-white p-6 sm:p-7 flex flex-col justify-between">
        <div>
            <!-- Campus Label -->
            <p class="text-xs font-semibold text-slate-400">Universitas Negeri Surabaya</p>

            <!-- Headline -->
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight mt-3 text-white">
                Buat Akun NesaCourt
            </h1>
            <p class="mt-2 text-slate-300 text-xs leading-relaxed">
                Daftar akun untuk kemudahan memesan makanan dan mengakses layanan di Food Court UNESA.
            </p>

            <!-- Photo of Canteen (Tersembunyi di Mobile, Hanya Tampil di Desktop) -->
            <div class="hidden lg:block mt-5 rounded-xl overflow-hidden border border-white/15 aspect-[16/10] relative">
                <img src="https://images.unsplash.com/photo-1543007630-9710e4a00a20?auto=format&fit=crop&w=800&q=80" 
                     alt="Kantin Mahasiswa" 
                     class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent"></div>
                <div class="absolute bottom-2 left-2.5 right-2.5 text-[11px] text-slate-200">
                    Kafetaria Kampus Ketintang
                </div>
            </div>
        </div>

        <!-- Integrated Campus Footer -->
        <div class="pt-4 border-t border-white/10 flex items-center justify-between text-[11px] text-slate-400">
            <span>Sistem Terintegrasi UNESA</span>
            <span>Ketintang &amp; Lidah Wetan</span>
        </div>
    </div>

    <!-- Right Side: Form Container -->
    <div class="lg:col-span-7 p-6 sm:p-8 lg:p-10 flex flex-col justify-center" x-data="{ showPassword: false, showConfirmPassword: false, loading: false }">
        <div class="max-w-lg w-full mx-auto">
            <!-- Header Title -->
            <div class="mb-5">
                <h2 class="text-xl font-bold text-slate-900 tracking-tight">
                    Daftar sebagai Mahasiswa
                </h2>
                <p class="text-slate-500 text-xs mt-1">
                    Lengkapi data diri untuk membuat akun NesaCourt Anda.
                </p>
            </div>

            <!-- Form -->
            <form action="{{ url('/register') }}" method="POST" @submit="loading = true" class="space-y-3.5">
                @csrf

                @if ($errors->any())
                    <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-700">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Nama Lengkap -->
                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">
                        Nama Lengkap
                    </label>
                    <div class="relative rounded-xl shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="user" class="w-4 h-4"></i>
                        </div>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name') }}"
                               required 
                               placeholder="Nama lengkap sesuai KTM" 
                               class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-600 transition">
                    </div>
                </div>

                <!-- NIM & Email Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <!-- NIM Mahasiswa -->
                    <div>
                        <label for="nim" class="block text-xs font-semibold text-slate-700 mb-1">
                            NIM Mahasiswa
                        </label>
                        <div class="relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i data-lucide="credit-card" class="w-4 h-4"></i>
                            </div>
                            <input type="text" 
                                   name="nim" 
                                   id="nim" 
                                   value="{{ old('nim') }}"
                                   required 
                                   placeholder="11 digit NIM" 
                                   class="w-full pl-10 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-600 transition">
                        </div>
                    </div>

                    <!-- Email Kampus / Aktif -->
                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">
                            Email Kampus / Aktif
                        </label>
                        <div class="relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i data-lucide="mail" class="w-4 h-4"></i>
                            </div>
                            <input type="email" 
                                   name="email" 
                                   id="email" 
                                   value="{{ old('email') }}"
                                   required 
                                   placeholder="nama@mhs.unesa.ac.id" 
                                   class="w-full pl-10 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-600 transition">
                        </div>
                    </div>
                </div>

                <!-- Kata Sandi -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">
                        Kata Sandi
                    </label>
                    <div class="relative rounded-xl shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </div>
                        <input :type="showPassword ? 'text' : 'password'" 
                               name="password" 
                               id="password" 
                               required 
                               placeholder="Minimal 8 karakter" 
                               class="w-full pl-10 pr-10 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-600 transition">
                        <button type="button" 
                                @click="showPassword = !showPassword" 
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
                            <i :data-lucide="showPassword ? 'eye-off' : 'eye'" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- Konfirmasi Kata Sandi -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1">
                        Konfirmasi Kata Sandi
                    </label>
                    <div class="relative rounded-xl shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="shield-check" class="w-4 h-4"></i>
                        </div>
                        <input :type="showConfirmPassword ? 'text' : 'password'" 
                               name="password_confirmation" 
                               id="password_confirmation" 
                               required 
                               placeholder="Ketik ulang kata sandi" 
                               class="w-full pl-10 pr-10 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-600 transition">
                        <button type="button" 
                                @click="showConfirmPassword = !showConfirmPassword" 
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
                            <i :data-lucide="showConfirmPassword ? 'eye-off' : 'eye'" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- Persetujuan Syarat & Ketentuan -->
                <div class="pt-0.5">
                    <label class="flex items-start gap-2 cursor-pointer select-none">
                        <input type="checkbox" required name="terms" class="mt-0.5 w-4 h-4 text-brand-600 rounded border-slate-300 focus:ring-brand-500/30">
                        <span class="text-xs text-slate-600 leading-normal">
                            Saya menyetujui <a href="#" class="font-semibold text-brand-600 hover:underline whitespace-nowrap">Syarat dan Ketentuan</a> penggunaan NESAFOOD.
                        </span>
                    </label>
                </div>

                <!-- Submit Button (Single Line) -->
                <div class="pt-2">
                    <button type="submit" 
                            :disabled="loading" 
                            class="w-full h-10 inline-flex items-center justify-center gap-2 px-4 rounded-lg text-xs sm:text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 transition disabled:opacity-75 disabled:cursor-not-allowed whitespace-nowrap">
                        <template x-if="!loading">
                            <span class="inline-flex items-center gap-1.5 whitespace-nowrap">
                                <span>Daftar sebagai Mahasiswa</span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                            </span>
                        </template>
                        <template x-if="loading">
                            <span class="inline-flex items-center gap-1.5 whitespace-nowrap">
                                <svg class="animate-spin h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Membuat Akun...</span>
                            </span>
                        </template>
                    </button>
                </div>
            </form>

            <!-- Bottom Login Link -->
            <p class="mt-5 text-center text-xs text-slate-500">
                Sudah memiliki akun? 
                <a href="{{ url('/login') }}" class="font-semibold text-brand-600 hover:text-brand-700 transition whitespace-nowrap">
                    Masuk ke Akun
                </a>
            </p>
        </div>
    </div>
</div>
@endsection
