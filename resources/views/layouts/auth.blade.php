<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100/75">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Autentikasi') — NesaCourt Smart Food Court</title>

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
                        'soft': '0 20px 40px -15px rgba(15, 23, 42, 0.08)',
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
<body class="min-h-full flex flex-col justify-between text-slate-800 antialiased selection:bg-brand-100 selection:text-brand-700">

    <!-- Top Header Bar -->
    <header class="w-full bg-white/90 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-30">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-16 py-3 flex items-center">
            <!-- Brand Logo -->
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 group shrink-0">
                <div class="w-8 h-8 rounded-lg bg-brand-600 text-white flex items-center justify-center font-bold text-base shadow-sm shadow-brand-500/20 group-hover:bg-brand-700 transition shrink-0">
                    N
                </div>
                <div>
                    <span class="font-bold text-base sm:text-lg text-slate-900 tracking-tight block">NesaCourt</span>
                    <p class="text-[11px] text-slate-500 font-medium -mt-0.5">Food Court UNESA Ketintang</p>
                </div>
            </a>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 flex items-center justify-center p-3 sm:p-5 lg:p-6">
        <div class="w-full max-w-4xl">
            @yield('content')
        </div>
    </main>

    <!-- Main Footer (Identik dengan Landing Page) -->
    <footer class="bg-slate-950 text-slate-300 text-sm py-12 border-t border-slate-800 w-full mt-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <!-- Col 1: Brand -->
                <div class="space-y-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-brand-600 text-white flex items-center justify-center font-bold text-base shrink-0">
                            N
                        </div>
                        <span class="font-bold text-base text-white tracking-tight">NesaCourt</span>
                    </div>
                    <p class="text-slate-400 text-xs leading-relaxed">
                        Sistem Pemesanan dan Rekomendasi Makanan berbasis web untuk Food Court Universitas Negeri Surabaya (UNESA).
                    </p>
                </div>

                <!-- Col 2: Navigasi -->
                <div class="space-y-2.5">
                    <p class="font-semibold text-white text-xs">Navigasi Utama</p>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ url('/') }}#beranda" class="text-slate-300 hover:text-white transition whitespace-nowrap">Beranda</a></li>
                        <li><a href="{{ url('/') }}#menu" class="text-slate-300 hover:text-white transition whitespace-nowrap">Menu Favorit</a></li>
                        <li><a href="{{ url('/') }}#rekomendasi" class="text-slate-300 hover:text-white transition whitespace-nowrap">Rekomendasi Cerdas</a></li>
                        <li><a href="{{ url('/') }}#fitur" class="text-slate-300 hover:text-white transition whitespace-nowrap">Fitur Layanan</a></li>
                    </ul>
                </div>

                <!-- Col 3: Lokasi -->
                <div class="space-y-2.5">
                    <p class="font-semibold text-white text-xs">Lokasi Layanan</p>
                    <ul class="space-y-2 text-xs text-slate-300">
                        <li class="whitespace-nowrap">Kafetaria Kampus Ketintang</li>
                        <li class="whitespace-nowrap">Food Court Vokasi UNESA</li>
                        <li class="whitespace-nowrap">Kafetaria Kampus Lidah Wetan</li>
                    </ul>
                </div>

                <!-- Col 4: Akses Akun -->
                <div class="space-y-2.5">
                    <p class="font-semibold text-white text-xs">Akses Masuk</p>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ url('/login') }}" class="text-slate-300 hover:text-white transition whitespace-nowrap">Login Mahasiswa</a></li>
                        <li><a href="{{ url('/login') }}" class="text-slate-300 hover:text-white transition whitespace-nowrap">Portal Stan Penjual</a></li>
                        <li><a href="{{ url('/login') }}" class="text-slate-300 hover:text-white transition whitespace-nowrap">Login Admin Pengelola</a></li>
                    </ul>
                </div>
            </div>

            <!-- Copyright Line -->
            <div class="pt-6 border-t border-slate-900 flex flex-col md:flex-row items-center justify-between gap-3 text-center md:text-left text-xs text-slate-400">
                <p>© 2026 NesaCourt (Universitas Negeri Surabaya - Vokasi). Seluruh hak cipta dilindungi.</p>
                <div class="flex items-center justify-center gap-4 flex-wrap">
                    <a href="#" class="hover:text-white transition whitespace-nowrap">Kebijakan Privasi</a>
                    <span>|</span>
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
