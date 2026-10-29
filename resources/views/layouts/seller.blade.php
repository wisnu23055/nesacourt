<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Portal Stan Penjual') — NesaCourt</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
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
        [x-cloak] { display: none !important; }

        /* Custom Thin Scrollbar for Clean UI on Mobile & Tablet */
        .thin-scrollbar::-webkit-scrollbar {
            height: 4px;
            width: 4px;
        }
        .thin-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 9999px;
        }
        .thin-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        .thin-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        .thin-scrollbar::-webkit-scrollbar-button {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }
        .thin-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f1f5f9;
        }
    </style>
</head>
<body class="min-h-full text-slate-800 antialiased selection:bg-brand-100 selection:text-brand-700"
      x-data="{
          sidebarOpen: false,
          isStoreOpen: true,
          toggleStore() {
              this.isStoreOpen = !this.isStoreOpen;
              this.$nextTick(() => {
                  if (window.lucide) {
                      window.lucide.createIcons();
                  }
              });
          }
      }">

    <div class="min-h-screen flex flex-col bg-slate-50">
        <!-- Area Utama (Sidebar + Konten Halaman) -->
        <div class="flex-1 flex min-w-0">
            <!-- Desktop Sidebar Navigation (Kiri) -->
            <aside class="hidden lg:flex lg:flex-col lg:w-64 bg-white border-r border-slate-200 shrink-0 sticky top-0 self-start h-screen z-30">
            <!-- Brand Logo & Stan Portal Info -->
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <a href="{{ url('/penjual/dashboard') }}" class="flex items-center gap-2.5 group">
                    <div class="w-8 h-8 rounded-lg bg-brand-600 text-white flex items-center justify-center font-bold text-base shadow-sm shadow-brand-500/20 group-hover:bg-brand-700 transition shrink-0">
                        N
                    </div>
                    <div>
                        <span class="font-bold text-base text-slate-900 tracking-tight block leading-tight">NesaCourt</span>
                        <p class="text-[10px] text-brand-600 font-semibold">Portal Stan Penjual</p>
                    </div>
                </a>
            </div>

            <!-- Stan Status Toggle Card -->
            <div class="p-4 mx-4 my-3 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2.5">
                <div class="flex items-center justify-between">
                    <div class="min-w-0">
                        <span class="text-xs font-bold text-slate-900 block truncate">Stan 01 - Barokah</span>
                        <span class="text-[10px] text-slate-400 block truncate">Counter Barat No. 01</span>
                    </div>
                    <span class="relative flex h-2.5 w-2.5 shrink-0">
                        <span x-show="isStoreOpen" class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5" :class="isStoreOpen ? 'bg-emerald-500' : 'bg-rose-500'"></span>
                    </span>
                </div>
                <button type="button" 
                        @click="toggleStore()" 
                        class="w-full py-2 px-3 rounded-xl text-xs font-bold flex items-center justify-center gap-2 transition shadow-xs cursor-pointer select-none active:scale-[0.98]"
                        :class="isStoreOpen 
                            ? 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-emerald-600/25 border border-emerald-600' 
                            : 'bg-rose-600 hover:bg-rose-700 text-white shadow-rose-600/25 border border-rose-600'">
                    <i data-lucide="power" class="w-3.5 h-3.5 text-white shrink-0"></i>
                    <span x-text="isStoreOpen ? 'Status Stan: Buka' : 'Status Stan: Tutup'"></span>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 space-y-1 overflow-y-auto thin-scrollbar text-xs font-semibold">
                <a href="{{ url('/penjual/dashboard') }}" 
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->is('penjual/dashboard') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="layout-dashboard" class="w-4 h-4 text-slate-400 {{ request()->is('penjual/dashboard') ? '!text-brand-600' : '' }}"></i>
                        <span>Dashboard</span>
                    </div>
                </a>

                <a href="{{ url('/penjual/pesanan') }}" 
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->is('penjual/pesanan*') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="cooking-pot" class="w-4 h-4 text-slate-400 {{ request()->is('penjual/pesanan*') ? '!text-brand-600' : '' }}"></i>
                        <span>Pesanan Masuk</span>
                    </div>
                    <span class="text-xs font-bold text-amber-600">3</span>
                </a>

                <a href="{{ url('/penjual/menu') }}" 
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->is('penjual/menu*') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="utensils" class="w-4 h-4 text-slate-400 {{ request()->is('penjual/menu*') ? '!text-brand-600' : '' }}"></i>
                        <span>Kelola Menu</span>
                    </div>
                </a>

                <a href="{{ url('/penjual/stok') }}" 
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->is('penjual/stok*') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="layers" class="w-4 h-4 text-slate-400 {{ request()->is('penjual/stok*') ? '!text-brand-600' : '' }}"></i>
                        <span>Kelola Stok Porsi</span>
                    </div>
                </a>

                <a href="{{ url('/penjual/qris') }}" 
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->is('penjual/qris*') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="qr-code" class="w-4 h-4 text-slate-400 {{ request()->is('penjual/qris*') ? '!text-brand-600' : '' }}"></i>
                        <span>Pengaturan QRIS</span>
                    </div>
                </a>

                <a href="{{ url('/penjual/laporan') }}" 
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->is('penjual/laporan*') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="bar-chart-3" class="w-4 h-4 text-slate-400 {{ request()->is('penjual/laporan*') ? '!text-brand-600' : '' }}"></i>
                        <span>Riwayat Penjualan</span>
                    </div>
                </a>

                <a href="{{ url('/penjual/prediksi') }}" 
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->is('penjual/prediksi*') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="sparkles" class="w-4 h-4 text-slate-400 {{ request()->is('penjual/prediksi*') ? '!text-brand-600' : '' }}"></i>
                        <span>AI Forecasting</span>
                    </div>
                </a>

                <a href="{{ url('/penjual/profil') }}" 
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->is('penjual/profil*') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="store" class="w-4 h-4 text-slate-400 {{ request()->is('penjual/profil*') ? '!text-brand-600' : '' }}"></i>
                        <span>Profil Stan</span>
                    </div>
                </a>
            </nav>

            <!-- Bottom User Card in Sidebar -->
            <div class="p-4 border-t border-slate-100 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2.5 min-w-0">
                    <img src="https://images.unsplash.com/photo-1556157382-97eda2d62296?auto=format&fit=crop&w=120&q=80" 
                         alt="Foto Pemilik Stan" 
                         class="w-9 h-9 rounded-xl object-cover ring-1 ring-slate-200 shrink-0">
                    <div class="min-w-0">
                        <span class="text-xs font-bold text-slate-900 block truncate">Pak Bambang</span>
                        <span class="text-[10px] text-slate-400 block truncate">Pemilik Stan 01</span>
                    </div>
                </div>
                <a href="{{ route('logout') }}" class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition shrink-0" title="Keluar">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                </a>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Mobile Header -->
            <header class="lg:hidden w-full bg-white/95 backdrop-blur-md border-b border-slate-200 sticky top-0 z-30">
                <div class="px-4 h-16 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <button type="button" 
                                @click="sidebarOpen = true" 
                                class="p-2 rounded-xl text-slate-700 hover:bg-slate-100 hover:text-slate-900 border border-slate-200/80 bg-white shadow-2xs inline-flex items-center justify-center transition active:scale-95" 
                                aria-label="Buka Menu Sidebar">
                            <i data-lucide="menu" class="w-5 h-5"></i>
                        </button>
                        <a href="{{ url('/penjual/dashboard') }}" class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-brand-600 text-white flex items-center justify-center font-bold text-sm">
                                N
                            </div>
                            <span class="font-bold text-base text-slate-900">Stan Penjual</span>
                        </a>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ url('/penjual/profil') }}" class="w-8 h-8 rounded-xl overflow-hidden ring-1 ring-slate-200">
                            <img src="https://images.unsplash.com/photo-1556157382-97eda2d62296?auto=format&fit=crop&w=120&q=80" alt="Penjual" class="w-full h-full object-cover">
                        </a>
                    </div>
                </div>
            </header>

            <!-- Mobile Off-canvas Drawer Navigation -->
            <div x-show="sidebarOpen" 
                 x-cloak 
                 class="relative z-50 lg:hidden" 
                 role="dialog" 
                 aria-modal="true">
                <div x-show="sidebarOpen" 
                     x-transition:enter="transition-opacity ease-linear duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition-opacity ease-linear duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" 
                     @click="sidebarOpen = false"></div>

                <div class="fixed inset-0 flex z-40">
                    <div x-show="sidebarOpen" 
                         x-transition:enter="transition ease-in-out duration-300 transform"
                         x-transition:enter-start="-translate-x-full"
                         x-transition:enter-end="translate-x-0"
                         x-transition:leave="transition ease-in-out duration-300 transform"
                         x-transition:leave-start="translate-x-0"
                         x-transition:leave-end="-translate-x-full"
                         class="relative flex-1 flex flex-col max-w-xs w-full bg-white shadow-xl">
                        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-brand-600 text-white flex items-center justify-center font-bold">N</div>
                                <span class="font-bold text-base text-slate-900">Stan Penjual</span>
                            </div>
                            <button type="button" @click="sidebarOpen = false" class="p-2 rounded-xl text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition" aria-label="Tutup Menu Sidebar">
                                <i data-lucide="x" class="w-5 h-5"></i>
                            </button>
                        </div>

                        <!-- Stan Status Toggle Card Mobile -->
                        <div class="p-3.5 mx-4 mt-3 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
                            <div class="flex items-center justify-between">
                                <div class="min-w-0">
                                    <span class="text-xs font-bold text-slate-900 block truncate">Stan 01 - Barokah</span>
                                    <span class="text-[10px] text-slate-400 block truncate">Counter Barat No. 01</span>
                                </div>
                                <span class="relative flex h-2.5 w-2.5 shrink-0">
                                    <span x-show="isStoreOpen" class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2.5 w-2.5" :class="isStoreOpen ? 'bg-emerald-500' : 'bg-rose-500'"></span>
                                </span>
                            </div>
                            <button type="button" 
                                    @click="toggleStore()" 
                                    class="w-full py-2 px-3 rounded-xl text-xs font-bold flex items-center justify-center gap-2 transition shadow-xs cursor-pointer select-none active:scale-[0.98]"
                                    :class="isStoreOpen 
                                        ? 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-emerald-600/25 border border-emerald-600' 
                                        : 'bg-rose-600 hover:bg-rose-700 text-white shadow-rose-600/25 border border-rose-600'">
                                <i data-lucide="power" class="w-3.5 h-3.5 text-white shrink-0"></i>
                                <span x-text="isStoreOpen ? 'Status Stan: Buka' : 'Status Stan: Tutup'"></span>
                            </button>
                        </div>
                        <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto text-xs font-semibold">
                            <a href="{{ url('/penjual/dashboard') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition {{ request()->is('penjual/dashboard') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <i data-lucide="layout-dashboard" class="w-4 h-4 text-slate-400 {{ request()->is('penjual/dashboard') ? '!text-brand-600' : '' }}"></i>
                                <span>Dashboard</span>
                            </a>
                            <a href="{{ url('/penjual/pesanan') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl transition {{ request()->is('penjual/pesanan*') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <div class="flex items-center gap-2.5">
                                    <i data-lucide="cooking-pot" class="w-4 h-4 text-slate-400 {{ request()->is('penjual/pesanan*') ? '!text-brand-600' : '' }}"></i>
                                    <span>Pesanan Masuk</span>
                                </div>
                                <span class="text-xs font-bold text-amber-600">3</span>
                            </a>
                            <a href="{{ url('/penjual/menu') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition {{ request()->is('penjual/menu*') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <i data-lucide="utensils" class="w-4 h-4 text-slate-400 {{ request()->is('penjual/menu*') ? '!text-brand-600' : '' }}"></i>
                                <span>Kelola Menu</span>
                            </a>
                            <a href="{{ url('/penjual/stok') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition {{ request()->is('penjual/stok*') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <i data-lucide="layers" class="w-4 h-4 text-slate-400 {{ request()->is('penjual/stok*') ? '!text-brand-600' : '' }}"></i>
                                <span>Kelola Stok Porsi</span>
                            </a>
                            <a href="{{ url('/penjual/qris') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition {{ request()->is('penjual/qris*') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <i data-lucide="qr-code" class="w-4 h-4 text-slate-400 {{ request()->is('penjual/qris*') ? '!text-brand-600' : '' }}"></i>
                                <span>Pengaturan QRIS</span>
                            </a>
                            <a href="{{ url('/penjual/laporan') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition {{ request()->is('penjual/laporan*') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <i data-lucide="bar-chart-3" class="w-4 h-4 text-slate-400 {{ request()->is('penjual/laporan*') ? '!text-brand-600' : '' }}"></i>
                                <span>Riwayat Penjualan</span>
                            </a>
                            <a href="{{ url('/penjual/prediksi') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition {{ request()->is('penjual/prediksi*') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <i data-lucide="sparkles" class="w-4 h-4 text-slate-400 {{ request()->is('penjual/prediksi*') ? '!text-brand-600' : '' }}"></i>
                                <span>AI Demand Forecasting</span>
                            </a>
                            <a href="{{ url('/penjual/profil') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition {{ request()->is('penjual/profil*') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <i data-lucide="store" class="w-4 h-4 text-slate-400 {{ request()->is('penjual/profil*') ? '!text-brand-600' : '' }}"></i>
                                <span>Profil Stan</span>
                            </a>
                            <div class="pt-4 border-t border-slate-100">
                                <a href="{{ route('logout') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-rose-600 font-semibold hover:bg-rose-50 transition">
                                    <i data-lucide="log-out" class="w-4 h-4"></i>
                                    <span>Keluar Akun</span>
                                </a>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Page Main Content Slot (Full Width on Desktop) -->
            <main class="flex-1 w-full px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-6">
                @yield('content')
            </main>
        </div>
    </div>
</div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
</body>
</html>
