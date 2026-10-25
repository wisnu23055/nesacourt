@extends('layouts.auth')

@section('title', 'Lupa Kata Sandi')

@section('content')
<div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-slate-200 grid grid-cols-1 lg:grid-cols-12 min-h-[500px]">

    <!-- Left Side: Campus Identity & Informational -->
    <div class="lg:col-span-5 bg-slate-900 text-white p-6 sm:p-7 flex flex-col justify-between">
        <div>
            <!-- Campus Label -->
            <p class="text-xs font-semibold text-slate-400">Universitas Negeri Surabaya</p>

            <!-- Headline -->
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight mt-3 text-white">
                Lupa Kata Sandi
            </h1>
            <p class="mt-2 text-slate-300 text-xs leading-relaxed">
                Ketikkan email kampus atau NIM akun terdaftar Anda untuk menerima tautan pemulihan kata sandi.
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
    <div class="lg:col-span-7 p-6 sm:p-7 flex flex-col justify-center bg-white" x-data="{ loading: false, sent: false }">
        <div class="max-w-sm w-full mx-auto">
            <!-- Header Title -->
            <div class="mb-5">
                <h2 class="text-xl font-bold text-slate-900 tracking-tight">
                    Pemulihan Kata Sandi
                </h2>
                <p class="text-slate-500 text-xs mt-1">
                    Silakan masukkan email kampus atau NIM akun yang Anda daftarkan.
                </p>
            </div>

            <!-- Form -->
            <form action="{{ url('/forgot-password') }}" method="POST" @submit.prevent="loading = true; setTimeout(() => { loading = false; sent = true }, 1000)" class="space-y-3.5">
                @csrf

                <!-- Input Email / NIM -->
                <div>
                    <label for="recovery_identifier" class="block text-xs font-semibold text-slate-700 mb-1">
                        Email Kampus atau NIM
                    </label>
                    <div class="relative rounded-lg shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="mail" class="w-4 h-4"></i>
                        </div>
                        <input type="text" 
                               name="recovery_identifier" 
                               id="recovery_identifier" 
                               required 
                               placeholder="nama@mhs.unesa.ac.id atau NIM" 
                               class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs sm:text-sm text-slate-900 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-brand-500 focus:border-brand-500 transition">
                    </div>
                </div>

                <!-- Alert state after sent -->
                <div x-show="sent" x-cloak class="p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-start gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5"></i>
                    <div>
                        <p class="font-bold">Tautan Terkirim</p>
                        <p class="mt-0.5 text-emerald-700">Periksa kotak masuk email kampus Anda untuk petunjuk pemulihan.</p>
                    </div>
                </div>

                <!-- Submit Button (Single Line) -->
                <div class="pt-1">
                    <button type="submit" 
                            :disabled="loading" 
                            class="w-full h-10 inline-flex items-center justify-center gap-2 px-4 rounded-lg text-xs sm:text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 transition disabled:opacity-75 disabled:cursor-not-allowed whitespace-nowrap">
                        <template x-if="!loading">
                            <span class="inline-flex items-center gap-1.5 whitespace-nowrap">
                                <span>Kirim Tautan Pemulihan</span>
                                <i data-lucide="send" class="w-3.5 h-3.5"></i>
                            </span>
                        </template>
                        <template x-if="loading">
                            <span class="inline-flex items-center gap-1.5 whitespace-nowrap">
                                <svg class="animate-spin h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Mengirim...</span>
                            </span>
                        </template>
                    </button>
                </div>
            </form>

            <!-- Back to Login Link -->
            <p class="mt-5 text-center text-xs text-slate-500">
                Ingat kata sandi Anda? 
                <a href="{{ url('/login') }}" class="font-semibold text-brand-600 hover:text-brand-700 transition whitespace-nowrap">
                    Masuk ke Akun
                </a>
            </p>
        </div>
    </div>
</div>
@endsection
