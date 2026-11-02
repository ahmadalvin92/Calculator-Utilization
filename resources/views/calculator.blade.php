<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>DASHCAM - Kalkulator Utilisasi Kapasitas Resource | Bank Mandiri</title>
    
    <!-- Google Fonts & Tailwind Assets -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        [x-cloak] { display: none !important; }
        
        /* Mandiri Colors */
        :root {
            --mandiri-blue: #003a70;
            --mandiri-dark: #002244;
            --mandiri-gold: #ffb700;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #0f172a;
        }
        ::-webkit-scrollbar-thumb {
            background: #ffb700;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #e6a500;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                color: black !important;
            }
            .print-card {
                background: white !important;
                border: 1px solid #cbd5e1 !important;
                color: black !important;
                box-shadow: none !important;
            }
        }
    </style>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 relative selection:bg-[#ffb700] selection:text-slate-950"
      x-data="calculatorApp({{ Js::from($scenario) }}, {{ Js::from($scenario->items) }})"
      x-init="initApp()"
      x-cloak>

    <!-- Background Image: Foto Gedung Livin' by Mandiri (Jelas Terlihat & Berkelas) -->
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
        <picture>
            <source srcset="{{ asset('images/bg-livin.webp') }}" type="image/webp">
            <img src="{{ asset('images/bg-tower.jpg') }}" alt="Gedung Livin by Mandiri" 
                 class="w-full h-full object-cover object-top opacity-75 filter brightness-85 contrast-105 scale-100">
        </picture>
        <div class="absolute inset-0 bg-gradient-to-b from-slate-950/60 via-slate-900/35 to-slate-950/80"></div>
    </div>

    <!-- Header Navigation (Mandiri Corporate IT Portal) -->
    <header class="relative z-20 border-b border-white/20 backdrop-blur-md bg-[#002244]/85 sticky top-0 no-print shadow-2xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            
            <!-- Left: Bank Mandiri Logo & App Title -->
            <div class="flex items-center gap-3 sm:gap-5">
                <div class="flex items-center bg-white px-3.5 py-1.5 rounded-xl shadow-lg border border-slate-100">
                    <img src="{{ asset('images/mandiri-logo.svg') }}" alt="Bank Mandiri" class="h-6 sm:h-7 w-auto object-contain">
                </div>

                <div class="h-8 w-px bg-white/20 hidden sm:block"></div>

                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-black tracking-wider text-lg sm:text-xl text-white">DASHCAM</span>
                        <span class="text-[11px] px-2.5 py-0.5 rounded-full bg-[#ffb700] text-slate-950 font-bold uppercase tracking-wider shadow-sm">
                            IT Capacity Management
                        </span>
                    </div>
                    <p class="text-xs text-slate-300 font-medium">Direktorat Information Technology • Bank Mandiri</p>
                </div>
            </div>

            <!-- Right: Actions, SQLite Status, Excel Export -->
            <div class="flex items-center gap-2.5 sm:gap-3">
                <div class="hidden xl:flex items-center gap-2 text-xs px-3 py-1.5 rounded-lg bg-slate-900/80 border border-white/15"
                     :class="syncStatus === 'saving' ? 'border-amber-400/50' : (syncStatus === 'saved' ? 'border-emerald-500/40 text-emerald-300' : 'text-slate-400')">
                    <span class="w-2 h-2 rounded-full"
                          :class="syncStatus === 'saving' ? 'bg-amber-400 animate-ping' : (syncStatus === 'saved' ? 'bg-emerald-400' : 'bg-slate-400')"></span>
                    <span x-text="syncStatusText">Tersimpan di SQLite</span>
                </div>

                <button @click="exportToExcel()" 
                        title="Download Laporan Format Excel (.xlsx) Lengkap & Rapih"
                        class="px-3.5 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-500 text-white transition-all flex items-center gap-1.5 shadow-md shadow-emerald-600/30 active:scale-95">
                    <svg class="w-4 h-4 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Export Excel</span>
                </button>

                <button @click="saveToDatabase(true)" 
                        class="px-3.5 py-2 rounded-xl text-xs font-bold bg-[#ffb700] hover:bg-[#ffc526] text-slate-950 transition-all flex items-center gap-1.5 shadow-md shadow-[#ffb700]/25 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    <span>Simpan</span>
                </button>

                <button onclick="window.print()"
                        title="Cetak atau simpan PDF"
                        class="hidden sm:flex px-3 py-2 rounded-xl text-xs font-semibold bg-white/10 hover:bg-white/20 text-white border border-white/15 transition-all items-center gap-1.5 backdrop-blur-sm">
                    <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak / PDF</span>
                </button>

                <form action="{{ route('calculator.reset') }}" method="POST" onsubmit="return confirm('Kembalikan data ke data awal?')">
                    @csrf
                    <button type="submit" 
                            title="Reset data ke default"
                            class="p-2 rounded-xl text-xs text-slate-400 hover:text-red-400 hover:bg-red-500/10 border border-transparent hover:border-red-500/30 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        <!-- Title Banner -->
        <div class="text-center space-y-2 py-2">
            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-[#002244]/80 backdrop-blur-md border border-[#ffb700]/40 text-amber-200 text-xs font-semibold shadow-lg">
                <span class="w-2 h-2 rounded-full bg-[#ffb700] animate-pulse"></span>
                Infrastruktur Server & Database Menara Mandiri
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-white uppercase drop-shadow-lg">
                Kalkulator Utilisasi
            </h1>
            <div class="w-28 h-1.5 bg-gradient-to-r from-[#ffb700] via-amber-300 to-[#ffb700] mx-auto rounded-full shadow-md"></div>
            <p class="text-xs sm:text-sm text-slate-200 max-w-3xl mx-auto font-medium drop-shadow">
                Simulasi perencanaan kapasitas infrastruktur IT (CPU Core, Memory RAM, dan Storage Disk) agar terhindar dari bottleneck transaksi dan menjaga Service Level Agreement (SLA).
            </p>
        </div>

        <!-- ============================================================ -->
        <!-- MODAL ANIMASI INTERAKTIF (LENGKAP: MENAMPILKAN EFEK GROWTH SECARA JELAS) -->
        <!-- ============================================================ -->
        <div x-show="animModal.open" 
             class="fixed inset-0 z-[100] overflow-y-auto bg-slate-950/85 backdrop-blur-md p-3 sm:p-6 flex items-start sm:items-center justify-center"
             @keydown.escape.window="animModal.open = false"
             style="display: none;">
            <div class="relative my-auto w-full max-w-2xl max-h-[92vh] overflow-y-auto bg-white rounded-3xl shadow-2xl p-5 sm:p-7 text-slate-900 border border-slate-200 space-y-5 animate-in fade-in zoom-in-95 duration-200"
                 @click.outside="animModal.open = false">
                
                <!-- Modal Header -->
                <div class="flex items-start justify-between border-b border-slate-200 pb-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black uppercase bg-[#003a70] text-[#ffb700]" x-text="animModal.item?.type"></span>
                            <h3 class="font-black text-slate-900 text-lg sm:text-xl" x-text="'Simulasi Efek: ' + (animModal.item?.name || 'Server')"></h3>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">
                            Perbandingan kondisi <strong>Beban Masa Depan (Akibat Growth)</strong> vs <strong>Setelah Ditambah Hardware</strong>.
                        </p>
                    </div>
                    <button @click="animModal.open = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Interactive Switcher (Sebelum vs Sesudah) -->
                <div class="flex items-center justify-center gap-2 p-1.5 bg-slate-100 rounded-2xl border border-slate-200">
                    <button @click="animModal.state = 'before'"
                            class="flex-1 py-2 rounded-xl text-xs sm:text-sm font-bold transition-all flex items-center justify-center gap-1.5"
                            :class="animModal.state === 'before' ? 'bg-red-600 text-white shadow-md' : 'text-slate-600 hover:text-slate-900'">
                        <span>🔴 1. Beban Mendatang (Tanpa Penambahan)</span>
                    </button>
                    <button @click="animModal.state = 'after'"
                            class="flex-1 py-2 rounded-xl text-xs sm:text-sm font-bold transition-all flex items-center justify-center gap-1.5"
                            :class="animModal.state === 'after' ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-600 hover:text-slate-900'">
                        <span>🟢 2. Setelah Ditambah (+<span x-text="animModal.item?.add_resource"></span> <span x-text="animModal.item?.spec_unit"></span>)</span>
                    </button>
                </div>

                <!-- Visual Server Capacity Gauge / Progress Bar -->
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
                    
                    <!-- Stats Overview (MENGHUBUNGKAN CURRENT VS GROWTH VS PROJECTED DENGAN JELAS) -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-center text-xs">
                        <!-- 1. Kapasitas Hardware -->
                        <div class="p-2.5 bg-white rounded-xl border border-slate-200">
                            <span class="text-[10px] text-slate-500 font-semibold block">Kapasitas Server</span>
                            <div class="text-base font-black text-slate-900 mt-0.5">
                                <span x-text="animModal.state === 'before' ? animModal.item?.spec_value : (animModal.item?.spec_value + animModal.item?.add_resource)"></span>
                                <span class="text-[10px] font-semibold text-slate-600" x-text="animModal.item?.spec_unit"></span>
                            </div>
                            <span class="text-[10px] text-slate-400" x-text="animModal.state === 'before' ? 'Spesifikasi saat ini' : 'Setelah di-upgrade'"></span>
                        </div>

                        <!-- 2. Pemakaian Riil Sekarang -->
                        <div class="p-2.5 bg-white rounded-xl border border-slate-200">
                            <span class="text-[10px] text-slate-500 font-semibold block">Pemakaian Sekarang</span>
                            <div class="text-base font-black text-slate-700 mt-0.5">
                                <span x-text="calcCurrentUsage(animModal.item)"></span>
                                <span class="text-[10px] font-semibold text-slate-500" x-text="animModal.item?.spec_unit"></span>
                            </div>
                            <span class="text-[10px] text-slate-400" x-text="'Utilisasi: ' + animModal.item?.current_util_percent + '%'"></span>
                        </div>

                        <!-- 3. Prediksi Beban Nanti (Akibat Growth) -->
                        <div class="p-2.5 bg-white rounded-xl border border-slate-200">
                            <span class="text-[10px] text-blue-600 font-bold block">Beban Nanti (Growth)</span>
                            <div class="text-base font-black text-[#003a70] mt-0.5">
                                <span x-text="calcProjectedUsage(animModal.item)"></span>
                                <span class="text-[10px] font-semibold text-slate-500" x-text="animModal.item?.spec_unit"></span>
                            </div>
                            <span class="text-[10px] text-blue-500 font-semibold" x-text="'+' + animModal.item?.growth_est_percent + '% (' + growth_months + ' ' + (growth_unit === 'day' ? 'Hari' : (growth_unit === 'year' ? 'Thn' : 'Bln')) + ')'"></span>
                        </div>

                        <!-- 4. Utilisasi Akhir -->
                        <div class="p-2.5 bg-white rounded-xl border border-slate-200">
                            <span class="text-[10px] text-slate-500 font-semibold block">Utilisasi Akhir</span>
                            <div class="text-base font-black mt-0.5"
                                 :class="animModal.state === 'before' 
                                    ? (calcProjectedUtilPercent(animModal.item) >= getItemThreshold(animModal.item) ? 'text-red-600' : 'text-amber-600') 
                                    : 'text-emerald-600'">
                                <span x-text="animModal.state === 'before' ? calcProjectedUtilPercent(animModal.item) : calcNewUtilWithGrowth(animModal.item)"></span>%
                            </div>
                            <span class="text-[10px] font-bold"
                                  :class="animModal.state === 'before' 
                                    ? (calcProjectedUtilPercent(animModal.item) >= getItemThreshold(animModal.item) ? 'text-red-500' : 'text-emerald-500') 
                                    : 'text-emerald-600'"
                                  x-text="animModal.state === 'before' 
                                    ? (calcProjectedUtilPercent(animModal.item) >= getItemThreshold(animModal.item) ? '⚠️ Bahaya' : '✓ Aman') 
                                    : '✓ Terkendali'"></span>
                        </div>
                    </div>

                    <!-- Progress Bar with Threshold Markers -->
                    <div class="space-y-1.5 pt-1">
                        <div class="flex items-center justify-between text-xs font-bold text-slate-600">
                            <span>0%</span>
                            <span class="text-amber-600" x-text="'Target Aman: ' + getItemSafePct(animModal.item) + '%'"></span>
                            <span class="text-red-600" x-text="'Batas Bahaya (Threshold): ' + getItemThreshold(animModal.item) + '%'"></span>
                            <span>100%</span>
                        </div>

                        <div class="h-8 w-full bg-slate-200 rounded-full relative overflow-hidden p-1 shadow-inner">
                            <div class="absolute top-0 bottom-0 w-0.5 border-r-2 border-dashed border-amber-500 z-10"
                                 :style="'left: ' + getItemSafePct(animModal.item) + '%'"></div>

                            <div class="absolute top-0 bottom-0 w-1 bg-red-600 z-10"
                                 :style="'left: ' + getItemThreshold(animModal.item) + '%'"></div>

                            <div class="h-full rounded-full transition-all duration-700 ease-out flex items-center justify-end pr-2 text-white text-xs font-bold shadow-md"
                                 :class="animModal.state === 'before' 
                                    ? (calcProjectedUtilPercent(animModal.item) >= getItemThreshold(animModal.item) ? 'bg-gradient-to-r from-red-500 to-red-600' : (calcProjectedUtilPercent(animModal.item) >= getItemSafePct(animModal.item) ? 'bg-gradient-to-r from-amber-400 to-amber-500' : 'bg-gradient-to-r from-emerald-400 to-emerald-500')) 
                                    : 'bg-gradient-to-r from-emerald-500 to-teal-600'"
                                 :style="'width: ' + Math.min(100, (animModal.state === 'before' ? calcProjectedUtilPercent(animModal.item) : calcNewUtilWithGrowth(animModal.item))) + '%'">
                            </div>
                        </div>
                    </div>

                    <!-- Penjelasan Gamblang Bahasa Manusia (Awam) -->
                    <div class="p-4 rounded-xl text-xs sm:text-sm leading-relaxed"
                         :class="animModal.state === 'before' 
                            ? (calcProjectedUtilPercent(animModal.item) >= getItemThreshold(animModal.item) ? 'bg-red-50 border border-red-200 text-red-900' : 'bg-blue-50 border border-blue-200 text-blue-900') 
                            : 'bg-emerald-50 border border-emerald-200 text-emerald-900'">
                        <div class="font-extrabold flex items-center gap-1.5 text-xs sm:text-sm mb-1">
                            <span x-text="animModal.state === 'before' ? '⚠️ Kondisi Jika Tidak Ditambah:' : '✅ Efek Setelah Penambahan Rekomendasi:'"></span>
                        </div>
                        <template x-if="animModal.state === 'before'">
                            <div>
                                <span x-show="calcProjectedUtilPercent(animModal.item) >= getItemThreshold(animModal.item)">
                                    Saat ini server memakai <strong><span x-text="calcCurrentUsage(animModal.item)"></span> <span x-text="animModal.item?.spec_unit"></span> (<span x-text="animModal.item?.current_util_percent"></span>%)</strong>.
                                    Dengan laju pertumbuhan <strong><span x-text="animModal.item?.growth_est_percent"></span>%</strong>, pemakaian akan membengkak menjadi <strong><span x-text="calcProjectedUsage(animModal.item)"></span> <span x-text="animModal.item?.spec_unit"></span></strong> atau setara <strong><span x-text="calcProjectedUtilPercent(animModal.item)"></span>%</strong>!
                                    Angka ini <strong>menembus batas bahaya <span x-text="getItemThreshold(animModal.item)"></span>%</strong> sehingga server berisiko macet/down jika tidak segera di-upgrade.
                                </span>
                                <span x-show="calcProjectedUtilPercent(animModal.item) < getItemThreshold(animModal.item)">
                                    Meskipun ada pertumbuhan <strong><span x-text="animModal.item?.growth_est_percent"></span>%</strong>, beban masa depan diperkirakan hanya mencapai <strong><span x-text="calcProjectedUsage(animModal.item)"></span> <span x-text="animModal.item?.spec_unit"></span> (<span x-text="calcProjectedUtilPercent(animModal.item)"></span>%)</strong>. Kapasitas saat ini (<span x-text="animModal.item?.spec_value"></span> <span x-text="animModal.item?.spec_unit"></span>) masih sangat lapang, sehingga <strong>belum memerlukan penambahan (0 <span x-text="animModal.item?.spec_unit"></span>)</strong>.
                                </span>
                            </div>
                        </template>
                        <template x-if="animModal.state === 'after'">
                            <div>
                                <span x-show="animModal.item?.add_resource > 0">
                                    Dengan membeli penambahan <strong>+<span x-text="animModal.item?.add_resource"></span> <span x-text="animModal.item?.spec_unit"></span></strong>, total kapasitas server bertambah menjadi <strong><span x-text="animModal.item?.spec_value + animModal.item?.add_resource"></span> <span x-text="animModal.item?.spec_unit"></span></strong>.
                                    Beban lonjakan <span x-text="calcProjectedUsage(animModal.item)"></span> <span x-text="animModal.item?.spec_unit"></span> kini hanya memakan <strong><span x-text="calcNewUtilWithGrowth(animModal.item)"></span>%</strong> dari kapasitas baru. Server dipastikan <strong>100% AMAN</strong> di bawah batas kritis.
                                </span>
                                <span x-show="animModal.item?.add_resource <= 0">
                                    Server ini sudah aman dan tidak memerlukan penambahan apapun.
                                </span>
                            </div>
                        </template>
                    </div>

                </div>

                <!-- Footer Action -->
                <div class="flex items-center justify-between pt-1">
                    <button @click="toggleAnimPlay()"
                            class="px-4 py-2 rounded-xl text-xs font-bold bg-[#ffb700] hover:bg-[#ffc526] text-slate-950 flex items-center gap-2 shadow-md active:scale-95 transition-all">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path></svg>
                        <span>Putar Animasi Transisi 🎬</span>
                    </button>

                    <button @click="animModal.open = false" 
                            class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        <!-- Pop-Up Info Modal untuk Menjelaskan Istilah Awam -->
        <div x-show="infoModal.open" 
             class="fixed inset-0 z-[100] overflow-y-auto bg-slate-950/80 backdrop-blur-sm p-4 flex items-center justify-center"
             @keydown.escape.window="infoModal.open = false"
             style="display: none;">
            <div class="relative my-auto bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 text-slate-900 border border-slate-200 space-y-4 animate-in fade-in zoom-in-95 duration-150"
                 @click.outside="infoModal.open = false">
                
                <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-[#003a70] flex items-center justify-center font-bold text-lg border border-blue-200">
                            ℹ️
                        </div>
                        <h3 class="font-extrabold text-slate-900 text-base" x-text="infoModal.title">Penjelasan</h3>
                    </div>
                    <button @click="infoModal.open = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-3 text-xs sm:text-sm text-slate-600 leading-relaxed">
                    <p x-html="infoModal.content"></p>
                    <template x-if="infoModal.example">
                        <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs">
                            <strong class="font-bold">Contoh Skenario:</strong>
                            <p class="mt-0.5" x-text="infoModal.example"></p>
                        </div>
                    </template>
                </div>

                <div class="pt-2 flex justify-end">
                    <button @click="infoModal.open = false" 
                            class="px-4 py-2 rounded-xl bg-[#003a70] hover:bg-[#002d59] text-white font-bold text-xs">
                        Mengerti 👍
                    </button>
                </div>
            </div>
        </div>

        <!-- Pop-Up Modal: Audit & Bedah Rumus Perhitungan Transparan -->
        <div x-show="calcBreakdownModal.open" 
             class="fixed inset-0 z-[100] overflow-y-auto bg-slate-950/80 backdrop-blur-sm p-4 flex items-center justify-center"
             @keydown.escape.window="calcBreakdownModal.open = false"
             style="display: none;">
            <div class="relative my-auto bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-6 text-slate-900 border border-slate-200 space-y-4 animate-in fade-in zoom-in-95 duration-150"
                 @click.outside="calcBreakdownModal.open = false">
                 
                <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-10 h-10 rounded-xl bg-blue-100 text-[#003a70] flex items-center justify-center font-bold text-xl border border-blue-200">
                            🧮
                        </div>
                        <div>
                            <h3 class="font-black text-slate-900 text-base sm:text-lg">Transparansi Rumus & Audit Rekomendasi</h3>
                            <p class="text-xs text-slate-500">Rincian langkah matematis penentuan kapasitas untuk server: <strong class="text-[#003a70]" x-text="calcBreakdownModal.item?.name"></strong></p>
                        </div>
                    </div>
                    <button @click="calcBreakdownModal.open = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <template x-if="calcBreakdownModal.stepData">
                    <div class="space-y-4 text-xs sm:text-sm">
                        <!-- Step 1 & 2 -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                                <span class="text-slate-500 font-bold block text-[11px] uppercase tracking-wider">Langkah 1: Pemakaian Riil Saat Ini</span>
                                <div class="mt-1 font-mono text-slate-900 text-xs">
                                    <span x-text="calcBreakdownModal.item.spec_value"></span> <span x-text="calcBreakdownModal.item.spec_unit"></span> × <span x-text="calcBreakdownModal.item.current_util_percent"></span>% =
                                    <strong class="text-[#003a70]" x-text="calcBreakdownModal.stepData.currentUsage + ' ' + calcBreakdownModal.item.spec_unit"></strong>
                                </div>
                                <span class="text-[10px] text-slate-500 block mt-1">Kapasitas fisik yang benar-benar terpakai sekarang.</span>
                            </div>

                            <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                                <span class="text-slate-500 font-bold block text-[11px] uppercase tracking-wider">Langkah 2: Proyeksi Growth Transaksi</span>
                                <div class="mt-1 font-mono text-slate-900 text-xs">
                                    <span x-text="calcBreakdownModal.stepData.currentUsage"></span> × (1 + <span x-text="calcBreakdownModal.stepData.growthRatePct"></span>% × <span x-text="calcBreakdownModal.stepData.duration"></span>) =
                                    <strong class="text-blue-900" x-text="calcBreakdownModal.stepData.projectedUsage + ' ' + calcBreakdownModal.item.spec_unit"></strong>
                                </div>
                                <span class="text-[10px] text-slate-500 block mt-1">Perkiraan beban di masa depan dengan durasi <span x-text="calcBreakdownModal.stepData.duration"></span> <span x-text="growth_unit"></span>.</span>
                            </div>
                        </div>

                        <!-- Step 3: Safety Target -->
                        <div class="p-3.5 bg-amber-50 rounded-xl border border-amber-200 space-y-1">
                            <span class="text-amber-900 font-bold block text-[11px] uppercase tracking-wider">Langkah 3: Menentukan Target Ambang Batas Aman</span>
                            <div class="font-mono text-amber-950 text-xs">
                                Batas Aman Utilisasi = Threshold (<span x-text="calcBreakdownModal.stepData.threshold"></span>%) - Buffer (<span x-text="calcBreakdownModal.stepData.buffer"></span>%) = <strong><span x-text="calcBreakdownModal.stepData.targetSafePct"></span>%</strong>
                            </div>
                            <div class="font-mono text-amber-950 text-xs">
                                Kapasitas Maksimal Aman Saat Ini = <span x-text="calcBreakdownModal.item.spec_value"></span> × <span x-text="calcBreakdownModal.stepData.targetSafePct"></span>% = <strong><span x-text="calcBreakdownModal.stepData.maxSafeCapacity + ' ' + calcBreakdownModal.item.spec_unit"></span></strong>
                            </div>
                            <span class="text-[10px] text-amber-800 block mt-0.5">Bila beban melampaui <strong x-text="calcBreakdownModal.stepData.maxSafeCapacity + ' ' + calcBreakdownModal.item.spec_unit"></strong>, sistem Mandiri berisiko lambat / timeout.</span>
                        </div>

                        <!-- Step 4: Decision Logic -->
                        <div class="p-4 rounded-xl border" :class="calcBreakdownModal.stepData.isExceeded ? 'bg-red-50 border-red-300' : 'bg-emerald-50 border-emerald-300'">
                            <span class="font-bold block text-[11px] uppercase tracking-wider" :class="calcBreakdownModal.stepData.isExceeded ? 'text-red-800' : 'text-emerald-800'">Langkah 4: Hasil Analisis & Rekomendasi Kapasitas</span>
                            
                            <template x-if="calcBreakdownModal.stepData.isExceeded">
                                <div class="mt-2 text-slate-800 space-y-2">
                                    <p class="text-xs">
                                        Beban masa depan (<strong class="text-red-700" x-text="calcBreakdownModal.stepData.projectedUsage + ' ' + calcBreakdownModal.item.spec_unit"></strong>) 
                                        <strong>TELAH MELEBIHI</strong> batas aman server (<strong x-text="calcBreakdownModal.stepData.maxSafeCapacity + ' ' + calcBreakdownModal.item.spec_unit"></strong>). Penambahan hardware wajib dilakukan!
                                    </p>
                                    <div class="p-3 bg-white rounded-lg border border-red-200 font-mono text-xs text-slate-800 space-y-1">
                                        <div>Kapasitas Baru yang Wajib Ada = <span x-text="calcBreakdownModal.stepData.projectedUsage"></span> ÷ <span x-text="calcBreakdownModal.stepData.targetSafePct"></span>% = <strong><span x-text="calcBreakdownModal.stepData.neededTotalSpec"></span> <span x-text="calcBreakdownModal.item.spec_unit"></span></strong></div>
                                        <div>Kekurangan = <span x-text="calcBreakdownModal.stepData.neededTotalSpec"></span> - <span x-text="calcBreakdownModal.item.spec_value"></span> = <strong><span x-text="calcBreakdownModal.stepData.rawAdditional"></span> <span x-text="calcBreakdownModal.item.spec_unit"></span></strong></div>
                                        <div class="text-emerald-700 font-bold pt-1 border-t border-slate-100">
                                            ➔ Rekomendasi Pengadaan (Pembulatan ke Atas): <strong>+<span x-text="calcBreakdownModal.stepData.finalAdd"></span> <span x-text="calcBreakdownModal.item.spec_unit"></span></strong>
                                        </div>
                                    </div>
                                    <p class="text-[11px] text-slate-600">
                                        Setelah ditambah <strong class="text-emerald-700" x-text="'+' + calcBreakdownModal.stepData.finalAdd + ' ' + calcBreakdownModal.item.spec_unit"></strong>, total kapasitas server menjadi <strong x-text="calcBreakdownModal.stepData.newCapacity + ' ' + calcBreakdownModal.item.spec_unit"></strong> dan beban utilisasi akan turun stabil ke <strong class="text-emerald-700" x-text="calcBreakdownModal.stepData.newUtil + '%'"></strong>.
                                    </p>
                                </div>
                            </template>

                            <template x-if="!calcBreakdownModal.stepData.isExceeded">
                                <div class="mt-2 text-emerald-950 space-y-2">
                                    <p class="text-xs">
                                        Beban masa depan (<strong class="text-emerald-800" x-text="calcBreakdownModal.stepData.projectedUsage + ' ' + calcBreakdownModal.item.spec_unit"></strong>) 
                                        <strong>MASIH JAUH DI BAWAH</strong> batas aman (<strong x-text="calcBreakdownModal.stepData.maxSafeCapacity + ' ' + calcBreakdownModal.item.spec_unit"></strong>).
                                    </p>
                                    <div class="p-3 bg-white rounded-lg border border-emerald-300 text-xs font-semibold text-emerald-900 flex items-center gap-2">
                                        <span class="text-lg">✅</span>
                                        <div>
                                            Rekomendasi Tambahan: <strong>0 <span x-text="calcBreakdownModal.item.spec_unit"></span> (Kapasitas Masih Sangat Cukup)</strong><br>
                                            <span class="text-[11px] font-normal text-slate-600">Bank Mandiri tidak perlu mengeluarkan anggaran belanja modal (CAPEX) untuk server ini.</span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <div class="pt-2 flex justify-end">
                    <button @click="calcBreakdownModal.open = false" 
                            class="px-5 py-2.5 rounded-xl bg-[#003a70] hover:bg-[#002d59] text-white font-bold text-xs">
                        Tutup Rincian Audit
                    </button>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs (5 Tab Lengkap) -->
        <div class="flex flex-wrap items-center justify-center gap-2 sm:gap-3 no-print">
            <button @click="activeTab = 'manual'" class="px-5 py-2.5 rounded-full text-xs sm:text-sm font-bold transition-all duration-200 flex items-center gap-2 shadow-md" :class="activeTab === 'manual' ? 'bg-[#003a70] text-white ring-2 ring-[#ffb700] shadow-blue-900/50' : 'bg-white hover:bg-slate-100 text-slate-800'">
                <svg class="w-4 h-4 text-[#ffb700]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                <span>Manual Simulation</span>
            </button>

            <button @click="activeTab = 'threshold'" class="px-5 py-2.5 rounded-full text-xs sm:text-sm font-bold transition-all duration-200 flex items-center gap-2 shadow-md" :class="activeTab === 'threshold' ? 'bg-[#003a70] text-white ring-2 ring-[#ffb700] shadow-blue-900/50' : 'bg-white hover:bg-slate-100 text-slate-800'">
                <svg class="w-4 h-4 text-[#ffb700]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" /></svg>
                <span>Simulation by Threshold</span>
            </button>

            <button @click="activeTab = 'chart'; $nextTick(() => renderCharts())" class="px-5 py-2.5 rounded-full text-xs sm:text-sm font-bold transition-all duration-200 flex items-center gap-2 shadow-md" :class="activeTab === 'chart' ? 'bg-[#003a70] text-white ring-2 ring-[#ffb700] shadow-blue-900/50' : 'bg-white hover:bg-slate-100 text-slate-800'">
                <svg class="w-4 h-4 text-[#ffb700]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" /></svg>
                <span>Data Projection Chart</span>
            </button>

            <button @click="activeTab = 'bar'; $nextTick(() => renderBarCharts())" class="px-5 py-2.5 rounded-full text-xs sm:text-sm font-bold transition-all duration-200 flex items-center gap-2 shadow-md" :class="activeTab === 'bar' ? 'bg-[#003a70] text-white ring-2 ring-[#ffb700] shadow-blue-900/50' : 'bg-white hover:bg-slate-100 text-slate-800'">
                <svg class="w-4 h-4 text-[#ffb700]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                <span>Data Projection Bar</span>
            </button>

            <button @click="activeTab = 'summary'" class="px-5 py-2.5 rounded-full text-xs sm:text-sm font-bold transition-all duration-200 flex items-center gap-2 shadow-md" :class="activeTab === 'summary' ? 'bg-emerald-700 text-white ring-2 ring-[#ffb700] shadow-emerald-900/50' : 'bg-white hover:bg-slate-100 text-slate-800'">
                <svg class="w-4 h-4 text-emerald-600" :class="{'text-[#ffb700]': activeTab === 'summary'}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>Ringkasan Awam & Biaya</span>
            </button>
        </div>

        <!-- ========================================== -->
        <!-- TAB 1: MANUAL SIMULATION                   -->
        <!-- ========================================== -->
        <div x-show="activeTab === 'manual'" class="space-y-6">
            <div class="bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl p-6 border border-slate-200 text-slate-900 print-card">
                <div class="border-b border-slate-200 pb-4 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                            <span>Manual Simulation</span>
                            <button @click="showInfo('manual_mode')" class="text-blue-600 hover:text-blue-800" title="Informasi Manual Mode">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                            </button>
                        </h2>
                        <p class="text-xs text-slate-500">Hitung manual kebutuhan kapasitas tambahan dengan menentukan utilisasi target yang diinginkan.</p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-[#003a70] border border-blue-200">
                        <span class="w-2 h-2 rounded-full bg-[#ffb700]"></span> Mandiri Enterprise IT Planner
                    </span>
                </div>

                <!-- Top Metric Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-[#003a70]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" /></svg>
                        </div>
                        <div>
                            <span class="text-xs uppercase font-bold tracking-wider text-slate-500">Total Add CPU</span>
                            <div class="text-2xl font-black text-[#003a70]"><span x-text="totalManualAddCpu"></span> <span class="text-sm font-semibold text-slate-600">Core</span></div>
                        </div>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center text-purple-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                        </div>
                        <div>
                            <span class="text-xs uppercase font-bold tracking-wider text-slate-500">Total Add Memory</span>
                            <div class="text-2xl font-black text-purple-700"><span x-text="totalManualAddMemory"></span> <span class="text-sm font-semibold text-slate-600">GB</span></div>
                        </div>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center text-amber-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" /></svg>
                        </div>
                        <div>
                            <span class="text-xs uppercase font-bold tracking-wider text-slate-500">Total Add Disk</span>
                            <div class="text-2xl font-black text-amber-700"><span x-text="totalManualAddDisk"></span> <span class="text-sm font-semibold text-slate-600">GB</span></div>
                        </div>
                    </div>
                </div>

                <!-- Manual Table -->
                <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-sm">
                    <table class="w-full text-left text-xs sm:text-sm">
                        <thead class="bg-[#002244] text-white uppercase text-[11px] font-bold tracking-wider">
                            <tr>
                                <th class="p-3 w-16 text-center">Action</th>
                                <th class="p-3 min-w-[180px]">Deskripsi Item</th>
                                <th class="p-3 w-32">Tipe</th>
                                <th class="p-3 w-36 bg-[#003a70] text-center">Spesifikasi</th>
                                <th class="p-3 w-32 bg-sky-900 text-center">Utilisation</th>
                                <th class="p-3 w-32 bg-sky-950 text-center">Current Usage</th>
                                <th class="p-3 w-32 bg-amber-800 text-center">Usage To Be</th>
                                <th class="p-3 w-36 bg-emerald-700 text-center">Add. Resource</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            <template x-for="(item, index) in manualItems" :key="'manual-'+index">
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="p-3 text-center">
                                        <button @click="removeManualItem(index)" title="Hapus baris" class="w-7 h-7 rounded-lg inline-flex items-center justify-center text-red-500 hover:text-red-700 hover:bg-red-50 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </td>
                                    <td class="p-3">
                                        <input type="text" x-model="item.name" @input="recalcManual()" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 text-slate-800 text-xs focus:ring-2 focus:ring-[#003a70] focus:outline-none" placeholder="Contoh: Server Aplikasi">
                                    </td>
                                    <td class="p-3">
                                        <select x-model="item.type" @change="onTypeChange(item); recalcManual()" class="w-full px-2 py-1.5 rounded-lg border border-slate-300 text-slate-800 text-xs font-semibold focus:ring-2 focus:ring-[#003a70] focus:outline-none bg-white">
                                            <option value="cpu">CPU</option>
                                            <option value="memory">Memory</option>
                                            <option value="disk">Disk</option>
                                        </select>
                                    </td>
                                    <td class="p-3 bg-blue-50/40">
                                        <div class="flex items-center gap-1.5">
                                            <input type="number" step="any" min="1" x-model.number="item.spec_value" @input="recalcManual()" class="w-20 px-2 py-1 rounded-md border border-slate-300 text-center font-bold text-slate-900 text-xs focus:ring-2 focus:ring-[#003a70] focus:outline-none">
                                            <span class="text-xs font-bold text-slate-600" x-text="item.spec_unit"></span>
                                        </div>
                                    </td>
                                    <td class="p-3 bg-sky-50/40 text-center">
                                        <div class="inline-flex items-center gap-1">
                                            <input type="number" step="any" min="0" max="100" x-model.number="item.current_util_percent" @input="recalcManual()" class="w-16 px-2 py-1 rounded-md border text-center font-bold text-xs focus:outline-none" :class="item.current_util_percent >= 80 ? 'text-red-600 border-red-300 bg-red-50' : (item.current_util_percent >= 70 ? 'text-amber-600 border-amber-300 bg-amber-50' : 'text-emerald-600 border-emerald-300 bg-emerald-50')">
                                            <span class="text-xs font-bold text-slate-600">%</span>
                                        </div>
                                    </td>
                                    <td class="p-3 text-center font-bold text-slate-800 bg-slate-50">
                                        <span x-text="calcCurrentUsage(item)"></span>
                                        <span class="text-[10px] text-slate-500" x-text="item.spec_unit"></span>
                                    </td>
                                    <td class="p-3 bg-amber-50/40 text-center">
                                        <div class="inline-flex items-center gap-1">
                                            <input type="number" step="any" min="1" max="100" x-model.number="item.target_util_percent" @input="recalcManual()" class="w-16 px-2 py-1 rounded-md border border-amber-300 bg-amber-50/70 text-center font-bold text-amber-900 text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
                                            <span class="text-xs font-bold text-amber-800">%</span>
                                        </div>
                                    </td>
                                    <td class="p-3 bg-emerald-50/50 text-center font-black">
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-emerald-100 text-emerald-800 border border-emerald-300">
                                            <span class="text-sm" x-text="item.add_resource"></span>
                                            <span class="text-xs font-semibold" x-text="item.spec_unit"></span>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <button @click="addManualItem()" class="w-full sm:w-auto px-5 py-2.5 rounded-xl border-2 border-dashed border-[#003a70] text-[#003a70] hover:bg-blue-50 font-bold text-xs sm:text-sm flex items-center justify-center gap-2 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        <span>+ Tambah Data Manual</span>
                    </button>
                    <div class="text-xs text-slate-500 italic">
                        💡 Tips: Masukkan target *Usage To Be* yang lebih rendah (misal 60%) untuk otomatis mendapatkan rekomendasi penambahan resource.
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 2: SIMULATION BY THRESHOLD             -->
        <!-- ========================================== -->
        <div x-show="activeTab === 'threshold'" class="space-y-6">
            <div class="bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl p-6 border border-slate-200 text-slate-900 print-card">
                
                <!-- Header with Parameters Bar -->
                <div class="border-b border-slate-200 pb-5 mb-6">
                    <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                                <span>Simulation by Threshold</span>
                                <button @click="showInfo('threshold_mode')" class="text-blue-600 hover:text-blue-800" title="Informasi Threshold Mode">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                                </button>
                            </h2>
                            <p class="text-xs text-slate-500">
                                Parameter ambang batas aman dan perkiraan pertumbuhan beban transaksi. Rekomendasi dihitung otomatis untuk menjaga server tetap di bawah batas aman.
                            </p>
                        </div>

                        <!-- Top Parameters Box -->
                        <div class="flex flex-wrap items-center gap-2 bg-slate-50 p-2.5 rounded-xl border border-slate-200 text-xs">
                            
                            <!-- CPU Thr -->
                            <div class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-white border border-red-300 shadow-sm" title="Batas maksimal utilisasi CPU sebelum kritis">
                                <span class="text-red-700 font-bold">CPU Thr:</span>
                                <input type="number" min="10" max="100" x-model.number="cpu_threshold" @input="recalcThreshold()" class="w-12 text-center font-bold text-slate-900 text-xs focus:outline-none">
                                <span class="text-slate-500 font-semibold">%</span>
                                <button @click="showInfo('threshold')" class="text-slate-400 hover:text-red-600 ml-0.5"><svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg></button>
                            </div>

                            <!-- Mem Thr -->
                            <div class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-white border border-red-300 shadow-sm" title="Batas maksimal utilisasi Memory RAM">
                                <span class="text-red-700 font-bold">Mem Thr:</span>
                                <input type="number" min="10" max="100" x-model.number="mem_threshold" @input="recalcThreshold()" class="w-12 text-center font-bold text-slate-900 text-xs focus:outline-none">
                                <span class="text-slate-500 font-semibold">%</span>
                                <button @click="showInfo('threshold')" class="text-slate-400 hover:text-red-600 ml-0.5"><svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg></button>
                            </div>

                            <!-- Disk Thr -->
                            <div class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-white border border-red-300 shadow-sm" title="Batas maksimal utilisasi Storage SSD">
                                <span class="text-red-700 font-bold">Disk Thr:</span>
                                <input type="number" min="10" max="100" x-model.number="disk_threshold" @input="recalcThreshold()" class="w-12 text-center font-bold text-slate-900 text-xs focus:outline-none">
                                <span class="text-slate-500 font-semibold">%</span>
                                <button @click="showInfo('threshold')" class="text-slate-400 hover:text-red-600 ml-0.5"><svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg></button>
                            </div>

                            <!-- Buffer -->
                            <div class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-amber-50 border border-amber-400 shadow-sm" title="Ruang pengaman cadangan di bawah threshold">
                                <span class="text-amber-800 font-extrabold flex items-center gap-1">Buffer:</span>
                                <input type="number" min="0" max="20" x-model.number="buffer_percent" @input="recalcThreshold()" class="w-10 text-center font-black text-amber-900 bg-transparent text-xs focus:outline-none">
                                <span class="text-amber-800 font-bold">%</span>
                                <button @click="showInfo('buffer')" class="text-amber-600 hover:text-amber-800 ml-0.5"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg></button>
                            </div>

                            <!-- Growth Active (Yes/No) -->
                            <div class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-white border border-blue-300 shadow-sm">
                                <span class="text-[#003a70] font-bold">Growth:</span>
                                <select x-model="growth_enabled" @change="recalcThreshold()" class="bg-transparent font-bold text-slate-900 text-xs focus:outline-none cursor-pointer">
                                    <option :value="true">Yes</option>
                                    <option :value="false">No</option>
                                </select>
                            </div>

                            <!-- Segmented Pill for Growth Unit: Hari / Bulan / Tahun -->
                            <div class="flex items-center bg-slate-200/80 p-0.5 rounded-lg border border-slate-300 shadow-sm" x-show="growth_enabled">
                                <button type="button" @click="setGrowthUnit('day')" class="px-2 py-1 rounded-md text-[11px] font-bold transition-all" :class="growth_unit === 'day' ? 'bg-[#003a70] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900'">Harian</button>
                                <button type="button" @click="setGrowthUnit('month')" class="px-2 py-1 rounded-md text-[11px] font-bold transition-all" :class="growth_unit === 'month' ? 'bg-[#003a70] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900'">Bulanan</button>
                                <button type="button" @click="setGrowthUnit('year')" class="px-2 py-1 rounded-md text-[11px] font-bold transition-all" :class="growth_unit === 'year' ? 'bg-[#003a70] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900'">Tahunan</button>
                                <button @click="showInfo('growth')" class="text-blue-600 hover:text-blue-800 px-1"><svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg></button>
                            </div>

                            <!-- Duration Value -->
                            <div class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-white border border-emerald-400 shadow-sm" x-show="growth_enabled">
                                <span class="text-emerald-800 font-bold" x-text="growthUnitLabel + ':'"></span>
                                <input type="number" min="1" max="365" x-model.number="growth_months" @input="recalcThreshold()" class="w-12 text-center font-black text-slate-900 text-xs focus:outline-none">
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Top Metric Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-[#003a70]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" /></svg>
                        </div>
                        <div>
                            <span class="text-xs uppercase font-bold tracking-wider text-slate-500">Total Rec CPU</span>
                            <div class="text-2xl font-black text-[#003a70]"><span x-text="totalThresholdRecCpu"></span> <span class="text-sm font-semibold text-slate-600">Core</span></div>
                        </div>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center text-purple-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                        </div>
                        <div>
                            <span class="text-xs uppercase font-bold tracking-wider text-slate-500">Total Rec Memory</span>
                            <div class="text-2xl font-black text-purple-700"><span x-text="totalThresholdRecMemory"></span> <span class="text-sm font-semibold text-slate-600">GB</span></div>
                        </div>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center text-amber-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" /></svg>
                        </div>
                        <div>
                            <span class="text-xs uppercase font-bold tracking-wider text-slate-500">Total Rec Disk</span>
                            <div class="text-2xl font-black text-amber-700"><span x-text="totalThresholdRecDisk"></span> <span class="text-sm font-semibold text-slate-600">GB</span></div>
                        </div>
                    </div>
                </div>

                <!-- PRESET SKENARIO CEPAT -->
                <div class="mb-4 flex flex-wrap items-center justify-between gap-2 bg-gradient-to-r from-blue-50/80 to-slate-50 p-3 rounded-xl border border-blue-200 text-xs">
                    <div class="flex items-center gap-1.5 font-bold text-slate-800">
                        <span class="text-base text-[#ffb700]">⚡</span>
                        <span>Preset Skenario Beban:</span>
                        <span class="text-[11px] font-normal text-slate-500">(1-klik untuk simulasi uji cepat)</span>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" @click="applyScenarioPreset('baseline')" class="px-3 py-1.5 rounded-lg bg-white border border-slate-300 hover:bg-slate-100 text-slate-800 font-bold shadow-sm transition-all flex items-center gap-1.5 text-xs">
                            <span>🏛️ Baseline Normal Mandiri</span>
                        </button>
                        <button type="button" @click="applyScenarioPreset('peak')" class="px-3 py-1.5 rounded-lg bg-amber-100/80 border border-amber-300 hover:bg-amber-200 text-amber-950 font-bold shadow-sm transition-all flex items-center gap-1.5 text-xs">
                            <span>🛍️ Peak Payday / Promo (+35%)</span>
                        </button>
                        <button type="button" @click="applyScenarioPreset('stress')" class="px-3 py-1.5 rounded-lg bg-red-100/80 border border-red-300 hover:bg-red-200 text-red-950 font-bold shadow-sm transition-all flex items-center gap-1.5 text-xs">
                            <span>🚨 Stress Test Overload</span>
                        </button>
                    </div>
                </div>

                <!-- CATEGORY SEGMENTED PILLS & SEARCH -->
                <div class="mb-4 flex flex-col md:flex-row items-center justify-between gap-3 text-xs">
                    <!-- Category Pills -->
                    <div class="flex flex-wrap items-center gap-1 p-1 bg-slate-100 rounded-xl border border-slate-200">
                        <button type="button" @click="typeFilter = 'all'" 
                                class="px-3 py-1.5 rounded-lg font-bold transition-all flex items-center gap-1.5" 
                                :class="typeFilter === 'all' ? 'bg-[#003a70] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900'">
                            <span>🌐 Semua Resource</span>
                            <span class="text-[10px] px-1.5 py-0.5 rounded-full" :class="typeFilter === 'all' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700'" x-text="thresholdItems.length"></span>
                        </button>

                        <button type="button" @click="typeFilter = 'cpu'" 
                                class="px-3 py-1.5 rounded-lg font-bold transition-all flex items-center gap-1.5" 
                                :class="typeFilter === 'cpu' ? 'bg-[#003a70] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900'">
                            <span>💻 CPU Core</span>
                            <span class="text-[10px] px-1.5 py-0.5 rounded-full" :class="typeFilter === 'cpu' ? 'bg-white/20 text-white' : 'bg-blue-100 text-[#003a70]'" x-text="thresholdItems.filter(i => i.type === 'cpu').length"></span>
                        </button>

                        <button type="button" @click="typeFilter = 'memory'" 
                                class="px-3 py-1.5 rounded-lg font-bold transition-all flex items-center gap-1.5" 
                                :class="typeFilter === 'memory' ? 'bg-[#003a70] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900'">
                            <span>🧠 Memory RAM</span>
                            <span class="text-[10px] px-1.5 py-0.5 rounded-full" :class="typeFilter === 'memory' ? 'bg-white/20 text-white' : 'bg-purple-100 text-purple-700'" x-text="thresholdItems.filter(i => i.type === 'memory').length"></span>
                        </button>

                        <button type="button" @click="typeFilter = 'disk'" 
                                class="px-3 py-1.5 rounded-lg font-bold transition-all flex items-center gap-1.5" 
                                :class="typeFilter === 'disk' ? 'bg-[#003a70] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900'">
                            <span>💾 Storage SSD</span>
                            <span class="text-[10px] px-1.5 py-0.5 rounded-full" :class="typeFilter === 'disk' ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-700'" x-text="thresholdItems.filter(i => i.type === 'disk').length"></span>
                        </button>

                        <button type="button" @click="typeFilter = 'needed'" 
                                class="px-3 py-1.5 rounded-lg font-bold transition-all flex items-center gap-1.5" 
                                :class="typeFilter === 'needed' ? 'bg-red-600 text-white shadow-sm' : 'text-red-700 hover:bg-red-50'">
                            <span>🚨 Butuh Tambahan</span>
                            <span class="text-[10px] px-1.5 py-0.5 rounded-full" :class="typeFilter === 'needed' ? 'bg-white/20 text-white' : 'bg-red-100 text-red-800'" x-text="thresholdItems.filter(i => i.add_resource > 0).length"></span>
                        </button>
                    </div>

                    <!-- Search Input -->
                    <div class="relative w-full md:w-64">
                        <input type="text" x-model="searchQuery" placeholder="Cari nama server..." 
                               class="w-full pl-8 pr-3 py-1.5 rounded-xl border border-slate-300 text-slate-800 text-xs focus:ring-2 focus:ring-[#003a70] focus:outline-none">
                        <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                </div>

                <!-- ==================================================================== -->
                <!-- TABEL THRESHOLD DENGAN STATUS SLA & BEDAH RUMUS AUDIT                 -->
                <!-- ==================================================================== -->
                <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-sm">
                    <table class="w-full text-left text-xs sm:text-sm">
                        <thead class="bg-[#002244] text-white uppercase text-[11px] font-bold tracking-wider">
                            <tr>
                                <th class="p-3 w-12 text-center">
                                    <span>Action</span>
                                    <span class="block text-[9px] font-normal text-slate-300 lowercase">hapus</span>
                                </th>
                                <th class="p-3 min-w-[140px]">
                                    <span>Deskripsi Item</span>
                                    <span class="block text-[9px] font-normal text-slate-300 lowercase">nama server</span>
                                </th>
                                <th class="p-3 w-24">
                                    <span>Tipe</span>
                                    <span class="block text-[9px] font-normal text-slate-300 lowercase">kategori</span>
                                </th>
                                <th class="p-3 w-28 bg-[#003a70] text-center">
                                    <span>Spesifikasi</span>
                                    <span class="block text-[9px] font-normal text-amber-300 lowercase">terpasang</span>
                                </th>
                                <th class="p-3 w-24 bg-sky-900 text-center">
                                    <span>Utilisasi</span>
                                    <span class="block text-[9px] font-normal text-sky-200 lowercase">riil (%)</span>
                                </th>
                                <th class="p-3 w-24 bg-sky-950 text-center">
                                    <span>Current</span>
                                    <span class="block text-[9px] font-normal text-slate-300 lowercase">pemakaian</span>
                                </th>
                                <th class="p-3 w-24 bg-emerald-800 text-center">
                                    <span x-text="'Growth'">Growth</span>
                                    <span class="block text-[9px] font-normal text-emerald-200 lowercase" x-text="growthUnitTableLabel"></span>
                                </th>
                                <th class="p-3 w-28 bg-blue-900 text-center">
                                    <span>Prediksi Nanti</span>
                                    <span class="block text-[9px] font-normal text-blue-200 lowercase">setelah growth</span>
                                </th>
                                <th class="p-3 w-28 text-center bg-slate-800">
                                    <span>Status & SLA</span>
                                    <span class="block text-[9px] font-normal text-amber-300 lowercase">urgensi pengadaan</span>
                                </th>
                                <th class="p-3 min-w-[210px] bg-emerald-700 text-center">
                                    <span>Rekomendasi & Audit</span>
                                    <span class="block text-[9px] font-normal text-emerald-100 lowercase">analisis & bedah rumus</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            <template x-for="(item, index) in filteredThresholdItems" :key="'threshold-'+index">
                                <tr class="hover:bg-blue-50/40 transition-colors">
                                    <!-- 1. Action -->
                                    <td class="p-3 text-center">
                                        <button @click="removeThresholdItem(index)" title="Hapus baris" class="w-7 h-7 rounded-lg inline-flex items-center justify-center text-red-500 hover:text-red-700 hover:bg-red-50 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </td>

                                    <!-- 2. Deskripsi Item -->
                                    <td class="p-3">
                                        <input type="text" x-model="item.name" @input="recalcThreshold()" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 text-slate-800 text-xs focus:ring-2 focus:ring-[#003a70] focus:outline-none" placeholder="Contoh: Server Host">
                                    </td>

                                    <!-- 3. Tipe (CPU, Memory, Disk) -->
                                    <td class="p-3">
                                        <select x-model="item.type" @change="onTypeChange(item); recalcThreshold()" class="w-full px-2 py-1.5 rounded-lg border border-slate-300 text-slate-800 text-xs font-semibold focus:ring-2 focus:ring-[#003a70] focus:outline-none bg-white">
                                            <option value="cpu">💻 CPU</option>
                                            <option value="memory">🧠 Memory</option>
                                            <option value="disk">💾 Disk</option>
                                        </select>
                                    </td>

                                    <!-- 4. Spesifikasi (Kapasitas Terpasang) -->
                                    <td class="p-3 bg-blue-50/40">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <input type="number" step="any" min="1" x-model.number="item.spec_value" @input="recalcThreshold()" class="w-16 px-2 py-1 rounded-md border border-slate-300 text-center font-bold text-slate-900 text-xs focus:ring-2 focus:ring-[#003a70] focus:outline-none">
                                            <span class="text-xs font-bold text-slate-600" x-text="item.spec_unit"></span>
                                        </div>
                                    </td>

                                    <!-- 5. Utilisasi Saat Ini (%) -->
                                    <td class="p-3 bg-sky-50/40 text-center">
                                        <div class="inline-flex items-center justify-center gap-1">
                                            <input type="number" step="any" min="0" max="100" x-model.number="item.current_util_percent" @input="recalcThreshold()" class="w-14 px-1.5 py-1 rounded-md border text-center font-bold text-xs focus:outline-none" :class="item.current_util_percent >= 80 ? 'text-red-600 border-red-300 bg-red-50' : (item.current_util_percent >= 70 ? 'text-amber-600 border-amber-300 bg-amber-50' : 'text-emerald-700 border-emerald-300 bg-emerald-50')">
                                            <span class="text-xs font-bold text-slate-600">%</span>
                                        </div>
                                    </td>

                                    <!-- 6. Current Usage (Pemakaian Riil Sekarang) -->
                                    <td class="p-3 text-center font-bold text-slate-800 bg-slate-50">
                                        <span x-text="calcCurrentUsage(item)"></span>
                                        <span class="text-[10px] text-slate-500" x-text="item.spec_unit"></span>
                                    </td>

                                    <!-- 7. Growth Est (%) - Input Pertumbuhan -->
                                    <td class="p-3 bg-emerald-50/30 text-center">
                                        <div class="inline-flex items-center justify-center gap-1">
                                            <input type="number" step="any" min="0" max="500" x-model.number="item.growth_est_percent" @input="recalcThreshold()" class="w-14 px-1.5 py-1 rounded-md border border-emerald-300 bg-white text-center font-bold text-emerald-800 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                            <span class="text-xs font-bold text-emerald-700">%</span>
                                        </div>
                                    </td>

                                    <!-- 8. PREDIKSI BEBAN MASA DEPAN -->
                                    <td class="p-3 bg-blue-50/60 text-center">
                                        <div class="space-y-0.5">
                                            <div class="font-extrabold text-xs text-[#003a70]">
                                                <span x-text="calcProjectedUsage(item)"></span>
                                                <span class="text-[10px]" x-text="item.spec_unit"></span>
                                            </div>
                                            <div class="text-[11px] font-black"
                                                 :class="calcProjectedUtilPercent(item) >= getItemThreshold(item) ? 'text-red-600' : (calcProjectedUtilPercent(item) >= getItemSafePct(item) ? 'text-amber-600' : 'text-emerald-600')">
                                                <span x-text="calcProjectedUtilPercent(item) + '%'"></span>
                                                <span x-show="calcProjectedUtilPercent(item) >= getItemThreshold(item)" title="Menembus Threshold!">⚠️</span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- 9. STATUS & SLA PROCUREMENT -->
                                    <td class="p-3 text-center">
                                        <template x-if="calcProjectedUtilPercent(item) >= getItemThreshold(item)">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-red-100 text-red-800 border border-red-300 text-[10px] font-black tracking-tight" title="Utilisasi menembus batas. Butuh pengadaan darurat segera.">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-600 animate-ping"></span>
                                                🔴 Kritis (&lt;30 Hari)
                                            </span>
                                        </template>
                                        <template x-if="calcProjectedUtilPercent(item) >= getItemSafePct(item) && calcProjectedUtilPercent(item) < getItemThreshold(item)">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 border border-amber-300 text-[10px] font-black tracking-tight" title="Mendekati buffer. Rencanakan pengadaan kuartal ini.">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                🟡 Rencanakan (30-90 Hari)
                                            </span>
                                        </template>
                                        <template x-if="calcProjectedUtilPercent(item) < getItemSafePct(item)">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300 text-[10px] font-bold" title="Kapasitas masih longgar dan optimal.">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                🟢 Aman
                                            </span>
                                        </template>
                                    </td>

                                    <!-- 10. REKOMENDASI TAMBAH + TOMBOL BEDAH RUMUS & ANIMASI -->
                                    <td class="p-3 bg-emerald-50/70 text-center">
                                        <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                            <!-- Tombol Bedah Rumus Popover -->
                                            <button type="button" @click="openCalcBreakdown(item)" title="Lihat langkah matematis detail & transparansi rumus hitungan" class="px-2 py-1 rounded-lg bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs border border-slate-300 shadow-sm transition-all hover:scale-105">
                                                <span>🧮 Rumus</span>
                                            </button>

                                            <!-- JIKA PERLU TAMBAHAN -->
                                            <template x-if="item.add_resource > 0">
                                                <button type="button" @click="openAnimModal(item)" title="Klik untuk melihat animasi sebelum vs sesudah penambahan" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-sm transition-all hover:scale-105 active:scale-95 group">
                                                    <span>+<span x-text="item.add_resource"></span> <span x-text="item.spec_unit"></span></span>
                                                    <span class="text-[10px] bg-emerald-800 text-white px-1 py-0.2 rounded font-bold">🎬 Cek</span>
                                                </button>
                                            </template>

                                            <!-- JIKA RESOURCE MASIH CUKUP -->
                                            <template x-if="item.add_resource <= 0">
                                                <button type="button" @click="openAnimModal(item)" title="Klik untuk melihat status kapasitas" class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs border border-slate-300 transition-all hover:scale-105">
                                                    <span class="text-emerald-600 font-bold">✓</span>
                                                    <span>Cukup (0 <span x-text="item.spec_unit"></span>)</span>
                                                </button>
                                            </template>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Tambah Data Rekomendasi Button -->
                <div class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <button @click="addThresholdItem()" class="w-full sm:w-auto px-5 py-2.5 rounded-xl border-2 border-dashed border-[#003a70] text-[#003a70] hover:bg-blue-50 font-bold text-xs sm:text-sm flex items-center justify-center gap-2 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        <span>+ Tambah Data Rekomendasi</span>
                    </button>

                    <div class="text-xs text-slate-700 bg-blue-50/90 px-3.5 py-2 rounded-xl border border-blue-200 flex items-center gap-2 shadow-sm">
                        <span class="text-base text-[#003a70]">💡</span>
                        <span>
                            <strong>Alur Logika:</strong> Kolom <em>Prediksi Nanti</em> otomatis menghitung kenaikan beban dari <em>Growth</em>. Jika angka persentasenya menembus batas aman threshold per resource (<span x-text="(cpu_threshold - buffer_percent) + '%'"></span> untuk CPU), kolom <em>Rekomendasi</em> akan otomatis memunculkan jumlah hardware yang wajib ditambah.
                        </span>
                    </div>
                </div>

                <!-- PANDUAN LENGKAP MEMBACA TABEL (KHUSUS ORANG AWAM) -->
                <div class="mt-6 pt-5 border-t border-slate-200 grid grid-cols-1 md:grid-cols-4 gap-3 text-xs">
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <span class="font-bold text-[#003a70] block">1. Utilisasi Saat Ini</span>
                        <p class="text-slate-600 mt-1">Menunjukkan seberapa padat server detik ini. Hijau jika di bawah 70%, Kuning jika 70-79%, Merah jika $\ge$80%.</p>
                    </div>

                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <span class="font-bold text-emerald-800 block">2. Growth (Pertumbuhan)</span>
                        <p class="text-slate-600 mt-1">Perkiraan kenaikan transaksi nasabah (misal: 20%). Ketika dinaikkan, kolom <em>Prediksi Nanti</em> akan langsung membengkak.</p>
                    </div>

                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <span class="font-bold text-blue-900 block">3. Prediksi Nanti</span>
                        <p class="text-slate-600 mt-1">Volume beban masa depan. Jika angka persentasenya menembus threshold (80%), server akan ditandai ⚠️.</p>
                    </div>

                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <span class="font-bold text-emerald-700 block">4. Rekomendasi Tambah</span>
                        <p class="text-slate-600 mt-1">Jumlah Core/GB yang harus diajukan ke bagian procurement agar server turun ke zona aman (78%).</p>
                    </div>
                </div>

            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 3: DATA PROJECTION CHART               -->
        <!-- ========================================== -->
        <div x-show="activeTab === 'chart'" class="space-y-6">
            <div class="bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl p-6 border border-slate-200 text-slate-900 print-card">
                
                <div class="border-b border-slate-200 pb-4 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                            <span>📈 Capacity Projection Line Chart</span>
                            <span class="text-xs px-2.5 py-0.5 rounded-full bg-blue-100 text-[#003a70] font-bold" x-text="'Skala: ' + (growth_unit === 'month' ? '12 Bulan' : (growth_unit === 'year' ? '5 Tahun' : 'Proyeksi Hari'))"></span>
                        </h2>
                        <p class="text-xs text-slate-500">Kurva proyeksi tingkat pemakaian (*Usage To Be*) setiap periode waktu. Garis putus-putus merah adalah batas ambang threshold bahaya.</p>
                    </div>

                    <div class="flex items-center gap-4 text-xs bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-200">
                        <span class="inline-flex items-center gap-1.5 text-red-600 font-bold">
                            <span class="w-4 h-0.5 border-t-2 border-dashed border-red-500"></span> Batas Threshold
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-[#003a70] font-bold">
                            <span class="w-3 h-3 rounded-full bg-[#003a70]"></span> Utilisasi Server
                        </span>
                    </div>
                </div>

                <div class="space-y-8">
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#003a70]"></span> CPU Projection
                            </h3>
                            <span class="text-xs text-slate-500">Batas Threshold: <strong x-text="cpu_threshold + '%'"></strong></span>
                        </div>
                        <div class="h-64 sm:h-72 w-full"><canvas id="cpuChartCanvas"></canvas></div>
                    </div>

                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-purple-600"></span> Memory Projection
                            </h3>
                            <span class="text-xs text-slate-500">Batas Threshold: <strong x-text="mem_threshold + '%'"></strong></span>
                        </div>
                        <div class="h-64 sm:h-72 w-full"><canvas id="memChartCanvas"></canvas></div>
                    </div>

                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-600"></span> Disk Projection
                            </h3>
                            <span class="text-xs text-slate-500">Batas Threshold: <strong x-text="disk_threshold + '%'"></strong></span>
                        </div>
                        <div class="h-64 sm:h-72 w-full"><canvas id="diskChartCanvas"></canvas></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 4: DATA PROJECTION BAR (DIBAGI PER KATEGORI: CPU, MEMORY, DISK) -->
        <!-- ========================================== -->
        <div x-show="activeTab === 'bar'" class="space-y-6">
            <div class="bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl p-6 border border-slate-200 text-slate-900 print-card space-y-6">
                
                <!-- Header with Category Tabs -->
                <div class="flex flex-col lg:flex-row lg:items-center justify-between border-b border-slate-200 pb-5 gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-2xl">📊</span>
                            <h2 class="text-xl font-bold text-slate-900">Data Projection Bar (Perbandingan Kapasitas Terpisah)</h2>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">
                            Grafik batang dipisahkan per kategori (<strong>CPU Core, Memory RAM, dan Storage SSD</strong>) agar skala kapasitas proporsional dan tidak gepeng tertutup kapasitas Disk.
                        </p>
                    </div>

                    <!-- Category Switcher Pill for Bar Chart -->
                    <div class="flex flex-wrap items-center bg-slate-100 p-1 rounded-xl border border-slate-200 text-xs shadow-inner">
                        <button type="button" @click="barCategoryTab = 'all'; renderBarCharts()" 
                                class="px-3 py-1.5 rounded-lg font-bold transition-all flex items-center gap-1" 
                                :class="barCategoryTab === 'all' ? 'bg-[#003a70] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900'">
                            <span>🌐 Semua (3 Kolom)</span>
                        </button>
                        <button type="button" @click="barCategoryTab = 'cpu'; renderBarCharts()" 
                                class="px-3 py-1.5 rounded-lg font-bold transition-all flex items-center gap-1" 
                                :class="barCategoryTab === 'cpu' ? 'bg-[#003a70] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900'">
                            <span>💻 CPU Core</span>
                        </button>
                        <button type="button" @click="barCategoryTab = 'memory'; renderBarCharts()" 
                                class="px-3 py-1.5 rounded-lg font-bold transition-all flex items-center gap-1" 
                                :class="barCategoryTab === 'memory' ? 'bg-[#003a70] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900'">
                            <span>🧠 Memory RAM</span>
                        </button>
                        <button type="button" @click="barCategoryTab = 'disk'; renderBarCharts()" 
                                class="px-3 py-1.5 rounded-lg font-bold transition-all flex items-center gap-1" 
                                :class="barCategoryTab === 'disk' ? 'bg-[#003a70] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900'">
                            <span>💾 Storage SSD</span>
                        </button>
                    </div>
                </div>

                <!-- 3 Responsive Cards for CPU, Memory, Disk -->
                <div class="grid grid-cols-1 gap-6" :class="barCategoryTab === 'all' ? 'lg:grid-cols-3' : 'lg:grid-cols-1'">
                    
                    <!-- 1. CPU Core Chart Card -->
                    <div x-show="barCategoryTab === 'all' || barCategoryTab === 'cpu'" 
                         class="p-5 rounded-2xl border border-blue-200 bg-slate-50/70 shadow-sm flex flex-col justify-between space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-8 h-8 rounded-lg bg-blue-100 text-[#003a70] flex items-center justify-center font-bold text-base">💻</span>
                                <div>
                                    <h3 class="font-extrabold text-sm text-slate-900">CPU Core Comparison</h3>
                                    <span class="text-[11px] text-slate-500">Skala: Core Prosesor (0 - 25 Core)</span>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full bg-blue-100 text-[#003a70] font-black text-xs" 
                                  x-text="'Tambahan: +' + totalThresholdRecCpu + ' Core'"></span>
                        </div>
                        <div class="w-full" :class="barCategoryTab === 'all' ? 'h-72' : 'h-80 sm:h-96'"><canvas id="barCpuCanvas"></canvas></div>
                        <div class="text-[11px] text-slate-600 bg-white p-2.5 rounded-lg border border-slate-200 flex items-center justify-between">
                            <span>Total Terpasang: <strong class="text-slate-900" x-text="totalInstalledCpu + ' Core'"></strong></span>
                            <span>Riil: <strong class="text-[#003a70]" x-text="totalUsedCpu + ' Core'"></strong></span>
                        </div>
                    </div>

                    <!-- 2. Memory RAM Chart Card -->
                    <div x-show="barCategoryTab === 'all' || barCategoryTab === 'memory'" 
                         class="p-5 rounded-2xl border border-purple-200 bg-slate-50/70 shadow-sm flex flex-col justify-between space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-8 h-8 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-base">🧠</span>
                                <div>
                                    <h3 class="font-extrabold text-sm text-slate-900">Memory RAM Comparison</h3>
                                    <span class="text-[11px] text-slate-500">Skala: Gigabyte (GB RAM)</span>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full bg-purple-100 text-purple-800 font-black text-xs" 
                                  x-text="'Tambahan: +' + totalThresholdRecMemory + ' GB'"></span>
                        </div>
                        <div class="w-full" :class="barCategoryTab === 'all' ? 'h-72' : 'h-80 sm:h-96'"><canvas id="barMemCanvas"></canvas></div>
                        <div class="text-[11px] text-slate-600 bg-white p-2.5 rounded-lg border border-slate-200 flex items-center justify-between">
                            <span>Total Terpasang: <strong class="text-slate-900" x-text="totalInstalledMem + ' GB'"></strong></span>
                            <span>Riil: <strong class="text-purple-700" x-text="totalUsedMem + ' GB'"></strong></span>
                        </div>
                    </div>

                    <!-- 3. Storage SSD Chart Card -->
                    <div x-show="barCategoryTab === 'all' || barCategoryTab === 'disk'" 
                         class="p-5 rounded-2xl border border-amber-200 bg-slate-50/70 shadow-sm flex flex-col justify-between space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-base">💾</span>
                                <div>
                                    <h3 class="font-extrabold text-sm text-slate-900">Storage SSD Comparison</h3>
                                    <span class="text-[11px] text-slate-500">Skala: Gigabyte (GB Storage)</span>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 font-black text-xs" 
                                  x-text="'Tambahan: +' + totalThresholdRecDisk + ' GB'"></span>
                        </div>
                        <div class="w-full" :class="barCategoryTab === 'all' ? 'h-72' : 'h-80 sm:h-96'"><canvas id="barDiskCanvas"></canvas></div>
                        <div class="text-[11px] text-slate-600 bg-white p-2.5 rounded-lg border border-slate-200 flex items-center justify-between">
                            <span>Total Terpasang: <strong class="text-slate-900" x-text="totalInstalledDisk + ' GB'"></strong></span>
                            <span>Riil: <strong class="text-amber-700" x-text="totalUsedDisk + ' GB'"></strong></span>
                        </div>
                    </div>

                </div>

                <!-- Petunjuk Awam & Legend -->
                <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 flex items-start gap-3 text-xs text-blue-950">
                    <span class="text-xl">ℹ️</span>
                    <div>
                        <strong class="font-bold">Interpretasi Grafik Batang Kategori:</strong>
                        <p class="mt-0.5">
                            Setiap kategori resource kini memiliki grafik dengan skala Y mandiri. Kapasitas kecil seperti CPU (4-18 Core) dan Memory (6 GB) terlihat jelas dan tegak, tidak lagi tertindih oleh kapasitas Disk (1000 GB).
                            <span class="inline-block ml-1 font-semibold text-slate-700">Abu-abu: Kapasitas Terpasang Saat Ini • Kuning: Pemakaian Riil Saat Ini • Hijau: Rekomendasi Setelah Ditambah.</span>
                        </p>
                    </div>
                </div>

            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 5: RINGKASAN AWAM & ESTIMASI BUDGET    -->
        <!-- ========================================== -->
        <div x-show="activeTab === 'summary'" class="space-y-6">
            <div class="bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl p-6 sm:p-8 border border-slate-200 text-slate-900 print-card space-y-6">
                
                <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-200 pb-5 gap-3">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-[#003a70] bg-blue-50 px-3 py-1 rounded-full border border-blue-200">
                            Executive Layman Summary & CAPEX
                        </span>
                        <h2 class="text-2xl font-black text-slate-900 mt-2">Ringkasan Kesehatan & Rencana Kapasitas</h2>
                        <p class="text-xs text-slate-500">Laporan bahasa awam yang siap dipresentasikan ke tim bisnis, manajemen, atau pimpinan Bank Mandiri.</p>
                    </div>

                    <div class="flex items-center gap-2 no-print">
                        <button @click="copySummaryToClipboard()" 
                                class="px-4 py-2 rounded-xl bg-[#003a70] hover:bg-[#002d59] text-white text-xs font-bold flex items-center gap-2 transition-all shadow-md">
                            <svg class="w-4 h-4 text-[#ffb700]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" /></svg>
                            <span x-text="copiedText ? 'Tersalin! ✅' : 'Salin Laporan Text'"></span>
                        </button>
                    </div>
                </div>

                <!-- Status Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="p-4 rounded-xl border border-red-200 bg-red-50/70">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-red-700">Status Kritis (Bahaya)</span>
                            <span class="w-3 h-3 rounded-full bg-red-500 animate-pulse"></span>
                        </div>
                        <div class="text-2xl font-black text-red-900 mt-1" x-text="criticalCount + ' Server'"></div>
                        <p class="text-[11px] text-red-700 mt-1">Utilisasi di atas 80%. Sangat rentan crash atau error saat ada lonjakan transaksi.</p>
                    </div>

                    <div class="p-4 rounded-xl border border-amber-200 bg-amber-50/70">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-amber-700">Status Waspada</span>
                            <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                        </div>
                        <div class="text-2xl font-black text-amber-900 mt-1" x-text="warningCount + ' Server'"></div>
                        <p class="text-[11px] text-amber-700 mt-1">Utilisasi antara 70% - 79%. Perlu segera direncanakan penambahan.</p>
                    </div>

                    <div class="p-4 rounded-xl border border-emerald-200 bg-emerald-50/70">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">Status Aman</span>
                            <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                        </div>
                        <div class="text-2xl font-black text-emerald-900 mt-1" x-text="safeCount + ' Server'"></div>
                        <p class="text-[11px] text-emerald-700 mt-1">Utilisasi di bawah 70%. Kapasitas masih sangat memadai.</p>
                    </div>
                </div>

                <!-- KALKULATOR ESTIMASI BIAYA / BUDGETING PENGADAAN -->
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200 pb-3">
                        <div>
                            <h3 class="font-extrabold text-base text-slate-900 flex items-center gap-2">
                                <span>💰 Estimasi Anggaran Belanja Pengadaan (CAPEX)</span>
                                <span class="text-[11px] px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold">Otomatis Dihitung</span>
                            </h3>
                            <p class="text-xs text-slate-500">Perkiraan total biaya pengadaan penambahan resource berdasarkan estimasi harga rata-rata.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
                        <div class="p-3 bg-white rounded-xl border border-slate-200 space-y-1">
                            <span class="text-slate-500 font-semibold">Harga Est. per Core CPU:</span>
                            <div class="flex items-center gap-1 font-bold text-slate-900">
                                <span>Rp</span>
                                <input type="number" step="100000" x-model.number="costPerCpuCore" class="w-full text-right font-black border-b border-slate-300 focus:outline-none text-xs">
                            </div>
                        </div>

                        <div class="p-3 bg-white rounded-xl border border-slate-200 space-y-1">
                            <span class="text-slate-500 font-semibold">Harga Est. per GB RAM:</span>
                            <div class="flex items-center gap-1 font-bold text-slate-900">
                                <span>Rp</span>
                                <input type="number" step="10000" x-model.number="costPerRamGb" class="w-full text-right font-black border-b border-slate-300 focus:outline-none text-xs">
                            </div>
                        </div>

                        <div class="p-3 bg-white rounded-xl border border-slate-200 space-y-1">
                            <span class="text-slate-500 font-semibold">Harga Est. per GB SSD:</span>
                            <div class="flex items-center gap-1 font-bold text-slate-900">
                                <span>Rp</span>
                                <input type="number" step="5000" x-model.number="costPerDiskGb" class="w-full text-right font-black border-b border-slate-300 focus:outline-none text-xs">
                            </div>
                        </div>

                        <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-300 space-y-1">
                            <span class="text-emerald-800 font-extrabold uppercase text-[10px] tracking-wider">Total Est. Biaya Pengadaan</span>
                            <div class="text-base sm:text-lg font-black text-emerald-950 mt-0.5" x-text="formattedTotalEstimatedCost"></div>
                        </div>
                    </div>
                </div>

                <!-- Narasi Rekomendasi Belanja / Upgrade Resource -->
                <div class="p-6 rounded-2xl bg-gradient-to-r from-[#002244] to-[#003a70] text-white space-y-4 shadow-xl border border-white/10">
                    <div class="flex items-center justify-between">
                        <h3 class="font-extrabold text-base sm:text-lg text-[#ffb700] flex items-center gap-2">
                            <span>🎯 Rekomendasi Total Penambahan Resource</span>
                        </h3>
                        <span class="text-xs px-2.5 py-1 rounded-full bg-white/10 text-slate-200 font-semibold" 
                              x-text="'Basis Growth: ' + (growth_unit === 'month' ? 'Bulanan' : (growth_unit === 'year' ? 'Tahunan' : 'Harian'))"></span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-200 leading-relaxed">
                        Agar seluruh infrastruktur aplikasi dan database Bank Mandiri tetap berjalan stabil dalam ambang batas aman 
                        <strong class="text-[#ffb700]" x-text="cpu_threshold + '%'"></strong> (dengan buffer cadangan <span x-text="buffer_percent + '%'"></span>), berikut total pengadaan penambahan yang direkomendasikan:
                    </p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                        <div class="bg-white/10 backdrop-blur-sm p-4 rounded-xl border border-white/10">
                            <span class="text-xs text-sky-200 font-medium">Tambahan CPU Core</span>
                            <div class="text-2xl sm:text-3xl font-black text-white mt-0.5">
                                +<span x-text="totalThresholdRecCpu"></span> <span class="text-sm font-semibold text-slate-300">Core</span>
                            </div>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm p-4 rounded-xl border border-white/10">
                            <span class="text-xs text-purple-200 font-medium">Tambahan RAM Memory</span>
                            <div class="text-2xl sm:text-3xl font-black text-white mt-0.5">
                                +<span x-text="totalThresholdRecMemory"></span> <span class="text-sm font-semibold text-slate-300">GB</span>
                            </div>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm p-4 rounded-xl border border-white/10">
                            <span class="text-xs text-amber-200 font-medium">Tambahan Storage SSD</span>
                            <div class="text-2xl sm:text-3xl font-black text-white mt-0.5">
                                +<span x-text="totalThresholdRecDisk"></span> <span class="text-sm font-semibold text-slate-300">GB</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detail Catatan Per Server -->
                <div class="space-y-3">
                    <h4 class="font-bold text-slate-900 text-sm">Rincian Kondisi Tiap Item Server:</h4>
                    <div class="divide-y divide-slate-100 border border-slate-200 rounded-xl overflow-hidden">
                        <template x-for="item in thresholdItems" :key="'sum-'+item.name">
                            <div class="p-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 hover:bg-slate-50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <span class="w-3 h-3 rounded-full flex-shrink-0"
                                          :class="item.current_util_percent >= 80 ? 'bg-red-500' : (item.current_util_percent >= 70 ? 'bg-amber-500' : 'bg-emerald-500')"></span>
                                    <div>
                                        <div class="font-bold text-slate-900 text-xs sm:text-sm">
                                            <span x-text="item.name"></span>
                                            <span class="ml-1 text-[11px] font-semibold text-[#003a70] uppercase bg-blue-50 px-2 py-0.5 rounded border border-blue-100" x-text="item.type"></span>
                                        </div>
                                        <div class="text-xs text-slate-500 mt-0.5">
                                            Kapasitas Terpasang: <span class="font-semibold text-slate-700" x-text="item.spec_value + ' ' + item.spec_unit"></span> |
                                            Pemakaian: <span class="font-semibold text-slate-700" x-text="calcCurrentUsage(item) + ' ' + item.spec_unit + ' (' + item.current_util_percent + '%)'"></span> |
                                            Prediksi Nanti: <span class="font-bold" :class="calcProjectedUtilPercent(item) >= getItemThreshold(item) ? 'text-red-600' : 'text-[#003a70]'" x-text="calcProjectedUsage(item) + ' ' + item.spec_unit + ' (' + calcProjectedUtilPercent(item) + '%)'"></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="sm:text-right">
                                    <template x-if="item.add_resource > 0">
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-900 text-xs font-black border border-emerald-300">
                                            <span>Rekomendasi Tambah:</span>
                                            <span x-text="'+' + item.add_resource + ' ' + item.spec_unit"></span>
                                        </div>
                                    </template>
                                    <template x-if="item.add_resource <= 0">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-semibold">
                                            ✓ Masih Cukup
                                        </span>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

            </div>
        </div>

    </main>

    <!-- Footer with Mandirian Signature -->
    <footer class="relative z-10 border-t border-white/20 py-8 bg-[#001428]/95 backdrop-blur-md text-slate-400 no-print mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                
                <div class="flex items-center gap-3">
                    <div class="bg-white p-2 rounded-lg shadow-sm">
                        <img src="{{ asset('images/mandiri-logo.svg') }}" alt="Bank Mandiri" class="h-5 w-auto">
                    </div>
                    <div class="text-xs text-left">
                        <p class="font-bold text-slate-200">PT Bank Mandiri (Persero) Tbk.</p>
                        <p class="text-slate-400">IT Infrastructure, Capacity Planning & Performance Engineering</p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-3">
                    <div class="px-4 py-1.5 rounded-full bg-[#002244] border border-[#ffb700]/50 text-xs text-slate-200 flex items-center gap-2 shadow-inner">
                        <span class="w-2 h-2 rounded-full bg-[#ffb700] animate-pulse"></span>
                        <span>Designed & Built with Mandirian Spirit</span>

                    </div>
                </div>

            </div>

            <div class="mt-4 pt-4 border-t border-white/5 text-center text-[11px] text-slate-400">
                © {{ date('Y') }} DASHCAM Capacity Management Portal. Terkoneksi ke SQLite Database Lokal.
            </div>
        </div>
    </footer>

    <!-- Alpine.js Application Script -->
    <script>
        function calculatorApp(initialScenario, initialItems) {
            return {
                scenarioId: initialScenario.id || 1,
                activeTab: 'threshold',
                
                // Threshold Parameters
                cpu_threshold: initialScenario.cpu_threshold ?? 80,
                mem_threshold: initialScenario.mem_threshold ?? 80,
                disk_threshold: initialScenario.disk_threshold ?? 80,
                buffer_percent: initialScenario.buffer_percent ?? 1,
                growth_enabled: initialScenario.growth_enabled ?? true,
                growth_unit: initialScenario.growth_unit || 'month',
                growth_months: initialScenario.growth_months ?? 1,

                // Search & Filter
                searchQuery: '',
                typeFilter: 'all',

                // Cost Estimation Parameters (Rupiah)
                costPerCpuCore: 2500000,
                costPerRamGb: 150000,
                costPerDiskGb: 25000,
                
                // Items
                manualItems: [],
                thresholdItems: [],
                
                // DB Sync Status
                syncStatus: 'saved',
                syncStatusText: 'Tersimpan di SQLite',
                debounceTimer: null,
                copiedText: false,

                // Info Popover / Modal state
                infoModal: {
                    open: false,
                    title: '',
                    content: '',
                    example: ''
                },

                // Interactive Animation Modal state
                animModal: {
                    open: false,
                    item: null,
                    state: 'before' // 'before' or 'after'
                },

                // Formula Breakdown & Audit Modal state
                calcBreakdownModal: {
                    open: false,
                    item: null,
                    stepData: null
                },
                
                // Chart instances
                cpuChart: null,
                memChart: null,
                diskChart: null,

                // Bar Chart Category filter & instances
                barCategoryTab: 'all',
                barCpuChart: null,
                barMemChart: null,
                barDiskChart: null,

                initApp() {
                    const mItems = initialItems.filter(i => i.mode === 'manual');
                    const tItems = initialItems.filter(i => i.mode === 'threshold');

                    if (mItems.length > 0) {
                        this.manualItems = mItems;
                    } else {
                        this.manualItems = [
                            { name: 'Aplikasi Web', type: 'cpu', spec_value: 16, spec_unit: 'Core', current_util_percent: 60, target_util_percent: 60, add_resource: 0 }
                        ];
                    }

                    if (tItems.length > 0) {
                        this.thresholdItems = tItems;
                    } else {
                        this.thresholdItems = [
                            { name: 'Faldy', type: 'cpu', spec_value: 18, spec_unit: 'Core', current_util_percent: 1, target_util_percent: 1, growth_est_percent: 1, add_resource: 0 },
                            { name: 'Tyo', type: 'cpu', spec_value: 4, spec_unit: 'Core', current_util_percent: 90, target_util_percent: 72, growth_est_percent: 0, add_resource: 1 },
                            { name: 'Heni', type: 'memory', spec_value: 6, spec_unit: 'GB', current_util_percent: 75, target_util_percent: 40.91, growth_est_percent: 80, add_resource: 5 },
                            { name: 'SSD FALDY', type: 'disk', spec_value: 1000, spec_unit: 'GB', current_util_percent: 8, target_util_percent: 8, growth_est_percent: 1, add_resource: 0 }
                        ];
                    }

                    this.recalcManual(false);
                    this.recalcThreshold(false);
                },

                // Filtered Threshold items for Search & Type filter
                get filteredThresholdItems() {
                    return this.thresholdItems.filter(item => {
                        const matchesSearch = item.name.toLowerCase().includes(this.searchQuery.toLowerCase());
                        let matchesType = true;
                        if (this.typeFilter === 'needed') {
                            matchesType = item.add_resource > 0;
                        } else if (this.typeFilter !== 'all') {
                            matchesType = item.type === this.typeFilter;
                        }
                        return matchesSearch && matchesType;
                    });
                },

                // Total Estimated Cost formatted to Rupiah
                get totalEstimatedCost() {
                    const cpuCost = this.totalThresholdRecCpu * this.costPerCpuCore;
                    const ramCost = this.totalThresholdRecMemory * this.costPerRamGb;
                    const diskCost = this.totalThresholdRecDisk * this.costPerDiskGb;
                    return cpuCost + ramCost + diskCost;
                },

                get formattedTotalEstimatedCost() {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(this.totalEstimatedCost);
                },

                setGrowthUnit(unit) {
                    this.growth_unit = unit;
                    if (unit === 'day' && this.growth_months < 7) {
                        this.growth_months = 30;
                    } else if (unit === 'year' && this.growth_months > 10) {
                        this.growth_months = 1;
                    } else if (unit === 'month' && this.growth_months > 60) {
                        this.growth_months = 1;
                    }
                    this.recalcThreshold();
                },

                get growthUnitLabel() {
                    if (this.growth_unit === 'day') return 'Durasi (Hari)';
                    if (this.growth_unit === 'year') return 'Durasi (Tahun)';
                    return 'Durasi (Bulan)';
                },

                get growthUnitTableLabel() {
                    if (this.growth_unit === 'day') return '%/Hari';
                    if (this.growth_unit === 'year') return '%/Thn';
                    return '%/Bln';
                },

                openAnimModal(item) {
                    this.animModal.item = item;
                    this.animModal.state = 'before';
                    this.animModal.open = true;
                },

                // Open Formula Audit Breakdown Modal
                openCalcBreakdown(item) {
                    if (!item) return;
                    const currentUsage = Number(this.calcCurrentUsage(item));
                    const duration = Number(this.growth_months) || 1;
                    const growthRatePct = Number(item.growth_est_percent) || 0;
                    const growthRate = growthRatePct / 100;
                    const growthMult = this.growth_enabled ? (1 + growthRate * duration) : 1;
                    const projectedUsage = Number((currentUsage * growthMult).toFixed(2));

                    let thr = this.cpu_threshold;
                    if (item.type === 'memory') thr = this.mem_threshold;
                    if (item.type === 'disk') thr = this.disk_threshold;

                    const buffer = this.buffer_percent;
                    const targetSafePct = Math.max(10, thr - buffer);
                    const maxSafeCapacity = Number((Number(item.spec_value) * (targetSafePct / 100)).toFixed(2));
                    const isExceeded = projectedUsage > maxSafeCapacity;

                    let neededTotalSpec = 0, rawAdditional = 0, finalAdd = 0, newCapacity = 0, newUtil = 0;
                    if (isExceeded) {
                        neededTotalSpec = Number((projectedUsage / (targetSafePct / 100)).toFixed(2));
                        rawAdditional = Number((neededTotalSpec - Number(item.spec_value)).toFixed(2));
                        finalAdd = Math.max(0, Math.ceil(rawAdditional));
                        newCapacity = Number(item.spec_value) + finalAdd;
                        newUtil = newCapacity > 0 ? Number(((projectedUsage / newCapacity) * 100).toFixed(2)) : 0;
                    }

                    this.calcBreakdownModal.item = item;
                    this.calcBreakdownModal.stepData = {
                        currentUsage, growthRatePct, duration, projectedUsage,
                        threshold: thr, buffer, targetSafePct, maxSafeCapacity,
                        isExceeded, neededTotalSpec, rawAdditional, finalAdd,
                        newCapacity, newUtil,
                    };
                    this.calcBreakdownModal.open = true;
                },

                // Apply Quick Scenario Preset
                applyScenarioPreset(preset) {
                    if (preset === 'baseline') {
                        this.thresholdItems.forEach(item => {
                            if (item.type === 'cpu') item.growth_est_percent = 5;
                            if (item.type === 'memory') item.growth_est_percent = 10;
                            if (item.type === 'disk') item.growth_est_percent = 8;
                        });
                    } else if (preset === 'peak') {
                        this.thresholdItems.forEach(item => {
                            item.growth_est_percent = 35;
                        });
                    } else if (preset === 'stress') {
                        this.thresholdItems.forEach(item => {
                            if (item.type === 'cpu') item.growth_est_percent = 80;
                            if (item.type === 'memory') item.growth_est_percent = 70;
                            if (item.type === 'disk') item.growth_est_percent = 60;
                        });
                    }
                    this.recalcThreshold();
                },

                toggleAnimPlay() {
                    this.animModal.state = 'before';
                    setTimeout(() => {
                        this.animModal.state = 'after';
                    }, 400);
                },

                showInfo(key) {
                    const explanations = {
                        buffer: {
                            title: 'Apa itu Buffer?',
                            content: `<strong>Buffer</strong> adalah batas cadangan pengaman darurat (safety margin) di bawah garis batas Threshold.<br><br>
                                      Bila Anda menetapkan <strong>Threshold = 80%</strong> dan <strong>Buffer = 1%</strong>, maka target batas aman sistem menjadi <strong>79% (80% - 1%)</strong>.<br><br>
                                      Dengan begitu, ketika terjadi lonjakan transaksi nasabah yang mendadak (misalnya saat <em>Payday</em>, promo belanja, atau batch akhir bulan), server memiliki ruang bernafas dan tidak langsung mengalami bottleneck atau crash.`,
                            example: 'Server transaksi dengan batas 80% dan buffer 1% akan diberi sinyal penambahan kapasitas saat utilisasi mendekati 79%.'
                        },
                        threshold: {
                            title: 'Apa itu Threshold?',
                            content: `<strong>Threshold</strong> adalah batas maksimal utilisasi yang diperbolehkan oleh kebijakan Enterprise Architecture Bank Mandiri sebelum server dinyatakan dalam kondisi <em>Warning / Critical</em>.<br><br>
                                      Batas standar pada perbankan adalah <strong>80%</strong>. Di atas 80%, respons time server mulai lambat dan risiko server timeout meningkat tajam.`,
                            example: 'Jika CPU server mencapai 90%, server sudah melewati threshold 80% dan harus segera ditambah core-nya.'
                        },
                        growth: {
                            title: 'Pilihan Satuan Pertumbuhan (Growth)',
                            content: `Anda bisa menghitung estimasi pertumbuhan dalam 3 skala waktu:<br>
                                      • <strong>Harian (Per Day)</strong>: Cocok saat ada peluncuran fitur baru / promo flash sale berdurasi beberapa hari.<br>
                                      • <strong>Bulanan (Per Month)</strong>: Standar capacity planning operasional bulanan IT.<br>
                                      • <strong>Tahunan (Per Year)</strong>: Cocok untuk penganggaran belanja modal (CAPEX) jangka panjang 1-5 tahun ke depan.`,
                            example: 'Jika rata-rata transaksi tumbuh 20% per bulan, masukkan Growth Est. 20% dengan durasi 1 bulan.'
                        },
                        threshold_mode: {
                            title: 'Logika Perhitungan Rekomendasi (Hanya Jika Kurang)',
                            content: `Sistem hanya akan merekomendasikan penambahan kapasitas <strong>jika beban server diperkirakan menembus batas aman (Threshold - Buffer)</strong>.<br><br>
                                      Bila utilisasi saat ini kecil (misalnya hanya 1%) dan pertumbuhan hanya 1%, kapasitas yang ada masih sangat cukup, sehingga <strong>Rekomendasi = 0</strong>.`,
                            example: 'Server Faldy (18 Core, utilisasi 1%) tidak memerlukan penambahan apapun karena 99% kapasitasnya masih menganggur.'
                        }
                    };

                    const info = explanations[key] || {
                        title: 'Informasi',
                        content: 'Detail parameter kalkulator utilisasi kapasitas.',
                        example: ''
                    };

                    this.infoModal.title = info.title;
                    this.infoModal.content = info.content;
                    this.infoModal.example = info.example;
                    this.infoModal.open = true;
                },

                onTypeChange(item) {
                    if (item.type === 'cpu') {
                        item.spec_unit = 'Core';
                    } else {
                        item.spec_unit = 'GB';
                    }
                },

                // 1. Current usage (pemakaian riil detik ini)
                calcCurrentUsage(item) {
                    if (!item) return 0;
                    const usage = (Number(item.spec_value) || 0) * ((Number(item.current_util_percent) || 0) / 100);
                    return Number(usage.toFixed(2));
                },

                // 2. Projected usage (pemakaian di masa depan setelah terkena growth)
                calcProjectedUsage(item) {
                    if (!item) return 0;
                    const currentUsage = this.calcCurrentUsage(item);
                    const duration = Number(this.growth_months) || 1;
                    const growthRate = (Number(item.growth_est_percent) || 0) / 100;
                    const growthMult = this.growth_enabled ? (1 + growthRate * duration) : 1;
                    const projected = currentUsage * growthMult;
                    return Number(projected.toFixed(2));
                },

                // 3. Persentase beban mendatang terhadap kapasitas saat ini (tanpa penambahan)
                calcProjectedUtilPercent(item) {
                    if (!item || !item.spec_value) return 0;
                    const projected = this.calcProjectedUsage(item);
                    const pct = (projected / Number(item.spec_value)) * 100;
                    return Number(pct.toFixed(2));
                },

                // 4. Persentase beban mendatang terhadap kapasitas baru yang sudah ditambah
                calcNewUtilWithGrowth(item) {
                    if (!item) return 0;
                    const projected = this.calcProjectedUsage(item);
                    const totalCapacity = Number(item.spec_value) + (Number(item.add_resource) || 0);
                    if (totalCapacity <= 0) return 0;
                    const pct = (projected / totalCapacity) * 100;
                    return Number(pct.toFixed(2));
                },

                recalcManual(triggerSave = true) {
                    this.manualItems.forEach(item => {
                        const currentUsage = item.spec_value * (item.current_util_percent / 100);
                        const targetUtil = item.target_util_percent > 0 ? (item.target_util_percent / 100) : 0.6;
                        
                        const neededTotal = currentUsage / targetUtil;
                        const additional = neededTotal - item.spec_value;
                        item.add_resource = additional > 0 ? Math.ceil(additional) : 0;
                    });

                    if (triggerSave) this.scheduleAutoSave();
                },

                // RECALCULATE THRESHOLD: LIVE & AKURAT TERHADAP PERUBAHAN GROWTH
                recalcThreshold(triggerSave = true) {
                    this.thresholdItems.forEach(item => {
                        const currentUsage = Number(item.spec_value) * (Number(item.current_util_percent) / 100);
                        
                        const duration = Number(this.growth_months) || 1;
                        const growthRate = (Number(item.growth_est_percent) || 0) / 100;
                        const growthMult = this.growth_enabled ? (1 + growthRate * duration) : 1;
                        
                        // BEBAN DI MASA DEPAN DENGAN GROWTH
                        const projectedUsage = currentUsage * growthMult;
                        
                        let thr = this.cpu_threshold;
                        if (item.type === 'memory') thr = this.mem_threshold;
                        if (item.type === 'disk') thr = this.disk_threshold;

                        // Target batas aman adalah (Threshold - Buffer) %
                        const targetSafePct = Math.max(10, thr - this.buffer_percent);
                        const maxSafeCapacity = Number(item.spec_value) * (targetSafePct / 100);

                        // HANYA REKOMENDASIKAN TAMBAHAN JIKA PROJECTED USAGE MELEBIHI BATAS AMAN!
                        if (projectedUsage > maxSafeCapacity) {
                            const neededTotalSpec = projectedUsage / (targetSafePct / 100);
                            const additional = neededTotalSpec - Number(item.spec_value);
                            item.add_resource = Math.max(0, Math.ceil(additional));
                        } else {
                            item.add_resource = 0;
                        }

                        // Target utilisasi yang diharapkan nanti
                        const newCapacity = Number(item.spec_value) + item.add_resource;
                        if (item.add_resource > 0 && newCapacity > 0) {
                            item.target_util_percent = Number(((projectedUsage / newCapacity) * 100).toFixed(2));
                        } else {
                            item.target_util_percent = Number(((projectedUsage / Number(item.spec_value)) * 100).toFixed(2));
                        }
                    });

                    if (this.activeTab === 'chart') {
                        this.renderCharts();
                    } else if (this.activeTab === 'bar') {
                        this.$nextTick(() => this.renderBarCharts());
                    }

                    if (triggerSave) this.scheduleAutoSave();
                },

                addManualItem() {
                    this.manualItems.push({
                        name: 'Server Baru',
                        type: 'cpu',
                        spec_value: 8,
                        spec_unit: 'Core',
                        current_util_percent: 50,
                        target_util_percent: 60,
                        add_resource: 0,
                    });
                    this.recalcManual();
                },

                removeManualItem(index) {
                    this.manualItems.splice(index, 1);
                    this.recalcManual();
                },

                addThresholdItem() {
                    this.thresholdItems.push({
                        name: 'Host Baru',
                        type: 'cpu',
                        spec_value: 8,
                        spec_unit: 'Core',
                        current_util_percent: 75,
                        target_util_percent: 75,
                        growth_est_percent: 5,
                        add_resource: 0,
                    });
                    this.recalcThreshold();
                },

                removeThresholdItem(index) {
                    this.thresholdItems.splice(index, 1);
                    this.recalcThreshold();
                },

                get totalManualAddCpu() {
                    return this.manualItems
                        .filter(i => i.type === 'cpu')
                        .reduce((sum, i) => sum + (Number(i.add_resource) || 0), 0);
                },
                get totalManualAddMemory() {
                    return this.manualItems
                        .filter(i => i.type === 'memory')
                        .reduce((sum, i) => sum + (Number(i.add_resource) || 0), 0);
                },
                get totalManualAddDisk() {
                    return this.manualItems
                        .filter(i => i.type === 'disk')
                        .reduce((sum, i) => sum + (Number(i.add_resource) || 0), 0);
                },

                get totalThresholdRecCpu() {
                    return this.thresholdItems
                        .filter(i => i.type === 'cpu')
                        .reduce((sum, i) => sum + (Number(i.add_resource) || 0), 0);
                },
                get totalThresholdRecMemory() {
                    return this.thresholdItems
                        .filter(i => i.type === 'memory')
                        .reduce((sum, i) => sum + (Number(i.add_resource) || 0), 0);
                },
                get totalThresholdRecDisk() {
                    return this.thresholdItems
                        .filter(i => i.type === 'disk')
                        .reduce((sum, i) => sum + (Number(i.add_resource) || 0), 0);
                },

                // Getters for Bar Chart Category Summaries
                get totalInstalledCpu() {
                    return this.thresholdItems.filter(i => i.type === 'cpu').reduce((sum, i) => sum + (Number(i.spec_value) || 0), 0);
                },
                get totalUsedCpu() {
                    return Number(this.thresholdItems.filter(i => i.type === 'cpu').reduce((sum, i) => sum + this.calcCurrentUsage(i), 0).toFixed(2));
                },
                get totalInstalledMem() {
                    return this.thresholdItems.filter(i => i.type === 'memory').reduce((sum, i) => sum + (Number(i.spec_value) || 0), 0);
                },
                get totalUsedMem() {
                    return Number(this.thresholdItems.filter(i => i.type === 'memory').reduce((sum, i) => sum + this.calcCurrentUsage(i), 0).toFixed(2));
                },
                get totalInstalledDisk() {
                    return this.thresholdItems.filter(i => i.type === 'disk').reduce((sum, i) => sum + (Number(i.spec_value) || 0), 0);
                },
                get totalUsedDisk() {
                    return Number(this.thresholdItems.filter(i => i.type === 'disk').reduce((sum, i) => sum + this.calcCurrentUsage(i), 0).toFixed(2));
                },

                getItemThreshold(item) {
                    if (!item) return this.cpu_threshold;
                    if (item.type === 'memory') return this.mem_threshold;
                    if (item.type === 'disk') return this.disk_threshold;
                    return this.cpu_threshold;
                },
                getItemSafePct(item) {
                    return Math.max(10, this.getItemThreshold(item) - this.buffer_percent);
                },

                get criticalCount() {
                    return this.thresholdItems.filter(i => this.calcProjectedUtilPercent(i) >= this.getItemThreshold(i)).length;
                },
                get warningCount() {
                    return this.thresholdItems.filter(i => {
                        const pct = this.calcProjectedUtilPercent(i);
                        return pct >= this.getItemSafePct(i) && pct < this.getItemThreshold(i);
                    }).length;
                },
                get safeCount() {
                    return this.thresholdItems.filter(i => this.calcProjectedUtilPercent(i) < this.getItemSafePct(i)).length;
                },

                scheduleAutoSave() {
                    this.syncStatus = 'saving';
                    this.syncStatusText = 'Menyimpan...';
                    clearTimeout(this.debounceTimer);
                    this.debounceTimer = setTimeout(() => {
                        this.saveToDatabase(false);
                    }, 1200);
                },

                async saveToDatabase(manualAlert = false) {
                    try {
                        this.syncStatus = 'saving';
                        this.syncStatusText = 'Menyimpan ke SQLite...';

                        const payload = {
                            scenario_id: this.scenarioId,
                            cpu_threshold: this.cpu_threshold,
                            mem_threshold: this.mem_threshold,
                            disk_threshold: this.disk_threshold,
                            buffer_percent: this.buffer_percent,
                            growth_enabled: this.growth_enabled,
                            growth_unit: this.growth_unit,
                            growth_months: this.growth_months,
                            manual_items: this.manualItems,
                            threshold_items: this.thresholdItems,
                        };

                        const response = await window.axios.post('/api/sync', payload);
                        if (response.data.success) {
                            this.syncStatus = 'saved';
                            this.syncStatusText = 'Tersimpan di SQLite (' + new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ')';
                            if (manualAlert) {
                                alert('Perubahan berhasil disimpan ke database lokal SQLite!');
                            }
                        }
                    } catch (e) {
                        console.error('Error saving to DB:', e);
                        this.syncStatus = 'error';
                        this.syncStatusText = 'Gagal menyimpan';
                    }
                },

                // EXPORT EXCEL PROFESIONAL & RAPIH MENGGUNAKAN XLSX (SHEETJS)
                exportToExcel() {
                    if (!window.XLSX) {
                        alert('Modul Excel sedang dimuat, silakan coba 2 detik lagi.');
                        return;
                    }

                    const wb = window.XLSX.utils.book_new();

                    // 1. DATA SHEET THRESHOLD SIMULATION
                    const thresholdRows = [
                        ['PT BANK MANDIRI (PERSERO) TBK'],
                        ['DASHCAM - IT CAPACITY MANAGEMENT & RESOURCE PLANNING'],
                        ['Tanggal Ekspor:', new Date().toLocaleString('id-ID')],
                        ['Parameter Threshold:', `CPU: ${this.cpu_threshold}%, Mem: ${this.mem_threshold}%, Disk: ${this.disk_threshold}%, Buffer: ${this.buffer_percent}%`],
                        ['Basis Growth:', `${this.growth_unit.toUpperCase()} (Durasi: ${this.growth_months})`],
                        [],
                        ['NO', 'DESKRIPSI ITEM SERVER', 'TIPE', 'SPESIFIKASI', 'SATUAN', 'UTILISASI SAAT INI (%)', 'CURRENT USAGE', `GROWTH EST (${this.growthUnitTableLabel})`, 'PREDIKSI BEBAN NANTI', 'PREDIKSI UTILISASI NANTI (%)', 'USAGE TO BE (%)', 'REKOMENDASI PENAMBAHAN', 'STATUS KESEHATAN']
                    ];

                    this.thresholdItems.forEach((item, idx) => {
                        const projUtil = this.calcProjectedUtilPercent(item);
                        const thr = this.getItemThreshold(item);
                        const safePct = this.getItemSafePct(item);
                        const status = projUtil >= thr ? 'KRITIS' : (projUtil >= safePct ? 'WASPADA' : 'AMAN');
                        thresholdRows.push([
                            idx + 1,
                            item.name,
                            item.type.toUpperCase(),
                            item.spec_value,
                            item.spec_unit,
                            item.current_util_percent,
                            this.calcCurrentUsage(item),
                            item.growth_est_percent,
                            this.calcProjectedUsage(item),
                            projUtil,
                            this.calcNewUtilWithGrowth(item),
                            item.add_resource > 0 ? item.add_resource : 0,
                            status
                        ]);
                    });

                    thresholdRows.push([]);
                    thresholdRows.push(['TOTAL REKOMENDASI PENAMBAHAN']);
                    thresholdRows.push(['Total Tambah CPU (Core):', this.totalThresholdRecCpu]);
                    thresholdRows.push(['Total Tambah Memory (GB):', this.totalThresholdRecMemory]);
                    thresholdRows.push(['Total Tambah Disk (GB):', this.totalThresholdRecDisk]);
                    thresholdRows.push(['Estimasi Anggaran (CAPEX):', this.formattedTotalEstimatedCost]);

                    const wsThreshold = window.XLSX.utils.aoa_to_sheet(thresholdRows);
                    wsThreshold['!cols'] = [
                        { wch: 6 },
                        { wch: 25 },
                        { wch: 12 },
                        { wch: 15 },
                        { wch: 10 },
                        { wch: 22 },
                        { wch: 15 },
                        { wch: 18 },
                        { wch: 22 },
                        { wch: 26 },
                        { wch: 16 },
                        { wch: 25 },
                        { wch: 18 }
                    ];

                    window.XLSX.utils.book_append_sheet(wb, wsThreshold, 'Simulasi Threshold');

                    // 2. DATA SHEET MANUAL SIMULATION
                    const manualRows = [
                        ['SIMULASI MANUAL KAPASITAS RESOURCE'],
                        ['Tanggal:', new Date().toLocaleDateString('id-ID')],
                        [],
                        ['NO', 'DESKRIPSI ITEM', 'TIPE', 'SPESIFIKASI', 'SATUAN', 'UTILISASI (%)', 'CURRENT USAGE', 'USAGE TO BE (%)', 'ADD. RESOURCE']
                    ];

                    this.manualItems.forEach((item, idx) => {
                        manualRows.push([
                            idx + 1,
                            item.name,
                            item.type.toUpperCase(),
                            item.spec_value,
                            item.spec_unit,
                            item.current_util_percent,
                            this.calcCurrentUsage(item),
                            item.target_util_percent,
                            item.add_resource
                        ]);
                    });

                    const wsManual = window.XLSX.utils.aoa_to_sheet(manualRows);
                    wsManual['!cols'] = [
                        { wch: 6 },
                        { wch: 25 },
                        { wch: 12 },
                        { wch: 15 },
                        { wch: 10 },
                        { wch: 15 },
                        { wch: 15 },
                        { wch: 16 },
                        { wch: 16 }
                    ];
                    window.XLSX.utils.book_append_sheet(wb, wsManual, 'Simulasi Manual');

                    const fileName = `DASHCAM_Kapasitas_Mandiri_${new Date().toISOString().slice(0,10)}.xlsx`;
                    window.XLSX.writeFile(wb, fileName);
                },

                copySummaryToClipboard() {
                    let text = `*LAPORAN REKOMENDASI KAPASITAS RESOURCE - BANK MANDIRI*\n`;
                    text += `Portal DASHCAM Capacity Management\n`;
                    text += `Skala Proyeksi: ${this.growth_unit.toUpperCase()} (Durasi: ${this.growth_months})\n`;
                    text += `Status: ${this.criticalCount} Kritis, ${this.warningCount} Waspada, ${this.safeCount} Aman\n\n`;
                    text += `*Rekomendasi Penambahan Total:*\n`;
                    text += `- CPU: +${this.totalThresholdRecCpu} Core\n`;
                    text += `- Memory: +${this.totalThresholdRecMemory} GB\n`;
                    text += `- Disk: +${this.totalThresholdRecDisk} GB\n`;
                    text += `- Estimasi Anggaran: ${this.formattedTotalEstimatedCost}\n\n`;
                    text += `*Rincian Per Server:*\n`;
                    this.thresholdItems.forEach(item => {
                        const recStr = item.add_resource > 0 ? `+${item.add_resource} ${item.spec_unit}` : `0 (Cukup)`;
                        text += `• ${item.name} (${item.type.toUpperCase()}): Saat ini ${item.current_util_percent}%, Prediksi Nanti: ${this.calcProjectedUtilPercent(item)}% -> Rekomendasi: ${recStr}\n`;
                    });
                    text += `\nIT Infrastructure Bank Mandiri`;

                    navigator.clipboard.writeText(text);
                    this.copiedText = true;
                    setTimeout(() => { this.copiedText = false; }, 2500);
                },

                renderCharts() {
                    let timeLabels = [];

                    if (this.growth_unit === 'day') {
                        timeLabels = ['Today', 'Added', 'Hari 7', 'Hari 14', 'Hari 30', 'Hari 60', 'Hari 90', 'Hari 120', 'Hari 180', 'Hari 240', 'Hari 300', 'Hari 365'];
                    } else if (this.growth_unit === 'year') {
                        timeLabels = ['Today', 'Added', 'Tahun 1', 'Tahun 2', 'Tahun 3', 'Tahun 4', 'Tahun 5'];
                    } else {
                        timeLabels = ['Today', 'Added', 'Bulan 1', 'Bulan 2', 'Bulan 3', 'Bulan 4', 'Bulan 5', 'Bulan 6', 'Bulan 7', 'Bulan 8', 'Bulan 9', 'Bulan 10', 'Bulan 11', 'Bulan 12'];
                    }
                    
                    const createLineChart = (canvasId, type, thresholdValue, colors) => {
                        const ctx = document.getElementById(canvasId);
                        if (!ctx) return null;

                        const items = this.thresholdItems.filter(i => i.type === type);
                        const datasets = [];

                        datasets.push({
                            label: `${type.toUpperCase()} Threshold (${thresholdValue}%)`,
                            data: Array(timeLabels.length).fill(thresholdValue),
                            borderColor: '#ef4444',
                            borderWidth: 2,
                            borderDash: [6, 4],
                            pointRadius: 0,
                            fill: false,
                        });

                        items.forEach((item, idx) => {
                            const color = colors[idx % colors.length];
                            const currentUsage = item.spec_value * (item.current_util_percent / 100);
                            const newCapacity = item.spec_value + item.add_resource;
                            
                            const dataPoints = [];
                            dataPoints.push(item.current_util_percent);
                            
                            const addedUtil = newCapacity > 0 ? (currentUsage / newCapacity) * 100 : item.current_util_percent;
                            dataPoints.push(Number(addedUtil.toFixed(2)));

                            for (let step = 1; step < timeLabels.length - 1; step++) {
                                let multiplierFactor = step;
                                if (this.growth_unit === 'day') {
                                    const daySteps = [7, 14, 30, 60, 90, 120, 180, 240, 300, 365];
                                    multiplierFactor = daySteps[step - 1] || step;
                                }

                                const growthRate = (item.growth_est_percent || 0) / 100;
                                const futureUsage = currentUsage * (1 + growthRate * multiplierFactor);
                                const futureUtil = newCapacity > 0 ? (futureUsage / newCapacity) * 100 : 0;
                                dataPoints.push(Number(futureUtil.toFixed(2)));
                            }

                            datasets.push({
                                label: item.name,
                                data: dataPoints,
                                borderColor: color,
                                backgroundColor: color,
                                borderWidth: 2.5,
                                tension: 0.3,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                            });
                        });

                        return new window.Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: timeLabels,
                                datasets: datasets
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    y: {
                                        min: 0,
                                        max: 120,
                                        ticks: {
                                            callback: (val) => val + '%',
                                            stepSize: 20
                                        },
                                        grid: { color: '#e2e8f0' }
                                    },
                                    x: {
                                        grid: { display: false }
                                    }
                                },
                                plugins: {
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                return `${context.dataset.label}: ${context.parsed.y}%`;
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    };

                    const paletteCpu = ['#003a70', '#2563eb', '#0284c7', '#0d9488'];
                    const paletteMem = ['#9333ea', '#c026d3', '#db2777', '#7c3aed'];
                    const paletteDisk = ['#d97706', '#b45309', '#ea580c', '#ca8a04'];

                    if (this.cpuChart) this.cpuChart.destroy();
                    this.cpuChart = createLineChart('cpuChartCanvas', 'cpu', this.cpu_threshold, paletteCpu);

                    if (this.memChart) this.memChart.destroy();
                    this.memChart = createLineChart('memChartCanvas', 'memory', this.mem_threshold, paletteMem);

                    if (this.diskChart) this.diskChart.destroy();
                    this.diskChart = createLineChart('diskChartCanvas', 'disk', this.disk_threshold, paletteDisk);
                },

                renderBarCharts() {
                    const makeBarChart = (canvasId, items, unitLabel, colorSpec, colorUsage, colorRec) => {
                        const ctx = document.getElementById(canvasId);
                        if (!ctx) return null;

                        const labels = items.map(i => i.name);
                        const currentSpecs = items.map(i => Number(i.spec_value));
                        const currentUsages = items.map(i => Number(this.calcCurrentUsage(i)));
                        const recommendedSpecs = items.map(i => Number(i.spec_value) + Number(i.add_resource));

                        return new window.Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [
                                    {
                                        label: 'Kapasitas Terpasang',
                                        data: currentSpecs,
                                        backgroundColor: colorSpec,
                                        borderRadius: 6,
                                        borderSkipped: false,
                                    },
                                    {
                                        label: 'Pemakaian Riil',
                                        data: currentUsages,
                                        backgroundColor: colorUsage,
                                        borderRadius: 6,
                                        borderSkipped: false,
                                    },
                                    {
                                        label: 'Rekomendasi Setelah Tambah',
                                        data: recommendedSpecs,
                                        backgroundColor: colorRec,
                                        borderRadius: 6,
                                        borderSkipped: false,
                                    },
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'top',
                                        labels: {
                                            font: { size: 11, weight: 'bold' },
                                            padding: 14,
                                        }
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: (ctx) => `${ctx.dataset.label}: ${ctx.parsed.y} ${unitLabel}`
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            callback: (val) => `${val} ${unitLabel}`,
                                            font: { size: 11 }
                                        },
                                        grid: { color: '#e2e8f0' }
                                    },
                                    x: {
                                        grid: { display: false },
                                        ticks: { font: { size: 11, weight: 'bold' } }
                                    }
                                }
                            }
                        });
                    };

                    const cpuItems = this.thresholdItems.filter(i => i.type === 'cpu');
                    const memItems = this.thresholdItems.filter(i => i.type === 'memory');
                    const diskItems = this.thresholdItems.filter(i => i.type === 'disk');

                    if (this.barCpuChart) { this.barCpuChart.destroy(); this.barCpuChart = null; }
                    if (this.barMemChart) { this.barMemChart.destroy(); this.barMemChart = null; }
                    if (this.barDiskChart) { this.barDiskChart.destroy(); this.barDiskChart = null; }

                    if (cpuItems.length > 0 && (this.barCategoryTab === 'all' || this.barCategoryTab === 'cpu')) {
                        this.barCpuChart = makeBarChart('barCpuCanvas', cpuItems, 'Core', '#94a3b8', '#ffb700', '#10b981');
                    }
                    if (memItems.length > 0 && (this.barCategoryTab === 'all' || this.barCategoryTab === 'memory')) {
                        this.barMemChart = makeBarChart('barMemCanvas', memItems, 'GB', '#c4b5fd', '#a78bfa', '#7c3aed');
                    }
                    if (diskItems.length > 0 && (this.barCategoryTab === 'all' || this.barCategoryTab === 'disk')) {
                        this.barDiskChart = makeBarChart('barDiskCanvas', diskItems, 'GB', '#fcd34d', '#f59e0b', '#d97706');
                    }
                },

                // Legacy alias (kept for safety, calls new function)
                renderBarChart() {
                    this.renderBarCharts();
                }
            }
        }
    </script>
</body>
</html>
