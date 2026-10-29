@extends('layouts.auth')

@section('title', 'Masuk ke Akun Anda')

@section('content')
<div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-slate-200 grid grid-cols-1 lg:grid-cols-12 min-h-[500px]">

    <!-- Left Side: Campus Identity & Informational -->
    <div class="lg:col-span-5 bg-slate-900 text-white p-6 sm:p-7 flex flex-col justify-between">
        <div>
            <!-- Campus Label -->
            <p class="text-xs font-semibold text-slate-400">Universitas Negeri Surabaya</p>

            <!-- Headline -->
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight mt-3 text-white">
                Sistem Pemesanan Food Court
            </h1>
            <p class="mt-2 text-slate-300 text-xs leading-relaxed">
                Silakan masuk menggunakan akun terdaftar Anda untuk memesan hidangan dari berbagai stan kantin kampus UNESA.
            </p>

            <!-- Photo of Canteen (Tersembunyi di Mobile, Hanya Tampil di Desktop) -->
            <div class="hidden lg:block mt-5 rounded-xl overflow-hidden border border-white/15 aspect-[16/10] relative">
                <img src="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=800&q=80" 
                     alt="Kantin Kampus UNESA" 
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
    <div class="lg:col-span-7 p-6 sm:p-7 flex flex-col justify-center bg-white" x-data="{ showPassword: false, loading: false }">
        <div class="max-w-sm w-full mx-auto">
            <!-- Form Header Title -->
            <div class="mb-5">
                <h2 class="text-xl font-bold text-slate-900 tracking-tight">
                    Masuk ke Akun Anda
                </h2>
                <p class="text-slate-500 text-xs mt-1">
                    Gunakan email kampus atau NIM Anda yang telah terdaftar.
                </p>
            </div>

            <!-- Form -->
            <form action="{{ url('/login') }}" method="POST" @submit="loading = true" class="space-y-3.5">
                @csrf

                @if ($errors->any())
                    <div class="p-3 rounded-lg bg-rose-50 border border-rose-200 text-xs text-rose-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                <!-- Input Email Kampus / NIM -->
                <div>
                    <label for="login" class="block text-xs font-semibold text-slate-700 mb-1">
                        Email Kampus atau NIM
                    </label>
                    <div class="relative rounded-lg shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="user" class="w-4 h-4"></i>
                        </div>
                        <input type="text" 
                               name="login" 
                               id="login" 
                               value="{{ old('login') }}"
                               required 
                               placeholder="nama@mhs.unesa.ac.id atau NIM" 
                               class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs sm:text-sm text-slate-900 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-brand-500 focus:border-brand-500 transition">
                    </div>
                </div>

                <!-- Input Kata Sandi -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="password" class="block text-xs font-semibold text-slate-700">
                            Kata Sandi
                        </label>
                        <a href="{{ url('/forgot-password') }}" class="text-xs text-brand-600 hover:text-brand-700 transition whitespace-nowrap">
                            Lupa kata sandi?
                        </a>
                    </div>
                    <div class="relative rounded-lg shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </div>
                        <input :type="showPassword ? 'text' : 'password'" 
                               name="password" 
                               id="password" 
                               required 
                               placeholder="Masukkan kata sandi" 
                               class="w-full pl-9 pr-9 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs sm:text-sm text-slate-900 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-brand-500 focus:border-brand-500 transition">
                        <button type="button" 
                                @click="showPassword = !showPassword" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
                            <i :data-lucide="showPassword ? 'eye-off' : 'eye'" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me Checkbox -->
                <div class="flex items-center justify-between pt-0.5">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="w-3.5 h-3.5 text-brand-600 rounded border-slate-300 focus:ring-brand-500">
                        <span class="text-xs text-slate-600 whitespace-nowrap">Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <!-- Submit Button (Single Line) -->
                <div class="pt-1">
                    <button type="submit" 
                            :disabled="loading" 
                            class="w-full h-10 inline-flex items-center justify-center gap-2 px-4 rounded-lg text-xs sm:text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 transition disabled:opacity-75 disabled:cursor-not-allowed whitespace-nowrap">
                        <template x-if="!loading">
                            <span class="inline-flex items-center gap-1.5 whitespace-nowrap">
                                <span>Masuk ke Akun</span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                            </span>
                        </template>
                        <template x-if="loading">
                            <span class="inline-flex items-center gap-1.5 whitespace-nowrap">
                                <svg class="animate-spin h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Memverifikasi...</span>
                            </span>
                        </template>
                    </button>
                </div>
            </form>

            <!-- SSO Divider -->
            <div class="relative my-4">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-slate-200"></div>
                </div>
                <div class="relative flex justify-center text-xs">
                    <span class="bg-white px-2 text-slate-400 whitespace-nowrap">atau</span>
                </div>
            </div>

            <!-- SSO UNESA Button (Tinggi Sama persis h-10) -->
            <button type="button" class="w-full h-10 inline-flex items-center justify-center gap-2 px-4 border border-slate-200 hover:border-slate-300 rounded-lg text-xs sm:text-sm font-medium text-slate-700 bg-white hover:bg-slate-50 transition shadow-sm whitespace-nowrap">
                <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                </svg>
                <span class="whitespace-nowrap">Masuk dengan SSO UNESA</span>
            </button>

            <!-- Bottom Register Link -->
            <p class="mt-5 text-center text-xs text-slate-500">
                Belum memiliki akun? 
                <a href="{{ url('/register') }}" class="font-semibold text-brand-600 hover:text-brand-700 transition whitespace-nowrap">
                    Daftar di sini
                </a>
            </p>
        </div>
    </div>
</div>
@endsection
