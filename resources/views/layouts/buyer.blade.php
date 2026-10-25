<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Kantin Mahasiswa') — NesaCourt</title>

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
      x-data="cartStore()"
      x-init="initCart()">

    <div class="min-h-screen flex flex-col bg-slate-50">
        <!-- Area Utama (Sidebar + Konten Halaman) -->
        <div class="flex-1 flex min-w-0">
            <!-- Desktop Sidebar Navigation (Kiri) - Desain Bersih Identik Penjual -->
            <aside class="hidden lg:flex lg:flex-col lg:w-64 bg-white border-r border-slate-200 shrink-0 sticky top-0 self-start h-screen z-30">
                <!-- Brand Logo & Portal Info -->
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <a href="{{ url('/dashboard') }}" class="flex items-center gap-2.5 group">
                        <div class="w-8 h-8 rounded-lg bg-brand-600 text-white flex items-center justify-center font-bold text-base shadow-sm shadow-brand-500/20 group-hover:bg-brand-700 transition shrink-0">
                            N
                        </div>
                        <div>
                            <span class="font-bold text-base text-slate-900 tracking-tight block leading-tight">NesaCourt</span>
                            <p class="text-[10px] text-brand-600 font-semibold">Portal Pembeli</p>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links (Tanpa Bungkus Container Label) -->
                <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto thin-scrollbar text-xs font-semibold">
                    <a href="{{ url('/dashboard') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->is('dashboard*') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="layout-dashboard" class="w-4 h-4 text-slate-400 {{ request()->is('dashboard*') ? '!text-brand-600' : '' }}"></i>
                            <span>Dashboard</span>
                        </div>
                    </a>

                    <a href="{{ url('/katalog') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->is('katalog*') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="utensils" class="w-4 h-4 text-slate-400 {{ request()->is('katalog*') ? '!text-brand-600' : '' }}"></i>
                            <span>Katalog Menu & Stan</span>
                        </div>
                    </a>

                    <a href="{{ url('/rekomendasi') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->is('rekomendasi*') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="sparkles" class="w-4 h-4 text-slate-400 {{ request()->is('rekomendasi*') ? '!text-brand-600' : '' }}"></i>
                            <span>Rekomendasi AI</span>
                        </div>
                        <span class="text-xs font-bold text-amber-600">Cerdas</span>
                    </a>

                    <button type="button" 
                            @click="drawerOpen = true" 
                            class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl transition text-slate-600 hover:bg-slate-100 hover:text-slate-900 cursor-pointer">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="shopping-bag" class="w-4 h-4 text-slate-400"></i>
                            <span>Keranjang Pesanan</span>
                        </div>
                        <span x-show="totalCount > 0" 
                              x-text="totalCount" 
                              class="text-xs font-bold text-brand-600">
                        </span>
                    </button>

                    <a href="{{ url('/pesanan/status') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->is('pesanan/status*') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="activity" class="w-4 h-4 text-slate-400 {{ request()->is('pesanan/status*') ? '!text-brand-600' : '' }}"></i>
                            <span>Status Pesanan</span>
                        </div>
                    </a>

                    <a href="{{ url('/riwayat') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->is('riwayat*') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="receipt" class="w-4 h-4 text-slate-400 {{ request()->is('riwayat*') ? '!text-brand-600' : '' }}"></i>
                            <span>Riwayat Transaksi</span>
                        </div>
                    </a>

                    <a href="{{ url('/profil') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->is('profil*') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="user" class="w-4 h-4 text-slate-400 {{ request()->is('profil*') ? '!text-brand-600' : '' }}"></i>
                            <span>Profil Mahasiswa</span>
                        </div>
                    </a>
                </nav>

                <!-- Bottom User Card in Sidebar -->
                <div class="p-4 border-t border-slate-100 flex items-center justify-between gap-3">
                    <a href="{{ url('/profil') }}" class="flex items-center gap-2.5 min-w-0 hover:opacity-80 transition">
                        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=120&q=80" 
                             alt="Foto Mahasiswa" 
                             class="w-9 h-9 rounded-xl object-cover ring-1 ring-slate-200 shrink-0">
                        <div class="min-w-0">
                            <span class="text-xs font-bold text-slate-900 block truncate">Aditya Pratama</span>
                            <span class="text-[10px] text-slate-400 block truncate">NIM: 23055310001</span>
                        </div>
                    </a>
                    <a href="{{ url('/login') }}" class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition shrink-0" title="Keluar">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                    </a>
                </div>
            </aside>

            <!-- Main Content Wrapper -->
            <div class="flex-1 flex flex-col min-w-0">
                <!-- Mobile Header - Hamburger Bersih Senada Penjual -->
                <header class="lg:hidden w-full bg-white/95 backdrop-blur-md border-b border-slate-200 sticky top-0 z-30">
                    <div class="px-4 h-16 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <button type="button" 
                                    @click="sidebarOpen = true" 
                                    class="p-2 rounded-xl text-slate-700 hover:bg-slate-100 hover:text-slate-900 border border-slate-200/80 bg-white shadow-2xs inline-flex items-center justify-center transition active:scale-95" 
                                    aria-label="Buka Menu Sidebar">
                                <i data-lucide="menu" class="w-5 h-5"></i>
                            </button>
                            <a href="{{ url('/dashboard') }}" class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-brand-600 text-white flex items-center justify-center font-bold text-sm">
                                    N
                                </div>
                                <span class="font-bold text-base text-slate-900">NesaCourt</span>
                            </a>
                        </div>

                        <div class="flex items-center gap-2">
                            <!-- Mobile Cart Button -->
                            <button type="button" 
                                    @click="drawerOpen = true" 
                                    class="relative p-2 rounded-xl text-slate-700 hover:bg-slate-100 transition"
                                    title="Lihat Keranjang">
                                <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                                <span x-show="totalCount > 0" 
                                      x-text="totalCount" 
                                      x-cloak
                                      class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-brand-600 text-white text-[10px] font-bold flex items-center justify-center shadow-sm">
                                </span>
                            </button>
                            <a href="{{ url('/profil') }}" class="w-8 h-8 rounded-xl overflow-hidden ring-1 ring-slate-200">
                                <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=120&q=80" alt="Mahasiswa" class="w-full h-full object-cover">
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
                                    <span class="font-bold text-base text-slate-900">Portal Pembeli</span>
                                </div>
                                <button type="button" @click="sidebarOpen = false" class="p-2 rounded-xl text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition" aria-label="Tutup Menu Sidebar">
                                    <i data-lucide="x" class="w-5 h-5"></i>
                                </button>
                            </div>

                            <!-- Mobile Drawer Nav Links -->
                            <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto text-xs font-semibold thin-scrollbar">
                                <a href="{{ url('/dashboard') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition {{ request()->is('dashboard*') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                    <i data-lucide="layout-dashboard" class="w-4 h-4 text-slate-400 {{ request()->is('dashboard*') ? '!text-brand-600' : '' }}"></i>
                                    <span>Dashboard</span>
                                </a>

                                <a href="{{ url('/katalog') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition {{ request()->is('katalog*') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                    <i data-lucide="utensils" class="w-4 h-4 text-slate-400 {{ request()->is('katalog*') ? '!text-brand-600' : '' }}"></i>
                                    <span>Katalog Menu & Stan</span>
                                </a>

                                <a href="{{ url('/rekomendasi') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl transition {{ request()->is('rekomendasi*') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                    <div class="flex items-center gap-2.5">
                                        <i data-lucide="sparkles" class="w-4 h-4 text-slate-400 {{ request()->is('rekomendasi*') ? '!text-brand-600' : '' }}"></i>
                                        <span>Rekomendasi AI</span>
                                    </div>
                                    <span class="text-xs font-bold text-amber-600">Cerdas</span>
                                </a>

                                <button type="button" 
                                        @click="sidebarOpen = false; drawerOpen = true"
                                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl transition text-slate-600 hover:bg-slate-100 hover:text-slate-900 cursor-pointer">
                                    <div class="flex items-center gap-2.5">
                                        <i data-lucide="shopping-bag" class="w-4 h-4 text-slate-400"></i>
                                        <span>Keranjang Pesanan</span>
                                    </div>
                                    <span x-show="totalCount > 0" 
                                          x-text="totalCount" 
                                          class="text-xs font-bold text-brand-600">
                                    </span>
                                </button>

                                <a href="{{ url('/pesanan/status') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition {{ request()->is('pesanan/status*') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                    <i data-lucide="activity" class="w-4 h-4 text-slate-400 {{ request()->is('pesanan/status*') ? '!text-brand-600' : '' }}"></i>
                                    <span>Status Pesanan</span>
                                </a>

                                <a href="{{ url('/riwayat') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition {{ request()->is('riwayat*') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                    <i data-lucide="receipt" class="w-4 h-4 text-slate-400 {{ request()->is('riwayat*') ? '!text-brand-600' : '' }}"></i>
                                    <span>Riwayat Transaksi</span>
                                </a>

                                <a href="{{ url('/profil') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition {{ request()->is('profil*') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                    <i data-lucide="user" class="w-4 h-4 text-slate-400 {{ request()->is('profil*') ? '!text-brand-600' : '' }}"></i>
                                    <span>Profil Mahasiswa</span>
                                </a>

                                <div class="pt-4 border-t border-slate-100">
                                    <a href="{{ url('/login') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-rose-600 font-semibold hover:bg-rose-50 transition">
                                        <i data-lucide="log-out" class="w-4 h-4"></i>
                                        <span>Keluar Akun</span>
                                    </a>
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- Page Main Content Slot -->
                <main class="flex-1 w-full px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-6">
                    @yield('content')
                </main>
            </div>
        </div>
    </div>

    <!-- Cart Slide-Over Drawer (MUNCUL DI SEBELAH KIRI) -->
    <div x-show="drawerOpen" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-hidden" 
         aria-labelledby="slide-over-title" 
         role="dialog" 
         aria-modal="true">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" 
             @click="drawerOpen = false"></div>

        <div class="pointer-events-none fixed inset-y-0 left-0 flex max-w-full pr-10">
            <div x-show="drawerOpen"
                 x-transition:enter="transform transition ease-in-out duration-300"
                 x-transition:enter-start="-translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transform transition ease-in-out duration-300"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="-translate-x-full"
                 class="pointer-events-auto w-screen max-w-md bg-white shadow-2xl flex flex-col justify-between z-50">
                <!-- Header -->
                <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i data-lucide="shopping-bag" class="w-5 h-5 text-slate-900"></i>
                        <h3 class="text-sm sm:text-base font-bold text-slate-900">Keranjang Pesanan</h3>
                    </div>
                    <button type="button" @click="drawerOpen = false" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Body Item List -->
                <div class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-3 thin-scrollbar">
                    <template x-if="items.length === 0">
                        <div class="text-center py-16 space-y-3">
                            <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                                <i data-lucide="shopping-bag" class="w-6 h-6"></i>
                            </div>
                            <p class="text-sm font-semibold text-slate-700">Keranjang Masih Kosong</p>
                            <p class="text-xs text-slate-400 max-w-xs mx-auto">Silakan jelajahi katalog menu atau rekomendasi AI untuk memesan makanan.</p>
                            <a href="{{ url('/katalog') }}" @click="drawerOpen = false" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-brand-600 bg-brand-50 hover:bg-brand-100 transition">
                                <span>Buka Katalog Menu</span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>
                    </template>

                    <template x-if="items.length > 0">
                        <div class="space-y-3">
                            <!-- Single-Seller Stall Banner -->
                            <div class="p-2.5 rounded-lg bg-brand-50 border border-brand-200 flex items-center gap-2 text-xs text-brand-800">
                                <i data-lucide="store" class="w-4 h-4 text-brand-600"></i>
                                <span>Stan: <strong x-text="currentSeller"></strong></span>
                            </div>

                            <!-- Item List -->
                            <template x-for="item in items" :key="item.id">
                                <div class="flex items-center justify-between p-3 rounded-xl border border-slate-100 bg-slate-50/50 gap-3">
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-xs font-bold text-slate-900 truncate" x-text="item.name"></h4>
                                        <p class="text-xs text-brand-600 font-semibold mt-0.5" x-text="formatRupiah(item.price)"></p>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <button type="button" @click="updateQty(item.id, -1)" class="w-6 h-6 rounded bg-white border border-slate-200 text-slate-600 flex items-center justify-center font-bold text-xs hover:bg-slate-100">-</button>
                                        <span class="text-xs font-bold w-4 text-center" x-text="item.qty"></span>
                                        <button type="button" @click="updateQty(item.id, 1)" class="w-6 h-6 rounded bg-white border border-slate-200 text-slate-600 flex items-center justify-center font-bold text-xs hover:bg-slate-100">+</button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- Footer Checkout Summary -->
                <div x-show="items.length > 0" class="p-4 sm:p-5 border-t border-slate-100 bg-white space-y-3">
                    <div class="flex items-center justify-between text-xs text-slate-600">
                        <span>Total Porsi</span>
                        <span class="font-bold" x-text="totalCount + ' Porsi'"></span>
                    </div>
                    <div class="flex items-center justify-between text-sm font-bold text-slate-900">
                        <span>Total Tagihan</span>
                        <span class="text-base text-brand-600" x-text="formatRupiah(subtotal)"></span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 pt-1">
                        <button type="button" @click="clearCart()" class="w-full py-2 px-3 rounded-lg text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 transition whitespace-nowrap">
                            Hapus Semua
                        </button>
                        <a href="{{ url('/checkout') }}" @click="drawerOpen = false" class="w-full inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-lg text-xs font-semibold text-white bg-brand-600 hover:bg-brand-700 shadow-sm transition whitespace-nowrap">
                            <span>Lanjut Checkout</span>
                            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Single Seller Warning Confirmation Modal -->
    <div x-show="showConflictModal" 
         x-cloak 
         class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white rounded-2xl max-w-sm w-full p-6 text-center space-y-4 shadow-2xl border border-slate-100">
            <div class="w-12 h-12 rounded-full bg-amber-100/80 border border-amber-200 text-amber-600 flex items-center justify-center mx-auto shadow-2xs">
                <i data-lucide="alert-circle" class="w-6 h-6"></i>
            </div>
            <div>
                <h4 class="text-base font-bold text-slate-900">Ganti Stan Kantin?</h4>
                <p class="text-xs text-slate-600 mt-1.5 leading-relaxed">
                    Satu pesanan hanya dapat berisi menu dari satu stan karena QRIS pembayaran berbeda. Menambahkan menu baru dari <strong class="text-slate-900" x-text="pendingItem?.seller"></strong> akan mengosongkan keranjang dari <strong class="text-slate-900" x-text="currentSeller"></strong>.
                </p>
            </div>
            <div class="flex items-center gap-2 pt-1">
                <button type="button" 
                        @click="showConflictModal = false; pendingItem = null" 
                        class="w-1/2 py-2.5 px-3 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition whitespace-nowrap">
                    Batal
                </button>
                <button type="button" 
                        @click="confirmReplaceSeller()" 
                        class="w-1/2 py-2.5 px-3 rounded-xl bg-amber-600 text-xs font-semibold text-white hover:bg-amber-700 transition whitespace-nowrap shadow-sm">
                    Ganti Stan
                </button>
            </div>
        </div>
    </div>

    <!-- Global Cart & Sidebar Script via Alpine.js -->
    <script>
        function cartStore() {
            return {
                sidebarOpen: false,
                drawerOpen: false,
                items: [],
                currentSeller: '',
                showConflictModal: false,
                pendingItem: null,

                initCart() {
                    const saved = localStorage.getItem('nesacourt_cart');
                    if (saved) {
                        try {
                            const parsed = JSON.parse(saved);
                            this.items = parsed.items || [];
                            this.currentSeller = parsed.currentSeller || '';
                        } catch (e) {
                            this.items = [];
                            this.currentSeller = '';
                        }
                    }

                    // Dummy data awal jika keranjang kosong
                    if (!this.items || this.items.length === 0) {
                        this.items = [
                            {
                                id: 101,
                                name: 'Ayam Geprek Sambal Bawang Level 3',
                                price: 13000,
                                seller: 'Stan 01 - Barokah Rasa',
                                qty: 1
                            },
                            {
                                id: 107,
                                name: 'Es Teh Manis Jumbo Vokasi',
                                price: 4000,
                                seller: 'Stan 01 - Barokah Rasa',
                                qty: 2
                            }
                        ];
                        this.currentSeller = 'Stan 01 - Barokah Rasa';
                        this.saveCart();
                    }
                },

                saveCart() {
                    localStorage.setItem('nesacourt_cart', JSON.stringify({
                        items: this.items,
                        currentSeller: this.currentSeller
                    }));
                },

                addToCart(item) {
                    // Proteksi Single-Seller
                    if (this.items.length > 0 && this.currentSeller && this.currentSeller !== item.seller) {
                        this.pendingItem = item;
                        this.showConflictModal = true;
                        return;
                    }

                    this.currentSeller = item.seller;
                    const existing = this.items.find(i => i.id === item.id);
                    if (existing) {
                        existing.qty += 1;
                    } else {
                        this.items.push({
                            id: item.id,
                            name: item.name,
                            price: item.price,
                            seller: item.seller,
                            qty: 1
                        });
                    }
                    this.saveCart();
                    this.drawerOpen = true;
                },

                confirmReplaceSeller() {
                    if (this.pendingItem) {
                        this.items = [{
                            id: this.pendingItem.id,
                            name: this.pendingItem.name,
                            price: this.pendingItem.price,
                            seller: this.pendingItem.seller,
                            qty: 1
                        }];
                        this.currentSeller = this.pendingItem.seller;
                        this.saveCart();
                        this.showConflictModal = false;
                        this.pendingItem = null;
                        this.drawerOpen = true;
                    }
                },

                updateQty(id, delta) {
                    const idx = this.items.findIndex(i => i.id === id);
                    if (idx !== -1) {
                        this.items[idx].qty += delta;
                        if (this.items[idx].qty <= 0) {
                            this.items.splice(idx, 1);
                        }
                    }
                    if (this.items.length === 0) {
                        this.currentSeller = '';
                    }
                    this.saveCart();
                },

                clearCart() {
                    this.items = [];
                    this.currentSeller = '';
                    this.saveCart();
                },

                get totalCount() {
                    return this.items.reduce((sum, i) => sum + i.qty, 0);
                },

                get subtotal() {
                    return this.items.reduce((sum, i) => sum + (i.price * i.qty), 0);
                },

                formatRupiah(number) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        maximumFractionDigits: 0
                    }).format(number);
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
</body>
</html>
