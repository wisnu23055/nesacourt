<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak - NesaCourt</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
                    colors: {
                        brand: {
                            50: '#eff6ff', 100: '#dbeafe', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8', 900: '#1e3a8a'
                        }
                    }
                }
            }
        }
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="min-h-full flex items-center justify-center p-4 sm:p-6 text-slate-800 antialiased selection:bg-brand-100 selection:text-brand-700">
    <!-- ponytail: error page 403 minimalis, anti-slop, kontras WCAG AA, responsive -->
    <div class="max-w-md w-full bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 text-center shadow-xs">
        <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mx-auto mb-5 ring-1 ring-rose-100">
            <i data-lucide="shield-alert" class="w-7 h-7"></i>
        </div>

        <span class="text-xs font-bold text-rose-600 uppercase tracking-wider block mb-1">Galat 403</span>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Akses Halaman Dibatasi</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-2 leading-relaxed">
            Akun Anda tidak memiliki izin untuk membuka halaman ini. Silakan masuk menggunakan akun dengan peran yang sesuai.
        </p>

        <div class="pt-6 mt-6 border-t border-slate-100 flex flex-col sm:flex-row gap-2.5 justify-center">
            <a href="{{ url('/') }}" class="inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-slate-900 text-white text-xs font-semibold hover:bg-slate-800 transition shadow-2xs">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Kembali ke Beranda</span>
            </a>
            <a href="{{ route('logout') }}" class="inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl border border-slate-200 bg-white text-slate-700 text-xs font-semibold hover:bg-slate-50 transition">
                <i data-lucide="log-out" class="w-4 h-4 text-slate-500"></i>
                <span>Ganti Akun</span>
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) { window.lucide.createIcons(); }
        });
    </script>
</body>
</html>
