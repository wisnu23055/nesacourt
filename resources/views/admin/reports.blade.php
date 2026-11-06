@extends('layouts.admin')

@section('title', 'Laporan Keuangan Kantin — NesaCourt')

@section('content')
<style>
@media print {
    /* Setup Page A4 Landscape agar seluruh kolom terlihat penuh tanpa terpotong */
    @page {
        size: A4 landscape;
        margin: 10mm 12mm;
    }

    body, html {
        background: #ffffff !important;
        color: #0f172a !important;
        font-size: 10pt !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    /* Sembunyikan sidebar, navbar, tombol, filter dan kartu yang tidak diperlukan saat cetak */
    aside,
    header,
    nav,
    .print\:hidden,
    .no-print {
        display: none !important;
    }

    /* Content full width */
    main {
        padding: 0 !important;
        margin: 0 !important;
        max-width: 100% !important;
        width: 100% !important;
    }

    .print-card-reset {
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        margin: 0 !important;
        background: transparent !important;
        overflow: visible !important;
    }

    .thin-scrollbar {
        overflow: visible !important;
    }

    /* Tabel formal bersih & rapi untuk ekspor PDF */
    .print-table {
        width: 100% !important;
        min-width: 100% !important;
        max-width: 100% !important;
        border-collapse: collapse !important;
        font-size: 9pt !important;
        table-layout: auto !important;
    }

    .print-table th,
    .print-table td {
        border: 1px solid #cbd5e1 !important;
        padding: 7px 10px !important;
        color: #0f172a !important;
    }

    .print-table th {
        background-color: #f8fafc !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        font-size: 8pt !important;
        letter-spacing: 0.04em;
    }

    .print-table tr {
        page-break-inside: avoid !important;
    }

    tfoot.print-footer {
        display: table-footer-group !important;
    }
}
</style>

<div class="space-y-6" x-data="{
    stallsRevenue: {{ Js::from($stallsRevenue ?? []) }},

    formatRupiah(num) {
        return 'Rp ' + Number(num).toLocaleString('id-ID');
    },

    printPDF() {
        window.print();
    }
}">

    <!-- Kop Cetak Laporan Resmi (Tampil Khusus Saat Cetak / Unduh PDF) -->
    <div class="hidden print:block mb-5">
        <div class="flex items-center justify-between pb-3 border-b-2 border-slate-900">
            <div>
                <img src="{{ asset('images/logo/Logo.webp') }}" alt="Logo NesaCourt" class="h-12 w-auto object-contain">
            </div>
            <div class="text-right">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-900 block">Laporan Keuangan & Rekapitulasi Kasir Platform</span>
                <p class="text-xs text-slate-600 font-medium mt-0.5">Waktu Cetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
            </div>
        </div>

        <!-- Metadata Pengelola & Periode -->
        <div class="grid grid-cols-2 gap-4 mt-3 p-3 bg-slate-50 rounded-lg border border-slate-200 text-xs">
            <div class="space-y-1">
                <div class="flex"><span class="w-32 text-slate-500 font-medium">Lembaga / Kampus:</span> <span class="font-bold text-slate-900">Universitas Negeri Surabaya (UNESA)</span></div>
                <div class="flex"><span class="w-32 text-slate-500 font-medium">Lokasi:</span> <span class="font-semibold text-slate-800">Food Court Ketintang</span></div>
                <div class="flex"><span class="w-32 text-slate-500 font-medium">Petugas Pengelola:</span> <span class="font-medium text-slate-800">{{ auth()->user()->name ?? 'Kepala Pengelola Food Court' }}</span></div>
            </div>
            <div class="space-y-1 text-right">
                <div class="flex justify-end gap-2"><span class="text-slate-500 font-medium">Periode Rekap:</span> <span class="font-bold text-slate-900">{{ now()->translatedFormat('F Y') }}</span></div>
                <div class="flex justify-end gap-2"><span class="text-slate-500 font-medium">Total Stan Terdaftar:</span> <span class="font-bold text-slate-900">{{ count($stallsRevenue ?? []) }} Stan Kantin</span></div>
                <div class="flex justify-end gap-2"><span class="text-slate-500 font-medium">Status Retribusi:</span> <span class="font-bold text-emerald-700">100% Tertib / Lunas</span></div>
            </div>
        </div>
    </div>

    <!-- Page Title Header (Web Screen Only) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200/80 print:hidden">
        <div>
            <div class="flex items-center gap-2.5">
                <i data-lucide="bar-chart-3" class="w-6 h-6 text-slate-900 shrink-0"></i>
                <h1 class="text-xl sm:text-2xl font-semibold text-slate-900 tracking-tight">Laporan Keuangan Kantin</h1>
            </div>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Rekapitulasi perputaran omzet bruto, total transaksi, setoran retribusi sewa counter, dan laporan keuangan stan Food Court Ketintang.</p>
        </div>

        <div class="flex items-center gap-2">
            <!-- Tombol Unduh PDF -->
            <button type="button" 
                    @click="printPDF()" 
                    class="whitespace-nowrap inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl border border-slate-200 bg-white text-slate-700 text-xs font-semibold hover:bg-slate-50 shadow-2xs transition cursor-pointer">
                <i data-lucide="printer" class="w-3.5 h-3.5 text-slate-900"></i>
                <span>Unduh PDF</span>
            </button>

            <!-- Tombol Ekspor Excel -->
            <a href="{{ route('admin.reports', ['export' => 'excel']) }}" 
               class="whitespace-nowrap inline-flex items-center justify-center gap-1.5 py-2 px-3.5 rounded-xl bg-slate-900 text-white text-xs font-semibold hover:bg-slate-800 shadow-sm transition">
                <i data-lucide="sheet" class="w-3.5 h-3.5 text-white"></i>
                <span>Ekspor Excel</span>
            </a>
        </div>
    </div>

    <!-- 4 Financial Stat Cards (Web Screen Only) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 shadow-xs print:hidden">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <!-- Card 1: Omzet Bruto Keseluruhan -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Omzet Bruto Keseluruhan</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5">Rp {{ number_format($totalOmset ?? 0, 0, ',', '.') }}</span>
                <span class="text-[10px] text-slate-500 block mt-0.5">Akumulasi seluruh stan</span>
            </div>

            <!-- Card 2: Total Transaksi Selesai -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Total Transaksi Selesai</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5">{{ number_format($totalTransactions ?? 0, 0, ',', '.') }} Pesanan</span>
                <span class="text-[10px] text-slate-500 block mt-0.5">Volume pesanan valid</span>
            </div>

            <!-- Card 3: Penerimaan Sewa Counter -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Penerimaan Sewa Counter</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5">Rp {{ number_format($totalFee ?? 0, 0, ',', '.') }}</span>
                <span class="text-[10px] text-slate-500 block mt-0.5">100% Lunas ({{ count($stallsRevenue ?? []) }} Stan)</span>
            </div>

            <!-- Card 4: Rata-rata Nilai Belanja -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-[11px] font-medium text-slate-500 block">Rata-rata Nilai Belanja</span>
                <span class="text-lg font-semibold text-slate-900 block mt-0.5">Rp {{ number_format($totalTransactions ? round(($totalOmset ?? 0) / $totalTransactions) : 0, 0, ',', '.') }}</span>
                <span class="text-[10px] text-slate-500 block mt-0.5">Ramah kantong mahasiswa</span>
            </div>
        </div>
    </div>

    <!-- Breakdown Table per Stan Kantin -->
    <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-xs print-card-reset">
        <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-1.5 sm:gap-4 print:hidden">
            <div class="flex items-center gap-2">
                <i data-lucide="layers" class="w-4 h-4 text-slate-900 shrink-0"></i>
                <h3 class="text-sm font-bold text-slate-900">Rincian Finansial & Penerimaan Tiap Stan Kantin</h3>
            </div>
            <span class="text-xs text-slate-500 pl-6 sm:pl-0">Periode: {{ now()->translatedFormat('F Y') }}</span>
        </div>

        <div class="overflow-x-auto thin-scrollbar">
            <table class="w-full text-left text-xs border-collapse min-w-[780px] print-table">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200/80 text-slate-500 font-semibold">
                        <th class="py-3 px-3 hidden print:table-cell text-center w-10">No</th>
                        <th class="py-3 px-4 min-w-[230px] sm:min-w-[260px]">Stan Kantin</th>
                        <th class="py-3 px-4 min-w-[140px]">Pemilik Stan</th>
                        <th class="py-3 px-4 text-center min-w-[110px]">Volume Pesanan</th>
                        <th class="py-3 px-4 text-right min-w-[120px]">Omzet Bruto</th>
                        <th class="py-3 px-4 text-right min-w-[110px]">Sewa / Bulan</th>
                        <th class="py-3 px-4 text-center min-w-[130px]">Status Retribusi</th>
                        <th class="py-3 px-4 text-center min-w-[100px] print:hidden">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <template x-for="(item, index) in stallsRevenue" :key="item.id">
                        <tr class="hover:bg-slate-50/50 transition">
                            <!-- No (Khusus Cetak / PDF) -->
                            <td class="py-3.5 px-3 hidden print:table-cell text-center font-semibold text-slate-600" x-text="index + 1"></td>

                            <!-- Stan Info -->
                            <td class="py-3.5 px-4 min-w-[230px] sm:min-w-[260px]">
                                <div class="flex items-center gap-3">
                                    <img :src="item.stallImage" :alt="item.name" class="w-12 h-12 rounded-xl object-cover ring-1 ring-slate-200 shrink-0 print:hidden">
                                    <div class="min-w-0">
                                        <span class="font-bold text-slate-900 block" x-text="item.name"></span>
                                        <span class="text-[11px] text-slate-500 block mt-0.5" x-text="item.location"></span>
                                    </div>
                                </div>
                            </td>

                            <!-- Owner Info -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2">
                                    <img :src="item.ownerImage" :alt="item.owner" class="w-7 h-7 rounded-full object-cover ring-1 ring-slate-200 shrink-0 print:hidden">
                                    <span class="font-medium text-slate-800" x-text="item.owner"></span>
                                </div>
                            </td>

                            <!-- Volume -->
                            <td class="py-3.5 px-4 text-center font-semibold text-slate-800" x-text="item.transactions + ' Porsi'"></td>

                            <!-- Omzet Bruto -->
                            <td class="py-3.5 px-4 text-right font-bold text-slate-900" x-text="formatRupiah(item.grossRevenue)"></td>

                            <!-- Fee -->
                            <td class="py-3.5 px-4 text-right font-semibold text-slate-700" x-text="formatRupiah(item.feeRent)"></td>

                            <!-- Status Retribusi (Teks Bersih Tanpa Badge) -->
                            <td class="py-3.5 px-4 text-center">
                                <span class="text-xs font-semibold text-emerald-700" x-text="item.rentStatus"></span>
                            </td>

                            <!-- Action (Sembunyi Saat Cetak) -->
                            <td class="py-3.5 px-4 text-center print:hidden">
                                <a :href="'{{ url('/admin/pesanan') }}?stan=' + item.id" 
                                   class="whitespace-nowrap px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 font-semibold text-[11px] text-slate-700 transition shadow-2xs inline-block">
                                    Audit Stan
                                </a>
                            </td>
                        </tr>
                    </template>
                </tbody>
                <!-- Table Footer (Total Akumulasi Menyatu dengan Kolom Tabel) -->
                <tfoot class="border-t-2 border-b border-slate-300 bg-slate-50 font-bold text-slate-900 print-footer">
                    <tr>
                        <!-- No (Khusus Cetak) -->
                        <td class="hidden print:table-cell text-center py-3.5 px-3 font-semibold text-slate-400">-</td>

                        <!-- Stan Kantin -->
                        <td class="py-3.5 px-4 font-bold text-slate-900 text-xs">
                            Total Rekapitulasi:
                        </td>

                        <!-- Pemilik Stan -->
                        <td class="py-3.5 px-4 text-slate-500 font-semibold text-xs">
                            {{ count($stallsRevenue ?? []) }} Stan Kantin
                        </td>

                        <!-- Volume Pesanan -->
                        <td class="py-3.5 px-4 text-center text-xs font-bold text-slate-900 whitespace-nowrap">
                            {{ number_format($totalTransactions ?? 0, 0, ',', '.') }} Porsi
                        </td>

                        <!-- Omzet Bruto -->
                        <td class="py-3.5 px-4 text-right text-xs font-black text-slate-900 whitespace-nowrap">
                            Rp {{ number_format($totalOmset ?? 0, 0, ',', '.') }}
                        </td>

                        <!-- Sewa / Bulan -->
                        <td class="py-3.5 px-4 text-right text-xs font-bold text-slate-800 whitespace-nowrap">
                            Rp {{ number_format($totalFee ?? 0, 0, ',', '.') }}
                        </td>

                        <!-- Status Retribusi -->
                        <td class="py-3.5 px-4 text-center text-xs text-emerald-700 font-bold">
                            100% Lunas
                        </td>

                        <!-- Aksi (Sembunyi Saat Cetak) -->
                        <td class="py-3.5 px-4 text-center text-xs font-semibold text-slate-400 print:hidden">
                            -
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>
@endsection
