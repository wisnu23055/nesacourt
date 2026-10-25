@extends('layouts.admin')

@section('title', 'Laporan Keuangan Kantin — NesaCourt')

@section('content')
<div class="space-y-6" x-data="{
    periodFilter: 'oktober_2026',
    stallFilter: 'semua',
    stallsRevenue: [
        {
            id: 1,
            name: 'Stan 01 - Barokah Rasa',
            location: 'Counter Barat No. 01',
            stallImage: 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=600&q=80',
            owner: 'Pak Bambang',
            ownerImage: 'https://images.unsplash.com/photo-1556157382-97eda2d62296?auto=format&fit=crop&w=120&q=80',
            transactions: 1040,
            grossRevenue: 15600000,
            feeRent: 1000000,
            rentStatus: 'Lunas Terverifikasi'
        },
        {
            id: 2,
            name: 'Stan 02 - Soto Bu Siti',
            location: 'Counter Barat No. 02',
            stallImage: 'https://images.unsplash.com/photo-1541832676-9b763b0239ab?auto=format&fit=crop&w=600&q=80',
            owner: 'Bu Siti Rahayu',
            ownerImage: 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=120&q=80',
            transactions: 780,
            grossRevenue: 11700000,
            feeRent: 1000000,
            rentStatus: 'Lunas Terverifikasi'
        },
        {
            id: 3,
            name: 'Stan 03 - Geprek Mas Bro',
            location: 'Counter Timur No. 01',
            stallImage: 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?auto=format&fit=crop&w=600&q=80',
            owner: 'Mas Hendra',
            ownerImage: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=120&q=80',
            transactions: 620,
            grossRevenue: 9920000,
            feeRent: 1000000,
            rentStatus: 'Lunas Terverifikasi'
        },
        {
            id: 4,
            name: 'Stan 04 - Aneka Jus Vokasi',
            location: 'Counter Timur No. 02',
            stallImage: 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?auto=format&fit=crop&w=600&q=80',
            owner: 'Bu Dian Fitri',
            ownerImage: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=120&q=80',
            transactions: 800,
            grossRevenue: 11530000,
            feeRent: 1000000,
            rentStatus: 'Lunas Terverifikasi'
        }
    ],

    formatRupiah(num) {
        return 'Rp ' + Number(num).toLocaleString('id-ID');
    },

    exportData(type) {
        alert('Mengunduh Laporan Keuangan Food Court format ' + type + '...');
    }
}">

    <!-- Page Title Header (Semi-Bold with Black Icon) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200/80">
        <div>
            <div class="flex items-center gap-2.5">
                <i data-lucide="bar-chart-3" class="w-6 h-6 text-slate-900 shrink-0"></i>
                <h1 class="text-xl sm:text-2xl font-semibold text-slate-900 tracking-tight">Laporan Keuangan Kantin</h1>
            </div>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Rekapitulasi perputaran omzet bruto, total transaksi, setoran retribusi sewa counter, dan laporan keuangan stan Food Court Ketintang.</p>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" 
                    @click="exportData('PDF')" 
                    class="whitespace-nowrap inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl border border-slate-200 bg-white text-slate-700 text-xs font-semibold hover:bg-slate-50 shadow-2xs transition">
                <i data-lucide="file-text" class="w-3.5 h-3.5 text-slate-900"></i>
                <span>Unduh PDF</span>
            </button>
            <button type="button" 
                    @click="exportData('Excel')" 
                    class="whitespace-nowrap inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl bg-slate-900 text-white text-xs font-semibold hover:bg-slate-800 shadow-sm transition">
                <i data-lucide="sheet" class="w-3.5 h-3.5 text-white"></i>
                <span>Ekspor Excel</span>
            </button>
        </div>
    </div>

    <!-- 4 Financial Stat Cards (Card Info Samain Desain Dashboard Penjual & Admin) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 shadow-xs">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <!-- Card 1: Omzet Bruto Keseluruhan -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Omzet Bruto Keseluruhan</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5">Rp 48.750.000</span>
                <span class="text-[10px] text-slate-500 block mt-0.5">+8.2% dari September 2026</span>
            </div>

            <!-- Card 2: Total Transaksi Selesai -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Total Transaksi Selesai</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5">3.240 Pesanan</span>
                <span class="text-[10px] text-slate-500 block mt-0.5">108 transaksi / hari</span>
            </div>

            <!-- Card 3: Penerimaan Sewa Counter -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Penerimaan Sewa Counter</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5">Rp 4.000.000</span>
                <span class="text-[10px] text-slate-500 block mt-0.5">100% Lunas (4 Stan)</span>
            </div>

            <!-- Card 4: Rata-rata Nilai Belanja -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Rata-rata Nilai Belanja</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5">Rp 15.046</span>
                <span class="text-[10px] text-slate-500 block mt-0.5">Ramah kantong mahasiswa</span>
            </div>
        </div>
    </div>

    <!-- Breakdown Table per Stan Kantin -->
    <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-xs">
        <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-1.5 sm:gap-4">
            <div class="flex items-center gap-2">
                <i data-lucide="layers" class="w-4 h-4 text-slate-900 shrink-0"></i>
                <h3 class="text-sm font-bold text-slate-900">Rincian Finansial & Penerimaan Tiap Stan Kantin</h3>
            </div>
            <span class="text-xs text-slate-500 pl-6 sm:pl-0">Periode: 01 - 25 Oktober 2026</span>
        </div>

        <div class="overflow-x-auto thin-scrollbar">
            <table class="w-full text-left text-xs border-collapse min-w-[780px]">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200/80 text-slate-500 font-semibold">
                        <th class="py-3 px-4 min-w-[230px] sm:min-w-[260px]">Stan Kantin</th>
                        <th class="py-3 px-4 min-w-[140px]">Pemilik Stan</th>
                        <th class="py-3 px-4 text-center min-w-[110px]">Volume Pesanan</th>
                        <th class="py-3 px-4 text-right min-w-[120px]">Omzet Bruto</th>
                        <th class="py-3 px-4 text-right min-w-[110px]">Sewa / Bulan</th>
                        <th class="py-3 px-4 text-center min-w-[130px]">Status Retribusi</th>
                        <th class="py-3 px-4 text-center min-w-[100px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <template x-for="item in stallsRevenue" :key="item.id">
                        <tr class="hover:bg-slate-50/50 transition">
                            <!-- Stan Info (Lebar Lega di Mobile & Desktop) -->
                            <td class="py-3.5 px-4 min-w-[230px] sm:min-w-[260px]">
                                <div class="flex items-center gap-3">
                                    <img :src="item.stallImage" :alt="item.name" class="w-12 h-12 rounded-xl object-cover ring-1 ring-slate-200 shrink-0">
                                    <div class="min-w-0">
                                        <span class="font-bold text-slate-900 block" x-text="item.name"></span>
                                        <span class="text-[11px] text-slate-400 block mt-0.5" x-text="item.location"></span>
                                    </div>
                                </div>
                            </td>

                            <!-- Owner Info -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2">
                                    <img :src="item.ownerImage" :alt="item.owner" class="w-7 h-7 rounded-full object-cover ring-1 ring-slate-200 shrink-0">
                                    <span class="font-medium text-slate-800" x-text="item.owner"></span>
                                </div>
                            </td>

                            <!-- Volume -->
                            <td class="py-3.5 px-4 text-center font-semibold text-slate-800" x-text="item.transactions + ' Porsi'"></td>

                            <!-- Omzet Bruto -->
                            <td class="py-3.5 px-4 text-right font-bold text-slate-900" x-text="formatRupiah(item.grossRevenue)"></td>

                            <!-- Fee -->
                            <td class="py-3.5 px-4 text-right font-semibold text-slate-700" x-text="formatRupiah(item.feeRent)"></td>

                            <!-- Status (Tanpa Container Pembungkus) -->
                            <td class="py-3.5 px-4 text-center">
                                <span class="text-xs font-semibold text-emerald-700" x-text="item.rentStatus"></span>
                            </td>

                            <!-- Action -->
                            <td class="py-3.5 px-4 text-center">
                                <button type="button" 
                                        @click="alert('Mengunduh rincian slip laporan ' + item.name)" 
                                        class="whitespace-nowrap px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 font-semibold text-[11px] text-slate-700 transition shadow-2xs">
                                    Slip Rekap
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
