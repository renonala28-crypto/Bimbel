<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Landing Page - <?php echo APP_NAME; ?></title>
    <meta name="description" content="Portal Bimbel Online Persiapan Seleksi Calon Siswa (Casis) TNI, Polri, dan Sekolah Kedinasan. Simulasi CBT, Latihan Kecermatan, dan Pemantauan Progres Siswa.">
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
    
    <!-- Google Material Symbols Outlined -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome 6.5.2 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    
    <style>
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        *:not(.material-symbols-outlined):not([class*="fa-"]) {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif !important;
        }

        .material-symbols-outlined {
            font-family: 'Material Symbols Outlined' !important;
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }

        .text-shadow-strong {
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.6);
        }

        .transition-all,
        .transition-colors,
        .transition-transform,
        .transition-opacity,
        .transition-shadow {
            transition-duration: 420ms !important;
            transition-timing-function: cubic-bezier(0.22, 1, 0.36, 1) !important;
        }

        a, button, .rounded, .rounded-lg, .rounded-xl, .rounded-2xl, .shadow-md, .shadow-lg, .shadow-2xl, img {
            transition: transform 420ms cubic-bezier(0.22, 1, 0.36, 1),
                box-shadow 420ms cubic-bezier(0.22, 1, 0.36, 1),
                background-color 420ms cubic-bezier(0.22, 1, 0.36, 1),
                border-color 420ms cubic-bezier(0.22, 1, 0.36, 1),
                filter 420ms cubic-bezier(0.22, 1, 0.36, 1) !important;
        }

        a:hover,
        button:hover,
        .hover\:scale-105:hover,
        .hover-lift:hover {
            transform: translateY(-4px) scale(1.02) !important;
            filter: drop-shadow(0 10px 24px rgba(11, 37, 69, 0.15)) !important;
        }

        /* Glassmorphism utility */
        .glass-card {
            background: rgba(253, 253, 253, 0.92);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(141, 169, 196, 0.25);
        }

        .glass-dark-card {
            background: rgba(11, 37, 69, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(141, 169, 196, 0.2);
        }

        /* Reduced motion */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation: none !important;
                transition: none !important;
                scroll-behavior: auto !important;
            }
        }

        /* Focus visible styles */
        :focus-visible {
            outline: 3px solid rgba(19, 64, 116, 0.45);
            outline-offset: 3px;
            border-radius: 0.25rem;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #EEF4F8;
        }
        ::-webkit-scrollbar-thumb {
            background: #8DA9C4;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #134074;
        }
    </style>

    <script id="tailwind-config">
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        "primary": "#0B2545",
                        "secondary": "#134074",
                        "accent": "#8DA9C4",
                        "icebg": "#EEF4F8",
                        "surface": "#FDFDFD",
                        "outline-variant": "#8DA9C4",
                        "primary-container": "#0B2545",
                        "secondary-container": "#134074",
                        "tertiary-container": "#111c2d",
                        "surface-container-lowest": "#FDFDFD",
                        "surface-container-low": "#EEF4F8",
                        "surface-container": "#e5eeff",
                        "surface-container-high": "#dce9ff",
                        "on-surface": "#0B2545",
                        "on-surface-variant": "#45474c",
                        "on-primary": "#ffffff",
                        "on-secondary": "#ffffff",
                        "on-tertiary-container": "#8DA9C4",
                        "background": "#EEF4F8"
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "2xl": "1rem",
                        "full": "9999px"
                    },
                    spacing: {
                        "margin": "24px",
                        "gutter": "24px",
                        "xs": "0.25rem",
                        "sm": "0.5rem",
                        "md": "1rem",
                        "lg": "1.5rem",
                        "xl": "2rem",
                        "2xl": "3rem"
                    },
                    fontFamily: {
                        "sans": ["Inter", "-apple-system", "sans-serif"],
                        "headline-lg": ["Inter", "-apple-system", "sans-serif"],
                        "title-md": ["Inter", "-apple-system", "sans-serif"],
                        "display-lg": ["Inter", "-apple-system", "sans-serif"],
                        "body-lg": ["Inter", "-apple-system", "sans-serif"],
                        "body-sm": ["Inter", "-apple-system", "sans-serif"],
                        "label-md": ["Inter", "-apple-system", "sans-serif"]
                    },
                    fontSize: {
                        "body-lg": ["16px", { "lineHeight": "24px", "fontWeight": "400" }],
                        "body-sm": ["14px", { "lineHeight": "20px", "fontWeight": "400" }],
                        "headline-lg": ["32px", { "lineHeight": "40px", "letterSpacing": "-0.01em", "fontWeight": "700" }],
                        "title-md": ["20px", { "lineHeight": "28px", "fontWeight": "600" }],
                        "display-lg": ["46px", { "lineHeight": "54px", "letterSpacing": "-0.02em", "fontWeight": "800" }],
                        "label-md": ["12px", { "lineHeight": "16px", "letterSpacing": "0.05em", "fontWeight": "500" }]
                    }
                },
            },
        }
    </script>
</head>

<body class="bg-background text-on-surface font-body-sm transition-colors selection:bg-secondary selection:text-white">

    <!-- 1. TopNavBar -->
    <nav id="navbar" class="bg-primary text-white shadow-[0_4px_20px_rgba(0,0,0,0.15)] top-0 sticky z-50 transition-all duration-300">
        <div class="flex justify-between items-center w-full px-margin py-4 max-w-[1280px] mx-auto transition-all duration-300">
            <!-- Brand Logo -->
            <a href="#beranda" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-secondary to-accent flex items-center justify-center shadow-md group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-white text-2xl">shield</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-headline-lg text-lg sm:text-xl font-extrabold tracking-tight text-white leading-tight"><?php echo APP_NAME; ?></span>
                    <span class="font-label-md text-[10px] text-accent uppercase tracking-widest">Portal Casis Taruna</span>
                </div>
            </a>

            <!-- Desktop Navigation Menu -->
            <div class="hidden md:flex items-center gap-7">
                <a class="flex items-center gap-2 font-label-md text-label-md bg-secondary text-white rounded-lg px-3.5 py-2 shadow-sm" href="#beranda">
                    <span class="material-symbols-outlined text-base">home</span>
                    <span>Beranda</span>
                </a>
                <a class="font-label-md text-label-md text-white/80 hover:text-white transition-colors" href="#tentang">Tentang</a>
                <a class="font-label-md text-label-md text-white/80 hover:text-white transition-colors" href="#program">Program</a>
                <a class="font-label-md text-label-md text-white/80 hover:text-white transition-colors" href="#alur">Alur Daftar</a>
                <a class="font-label-md text-label-md text-white/80 hover:text-white transition-colors" href="#kenapa">Kenapa Kami</a>
                <a class="font-label-md text-label-md text-white/80 hover:text-white transition-colors" href="#lokasi">Lokasi</a>
            </div>

            <!-- Desktop Right Actions -->
            <div class="hidden md:flex items-center gap-3">
                <a href="<?php echo route('login'); ?>" class="bg-secondary hover:bg-secondary/80 text-white px-5 py-2.5 rounded-lg font-label-md text-label-md uppercase tracking-wider shadow-md hover:shadow-lg transition-all text-center border border-accent/30">
                    Masuk
                </a>
            </div>

            <!-- Mobile Hamburger Button -->
            <div class="flex items-center gap-2 md:hidden">
                <button id="menu-btn" aria-controls="mobile-menu" aria-expanded="false" aria-label="Buka menu" class="block md:hidden text-white focus:outline-none p-1">
                    <span class="material-symbols-outlined text-3xl">menu</span>
                </button>
            </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div id="mobile-menu" role="navigation" aria-hidden="true" class="hidden md:hidden bg-primary border-t border-white/10 px-margin py-4 space-y-3">
            <a class="flex items-center gap-3 font-label-md text-label-md bg-secondary text-white rounded-lg px-4 py-2.5" href="#beranda">
                <span class="material-symbols-outlined text-lg">home</span>
                <span>Beranda</span>
            </a>
            <a class="block font-label-md text-label-md text-white/80 hover:text-white transition-colors py-1" href="#tentang">Tentang Kami</a>
            <a class="block font-label-md text-label-md text-white/80 hover:text-white transition-colors py-1" href="#program">Program Fitur</a>
            <a class="block font-label-md text-label-md text-white/80 hover:text-white transition-colors py-1" href="#alur">Alur Pendaftaran</a>
            <a class="block font-label-md text-label-md text-white/80 hover:text-white transition-colors py-1" href="#kenapa">Kenapa Kami</a>
            <a class="block font-label-md text-label-md text-white/80 hover:text-white transition-colors py-1" href="#lokasi">Lokasi Kampus</a>
            <div class="pt-3 border-t border-white/10 flex flex-col gap-2">
                <a href="<?php echo route('login'); ?>" class="block w-full bg-secondary text-white py-2.5 rounded-lg font-label-md text-label-md uppercase tracking-wider shadow-md text-center">Masuk</a>
            </div>
        </div>
    </nav>

    <!-- SideNavBar (Mobile Drawer alternative) -->
    <aside id="mobile-nav" class="fixed left-0 top-0 h-full w-64 z-50 md:hidden bg-primary text-white shadow-2xl -translate-x-full transition-transform duration-300 flex flex-col">
        <div class="p-lg border-b border-white/10">
            <div class="flex items-center justify-between mb-2">
                <div class="font-headline-lg text-title-md font-extrabold text-white uppercase tracking-tight">
                    <?php echo APP_NAME; ?>
                </div>
                <button id="close-mobile-nav" class="text-white/80 hover:text-white focus:outline-none">
                    <span class="material-symbols-outlined text-2xl">close</span>
                </button>
            </div>
            <p class="font-label-md text-label-md text-accent uppercase tracking-wider">Persiapan Taruna Casis</p>
        </div>
        <nav class="flex-1 overflow-y-auto py-md px-sm flex flex-col gap-sm">
            <a class="flex items-center gap-md font-body-lg text-body-lg bg-secondary text-white font-bold px-md py-sm rounded-xl" href="#beranda">
                <span class="material-symbols-outlined">home</span>
                Beranda
            </a>
            <a class="flex items-center gap-md font-body-lg text-body-lg text-white/80 px-md py-sm hover:bg-white/10 rounded-xl transition-colors" href="#tentang">
                <span class="material-symbols-outlined">info</span>
                Tentang
            </a>
            <a class="flex items-center gap-md font-body-lg text-body-lg text-white/80 px-md py-sm hover:bg-white/10 rounded-xl transition-colors" href="#program">
                <span class="material-symbols-outlined">workspace_premium</span>
                Program
            </a>
            <a class="flex items-center gap-md font-body-lg text-body-lg text-white/80 px-md py-sm hover:bg-white/10 rounded-xl transition-colors" href="#alur">
                <span class="material-symbols-outlined">checklist</span>
                Alur Daftar
            </a>
            <a class="flex items-center gap-md font-body-lg text-body-lg text-white/80 px-md py-sm hover:bg-white/10 rounded-xl transition-colors" href="#kenapa">
                <span class="material-symbols-outlined">star</span>
                Kenapa Kami
            </a>
            <a class="flex items-center gap-md font-body-lg text-body-lg text-white/80 px-md py-sm hover:bg-white/10 rounded-xl transition-colors" href="#lokasi">
                <span class="material-symbols-outlined">location_on</span>
                Lokasi
            </a>
        </nav>
        <div class="p-lg border-t border-white/10 mt-auto flex flex-col gap-2">
            <a href="<?php echo route('login'); ?>" class="block w-full text-center bg-secondary text-white font-label-md text-label-md py-2.5 rounded-lg shadow-md">Masuk</a>
        </div>
    </aside>
    <div id="mobile-nav-overlay" class="fixed inset-0 bg-black/60 z-40 hidden"></div>


    <!-- 2. Hero Section -->
    <header id="beranda" class="relative w-full min-h-[850px] flex items-center overflow-hidden bg-primary text-white">
        <!-- Overlay Gradasi Transparan agar Foto Latar Belakang Terlihat Jelas Full -->
        <div class="absolute inset-0 bg-gradient-to-r from-primary/85 via-primary/70 to-primary/50 z-10"></div>
        
        <!-- Full Background Foto Hero -->
        <!-- GANTI DENGAN FOTO ASLI -->
        <div id="hero-bg" class="absolute inset-0 z-0 bg-cover bg-center opacity-90 transition-transform duration-700"
            style="background-image: url('https://images.unsplash.com/photo-1541829070764-84a7d30dd3f3?q=80&w=1600&auto=format&fit=crop'), url('/assets/images/hero-taruna.jpg'); will-change: transform;">
        </div>

        <div class="relative z-20 w-full px-margin max-w-[1280px] mx-auto py-20">
            <div class="max-w-3xl space-y-6">
                <!-- Badge -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 border border-accent/30 text-accent font-label-md text-label-md uppercase tracking-wider backdrop-blur-md">
                    <span class="material-symbols-outlined text-amber-400 text-base">verified</span>
                    <span>Bimbel Resmi Persiapan Casis TNI / Polri / Kedinasan</span>
                </div>

                <h1 class="font-display-lg text-display-lg text-white uppercase tracking-tight text-shadow-strong leading-tight">
                    Wujudkan Mimpi Jadi <br /><span class="text-accent">Taruna Bangsa</span> Bersama <?php echo APP_NAME; ?>
                </h1>

                <p class="font-body-lg text-body-lg text-white/90 max-w-2xl leading-relaxed">
                    Portal belajar all-in-one persiapan seleksi penerimaan TNI, Polri, dan Sekolah Kedinasan. Nikmati simulasi Tryout CBT standar nasional, Game Sudoku Kecermatan, modul lengkap, dan pantau perkembangan fisik &amp; akademik secara transparan.
                </p>

                <div class="flex flex-wrap gap-4 pt-4">
                    <a href="<?php echo route('login'); ?>"
                        class="bg-secondary text-white px-8 py-4 rounded-lg font-label-md text-label-md uppercase tracking-widest hover:bg-secondary/80 transition-all shadow-lg text-center flex items-center gap-3 border border-accent/30">
                        <span class="material-symbols-outlined text-base">login</span>
                        <span>Masuk Akun</span>
                    </a>
                </div>

                <!-- 3 Statistics Placeholder Cards -->
                <!-- angka ini contoh, nanti diisi data asli -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-8">
                    <div class="glass-dark-card p-4 rounded-xl border border-white/10 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-accent/20 flex items-center justify-center text-accent">
                            <span class="material-symbols-outlined text-2xl">groups</span>
                        </div>
                        <div>
                            <!-- angka ini contoh, nanti diisi data asli -->
                            <h4 class="font-headline-lg text-2xl text-white">1.500+</h4>
                            <p class="font-label-md text-[11px] text-accent uppercase">Siswa Terdaftar</p>
                        </div>
                    </div>
                    <div class="glass-dark-card p-4 rounded-xl border border-white/10 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-amber-400/20 flex items-center justify-center text-amber-400">
                            <span class="material-symbols-outlined text-2xl">workspace_premium</span>
                        </div>
                        <div>
                            <!-- angka ini contoh, nanti diisi data asli -->
                            <h4 class="font-headline-lg text-2xl text-white">95%</h4>
                            <p class="font-label-md text-[11px] text-accent uppercase">Kelulusan Seleksi</p>
                        </div>
                    </div>
                    <div class="glass-dark-card p-4 rounded-xl border border-white/10 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-emerald-400/20 flex items-center justify-center text-emerald-400">
                            <span class="material-symbols-outlined text-2xl">computer</span>
                        </div>
                        <div>
                            <!-- angka ini contoh, nanti diisi data asli -->
                            <h4 class="font-headline-lg text-2xl text-white">100+</h4>
                            <p class="font-label-md text-[11px] text-accent uppercase">Paket Tryout CBT</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </header>


    <!-- 3. Service Summary / Fitur Program -->
    <section id="program" class="py-24 px-margin max-w-[1280px] mx-auto">
        <div class="mb-16">
            <span class="text-primary font-label-md text-label-md uppercase tracking-[0.2em] block mb-2">Capabilities</span>
            <h2 class="font-headline-lg text-headline-lg uppercase text-primary">
                Program &amp; Fitur Unggulan
            </h2>
        </div>

        <!-- 5 Features Grid Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-gutter">
            
            <!-- Card 1: Tryout CBT -->
            <div class="bg-surface-container-lowest p-8 rounded-xl border border-outline-variant/30 hover:shadow-xl hover-lift transition-all duration-300 group flex flex-col justify-between">
                <div>
                    <div class="w-14 h-14 bg-surface-container-low rounded-xl flex items-center justify-center mb-6 text-secondary group-hover:bg-secondary group-hover:text-white transition-colors">
                        <i class="fa-solid fa-clipboard-check text-2xl"></i>
                    </div>
                    <h3 class="font-title-md text-title-md mb-3 text-primary uppercase">Tryout CBT Interactive</h3>
                    <p class="text-on-surface-variant font-body-sm mb-6">
                        Simulasi pilihan ganda timer countdown presisi, acak soal &amp; opsi jawaban, serta penilaian otomatis transparan.
                    </p>
                </div>
                <div>
                    <hr class="border-outline-variant/30 mb-4" />
                    <ul class="space-y-2 text-[12px] font-medium text-on-surface opacity-90">
                        <li class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm text-secondary">check_circle</span> 
                            Timer Countdown Real-Time
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm text-secondary">check_circle</span> 
                            Soal &amp; Opsi Acak Otomatis
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Card 2: Game Sudoku Kecermatan -->
            <div class="bg-surface-container-lowest p-8 rounded-xl border border-outline-variant/30 hover:shadow-xl hover-lift transition-all duration-300 group flex flex-col justify-between">
                <div>
                    <div class="w-14 h-14 bg-surface-container-low rounded-xl flex items-center justify-center mb-6 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                        <i class="fa-solid fa-table-cells text-2xl"></i>
                    </div>
                    <h3 class="font-title-md text-title-md mb-3 text-primary uppercase">Sudoku Kecermatan</h3>
                    <p class="text-on-surface-variant font-body-sm mb-6">
                        Latihan ketelitian &amp; kecepatan melalui tes angka hilang/simbol. Skor total dan durasi pengerjaan tercatat rapi.
                    </p>
                </div>
                <div>
                    <hr class="border-outline-variant/30 mb-4" />
                    <ul class="space-y-2 text-[12px] font-medium text-on-surface opacity-90">
                        <li class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm text-emerald-600">check_circle</span> 
                            Latih Kecepatan Otak
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm text-emerald-600">check_circle</span> 
                            Skor &amp; Durasi Terrekam
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Card 3: Materi Belajar -->
            <div class="bg-surface-container-lowest p-8 rounded-xl border border-outline-variant/30 hover:shadow-xl hover-lift transition-all duration-300 group flex flex-col justify-between">
                <div>
                    <div class="w-14 h-14 bg-surface-container-low rounded-xl flex items-center justify-center mb-6 text-amber-500 group-hover:bg-amber-500 group-hover:text-white transition-colors">
                        <i class="fa-solid fa-book-open text-2xl"></i>
                    </div>
                    <h3 class="font-title-md text-title-md mb-3 text-primary uppercase">Materi Belajar Lengkap</h3>
                    <p class="text-on-surface-variant font-body-sm mb-6">
                        Dokumen PDF/Word terstruktur per kategori: Psikologi, PWK, Pengetahuan Umum, hingga Jasmani Lari &amp; Renang.
                    </p>
                </div>
                <div>
                    <hr class="border-outline-variant/30 mb-4" />
                    <ul class="space-y-2 text-[12px] font-medium text-on-surface opacity-90">
                        <li class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm text-amber-500">check_circle</span> 
                            Download PDF &amp; Word
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm text-amber-500">check_circle</span> 
                            Kategori Terstruktur
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Card 4: Grafik Perkembangan -->
            <div class="bg-surface-container-lowest p-8 rounded-xl border border-outline-variant/30 hover:shadow-xl hover-lift transition-all duration-300 group flex flex-col justify-between">
                <div>
                    <div class="w-14 h-14 bg-surface-container-low rounded-xl flex items-center justify-center mb-6 text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                        <i class="fa-solid fa-chart-line text-2xl"></i>
                    </div>
                    <h3 class="font-title-md text-title-md mb-3 text-primary uppercase">Grafik Perkembangan</h3>
                    <p class="text-on-surface-variant font-body-sm mb-6">
                        Pantau tren nilai CBT, skor Sudoku, dan catatan capaian fisik Anda dari waktu ke waktu secara transparan.
                    </p>
                </div>
                <div>
                    <hr class="border-outline-variant/30 mb-4" />
                    <ul class="space-y-2 text-[12px] font-medium text-on-surface opacity-90">
                        <li class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm text-indigo-600">check_circle</span> 
                            Grafik Evaluasi Berkala
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm text-indigo-600">check_circle</span> 
                            Capaian Fisik &amp; Akademik
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Card 5: Leaderboard -->
            <div class="bg-surface-container-lowest p-8 rounded-xl border border-outline-variant/30 hover:shadow-xl hover-lift transition-all duration-300 group flex flex-col justify-between md:col-span-2 lg:col-span-1">
                <div>
                    <div class="w-14 h-14 bg-surface-container-low rounded-xl flex items-center justify-center mb-6 text-yellow-500 group-hover:bg-yellow-500 group-hover:text-white transition-colors">
                        <i class="fa-solid fa-trophy text-2xl"></i>
                    </div>
                    <h3 class="font-title-md text-title-md mb-3 text-primary uppercase">Papan Peringkat</h3>
                    <p class="text-on-surface-variant font-body-sm mb-6">
                        Peringkat nilai transparan antar seluruh siswa aktif <?php echo APP_NAME; ?> untuk memicu kompetisi sehat.
                    </p>
                </div>
                <div>
                    <hr class="border-outline-variant/30 mb-4" />
                    <ul class="space-y-2 text-[12px] font-medium text-on-surface opacity-90">
                        <li class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm text-yellow-500">check_circle</span> 
                            Ranking Seluruh Siswa
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm text-yellow-500">check_circle</span> 
                            Motivasi Kelulusan
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </section>


    <!-- 3b. Layanan / Tentang Kami Detailed -->
    <section id="tentang" class="bg-surface-container-low py-24 transition-colors">
        <div class="px-margin max-w-[1280px] mx-auto">
            <div class="mb-16">
                <span class="text-primary font-label-md text-label-md uppercase tracking-[0.2em] block mb-2">About Us</span>
                <h2 class="font-headline-lg text-headline-lg uppercase text-primary">Tentang <?php echo APP_NAME; ?></h2>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-gutter items-center">

                <!-- Visual Image Card -->
                <div class="relative rounded-2xl overflow-hidden shadow-2xl border-4 border-white">
                    <!-- GANTI DENGAN FOTO ASLI -->
                    <img src="/assets/images/tentang-taruna.jpg" alt="Tentang Bimbel Alahaido" class="w-full h-[450px] object-cover" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1541829070764-84a7d30dd3f3?q=80&w=1200&auto=format&fit=crop';">
                    <div class="absolute inset-0 bg-gradient-to-t from-primary/90 via-transparent to-transparent"></div>
                    <div class="absolute bottom-6 left-6 right-6 text-white">
                        <span class="font-label-md text-xs uppercase tracking-widest text-accent">Disiplin &amp; Terukur</span>
                        <h3 class="font-title-md text-xl font-bold">Membangun Karakter Taruna Unggul</h3>
                    </div>
                </div>

                <!-- Detailed Content -->
                <div class="space-y-6">
                    <h3 class="font-headline-lg text-2xl sm:text-3xl text-primary uppercase leading-tight">
                        Portal Belajar Satu Pintu Khusus Persiapan Casis TNI, POLRI &amp; Kedinasan
                    </h3>
                    <p class="text-on-surface-variant font-body-lg leading-relaxed">
                        <strong><?php echo APP_NAME; ?></strong> dirancang khusus sebagai sistem terpadu dalam menemani perjuangan calon siswa taruna. Kami mengintegrasikan kedisiplinan, kesiapan akademik, latihan psikologi kecermatan, dan ketahanan fisik dalam satu portal intuitif.
                    </p>

                    <div class="space-y-3 pt-2">
                        <div class="flex items-start gap-3 text-on-surface font-body-sm p-3.5 rounded-xl bg-white border border-outline-variant/30 shadow-sm">
                            <span class="material-symbols-outlined text-secondary text-xl shrink-0 mt-0.5">verified</span>
                            <div>
                                <strong class="text-primary block font-title-md text-base">Kurikulum Terpadu &amp; Simulasi Real-Time</strong>
                                Antarmuka dan timer ujian disesuaikan dengan standar CAT asli kepolisian &amp; kedinasan.
                            </div>
                        </div>

                        <div class="flex items-start gap-3 text-on-surface font-body-sm p-3.5 rounded-xl bg-white border border-outline-variant/30 shadow-sm">
                            <span class="material-symbols-outlined text-secondary text-xl shrink-0 mt-0.5">verified</span>
                            <div>
                                <strong class="text-primary block font-title-md text-base">Pembinaan Fisik &amp; Mental Komprehensif</strong>
                                Modul materi jasmani (Lari, Renang, Pull-up) dilengkapi grafik pantau capaian secara berkala.
                            </div>
                        </div>
                    </div>

                    <div class="pt-4">
                        <a href="<?php echo route('register'); ?>" class="inline-flex items-center gap-2 bg-secondary text-white font-label-md text-label-md uppercase tracking-wider px-6 py-3 rounded-lg hover:bg-secondary/80 transition-all shadow-md">
                            Daftar Calon Siswa Sekarang <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>


    <!-- 4. Alur Pendaftaran Section -->
    <section id="alur" class="py-24 px-margin max-w-[1280px] mx-auto">
        <div class="mb-16">
            <span class="text-primary font-label-md text-label-md uppercase tracking-[0.2em] block mb-2">Registration Flow</span>
            <h2 class="font-headline-lg text-headline-lg uppercase text-primary">Alur Pendaftaran</h2>
        </div>

        <!-- 4 Steps Timeline Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-gutter">
            
            <!-- Step 1 -->
            <div class="bg-surface-container-lowest p-8 rounded-xl border border-outline-variant/30 hover-lift transition-all">
                <div class="w-12 h-12 rounded-full bg-primary text-white flex items-center justify-center font-headline-lg text-xl mb-6 shadow-md">
                    1
                </div>
                <h4 class="font-title-md text-title-md text-primary uppercase mb-2">Isi Form Pendaftaran</h4>
                <p class="text-on-surface-variant font-body-sm">
                    Lengkapi data diri calon siswa meliputi Nama Lengkap, Nomor WhatsApp aktif, Email, dan Kata Sandi akun.
                </p>
            </div>

            <!-- Step 2 -->
            <div class="bg-surface-container-lowest p-8 rounded-xl border border-outline-variant/30 hover-lift transition-all">
                <div class="w-12 h-12 rounded-full bg-secondary text-white flex items-center justify-center font-headline-lg text-xl mb-6 shadow-md">
                    2
                </div>
                <h4 class="font-title-md text-title-md text-primary uppercase mb-2">Bayar Biaya Pendaftaran</h4>
                <p class="text-on-surface-variant font-body-sm">
                    Lakukan pembayaran biaya pendaftaran sebesar <strong>Rp 100.000</strong> via QRIS / Transfer &amp; unggah bukti pembayaran.
                </p>
            </div>

            <!-- Step 3 -->
            <div class="bg-surface-container-lowest p-8 rounded-xl border border-outline-variant/30 hover-lift transition-all">
                <div class="w-12 h-12 rounded-full bg-accent text-primary font-bold flex items-center justify-center font-headline-lg text-xl mb-6 shadow-md">
                    3
                </div>
                <h4 class="font-title-md text-title-md text-primary uppercase mb-2">Verifikasi Admin</h4>
                <p class="text-on-surface-variant font-body-sm">
                    Tim Admin memverifikasi bukti pembayaran (Status akun berubah dari <em>Pending</em> menjadi <em>Active</em>).
                </p>
            </div>

            <!-- Step 4 -->
            <div class="bg-surface-container-lowest p-8 rounded-xl border border-outline-variant/30 hover-lift transition-all">
                <div class="w-12 h-12 rounded-full bg-emerald-600 text-white flex items-center justify-center font-headline-lg text-xl mb-6 shadow-md">
                    4
                </div>
                <h4 class="font-title-md text-title-md text-primary uppercase mb-2">Akses Penuh Dashboard</h4>
                <p class="text-on-surface-variant font-body-sm">
                    Login ke akun Anda dan langsung mulai belajar, mengerjakan Tryout CBT, Sudoku, serta mengunduh materi lengkap.
                </p>
            </div>

        </div>
    </section>


    <!-- 5. Statistics & Kenapa Kami Section -->
    <section id="kenapa" class="bg-primary text-white py-24">
        <div class="px-margin max-w-[1280px] mx-auto space-y-16">
            
            <!-- Statistics Banner -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center border-b border-white/10 pb-16">
                <div>
                    <div class="font-display-lg text-display-lg text-accent mb-2">1.500+</div>
                    <div class="font-label-md text-label-md text-white/70 uppercase tracking-widest">Siswa Terdaftar</div>
                </div>
                <div>
                    <div class="font-display-lg text-display-lg text-accent mb-2">95%</div>
                    <div class="font-label-md text-label-md text-white/70 uppercase tracking-widest">Tingkat Kelulusan</div>
                </div>
                <div>
                    <div class="font-display-lg text-display-lg text-accent mb-2">100+</div>
                    <div class="font-label-md text-label-md text-white/70 uppercase tracking-widest">Paket Tryout CBT</div>
                </div>
                <div>
                    <div class="font-display-lg text-display-lg text-accent mb-2">100%</div>
                    <div class="font-label-md text-label-md text-white/70 uppercase tracking-widest">Progres Transparan</div>
                </div>
            </div>

            <!-- Kenapa Pilih Kami Grid -->
            <div>
                <div class="text-center max-w-2xl mx-auto mb-12">
                    <span class="font-label-md text-label-md text-accent uppercase tracking-widest">Why Choose Us</span>
                    <h2 class="font-headline-lg text-headline-lg uppercase text-white mt-1">Mengapa Memilih <?php echo APP_NAME; ?>?</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-gutter">
                    
                    <div class="glass-dark-card p-6 rounded-xl border border-white/10 space-y-3 hover-lift">
                        <div class="w-12 h-12 rounded-lg bg-accent/20 text-accent flex items-center justify-center">
                            <span class="material-symbols-outlined text-2xl">school</span>
                        </div>
                        <h3 class="font-title-md text-title-md text-white uppercase">Materi Terstruktur</h3>
                        <p class="text-slate-300 text-sm leading-relaxed">
                            Disusun secara sistematis mengacu pada kisi-kisi resmi seleksi TNI, POLRI, dan Sekolah Kedinasan.
                        </p>
                    </div>

                    <div class="glass-dark-card p-6 rounded-xl border border-white/10 space-y-3 hover-lift">
                        <div class="w-12 h-12 rounded-lg bg-emerald-400/20 text-emerald-400 flex items-center justify-center">
                            <span class="material-symbols-outlined text-2xl">monitoring</span>
                        </div>
                        <h3 class="font-title-md text-title-md text-white uppercase">Progres Transparan</h3>
                        <p class="text-slate-300 text-sm leading-relaxed">
                            Setiap hasil latihan tercatat rasional dalam bentuk grafik sehingga perkembangan Anda terpantau jelas.
                        </p>
                    </div>

                    <div class="glass-dark-card p-6 rounded-xl border border-white/10 space-y-3 hover-lift">
                        <div class="w-12 h-12 rounded-lg bg-amber-400/20 text-amber-400 flex items-center justify-center">
                            <span class="material-symbols-outlined text-2xl">psychology</span>
                        </div>
                        <h3 class="font-title-md text-title-md text-white uppercase">Latihan Kecermatan</h3>
                        <p class="text-slate-300 text-sm leading-relaxed">
                            Mengasah ketahanan mental &amp; konsentrasi siswa secara berkala melalui tes kecermatan interaktif.
                        </p>
                    </div>

                    <div class="glass-dark-card p-6 rounded-xl border border-white/10 space-y-3 hover-lift">
                        <div class="w-12 h-12 rounded-lg bg-indigo-400/20 text-indigo-400 flex items-center justify-center">
                            <span class="material-symbols-outlined text-2xl">leaderboard</span>
                        </div>
                        <h3 class="font-title-md text-title-md text-white uppercase">Kompetisi Sehat</h3>
                        <p class="text-slate-300 text-sm leading-relaxed">
                            Papan peringkat nasional memacu semangat belajar tinggi untuk terus meningkatkan skor terbaik Anda.
                        </p>
                    </div>

                </div>
            </div>

        </div>
    </section>


    <!-- 6. Mid-Page CTA -->
    <section class="relative py-24 overflow-hidden bg-surface-container-low transition-colors">
        <div class="relative z-10 px-margin max-w-[1280px] mx-auto text-center space-y-6">
            <h2 class="font-headline-lg text-headline-lg uppercase text-primary">Siap Memulai Langkah Menjadi Taruna Impian?</h2>
            <p class="font-body-lg text-body-lg text-on-surface-variant max-w-2xl mx-auto">
                Bergabunglah sekarang dengan ratusan calon siswa lainnya dan dapatkan akses penuh ke seluruh modul &amp; simulasi CBT <?php echo APP_NAME; ?>.
            </p>
            <div class="flex flex-wrap items-center justify-center gap-4 pt-4">
                <!-- GANTI NOMOR WHATSAPP -->
                <a href="https://wa.me/6281234567890?text=Halo%20Admin%20Bimbel%20Alahaido,%20saya%20ingin%20tanya%20informasi%20pendaftaran" target="_blank" rel="noopener noreferrer"
                    class="bg-emerald-600 text-white px-8 py-4 rounded-lg font-label-md text-label-md uppercase tracking-[0.15em] hover:bg-emerald-500 transition-all shadow-md inline-flex items-center gap-3">
                    <i class="fa-brands fa-whatsapp text-xl"></i>
                    <span>Konsultasi WhatsApp</span>
                </a>
                <a href="<?php echo route('login'); ?>"
                    class="bg-primary text-white px-8 py-4 rounded-lg font-label-md text-label-md uppercase tracking-[0.15em] hover:bg-secondary transition-all shadow-md inline-flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">login</span>
                    <span>Masuk Akun</span>
                </a>
            </div>
        </div>
    </section>


    <!-- 7. Google Maps & Location Section -->
    <section id="lokasi" class="py-24 px-margin max-w-[1280px] mx-auto">
        <div class="mb-12">
            <span class="text-primary font-label-md text-label-md uppercase tracking-[0.2em] block mb-2">Our Location</span>
            <h2 class="font-headline-lg text-headline-lg uppercase text-primary">
                Lokasi Bimbel
            </h2>
            <p class="text-on-surface-variant font-body-lg max-w-2xl mt-2">
                Kunjungi kampus bimbingan belajar kami untuk konsultasi tatap muka, pendaftaran offline, dan melihat langsung fasilitas simulasi CBT &amp; area pembinaan fisik.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
            
            <!-- Left Info Card (5 Cols) -->
            <div class="lg:col-span-5 bg-surface-container-lowest p-8 rounded-2xl border border-outline-variant/30 shadow-lg flex flex-col justify-between space-y-6">
                <div class="space-y-6">
                    <div class="flex items-center gap-3 border-b border-outline-variant/20 pb-4">
                        <div class="w-12 h-12 rounded-xl bg-primary text-white flex items-center justify-center">
                            <span class="material-symbols-outlined text-2xl">location_on</span>
                        </div>
                        <div>
                            <h3 class="font-title-md text-title-md text-primary uppercase"><?php echo APP_NAME; ?></h3>
                            <p class="font-label-md text-xs text-accent uppercase">Kampus Utama Persiapan Casis</p>
                        </div>
                    </div>

                    <div class="space-y-4 font-body-sm text-on-surface">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-secondary text-xl mt-0.5 shrink-0">map</span>
                            <div>
                                <strong class="text-primary block font-title-md text-sm">Alamat Lengkap:</strong>
                                <span>Jl. Raya Karangsono, Sono Tengah, Kebonagung, Kec. Pakisaji, Kabupaten Malang, Jawa Timur 65162</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-secondary text-xl mt-0.5 shrink-0">call</span>
                            <div>
                                <strong class="text-primary block font-title-md text-sm">Kontak Telepon / WhatsApp:</strong>
                                <!-- GANTI NOMOR WHATSAPP -->
                                <span>+62 812-3456-7890</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-outline-variant/20">
                    <!-- GANTI NOMOR WHATSAPP / LINK MAPS -->
                    <a href="https://maps.app.goo.gl/SNXQWmorneBUqduF8?g_st=ic" target="_blank" rel="noopener noreferrer" 
                        class="w-full bg-secondary hover:bg-secondary/80 text-white font-label-md text-label-md uppercase tracking-wider py-3.5 px-6 rounded-xl flex items-center justify-center gap-2 shadow-md transition-all text-center">
                        <span class="material-symbols-outlined text-lg">directions</span>
                        <span>Petunjuk Arah (Google Maps)</span>
                    </a>
                </div>
            </div>

            <!-- Right Interactive Google Maps Embed (7 Cols) -->
            <div class="lg:col-span-7 relative min-h-[400px] rounded-2xl overflow-hidden shadow-xl border-4 border-white bg-gray-200">
                <!-- EMBED LINK GOOGLE MAPS -->
                <iframe 
                    title="Lokasi Kampus Bimbel"
                    src="https://maps.google.com/maps?q=Toko+Navy,+Jl.+Raya+Karangsono,+Sono+Tengah,+Kebonagung,+Kec.+Pakisaji,+Kabupaten+Malang,+Jawa+Timur+65162&amp;t=&amp;z=16&amp;ie=UTF8&amp;iwloc=&amp;output=embed" 
                    class="w-full h-full min-h-[400px] border-0" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>

        </div>
    </section>


    <!-- 8. Floating WhatsApp Button -->
    <!-- GANTI NOMOR WHATSAPP -->
    <a class="fixed bottom-8 right-8 z-[100] bg-[#00ac40] text-white w-16 h-16 rounded-full flex items-center justify-center shadow-2xl hover:scale-110 transition-transform group"
        href="https://wa.me/6281234567890?text=Halo%20Admin%20Bimbel%20Alahaido,%20saya%20ingin%20bertanya" target="_blank" rel="noopener noreferrer" aria-label="Chat via WhatsApp">
        <i class="fa-brands fa-whatsapp text-3xl"></i>
        <span class="absolute right-full mr-4 bg-white text-primary px-4 py-2 rounded shadow-lg text-xs font-bold whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
            Hubungi via WhatsApp
        </span>
    </a>


    <!-- 9. Footer -->
    <footer class="bg-primary border-t border-white/10 text-white">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-gutter px-margin py-20 max-w-[1280px] mx-auto">
            <div class="col-span-1 md:col-span-2 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-secondary flex items-center justify-center text-white">
                        <span class="material-symbols-outlined text-2xl">shield</span>
                    </div>
                    <div class="font-title-md text-title-md text-white uppercase font-extrabold tracking-tight"><?php echo APP_NAME; ?></div>
                </div>
                <p class="opacity-80 max-w-md font-body-sm text-slate-300">
                    Portal bimbingan belajar terpadu persiapan penerimaan calon taruna TNI, POLRI, dan Sekolah Kedinasan terdepan dan terpercaya.
                </p>
                <div class="flex gap-3 pt-2">
                    <a class="w-10 h-10 border border-white/20 rounded-lg flex items-center justify-center hover:bg-secondary hover:border-transparent transition-colors text-white"
                        href="https://instagram.com" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                        <i class="fa-brands fa-instagram text-lg"></i>
                    </a>
                    <!-- GANTI NOMOR WHATSAPP -->
                    <a class="w-10 h-10 border border-white/20 rounded-lg flex items-center justify-center hover:bg-secondary hover:border-transparent transition-colors text-white"
                        href="https://wa.me/6281234567890" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp">
                        <i class="fa-brands fa-whatsapp text-lg"></i>
                    </a>
                    <a class="w-10 h-10 border border-white/20 rounded-lg flex items-center justify-center hover:bg-secondary hover:border-transparent transition-colors text-white"
                        href="https://youtube.com" target="_blank" rel="noopener noreferrer" aria-label="YouTube">
                        <i class="fa-brands fa-youtube text-lg"></i>
                    </a>
                </div>
            </div>
            <div>
                <h4 class="font-label-md text-label-md uppercase tracking-widest text-accent mb-6">Navigasi Cepat</h4>
                <ul class="space-y-3 font-label-md text-label-md text-slate-300">
                    <li><a class="hover:text-white transition-colors" href="#beranda">Beranda</a></li>
                    <li><a class="hover:text-white transition-colors" href="#tentang">Tentang Kami</a></li>
                    <li><a class="hover:text-white transition-colors" href="#program">Program &amp; Fitur</a></li>
                    <li><a class="hover:text-white transition-colors" href="#alur">Alur Pendaftaran</a></li>
                    <li><a class="hover:text-white transition-colors" href="#kenapa">Keunggulan</a></li>
                    <li><a class="hover:text-white transition-colors" href="#lokasi">Lokasi</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-label-md text-label-md uppercase tracking-widest text-accent mb-6">Kontak &amp; Alamat</h4>
                <ul class="space-y-3 font-label-md text-label-md text-slate-300">
                    <li class="flex items-start gap-2">
                        <span class="material-symbols-outlined text-accent text-base mt-0.5">location_on</span>
                        Jl. Raya Karangsono, Sono Tengah, Kebonagung, Kec. Pakisaji, Kabupaten Malang, Jawa Timur 65162
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-accent text-base">call</span>
                        <!-- GANTI NOMOR WHATSAPP -->
                        +62 812-3456-7890
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-accent text-base">mail</span>
                        info@alahaido.com
                    </li>
                </ul>
            </div>
        </div>
        <div class="px-margin py-6 border-t border-white/10 max-w-[1280px] mx-auto text-center opacity-70 font-label-md text-label-md flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>&copy; <span id="copyright-year"></span> <?php echo APP_NAME; ?>. All rights reserved.</div>
            <div>Berani Jujur Berhasil</div>
        </div>
    </footer>


    <!-- ================= JAVASCRIPT ================= -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // // COPYRIGHT YEAR AUTOMATIC
            const yearSpan = document.getElementById('copyright-year');
            if (yearSpan) {
                yearSpan.textContent = new Date().getFullYear();
            }

            // // NAVBAR SCROLL PADDING ADJUSTMENT & PARALLAX HERO
            const nav = document.getElementById('navbar');
            const heroBg = document.getElementById('hero-bg');

            window.addEventListener('scroll', () => {
                const scrolled = window.scrollY;

                if (nav) {
                    const navContainer = nav.querySelector('div');
                    if (scrolled > 50) {
                        navContainer.classList.add('py-2');
                        navContainer.classList.remove('py-4');
                    } else {
                        navContainer.classList.add('py-4');
                        navContainer.classList.remove('py-2');
                    }
                }

                if (heroBg) {
                    heroBg.style.transform = `translate3d(0, ${scrolled * 0.35}px, 0) scale(1.1)`;
                }
            });

            // // MOBILE DRAWER & MENU TOGGLE
            const menuBtn = document.getElementById('menu-btn');
            const mobileMenu = document.getElementById('mobile-menu');
            const mobileNav = document.getElementById('mobile-nav');
            const closeMobileNav = document.getElementById('close-mobile-nav');
            const mobileNavOverlay = document.getElementById('mobile-nav-overlay');

            if (menuBtn) {
                menuBtn.addEventListener('click', (e) => {
                    if (window.innerWidth < 768 && mobileNav && mobileNavOverlay) {
                        mobileNav.classList.remove('-translate-x-full');
                        mobileNavOverlay.classList.remove('hidden');
                        if (mobileMenu) mobileMenu.classList.add('hidden');
                        e.stopPropagation();
                    } else if (mobileMenu) {
                        mobileMenu.classList.toggle('hidden');
                    }
                });
            }

            if (mobileNav && closeMobileNav && mobileNavOverlay) {
                const closeMenu = () => {
                    mobileNav.classList.add('-translate-x-full');
                    mobileNavOverlay.classList.add('hidden');
                };

                closeMobileNav.addEventListener('click', closeMenu);
                mobileNavOverlay.addEventListener('click', closeMenu);
            }

        });
    </script>
</body>
</html>
