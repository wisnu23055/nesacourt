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
<body class="min-h-full flex flex-col justify-between text-slate-800 antialiased selection:bg-brand-100 selection:text-brand-700 pb-16 md:pb-0"
      x-data="cartStore()"
      x-init="initCart()">

    <!-- Top Navigation Bar -->
    <header class="w-full bg-white/90 backdrop-blur-md border-b border-slate-200 sticky top-0 z-30">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="{{ url('/dashboard') }}" class="flex items-center gap-2.5 group shrink-0">
                <div class="w-8 h-8 rounded-lg bg-brand-600 text-white flex items-center justify-center font-bold text-base shadow-sm shadow-brand-500/20 group-hover:bg-brand-700 transition shrink-0">
                    N
                </div>
                <div>
                    <span class="font-bold text-base sm:text-lg text-slate-900 tracking-tight block">NesaCourt</span>
                    <p class="text-[10px] text-slate-500 font-medium -mt-0.5">Food Court UNESA</p>
                </div>
            </a>

            <!-- Desktop Nav Links (Tampil di Layar Lebar Desktop) -->
            <nav class="hidden lg:flex items-center gap-6 text-xs sm:text-sm font-semibold text-slate-600">
                <a href="{{ url('/dashboard') }}" class="hover:text-brand-600 transition whitespace-nowrap {{ request()->is('dashboard*') ? 'text-brand-600 font-bold' : '' }}">
                    Dashboard
                </a>
                <a href="{{ url('/katalog') }}" class="hover:text-brand-600 transition whitespace-nowrap {{ request()->is('katalog*') ? 'text-brand-600 font-bold' : '' }}">
                    Katalog Menu
                </a>
                <a href="{{ url('/rekomendasi') }}" class="hover:text-brand-600 transition whitespace-nowrap {{ request()->is('rekomendasi*') ? 'text-brand-600 font-bold' : '' }}">
                    Rekomendasi AI
                </a>
                <a href="{{ url('/riwayat') }}" class="hover:text-brand-600 transition whitespace-nowrap {{ request()->is('riwayat*') ? 'text-brand-600 font-bold' : '' }}">
                    Riwayat Pesanan
                </a>
            </nav>

            <!-- Right Header Actions -->
            <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                <!-- Cart Button with Counter -->
                <button type="button" 
                        @click="drawerOpen = true" 
                        class="relative p-2 rounded-xl text-slate-700 hover:text-brand-600 hover:bg-slate-100 transition"
                        title="Lihat Keranjang">
                    <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                    <span x-show="totalCount > 0" 
                          x-text="totalCount" 
                          x-cloak
                          class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-brand-600 text-white text-[10px] font-bold flex items-center justify-center shadow-sm">
                    </span>
                </button>

                <!-- Profile Menu -->
                <a href="{{ url('/profil') }}" class="flex items-center gap-2 pl-2 border-l border-slate-200 hover:opacity-80 transition">
                    <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=120&q=80" 
                         alt="Mahasiswa" 
                         class="w-7 h-7 rounded-full object-cover ring-1 ring-slate-200">
                    <div class="hidden sm:block text-left">
                        <span class="text-xs font-bold text-slate-900 block leading-tight">Aditya</span>
                        <span class="text-[10px] text-slate-500 block leading-tight">Mahasiswa UNESA</span>
                    </div>
                </a>

                <!-- Hamburger Button for Mobile & Tablet (< lg) -->
                <button type="button" 
                        @click="mobileNavOpen = !mobileNavOpen; $nextTick(() => lucide.createIcons())" 
                        class="lg:hidden p-2 rounded-xl text-slate-700 hover:text-brand-600 hover:bg-slate-100 transition shrink-0"
                        title="Menu Navigasi">
                    <i data-lucide="menu" class="w-5 h-5" x-show="!mobileNavOpen"></i>
                    <i data-lucide="x" class="w-5 h-5" x-show="mobileNavOpen" x-cloak></i>
                </button>
            </div>
        </div>

        <!-- Mobile & Tablet Dropdown Navigation Drawer -->
        <div x-show="mobileNavOpen" 
             x-cloak 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             @click.outside="mobileNavOpen = false"
             class="lg:hidden border-t border-slate-200 bg-white/95 backdrop-blur-md shadow-xl px-4 py-4 space-y-3">
            
            <!-- Student Mini Info -->
            <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
                <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=120&q=80" 
                     alt="Mahasiswa" 
                     class="w-9 h-9 rounded-full object-cover ring-1 ring-slate-200">
                <div class="min-w-0">
                    <span class="text-xs font-bold text-slate-900 block truncate">Aditya Pratama</span>
                    <span class="text-[11px] text-slate-500 block truncate">NIM: 23055310001 | Vokasi UNESA</span>
                </div>
            </div>

            <!-- Nav Links List (Tanpa Icon) -->
            <nav class="space-y-1 text-xs">
                <a href="{{ url('/dashboard') }}" @click="mobileNavOpen = false" 
                   class="block p-2.5 rounded-xl font-semibold transition {{ request()->is('dashboard*') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                    Dashboard Pembeli
                </a>

                <a href="{{ url('/katalog') }}" @click="mobileNavOpen = false" 
                   class="block p-2.5 rounded-xl font-semibold transition {{ request()->is('katalog*') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                    Katalog Menu & Stan
                </a>

                <a href="{{ url('/rekomendasi') }}" @click="mobileNavOpen = false" 
                   class="block p-2.5 rounded-xl font-semibold transition {{ request()->is('rekomendasi*') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                    Rekomendasi Menu Cerdas
                </a>

                <a href="{{ url('/riwayat') }}" @click="mobileNavOpen = false" 
                   class="block p-2.5 rounded-xl font-semibold transition {{ request()->is('riwayat*') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                    Riwayat Transaksi Pesanan
                </a>

                <a href="{{ url('/profil') }}" @click="mobileNavOpen = false" 
                   class="block p-2.5 rounded-xl font-semibold transition {{ request()->is('profil*') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                    Profil Mahasiswa
                </a>
            </nav>

            <!-- Bottom Action Row (Tanpa Icon) -->
            <div class="pt-2 border-t border-slate-100 flex items-center justify-between gap-2">
                <button type="button" 
                        @click="mobileNavOpen = false; drawerOpen = true" 
                        class="whitespace-nowrap flex-1 inline-flex items-center justify-center py-2 px-3 rounded-xl bg-brand-50 text-brand-700 text-xs font-bold hover:bg-brand-100 transition">
                    <span>Keranjang (<span x-text="totalCount">0</span>)</span>
                </button>
                <a href="{{ url('/login') }}" 
                   class="whitespace-nowrap inline-flex items-center justify-center py-2 px-4 rounded-xl border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-50 transition">
                    <span>Keluar</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 max-w-6xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @yield('content')
    </main>

    <!-- Cart Slide-Over Drawer -->
    <div x-show="drawerOpen" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-hidden" 
         aria-labelledby="slide-over-title" 
         role="dialog" 
         aria-modal="true">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" 
             @click="drawerOpen = false"></div>

        <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
            <div class="pointer-events-auto w-screen max-w-md bg-white shadow-2xl flex flex-col justify-between">
                <!-- Header -->
                <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i data-lucide="shopping-bag" class="w-5 h-5 text-brand-600"></i>
                        <h3 class="text-sm sm:text-base font-bold text-slate-900">Keranjang Pesanan</h3>
                    </div>
                    <button type="button" @click="drawerOpen = false" class="text-slate-400 hover:text-slate-600 p-1">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Body Item List -->
                <div class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-3">
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
                            <div class="p-2.5 rounded-lg bg-brand-50 border border-brand-200 flex items-center justify-between text-xs text-brand-800">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="store" class="w-4 h-4 text-brand-600"></i>
                                    <span>Stan: <strong x-text="currentSeller"></strong></span>
                                </div>
                                <span class="text-[10px] text-brand-600 bg-white px-2 py-0.5 rounded border border-brand-200">1 Transaksi QRIS</span>
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

    <!-- Mobile Bottom Navigation Bar -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200 py-2 px-4 flex items-center justify-around shadow-lg">
        <a href="{{ url('/dashboard') }}" class="flex flex-col items-center gap-1 text-[10px] font-medium {{ request()->is('dashboard*') ? 'text-brand-600 font-bold' : 'text-slate-500' }}">
            <i data-lucide="home" class="w-4 h-4"></i>
            <span>Beranda</span>
        </a>
        <a href="{{ url('/katalog') }}" class="flex flex-col items-center gap-1 text-[10px] font-medium {{ request()->is('katalog*') ? 'text-brand-600 font-bold' : 'text-slate-500' }}">
            <i data-lucide="utensils" class="w-4 h-4"></i>
            <span>Katalog</span>
        </a>
        <a href="{{ url('/rekomendasi') }}" class="flex flex-col items-center gap-1 text-[10px] font-medium {{ request()->is('rekomendasi*') ? 'text-brand-600 font-bold' : 'text-slate-500' }}">
            <i data-lucide="sparkles" class="w-4 h-4 text-amber-500"></i>
            <span>AI Saran</span>
        </a>
        <a href="{{ url('/riwayat') }}" class="flex flex-col items-center gap-1 text-[10px] font-medium {{ request()->is('riwayat*') ? 'text-brand-600 font-bold' : 'text-slate-500' }}">
            <i data-lucide="clock" class="w-4 h-4"></i>
            <span>Riwayat</span>
        </a>
        <a href="{{ url('/profil') }}" class="flex flex-col items-center gap-1 text-[10px] font-medium {{ request()->is('profil*') ? 'text-brand-600 font-bold' : 'text-slate-500' }}">
            <i data-lucide="user" class="w-4 h-4"></i>
            <span>Profil</span>
        </a>
    </nav>

    <!-- Main Footer Identik -->
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
                        <li><a href="{{ url('/dashboard') }}" class="text-slate-300 hover:text-white transition whitespace-nowrap">Dashboard</a></li>
                        <li><a href="{{ url('/katalog') }}" class="text-slate-300 hover:text-white transition whitespace-nowrap">Katalog Menu</a></li>
                        <li><a href="{{ url('/rekomendasi') }}" class="text-slate-300 hover:text-white transition whitespace-nowrap">Rekomendasi Cerdas AI</a></li>
                        <li><a href="{{ url('/riwayat') }}" class="text-slate-300 hover:text-white transition whitespace-nowrap">Riwayat Pesanan</a></li>
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
                    <p class="font-semibold text-white text-xs">Akses Akun</p>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ url('/profil') }}" class="text-slate-300 hover:text-white transition whitespace-nowrap">Pengaturan Akun Mahasiswa</a></li>
                        <li><a href="{{ url('/riwayat') }}" class="text-slate-300 hover:text-white transition whitespace-nowrap">Status Pembayaran QRIS</a></li>
                        <li><a href="{{ url('/') }}" class="text-slate-300 hover:text-white transition whitespace-nowrap">Halaman Depan Publik</a></li>
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

    <!-- Global Cart Script via Alpine.js -->
    <script>
        function cartStore() {
            return {
                mobileNavOpen: false,
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

                    // Dummy data sementara untuk pengujian desain keranjang pesanan
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
            lucide.createIcons();
        });
    </script>
</body>
</html>
