{{-- ponytail: toast notifikasi flash session atomik, auto-dismiss, anti-slop, kontras WCAG AA --}}
@if (session('success') || session('error') || session('status_error') || session('warning') || session('info') || $errors->any())
<div class="fixed top-4 right-4 z-50 max-w-sm w-full space-y-2 pointer-events-none" x-data>
    @if (session('success'))
    <div x-data="{ show: true }"
         x-show="show"
         x-init="setTimeout(() => show = false, 4500)"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         class="pointer-events-auto flex items-start gap-3 p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200/90 text-emerald-900 shadow-sm"
         role="alert">
        <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5"></i>
        <div class="flex-1 text-xs leading-relaxed">
            <span class="font-bold block">Berhasil</span>
            <span class="text-emerald-800">{{ session('success') }}</span>
        </div>
        <button type="button" @click="show = false" class="text-emerald-500 hover:text-emerald-700 p-1 rounded-lg transition" aria-label="Tutup pemberitahuan">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    </div>
    @endif

    @if (session('error') || session('status_error'))
    <div x-data="{ show: true }"
         x-show="show"
         x-init="setTimeout(() => show = false, 5000)"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         class="pointer-events-auto flex items-start gap-3 p-3.5 rounded-2xl bg-rose-50 border border-rose-200/90 text-rose-900 shadow-sm"
         role="alert">
        <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0 mt-0.5"></i>
        <div class="flex-1 text-xs leading-relaxed">
            <span class="font-bold block">Perhatian</span>
            <span class="text-rose-800">{{ session('error') ?? session('status_error') }}</span>
        </div>
        <button type="button" @click="show = false" class="text-rose-500 hover:text-rose-700 p-1 rounded-lg transition" aria-label="Tutup pemberitahuan">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    </div>
    @endif

    @if (session('warning'))
    <div x-data="{ show: true }"
         x-show="show"
         x-init="setTimeout(() => show = false, 4500)"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         class="pointer-events-auto flex items-start gap-3 p-3.5 rounded-2xl bg-amber-50 border border-amber-200/90 text-amber-900 shadow-sm"
         role="alert">
        <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-600 shrink-0 mt-0.5"></i>
        <div class="flex-1 text-xs leading-relaxed">
            <span class="font-bold block">Peringatan</span>
            <span class="text-amber-800">{{ session('warning') }}</span>
        </div>
        <button type="button" @click="show = false" class="text-amber-500 hover:text-amber-700 p-1 rounded-lg transition" aria-label="Tutup pemberitahuan">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    </div>
    @endif

    @if ($errors->any())
    <div x-data="{ show: true }"
         x-show="show"
         x-init="setTimeout(() => show = false, 6000)"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         class="pointer-events-auto p-3.5 rounded-2xl bg-rose-50 border border-rose-200/90 text-rose-900 shadow-sm"
         role="alert">
        <div class="flex items-start gap-3">
            <i data-lucide="alert-octagon" class="w-5 h-5 text-rose-600 shrink-0 mt-0.5"></i>
            <div class="flex-1 text-xs">
                <span class="font-bold block mb-1">Ada kesalahan pengisian data:</span>
                <ul class="list-disc pl-4 space-y-0.5 text-rose-800">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" @click="show = false" class="text-rose-500 hover:text-rose-700 p-1 rounded-lg transition" aria-label="Tutup pemberitahuan">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    </div>
    @endif
</div>
@endif
