@extends('layouts.seller')

@section('title', 'Tambah / Edit Menu Stan — NesaCourt')

@section('content')
<div class="space-y-6" x-data="{
    isEdit: {{ $menu ? 'true' : 'false' }},
    formData: {
        id: '{{ $menu ? $menu->id : '' }}',
        name: '{{ $menu ? addslashes($menu->name) : '' }}',
        category: '{{ $menu && $menu->category ? $menu->category->slug : 'makanan' }}',
        price: '{{ $menu ? $menu->price : '' }}',
        stock: '{{ $menu ? $menu->stock : '20' }}',
        prepTime: '{{ $menu ? $menu->estimated_time_minutes : '10' }}',
        status: '{{ $menu && !$menu->is_available ? 'habis' : 'tersedia' }}',
        desc: '{{ $menu ? addslashes($menu->description) : '' }}',
        photoPreview: '{{ $menu ? $menu->image_url : '' }}'
    },
    handleFileSelect(event) {
        const file = event.target.files[0];
        if (file) {
            this.formData.photoPreview = URL.createObjectURL(file);
        }
    }
}">

    <!-- Page Header & Action Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-7 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                    <a href="{{ url('/penjual/dashboard') }}" class="hover:text-brand-600">Portal Stan</a>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-400"></i>
                    <a href="{{ url('/penjual/menu') }}" class="hover:text-brand-600">Kelola Menu</a>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-400"></i>
                    <span class="text-slate-800 font-semibold" x-text="isEdit ? 'Ubah Menu' : 'Tambah Menu'"></span>
                </div>
                <h1 class="text-xl sm:text-2xl font-semibold text-slate-900 tracking-tight" x-text="isEdit ? 'Ubah Informasi Menu' : 'Tambah Menu Baru'"></h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-2 max-w-xl">Lengkapi data menu masakan atau minuman untuk ditampilkan pada katalog pembeli mahasiswa.</p>
            </div>

            <!-- Buttons: On mobile displayed below description, on desktop aligned to the right -->
            <div class="flex items-center gap-2.5 shrink-0 pt-1 sm:pt-0">
                <a href="{{ url('/penjual/menu') }}" 
                   class="flex-1 sm:flex-none h-10 py-2.5 px-4 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                    <i data-lucide="arrow-left" class="w-4 h-4 text-slate-900"></i>
                    <span>Kembali ke Daftar Menu</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Success Toast Notification -->
    <div x-show="saveSuccess" 
         x-cloak 
         x-transition 
         class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2.5">
        <i data-lucide="check-circle-2" class="w-5 h-5 text-slate-900 shrink-0"></i>
        <span>Menu berhasil disimpan! Mengalihkan ke daftar menu stan...</span>
    </div>

    <!-- Form Grid -->
    <form action="{{ route('seller.menu.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @csrf
        <input type="hidden" name="id" :value="formData.id">
        <!-- Main Form Information (Left 2 cols) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Details Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs space-y-4">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                    <i data-lucide="file-text" class="w-4 h-4 text-slate-900"></i>
                    <h2 class="text-sm font-bold text-slate-900">Informasi Pokok Menu</h2>
                </div>

                <!-- Nama Menu -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700">Nama Menu Masakan / Minuman <span class="text-rose-500">*</span></label>
                    <input type="text" 
                           name="name"
                           x-model="formData.name" 
                           required 
                           placeholder="Contoh: Paket Nasi Ayam Geprek Sambal Bawang" 
                           class="w-full h-10 px-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                </div>

                <!-- Kategori & Estimasi Waktu -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700">Kategori Menu <span class="text-rose-500">*</span></label>
                        <select name="category" 
                                x-model="formData.category" 
                                required 
                                class="w-full h-10 px-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->slug }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700">Estimasi Masak / Siap Saji <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input type="number" 
                                   name="estimated_time_minutes"
                                   x-model="formData.prepTime" 
                                   min="1" 
                                   max="60" 
                                   required 
                                   placeholder="10" 
                                   class="w-full h-10 px-3.5 pr-14 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                            <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-medium">menit</span>
                        </div>
                    </div>
                </div>

                <!-- Harga & Stok Porsi Harian -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700">Harga Jual Satuan (Rp) <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-500">Rp</span>
                            <input type="number" 
                                   name="price"
                                   x-model="formData.price" 
                                   step="500" 
                                   min="1000" 
                                   required 
                                   placeholder="15000" 
                                   class="w-full h-10 pl-10 pr-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700">Stok Porsi Awal Hari Ini <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input type="number" 
                                   name="stock"
                                   x-model="formData.stock" 
                                   min="0" 
                                   max="500" 
                                   required 
                                   placeholder="25" 
                                   class="w-full h-10 px-3.5 pr-14 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                            <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-medium">porsi</span>
                        </div>
                    </div>
                </div>

                <!-- Deskripsi & Komposisi Menu -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700">Deskripsi & Isi Porsi Menu</label>
                    <textarea name="description"
                              x-model="formData.desc" 
                              rows="3" 
                              placeholder="Tuliskan komposisi menu (contoh: Nasi putih, ayam krispi pedas, lalapan timun segar dan sambal bawang ulek khas Stan 01)..." 
                              class="w-full p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition leading-relaxed"></textarea>
                    <p class="text-[11px] text-slate-400">Deskripsi yang informatif mempermudah mahasiswa memutuskan pesanan.</p>
                </div>
            </div>

            <!-- Status Jual Langsung (Tombol Radio Center Atas Bawah / Vertically Centered) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs space-y-3">
                <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                    <i data-lucide="toggle-right" class="w-4 h-4 text-slate-900"></i>
                    <h2 class="text-sm font-bold text-slate-900">Status Ketersediaan Awal</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    <label class="flex items-center gap-3 p-3.5 rounded-xl border cursor-pointer transition select-none"
                           :class="formData.status === 'tersedia' ? 'bg-emerald-50/50 border-emerald-300' : 'bg-slate-50 border-slate-200'">
                        <div class="shrink-0 flex items-center justify-center self-center">
                            <input type="radio" name="status" value="tersedia" x-model="formData.status" class="w-4 h-4 m-0 shrink-0 self-center text-brand-600 accent-brand-600 cursor-pointer">
                        </div>
                        <div class="min-w-0">
                            <span class="text-xs font-bold text-slate-900 block">Langsung Tersedia (Aktif)</span>
                            <span class="text-[11px] text-slate-500 block mt-0.5">Menu dapat langsung dipesan oleh mahasiswa di aplikasi.</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-3.5 rounded-xl border cursor-pointer transition select-none"
                           :class="formData.status === 'habis' ? 'bg-amber-50/50 border-amber-300' : 'bg-slate-50 border-slate-200'">
                        <div class="shrink-0 flex items-center justify-center self-center">
                            <input type="radio" name="status" value="habis" x-model="formData.status" class="w-4 h-4 m-0 shrink-0 self-center text-brand-600 accent-brand-600 cursor-pointer">
                        </div>
                        <div class="min-w-0">
                            <span class="text-xs font-bold text-slate-900 block">Porsi Habis Sementara</span>
                            <span class="text-[11px] text-slate-500 block mt-0.5">Tampil di katalog tetapi berlabel Habis sehingga tidak bisa dicheckout.</span>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Sidebar / Media Upload Card (Right 1 col) -->
        <div class="space-y-6">
            <!-- Upload Foto Menu Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs space-y-4">
                <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                    <i data-lucide="image" class="w-4 h-4 text-slate-900"></i>
                    <h2 class="text-sm font-bold text-slate-900">Foto Menu Masakan</h2>
                </div>

                <!-- Photo Preview Box -->
                <div class="relative w-full aspect-4/3 rounded-xl overflow-hidden bg-slate-100 border border-slate-200 flex items-center justify-center">
                    <template x-if="formData.photoPreview">
                        <img :src="formData.photoPreview" alt="Preview Foto Menu" class="w-full h-full object-cover">
                    </template>
                    <template x-if="!formData.photoPreview">
                        <div class="text-center p-4 text-slate-400">
                            <i data-lucide="camera" class="w-8 h-8 mx-auto mb-1.5 text-slate-900"></i>
                            <span class="text-xs font-medium block">Belum ada foto</span>
                            <span class="text-[10px] text-slate-400 block mt-0.5">Format JPG / PNG maks 2 MB</span>
                        </div>
                    </template>
                </div>

                <!-- File Input Trigger -->
                <div>
                    <label class="w-full h-10 py-2.5 px-4 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 cursor-pointer transition">
                        <i data-lucide="upload" class="w-4 h-4 text-slate-900"></i>
                        <span x-text="formData.photoPreview ? 'Ganti Foto Menu' : 'Pilih File Foto'"></span>
                        <input type="file" name="image" accept="image/*" @change="handleFileSelect($event)" class="hidden">
                        </label>
                        </div>

                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 text-[11px] text-slate-500 space-y-1">
                        <span class="font-bold text-slate-700 block">Saran Tampilan Foto:</span>
                        <p class="leading-relaxed">Gunakan foto asli hidangan dengan pencahayaan terang untuk menarik pembeli.</p>
                        </div>
                        </div>

                        <!-- Stand Counter Summary -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs space-y-3">
                        <div class="flex items-center gap-2">
                        <i data-lucide="store" class="w-4 h-4 text-slate-900"></i>
                        <h3 class="text-xs font-bold text-slate-900">Informasi Stan Penjual</h3>
                        </div>
                        <div class="space-y-1.5 text-xs text-slate-600">
                        <div class="flex justify-between">
                        <span class="text-slate-400">Nama Stan:</span>
                        <span class="font-bold text-slate-800">{{ $seller->stall_name }}</span>
                        </div>
                        <div class="flex justify-between">
                        <span class="text-slate-400">Lokasi:</span>
                        <span class="font-semibold text-slate-800">{{ $seller->location }}</span>
                        </div>
                        <div class="flex justify-between">
                        <span class="text-slate-400">Pemilik:</span>
                        <span class="font-semibold text-slate-800">{{ auth()->user()->name }}</span>
                        </div>
                        </div>
                        </div>

            <!-- Form Action Buttons -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs space-y-2.5">
                <button type="submit" 
                        class="w-full h-10 py-2.5 px-4 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-700 text-white shadow-sm shadow-brand-500/20 transition">
                    <i data-lucide="check" class="w-4 h-4 text-white"></i>
                    <span x-text="isEdit ? 'Simpan Perubahan Menu' : 'Terbitkan Menu Baru'"></span>
                </button>

                <a href="{{ url('/penjual/menu') }}" 
                   class="w-full h-10 py-2.5 px-4 rounded-xl text-xs font-semibold whitespace-nowrap inline-flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                    <span>Batal & Kembali</span>
                </a>
            </div>
        </div>
    </form>

</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });
</script>
@endsection
