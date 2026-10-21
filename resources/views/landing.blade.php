<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NesaCourt — Smart Food Court UNESA Ketintang</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN with Custom Theme -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                            950: '#0f172a',
                        }
                    },
                    boxShadow: {
                        'soft': '0 20px 40px -15px rgba(15, 23, 42, 0.07)',
                        'card': '0 25px 50px -12px rgba(37, 99, 235, 0.12)',
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        * {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-font-smoothing: antialiased;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-brand-100 selection:text-brand-700" x-data="{ mobileMenuOpen: false }">

    <!-- Top Announcement Bar -->
    <div class="bg-brand-700 text-white text-xs py-2 px-4 text-center font-medium shadow-sm">
        <div class="max-w-6xl mx-auto flex items-center justify-center gap-2 flex-wrap">
            <span class="px-2 py-0.5 rounded bg-brand-800 text-white font-semibold text-[11px] whitespace-nowrap">Informasi</span>
            <span>Layanan pemesanan digital Food Court UNESA Ketintang telah aktif.</span>
            <a href="{{ url('/login') }}" class="underline font-semibold hover:text-amber-300 ml-1 whitespace-nowrap">Masuk untuk Memesan</a>
        </div>
    </div>

    <!-- Navigation Header -->
    <header class="w-full bg-white/90 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-40">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 group shrink-0">
                <div class="w-9 h-9 rounded-xl bg-brand-600 text-white flex items-center justify-center font-bold text-lg shadow-sm shadow-brand-500/20 group-hover:bg-brand-700 transition shrink-0">
                    N
                </div>
                <div>
                    <span class="font-bold text-lg sm:text-xl text-slate-900 tracking-tight block">NesaCourt</span>
                    <p class="text-[11px] text-slate-500 font-medium -mt-0.5">Food Court UNESA Ketintang</p>
                </div>
            </a>

            <!-- Desktop Menu Links (Shown only on XL / Wide Screens) -->
            <nav class="hidden xl:flex items-center gap-8 text-sm font-semibold text-slate-600">
                <a href="#beranda" class="text-brand-600 transition whitespace-nowrap">Beranda</a>
                <a href="#menu" class="hover:text-brand-600 transition whitespace-nowrap">Menu Favorit</a>
                <a href="#fitur" class="hover:text-brand-600 transition whitespace-nowrap">Fitur Layanan</a>
                <a href="#rekomendasi" class="hover:text-brand-600 transition whitespace-nowrap">Rekomendasi Cerdas</a>
                <a href="#fasilitas" class="hover:text-brand-600 transition whitespace-nowrap">Fasilitas</a>
            </nav>

            <!-- Desktop Right CTA (Shown only on XL / Wide Screens) -->
            <div class="hidden xl:flex items-center gap-3 shrink-0">
                <a href="{{ url('/login') }}" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:text-brand-600 hover:bg-slate-100 transition whitespace-nowrap shrink-0">
                    Masuk
                </a>
                <a href="{{ url('/register') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 shadow-sm transition whitespace-nowrap shrink-0">
                    <span>Daftar Akun</span>
                    <i data-lucide="arrow-right" class="w-4 h-4 shrink-0"></i>
                </a>
            </div>

            <!-- Hamburger Button (Switches immediately on any screen width below XL) -->
            <button type="button" 
                    @click="mobileMenuOpen = !mobileMenuOpen" 
                    class="xl:hidden p-2 rounded-xl text-slate-600 hover:bg-slate-100 hover:text-slate-900 focus:outline-none shrink-0"
                    aria-label="Toggle navigation menu">
                <i :data-lucide="mobileMenuOpen ? 'x' : 'menu'" class="w-6 h-6"></i>
            </button>
        </div>

        <!-- Mobile / Tablet Dropdown Navigation -->
        <div x-show="mobileMenuOpen" 
             x-cloak 
             @click.away="mobileMenuOpen = false"
             class="xl:hidden bg-white border-b border-slate-200 px-5 pt-3 pb-6 space-y-3 shadow-xl">
            <a href="#beranda" @click="mobileMenuOpen = false" class="block py-2 text-sm font-semibold text-brand-600 whitespace-nowrap">Beranda</a>
            <a href="#menu" @click="mobileMenuOpen = false" class="block py-2 text-sm font-semibold text-slate-700 hover:text-brand-600 whitespace-nowrap">Menu Favorit</a>
            <a href="#fitur" @click="mobileMenuOpen = false" class="block py-2 text-sm font-semibold text-slate-700 hover:text-brand-600 whitespace-nowrap">Fitur Layanan</a>
            <a href="#rekomendasi" @click="mobileMenuOpen = false" class="block py-2 text-sm font-semibold text-slate-700 hover:text-brand-600 whitespace-nowrap">Rekomendasi Cerdas</a>
            <a href="#fasilitas" @click="mobileMenuOpen = false" class="block py-2 text-sm font-semibold text-slate-700 hover:text-brand-600 whitespace-nowrap">Fasilitas</a>
            <div class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row gap-2.5">
                <a href="{{ url('/login') }}" class="w-full text-center py-2.5 px-4 rounded-xl text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 whitespace-nowrap">
                    Masuk ke Akun
                </a>
                <a href="{{ url('/register') }}" class="w-full text-center py-2.5 px-4 rounded-xl text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 shadow-sm whitespace-nowrap">
                    Daftar Akun Baru
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="beranda" class="relative pt-6 sm:pt-10 pb-12 overflow-hidden bg-gradient-to-b from-white to-slate-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                
                <!-- Left Content -->
                <div class="lg:col-span-7 space-y-4 text-center lg:text-left">
                    <!-- Location Pill -->
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-brand-50 text-brand-700 border border-brand-200">
                        <span class="w-2 h-2 rounded-full bg-brand-600"></span>
                        <span>Food Court Digital UNESA Ketintang</span>
                    </div>

                    <!-- Main Headline -->
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-slate-900 tracking-tight leading-tight">
                        Makan Cerdas di Kampus, <br class="hidden sm:inline">
                        <span class="text-brand-600">Tanpa Ribet</span> Tanpa Antre
                    </h1>

                    <!-- Subtitle -->
                    <p class="text-xs sm:text-sm text-slate-600 max-w-lg mx-auto lg:mx-0 leading-relaxed">
                        Layanan pemesanan menu kantin terpadu, rekomendasi cerdas sesuai anggaran, dan pembayaran langsung melalui QRIS statis di masing-masing stan Food Court UNESA.
                    </p>

                    <!-- CTA Buttons (All Single Line) -->
                    <div class="pt-1 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-2.5">
                        <a href="{{ url('/login') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 shadow-sm transition whitespace-nowrap">
                            <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                            <span>Pesan Sekarang</span>
                        </a>
                        <a href="#rekomendasi" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 shadow-sm transition whitespace-nowrap">
                            <i data-lucide="sparkles" class="w-4 h-4 text-brand-600"></i>
                            <span>Lihat Rekomendasi Menu</span>
                        </a>
                    </div>

                    <!-- Social Proof / Rating -->
                    <div class="pt-3 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-3.5">
                        <div class="flex -space-x-2">
                            <img class="inline-block h-8 w-8 rounded-full ring-2 ring-white object-cover" src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=120&q=80" alt="Pengguna">
                            <img class="inline-block h-8 w-8 rounded-full ring-2 ring-white object-cover" src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=120&q=80" alt="Pengguna">
                            <img class="inline-block h-8 w-8 rounded-full ring-2 ring-white object-cover" src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=120&q=80" alt="Pengguna">
                        </div>
                        <div class="text-xs text-slate-500 text-center sm:text-left">
                            <div class="flex items-center justify-center sm:justify-start gap-1 text-slate-900 font-semibold">
                                <i data-lucide="star" class="w-3.5 h-3.5 fill-amber-400 text-amber-400"></i>
                                <span>Rating 4.9 dari 1.200 Mahasiswa</span>
                            </div>
                            <p class="text-slate-500 text-[11px] mt-0.5">Membantu efisiensi waktu istirahat perkuliahan</p>
                        </div>
                    </div>
                </div>

                <!-- Right Visual with Vector Icon Badges -->
                <div class="lg:col-span-5 relative">
                    <!-- Main Canteen Food Photo -->
                    <div class="relative rounded-2xl overflow-hidden shadow-lg border border-slate-200 aspect-[4/3] sm:aspect-[16/11]">
                        <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=1000&q=80" 
                             alt="Menu Makanan Kantin UNESA" 
                             class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/40 via-transparent to-transparent"></div>
                    </div>

                    <!-- Floating Badge 1: Estimasi Waktu -->
                    <div class="absolute -bottom-4 left-3 sm:-left-4 bg-white p-3 rounded-xl shadow-md border border-slate-100 flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center shrink-0">
                            <i data-lucide="clock" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-500 font-medium">Estimasi Siap</p>
                            <p class="text-xs font-bold text-slate-900">8 - 12 Menit</p>
                        </div>
                    </div>

                    <!-- Floating Badge 2: QRIS per Stan -->
                    <div class="absolute -top-3 right-3 sm:-right-3 bg-white px-3 py-2 rounded-xl shadow-md border border-slate-100 flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                            <i data-lucide="qr-code" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-500 font-medium">Metode Bayar</p>
                            <p class="text-xs font-bold text-slate-900">QRIS Statis Stan</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Stats Counter Bar -->
    <section class="py-8 bg-white border-y border-slate-200">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center divide-y md:divide-y-0 md:divide-x divide-slate-100">
                <div class="pt-3 md:pt-0">
                    <p class="text-2xl sm:text-3xl font-bold text-brand-600">24 Stan</p>
                    <p class="text-xs font-semibold text-slate-800 mt-0.5">Kuliner Aktif</p>
                    <p class="text-[11px] text-slate-500">Makanan dan Minuman</p>
                </div>
                <div class="pt-3 md:pt-0">
                    <p class="text-2xl sm:text-3xl font-bold text-brand-600">10.000+</p>
                    <p class="text-xs font-semibold text-slate-800 mt-0.5">Pesanan Terlayani</p>
                    <p class="text-[11px] text-slate-500">Sivitas Kampus UNESA</p>
                </div>
                <div class="pt-3 md:pt-0">
                    <p class="text-2xl sm:text-3xl font-bold text-brand-600">0 Menit</p>
                    <p class="text-xs font-semibold text-slate-800 mt-0.5">Antrean Kasir</p>
                    <p class="text-[11px] text-slate-500">Pesan dari Tempat Duduk</p>
                </div>
                <div class="pt-3 md:pt-0">
                    <p class="text-2xl sm:text-3xl font-bold text-brand-600">100%</p>
                    <p class="text-xs font-semibold text-slate-800 mt-0.5">Higienis & Terverifikasi</p>
                    <p class="text-[11px] text-slate-500">Standar Kantin Kampus</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section: Fitur Unggulan Khusus Mahasiswa -->
    <section id="fitur" class="py-16 bg-slate-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-xs font-semibold text-brand-600 bg-brand-50 px-3 py-1 rounded-full border border-brand-200">
                    Fitur Layanan
                </span>
                <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight mt-2.5">
                    Fitur Utama untuk Kemudahan Memesan
                </h2>
                <p class="text-sm text-slate-600 mt-1.5 leading-relaxed">
                    Dirancang untuk mempermudah pemilihan menu, efisiensi waktu, dan kenyamanan transaksi di food court.
                </p>
            </div>

            <!-- Grid 3 Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card 1 -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md transition">
                    <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center mb-4">
                        <i data-lucide="sparkles" class="w-5 h-5"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 mb-1.5">Smart Recommendation AI</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Sistem pencocokan menu berdasarkan anggaran belanja harian, preferensi rasa, dan estimasi waktu persiapan makanan.
                    </p>
                </div>

                <!-- Card 2 -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md transition">
                    <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center mb-4">
                        <i data-lucide="clock" class="w-5 h-5"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 mb-1.5">Pesan Langsung Tanpa Antre</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Pilih menu dari perangkat Anda, pantau status pengerjaan makanan, dan ambil langsung saat pesanan siap saji.
                    </p>
                </div>

                <!-- Card 3 -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md transition">
                    <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center mb-4">
                        <i data-lucide="qr-code" class="w-5 h-5"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 mb-1.5">Pembayaran QRIS Tiap Stan</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Setiap transaksi langsung terhubung ke kode QRIS statis stan yang dipilih dengan konfirmasi pembayaran oleh penjual.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Campus Ambience Section -->
    <section id="fasilitas" class="py-14 bg-white border-b border-slate-200">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                <!-- Text Left -->
                <div class="lg:col-span-6 space-y-4">
                    <span class="text-xs font-semibold text-brand-600 bg-brand-50 px-3 py-1 rounded-full border border-brand-200">
                        Area Food Court
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">
                        Ruang Santap dan Kolaborasi Mahasiswa
                    </h2>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Fasilitas kantin modern yang mendukung kenyamanan bersantap sekaligus ruang diskusi tugas kelompok dengan akses fasilitas penunjang belajar.
                    </p>
                    <div class="flex flex-wrap gap-2.5 pt-1">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-100 text-slate-700 whitespace-nowrap">
                            <i data-lucide="wifi" class="w-3.5 h-3.5 text-brand-600"></i> Free Wi-Fi Kampus
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-100 text-slate-700 whitespace-nowrap">
                            <i data-lucide="plug" class="w-3.5 h-3.5 text-brand-600"></i> Stop Kontak Meja
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-100 text-slate-700 whitespace-nowrap">
                            <i data-lucide="check-circle" class="w-3.5 h-3.5 text-brand-600"></i> Area Bersih dan Terawat
                        </span>
                    </div>
                </div>

                <!-- Image Right -->
                <div class="lg:col-span-6">
                    <div class="rounded-2xl overflow-hidden shadow-sm border border-slate-200">
                        <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1000&q=80" 
                             alt="Mahasiswa di Food Court UNESA" 
                             class="w-full h-72 object-cover">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Popular Menu Section -->
    <section id="menu" class="py-16 bg-slate-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-10 gap-3">
                <div>
                    <span class="text-xs font-semibold text-brand-600 bg-brand-50 px-3 py-1 rounded-full border border-brand-200">
                        Menu Favorit
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight mt-2">
                        Pilihan Menu Populer
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">Daftar hidangan yang banyak diminati di lingkungan kantin UNESA.</p>
                </div>
                <a href="{{ url('/login') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-600 hover:text-brand-700 whitespace-nowrap">
                    <span>Lihat Semua Menu</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>

            <!-- Menu Cards Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Food Item 1 -->
                <div class="bg-white rounded-2xl overflow-hidden border border-slate-200 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <div class="relative aspect-[4/3] overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?auto=format&fit=crop&w=600&q=80" 
                                 alt="Ayam Geprek Sambal Korek" 
                                 class="w-full h-full object-cover">
                            <span class="absolute top-2.5 right-2.5 px-2 py-0.5 rounded text-[11px] font-semibold bg-white/90 backdrop-blur-md text-slate-800 shadow-sm whitespace-nowrap">
                                Pedas Gurih
                            </span>
                        </div>
                        <div class="p-4">
                            <div class="flex items-center justify-between text-xs text-slate-400 mb-1">
                                <span>Stan Bu Titik</span>
                                <span class="flex items-center gap-1 text-slate-700 font-semibold">
                                    <i data-lucide="star" class="w-3.5 h-3.5 fill-amber-400 text-amber-400"></i> 4.9
                                </span>
                            </div>
                            <h4 class="font-bold text-sm text-slate-900">Ayam Geprek Sambal Korek</h4>
                            <p class="text-xs text-slate-500 mt-1">Ayam goreng krispi dengan racikan cabai rawit dan nasi putih.</p>
                        </div>
                    </div>
                    <div class="px-4 pb-4 pt-0 flex items-center justify-between border-t border-slate-100 mt-2">
                        <span class="text-sm font-bold text-brand-600">Rp15.000</span>
                        <a href="{{ url('/login') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-brand-600 bg-brand-50 hover:bg-brand-600 hover:text-white transition whitespace-nowrap">
                            Pesan
                        </a>
                    </div>
                </div>

                <!-- Food Item 2 -->
                <div class="bg-white rounded-2xl overflow-hidden border border-slate-200 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <div class="relative aspect-[4/3] overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=600&q=80" 
                                 alt="Mie Pangsit Jamur" 
                                 class="w-full h-full object-cover">
                            <span class="absolute top-2.5 right-2.5 px-2 py-0.5 rounded text-[11px] font-semibold bg-white/90 backdrop-blur-md text-slate-800 shadow-sm whitespace-nowrap">
                                Gurih Manis
                            </span>
                        </div>
                        <div class="p-4">
                            <div class="flex items-center justify-between text-xs text-slate-400 mb-1">
                                <span>Mie Noodle Bar</span>
                                <span class="flex items-center gap-1 text-slate-700 font-semibold">
                                    <i data-lucide="star" class="w-3.5 h-3.5 fill-amber-400 text-amber-400"></i> 4.8
                                </span>
                            </div>
                            <h4 class="font-bold text-sm text-slate-900">Mie Pangsit Jamur</h4>
                            <p class="text-xs text-slate-500 mt-1">Mie kenyal dengan potongan ayam manis, jamur kancing, dan kaldu.</p>
                        </div>
                    </div>
                    <div class="px-4 pb-4 pt-0 flex items-center justify-between border-t border-slate-100 mt-2">
                        <span class="text-sm font-bold text-brand-600">Rp13.000</span>
                        <a href="{{ url('/login') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-brand-600 bg-brand-50 hover:bg-brand-600 hover:text-white transition whitespace-nowrap">
                            Pesan
                        </a>
                    </div>
                </div>

                <!-- Food Item 3 -->
                <div class="bg-white rounded-2xl overflow-hidden border border-slate-200 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <div class="relative aspect-[4/3] overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=600&q=80" 
                                 alt="Rice Bowl Chicken Teriyaki" 
                                 class="w-full h-full object-cover">
                            <span class="absolute top-2.5 right-2.5 px-2 py-0.5 rounded text-[11px] font-semibold bg-white/90 backdrop-blur-md text-slate-800 shadow-sm whitespace-nowrap">
                                Saus Teriyaki
                            </span>
                        </div>
                        <div class="p-4">
                            <div class="flex items-center justify-between text-xs text-slate-400 mb-1">
                                <span>Bento Kampus</span>
                                <span class="flex items-center gap-1 text-slate-700 font-semibold">
                                    <i data-lucide="star" class="w-3.5 h-3.5 fill-amber-400 text-amber-400"></i> 4.9
                                </span>
                            </div>
                            <h4 class="font-bold text-sm text-slate-900">Rice Bowl Chicken Teriyaki</h4>
                            <p class="text-xs text-slate-500 mt-1">Potongan daging ayam bersaus teriyaki gurih manis dengan salad.</p>
                        </div>
                    </div>
                    <div class="px-4 pb-4 pt-0 flex items-center justify-between border-t border-slate-100 mt-2">
                        <span class="text-sm font-bold text-brand-600">Rp17.000</span>
                        <a href="{{ url('/login') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-brand-600 bg-brand-50 hover:bg-brand-600 hover:text-white transition whitespace-nowrap">
                            Pesan
                        </a>
                    </div>
                </div>

                <!-- Food Item 4 -->
                <div class="bg-white rounded-2xl overflow-hidden border border-slate-200 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <div class="relative aspect-[4/3] overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1556679343-c7306c1976bc?auto=format&fit=crop&w=600&q=80" 
                                 alt="Es Teh Manis Jumbo" 
                                 class="w-full h-full object-cover">
                            <span class="absolute top-2.5 right-2.5 px-2 py-0.5 rounded text-[11px] font-semibold bg-white/90 backdrop-blur-md text-slate-800 shadow-sm whitespace-nowrap">
                                Minuman Dingin
                            </span>
                        </div>
                        <div class="p-4">
                            <div class="flex items-center justify-between text-xs text-slate-400 mb-1">
                                <span>Stan Minuman Segar</span>
                                <span class="flex items-center gap-1 text-slate-700 font-semibold">
                                    <i data-lucide="star" class="w-3.5 h-3.5 fill-amber-400 text-amber-400"></i> 4.9
                                </span>
                            </div>
                            <h4 class="font-bold text-sm text-slate-900">Es Teh Manis Jumbo</h4>
                            <p class="text-xs text-slate-500 mt-1">Teh melati seduh segar dengan es kristal dalam gelas porsi besar.</p>
                        </div>
                    </div>
                    <div class="px-4 pb-4 pt-0 flex items-center justify-between border-t border-slate-100 mt-2">
                        <span class="text-sm font-bold text-brand-600">Rp4.000</span>
                        <a href="{{ url('/login') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-brand-600 bg-brand-50 hover:bg-brand-600 hover:text-white transition whitespace-nowrap">
                            Pesan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- AI Showcase Section -->
    <section id="rekomendasi" class="py-16 bg-slate-900 text-white relative overflow-hidden">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                <!-- Text Left -->
                <div class="lg:col-span-6 space-y-4">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-brand-500/20 text-brand-300 border border-brand-400/30">
                        <i data-lucide="sparkles" class="w-3.5 h-3.5 text-brand-400"></i>
                        Fitur Rekomendasi Pintar
                    </span>
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight leading-tight">
                        Rekomendasi Menu Sesuai Anggaran dan Waktu Anda
                    </h2>
                    <p class="text-slate-300 text-xs sm:text-sm leading-relaxed">
                        Tentukan batas anggaran belanja makan, pilihan rasa yang diinginkan, dan jeda waktu istirahat perkuliahan untuk mendapatkan saran menu yang tersedia secara tepat.
                    </p>

                    <!-- Filter Tags Pill -->
                    <div class="flex flex-wrap gap-2 pt-1">
                        <span class="px-2.5 py-1 rounded-lg text-xs font-medium bg-white/10 text-slate-200 border border-white/10 whitespace-nowrap">
                            Anggaran: &lt; Rp20.000
                        </span>
                        <span class="px-2.5 py-1 rounded-lg text-xs font-medium bg-white/10 text-slate-200 border border-white/10 whitespace-nowrap">
                            Rasa: Pedas Gurih
                        </span>
                        <span class="px-2.5 py-1 rounded-lg text-xs font-medium bg-white/10 text-slate-200 border border-white/10 whitespace-nowrap">
                            Waktu: &lt; 10 Menit
                        </span>
                    </div>

                    <div class="pt-2">
                        <a href="{{ url('/login') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-slate-900 bg-amber-400 hover:bg-amber-300 shadow-sm transition whitespace-nowrap">
                            <i data-lucide="sparkles" class="w-4 h-4"></i>
                            <span>Buka Rekomendasi Menu</span>
                        </a>
                    </div>
                </div>

                <!-- Card Right: AI Result Mockup -->
                <div class="lg:col-span-6">
                    <div class="bg-white/10 backdrop-blur-md border border-white/15 rounded-2xl p-5 sm:p-6 shadow-xl space-y-4">
                        <div class="flex items-center justify-between border-b border-white/10 pb-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-amber-400 text-slate-950 flex items-center justify-center font-bold shrink-0">
                                    <i data-lucide="sparkles" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <h4 class="text-xs sm:text-sm font-bold text-white">Contoh Hasil Rekomendasi</h4>
                                    <p class="text-[11px] text-slate-400">Tingkat kecocokan tinggi</p>
                                </div>
                            </div>
                            <span class="text-[11px] font-semibold text-emerald-300 bg-emerald-500/20 px-2 py-0.5 rounded border border-emerald-400/30 whitespace-nowrap">
                                Sesuai Kriteria
                            </span>
                        </div>

                        <!-- Sample Recommended Dish Card -->
                        <div class="bg-white/5 border border-white/10 rounded-xl p-3.5 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <img src="https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?auto=format&fit=crop&w=200&q=80" 
                                     alt="Ayam Geprek" 
                                     class="w-12 h-12 rounded-lg object-cover shrink-0">
                                <div>
                                    <h5 class="text-xs sm:text-sm font-semibold text-white">Paket Ayam Geprek Sambal Korek</h5>
                                    <p class="text-[11px] text-slate-300 mt-0.5">Waktu siap 8 menit • Stan Bu Titik</p>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-xs sm:text-sm font-bold text-amber-400">Rp15.000</p>
                                <span class="text-[10px] text-slate-400 whitespace-nowrap">Di Bawah Batas</span>
                            </div>
                        </div>

                        <div class="text-xs text-slate-300 bg-white/5 rounded-lg p-3 border border-white/5 flex items-start gap-2">
                            <i data-lucide="info" class="w-4 h-4 text-brand-400 shrink-0 mt-0.5"></i>
                            <span>Menu disarankan karena memenuhi batas biaya Rp20.000 dan waktu penyajian di bawah 10 menit.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bottom CTA Banner -->
    <section class="py-16 bg-white border-t border-slate-200">
        <div class="max-w-3xl mx-auto px-4 text-center space-y-4">
            <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">
                Mulai Memesan Makanan di NesaCourt
            </h2>
            <p class="text-xs sm:text-sm text-slate-600 max-w-lg mx-auto leading-relaxed">
                Gunakan akun mahasiswa UNESA Anda untuk memesan menu dari stan favorit tanpa perlu mengantre.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
                <a href="{{ url('/register') }}" class="w-full sm:w-auto px-6 py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 shadow-sm transition whitespace-nowrap">
                    Daftar Akun Mahasiswa
                </a>
                <a href="{{ url('/login') }}" class="w-full sm:w-auto px-6 py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 shadow-sm transition whitespace-nowrap">
                    Masuk ke Akun
                </a>
            </div>
        </div>
    </section>

    <!-- Main Footer -->
    <footer class="bg-slate-950 text-slate-300 text-sm py-16 border-t border-slate-800">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 mb-12">
                <!-- Col 1: Brand -->
                <div class="space-y-3.5">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-brand-600 text-white flex items-center justify-center font-bold text-lg shrink-0">
                            N
                        </div>
                        <span class="font-bold text-lg text-white tracking-tight">NesaCourt</span>
                    </div>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Sistem Pemesanan dan Rekomendasi Makanan cerdas berbasis web untuk Food Court Universitas Negeri Surabaya (UNESA).
                    </p>
                </div>

                <!-- Col 2: Navigasi -->
                <div class="space-y-3">
                    <p class="font-bold text-white text-sm">Navigasi Utama</p>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="#beranda" class="text-slate-300 hover:text-white transition whitespace-nowrap">Beranda</a></li>
                        <li><a href="#menu" class="text-slate-300 hover:text-white transition whitespace-nowrap">Menu Favorit</a></li>
                        <li><a href="#rekomendasi" class="text-slate-300 hover:text-white transition whitespace-nowrap">Rekomendasi Cerdas</a></li>
                        <li><a href="#fitur" class="text-slate-300 hover:text-white transition whitespace-nowrap">Fitur Layanan</a></li>
                    </ul>
                </div>

                <!-- Col 3: Lokasi -->
                <div class="space-y-3">
                    <p class="font-bold text-white text-sm">Lokasi Layanan</p>
                    <ul class="space-y-2.5 text-sm text-slate-300">
                        <li class="whitespace-nowrap">Kafetaria Kampus Ketintang</li>
                        <li class="whitespace-nowrap">Food Court Vokasi UNESA</li>
                        <li class="whitespace-nowrap">Kafetaria Kampus Lidah Wetan</li>
                    </ul>
                </div>

                <!-- Col 4: Akses Akun -->
                <div class="space-y-3">
                    <p class="font-bold text-white text-sm">Akses Masuk</p>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ url('/login') }}" class="text-slate-300 hover:text-white transition whitespace-nowrap">Login Mahasiswa</a></li>
                        <li><a href="{{ url('/login') }}" class="text-slate-300 hover:text-white transition whitespace-nowrap">Portal Stan Penjual</a></li>
                        <li><a href="{{ url('/login') }}" class="text-slate-300 hover:text-white transition whitespace-nowrap">Login Admin Pengelola</a></li>
                    </ul>
                </div>
            </div>

            <!-- Copyright Line -->
            <div class="pt-8 border-t border-slate-900 flex flex-col md:flex-row items-center justify-between gap-4 text-center md:text-left text-xs sm:text-sm text-slate-400">
                <p>© 2026 NesaCourt (Universitas Negeri Surabaya - Vokasi). Seluruh hak cipta dilindungi.</p>
                <div class="flex items-center justify-center gap-5 flex-wrap">
                    <a href="#" class="hover:text-white transition whitespace-nowrap">Kebijakan Privasi</a>
                    <span>•</span>
                    <a href="#" class="hover:text-white transition whitespace-nowrap">Syarat dan Ketentuan</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
</body>
</html>
