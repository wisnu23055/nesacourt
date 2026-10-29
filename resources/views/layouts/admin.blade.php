<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Pengelola Food Court') — NesaCourt</title>

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

        /* Hilangkan tombol panah scrollbar default browser Windows secara universal */
        ::-webkit-scrollbar-button {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }

        /* Scrollbar Super Tipis, Ramping & Elegan (3px) */
        .thin-scrollbar::-webkit-scrollbar {
            height: 3px;
            width: 3px;
        }
        .thin-scrollbar::-webkit-scrollbar-track {
            background: transparent;
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
            scrollbar-color: #cbd5e1 transparent;
        }

        /* Sembunyikan scrollbar sepenuhnya jika diperlukan */
        .scrollbar-none::-webkit-scrollbar,
        .no-scrollbar::-webkit-scrollbar {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }
        .scrollbar-none,
        .no-scrollbar {
            -ms-overflow-style: none !important;
            scrollbar-width: none !important;
        }
    </style>
</head>
<body class="min-h-full text-slate-800 antialiased selection:bg-brand-100 selection:text-brand-700"
      x-data="{
          sidebarOpen: false,
          adminNotificationCount: 2
      }">

    <div class="min-h-screen flex flex-col bg-slate-50">
        <!-- Area Utama (Sidebar + Konten Halaman) -->
        <div class="flex-1 flex min-w-0">
            <!-- Desktop Sidebar Navigation (Kiri) -->
            <aside class="hidden lg:flex lg:flex-col lg:w-64 bg-white border-r border-slate-200 shrink-0 sticky top-0 self-start h-screen z-30">
                <!-- Brand Logo & Portal Info -->
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <a href="{{ url('/admin/dashboard') }}" class="flex items-center gap-2.5 group">
                        <div class="w-8 h-8 rounded-lg bg-slate-900 text-white flex items-center justify-center font-bold text-base shadow-sm group-hover:bg-brand-600 transition shrink-0">
                            N
                        </div>
                        <div>
                            <span class="font-bold text-base text-slate-900 tracking-tight block leading-tight">NesaCourt</span>
                            <p class="text-[10px] text-brand-600 font-semibold">Portal Pengelola Kampus</p>
                        </div>
                    </a>
                </div>

                <!-- Food Court Status Header Badge -->
                <div class="p-3.5 mx-4 my-3 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1.5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-900 truncate">Food Court Ketintang</span>
                        <span class="relative flex h-2 w-2 shrink-0">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                    </div>
                    <p class="text-[10px] text-slate-500">Operasional Aktif (08.00 - 17.00 WIB)</p>
                </div>

                <!-- Navigation Links -->
                <nav class="flex-1 px-4 space-y-1 overflow-y-auto thin-scrollbar text-xs font-semibold">
                    <a href="{{ url('/admin/dashboard') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->is('admin/dashboard') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="layout-dashboard" class="w-4 h-4 text-slate-400 {{ request()->is('admin/dashboard') ? '!text-brand-600' : '' }}"></i>
                            <span>Dashboard Utama</span>
                        </div>
                    </a>

                    <a href="{{ url('/admin/stan') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->is('admin/stan*') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="store" class="w-4 h-4 text-slate-400 {{ request()->is('admin/stan*') ? '!text-brand-600' : '' }}"></i>
                            <span>Manajemen Stan</span>
                        </div>
                        <span class="text-[11px] font-semibold text-slate-500">4 Stan</span>
                    </a>

                    <a href="{{ url('/admin/menu') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->is('admin/menu*') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="utensils" class="w-4 h-4 text-slate-400 {{ request()->is('admin/menu*') ? '!text-brand-600' : '' }}"></i>
                            <span>Monitoring Menu</span>
                        </div>
                    </a>

                    <a href="{{ url('/admin/pesanan') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->is('admin/pesanan*') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="shopping-bag" class="w-4 h-4 text-slate-400 {{ request()->is('admin/pesanan*') ? '!text-brand-600' : '' }}"></i>
                            <span>Monitoring Pesanan</span>
                        </div>
                        <span class="text-xs font-bold text-amber-600">Live</span>
                    </a>

                    <a href="{{ url('/admin/laporan') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->is('admin/laporan*') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="bar-chart-3" class="w-4 h-4 text-slate-400 {{ request()->is('admin/laporan*') ? '!text-brand-600' : '' }}"></i>
                            <span>Laporan Keuangan</span>
                        </div>
                    </a>

                    <a href="{{ url('/admin/log') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->is('admin/log*') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="shield-alert" class="w-4 h-4 text-slate-400 {{ request()->is('admin/log*') ? '!text-brand-600' : '' }}"></i>
                            <span>Log Aktivitas Sistem</span>
                        </div>
                    </a>
                </nav>

                <!-- Bottom User Card in Sidebar -->
                <div class="p-4 border-t border-slate-100 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&w=120&q=80" 
                             alt="Foto Profil Admin" 
                             class="w-9 h-9 rounded-xl object-cover ring-1 ring-slate-200 shrink-0">
                        <div class="min-w-0">
                            <span class="text-xs font-bold text-slate-900 block truncate">Pak Eko Prasetyo</span>
                            <span class="text-[10px] text-slate-400 block truncate">Kepala Pengelola</span>
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
                            <a href="{{ url('/admin/dashboard') }}" class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-slate-900 text-white flex items-center justify-center font-bold text-sm">
                                    N
                                </div>
                                <span class="font-bold text-sm text-slate-900 tracking-tight">NesaCourt Admin</span>
                            </a>
                        </div>

                        <div class="flex items-center gap-2">
                            <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&w=120&q=80" 
                                 alt="Foto Profil Admin" 
                                 class="w-8 h-8 rounded-lg object-cover ring-1 ring-slate-200">
                        </div>
                    </div>
                </header>

                <!-- Mobile Drawer Navigation -->
                <div x-show="sidebarOpen" 
                     x-cloak 
                     class="fixed inset-0 z-50 lg:hidden" 
                     role="dialog" 
                     aria-modal="true">
                    <!-- Overlay Backdrop -->
                    <div x-show="sidebarOpen" 
                         x-transition:enter="transition-opacity ease-linear duration-300" 
                         x-transition:enter-start="opacity-0" 
                         x-transition:enter-end="opacity-100" 
                         x-transition:leave="transition-opacity ease-linear duration-300" 
                         x-transition:leave-start="opacity-100" 
                         x-transition:leave-end="opacity-0" 
                         @click="sidebarOpen = false" 
                         class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs"></div>

                    <!-- Drawer Panel -->
                    <div x-show="sidebarOpen" 
                         x-transition:enter="transition ease-in-out duration-300 transform" 
                         x-transition:enter-start="-translate-x-full" 
                         x-transition:enter-end="translate-x-0" 
                         x-transition:leave="transition ease-in-out duration-300 transform" 
                         x-transition:leave-start="translate-x-0" 
                         x-transition:leave-end="-translate-x-full" 
                         class="relative flex-1 flex flex-col max-w-xs w-full bg-white h-full shadow-2xl">
                        
                        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-slate-900 text-white flex items-center justify-center font-bold text-sm">
                                    N
                                </div>
                                <div>
                                    <span class="font-bold text-sm text-slate-900">NesaCourt Admin</span>
                                    <p class="text-[10px] text-brand-600 font-semibold">Pengelola Kampus</p>
                                </div>
                            </div>
                            <button type="button" 
                                    @click="sidebarOpen = false" 
                                    class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition">
                                <i data-lucide="x" class="w-5 h-5"></i>
                            </button>
                        </div>

                        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto text-xs font-semibold">
                            <a href="{{ url('/admin/dashboard') }}" 
                               @click="sidebarOpen = false" 
                               class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition {{ request()->is('admin/dashboard') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                                <span>Dashboard Utama</span>
                            </a>

                            <a href="{{ url('/admin/stan') }}" 
                               @click="sidebarOpen = false" 
                               class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition {{ request()->is('admin/stan*') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <i data-lucide="store" class="w-4 h-4"></i>
                                <span>Manajemen Stan</span>
                            </a>

                            <a href="{{ url('/admin/menu') }}" 
                               @click="sidebarOpen = false" 
                               class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition {{ request()->is('admin/menu*') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <i data-lucide="utensils" class="w-4 h-4"></i>
                                <span>Monitoring Menu</span>
                            </a>

                            <a href="{{ url('/admin/pesanan') }}" 
                               @click="sidebarOpen = false" 
                               class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition {{ request()->is('admin/pesanan*') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                                <span>Monitoring Pesanan</span>
                            </a>

                            <a href="{{ url('/admin/laporan') }}" 
                               @click="sidebarOpen = false" 
                               class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition {{ request()->is('admin/laporan*') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                                <span>Laporan Keuangan</span>
                            </a>

                            <a href="{{ url('/admin/log') }}" 
                               @click="sidebarOpen = false" 
                               class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition {{ request()->is('admin/log*') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <i data-lucide="shield-alert" class="w-4 h-4"></i>
                                <span>Log Aktivitas Sistem</span>
                            </a>
                        </nav>

                        <div class="p-4 border-t border-slate-100 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&w=120&q=80" 
                                     alt="Foto Profil Admin" 
                                     class="w-9 h-9 rounded-xl object-cover ring-1 ring-slate-200">
                                <div>
                                    <span class="text-xs font-bold text-slate-900 block">Pak Eko Prasetyo</span>
                                    <span class="text-[10px] text-slate-400 block">Kepala Pengelola</span>
                                </div>
                            </div>
                            <a href="{{ route('logout') }}" class="p-2 text-slate-400 hover:text-rose-600">
                                <i data-lucide="log-out" class="w-5 h-5"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Main Content Body -->
                <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
                    @yield('content')
                </main>
            </div>
        </div>
    </div>

    <!-- Initialize Lucide Icons -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
</body>
</html>
