<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Informasi KAPUAS') - Pengarsipan Dokumen Digital</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN & Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Script deteksi status sidebar awal untuk mencegah flicker -->
    <script>
        try {
            if (window.innerWidth >= 768 && localStorage.getItem('kapuas_sidebar_collapsed') === 'true') {
                document.documentElement.classList.add('sidebar-init-collapsed');
            }
        } catch (e) {}
    </script>

    <style type="text/tailwindcss">
        @theme {
            --color-navy:          #003CAA;
            --color-navy-dark:     #0046B4;
            --color-primary:       #0050C8;
            --color-primary-light: #1478F0;
            --color-accent:        #3296FA;
            --color-accent-light:  #46AAFA;
            --color-ice:           #D2E6FA;
            --color-ink:           #1E293B;
            --color-ink-muted:     #64748B;
            --color-line:          #E2E8F0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        [x-cloak] { display: none !important; }

        /* Sidebar gradient */
        #main-sidebar {
            background: linear-gradient(160deg, #003CAA 0%, #0050C8 55%, #3296FA 100%);
        }

        /* Active menu item accent bar */
        .nav-active {
            background: rgba(255,255,255,0.15);
            border-left: 3px solid #46AAFA;
            padding-left: calc(0.75rem - 3px);
        }

        /* Hover on table rows */
        .table-row-hover:hover {
            background-color: rgba(210, 230, 250, 0.3);
        }

        /* Custom scrollbar for sidebar */
        #main-sidebar nav::-webkit-scrollbar { width: 4px; }
        #main-sidebar nav::-webkit-scrollbar-track { background: transparent; }
        #main-sidebar nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 4px; }

        /* Smooth sidebar transitions */
        #main-sidebar {
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @media (min-width: 768px) {
            #main-sidebar.desktop-collapsed {
                margin-left: -16rem !important;
            }
        }

        html.sidebar-init-collapsed #main-sidebar {
            margin-left: -16rem !important;
        }
    </style>
    @stack('styles')
</head>
<body class="h-full antialiased text-ink bg-slate-50 flex flex-col min-h-screen">

    <!-- Mobile sidebar backdrop -->
    <div id="sidebar-backdrop" class="fixed inset-0 z-40 bg-navy/60 backdrop-blur-sm transition-opacity duration-300 hidden md:hidden" onclick="toggleSidebar()"></div>

    <!-- Main Container -->
    <div class="flex flex-1 min-h-screen overflow-x-hidden">
        
        <!-- Sidebar Navigation -->
        <aside id="main-sidebar" class="fixed inset-y-0 left-0 z-50 w-64 text-white/90 transform -translate-x-full md:translate-x-0 md:static flex flex-col shadow-2xl flex-shrink-0">
            
            <!-- App Brand / Logo -->
            <div class="h-16 px-5 flex items-center justify-between border-b border-white/10">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-white/15 border border-white/20 flex items-center justify-center shadow-lg group-hover:bg-white/25 transition-all duration-200">
                        <img src="{{ asset('images/LOGO_KAPUAS.png') }}" alt="Logo KAPUAS" class="w-7 h-7 object-contain" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
                        <span class="text-white font-extrabold text-lg hidden items-center justify-center w-full h-full">K</span>
                    </div>
                    <div>
                        <div class="text-white font-bold text-base tracking-wide flex items-center gap-1.5">
                            KAPUAS
                            <span class="text-[9px] uppercase font-bold px-1.5 py-0.5 rounded bg-white/20 text-white/90 border border-white/20">Arsip</span>
                        </div>
                        <div class="text-[11px] text-white/60 font-medium">Pengarsipan Digital</div>
                    </div>
                </a>
                <button type="button" class="text-white/70 hover:text-white p-1.5 rounded-lg hover:bg-white/15 transition-colors flex items-center justify-center cursor-pointer" onclick="toggleSidebar()" title="Tutup Sidebar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path></svg>
                </button>
            </div>

            <!-- Current User Badge (Sidebar Header) -->
            <div class="p-4 mx-3 my-3 rounded-xl bg-white/10 border border-white/15 flex items-center gap-3 backdrop-blur-sm">
                <div class="w-10 h-10 rounded-lg bg-white/20 border border-white/25 text-white flex items-center justify-center font-bold text-sm shadow-sm">
                    {{ strtoupper(substr(auth()->user()->nama_user, 0, 2)) }}
                </div>
                <div class="overflow-hidden flex-1">
                    <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->nama_user }}</p>
                    <div class="flex items-center gap-1 mt-0.5">
                        @if(auth()->user()->isAdmin())
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-white text-navy">
                                Admin
                            </span>
                        @elseif(auth()->user()->isOperator())
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-accent-light/30 text-white border border-accent-light/40">
                                Operator
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-transparent text-white/70 border border-white/30">
                                Viewer
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Menu Navigation Links -->
            <nav class="flex-1 px-3 space-y-1 overflow-y-auto pt-1 pb-2">
                <div class="px-3 pb-1.5 text-[10px] font-bold uppercase tracking-widest text-white/40">Menu Utama</div>

                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('dashboard') ? 'nav-active text-white' : 'text-white/75 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    <span>Dashboard</span>
                </a>

                <!-- Data Arsip -->
                <a href="{{ route('arsip.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('arsip.*') ? 'nav-active text-white' : 'text-white/75 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span>Data Arsip</span>
                </a>

                <!-- Kategori Arsip (Admin & Operator) -->
                @if(auth()->user()->canManageArsip())
                <a href="{{ route('kategori.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('kategori.*') ? 'nav-active text-white' : 'text-white/75 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                    <span>Kategori Dokumen</span>
                </a>
                @endif

                <!-- Master Data (Khusus Admin) -->
                @if(auth()->user()->isAdmin())
                <div class="pt-4 px-3 pb-1.5 text-[10px] font-bold uppercase tracking-widest text-white/40">Master Data</div>
                <a href="{{ route('master-data.program.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('master-data.program.*') ? 'nav-active text-white' : 'text-white/75 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <span>Hierarki Program</span>
                </a>
                <a href="{{ route('master-data.sub-jenis-spj.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('master-data.sub-jenis-spj.*') ? 'nav-active text-white' : 'text-white/75 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    <span>Sub Jenis SPJ</span>
                </a>
                <a href="{{ route('master-data.tahun-arsip.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('master-data.tahun-arsip.*') ? 'nav-active text-white' : 'text-white/75 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span>Tahun Arsip</span>
                </a>

                <div class="pt-4 px-3 pb-1.5 text-[10px] font-bold uppercase tracking-widest text-white/40">Pengaturan Sistem</div>
                <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('users.*') ? 'nav-active text-white' : 'text-white/75 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <span>Kelola Pengguna</span>
                </a>
                @endif
            </nav>

            <!-- Bottom Logout -->
            <div class="p-4 border-t border-white/10">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-xs font-semibold text-white/70 bg-white/8 hover:bg-white/15 border border-white/15 hover:border-white/25 hover:text-white transition-all duration-150">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        <span>Keluar (Logout)</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            
            <!-- Top Navbar -->
            <header class="h-16 bg-white border-b border-line flex items-center justify-between px-4 sm:px-6 lg:px-8 z-10 sticky top-0 shadow-sm">
                <div class="flex items-center gap-4">
                    <button type="button" id="sidebar-toggle-btn" class="text-slate-600 hover:text-slate-900 p-2 rounded-xl hover:bg-slate-100 transition-colors flex items-center justify-center cursor-pointer" onclick="toggleSidebar()" title="Buka / Tutup Sidebar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <div>
                        <h1 class="text-base sm:text-lg font-bold text-ink">@yield('header-title', 'Dashboard')</h1>
                        <p class="text-xs text-ink-muted hidden sm:block">@yield('header-subtitle', 'Sistem Pengarsipan Dokumen Digital Resmi')</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Date Pill -->
                    <div class="hidden lg:flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-ice/60 text-ink-muted text-xs font-medium border border-line">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                    </div>

                    <!-- User + Role Chip -->
                    <div class="flex items-center gap-2 pl-3 border-l border-line">
                        <div class="text-right hidden sm:block">
                            <div class="text-xs font-bold text-ink leading-tight">{{ auth()->user()->nama_user }}</div>
                            <div class="text-[11px] text-ink-muted capitalize">
                                @if(auth()->user()->isAdmin())
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-navy text-white">Admin</span>
                                @elseif(auth()->user()->isOperator())
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-primary text-white">Operator</span>
                                @else
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold border border-ink-muted text-ink-muted">Viewer</span>
                                @endif
                            </div>
                        </div>
                        <div class="w-9 h-9 rounded-full text-white font-bold text-xs flex items-center justify-center shadow-sm" style="background: linear-gradient(135deg, #003CAA, #3296FA)">
                            {{ strtoupper(substr(auth()->user()->nama_user, 0, 1)) }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 bg-slate-50">
                
                <!-- Toast Alerts / Notifications -->
                @if(session('success'))
                <div id="toast-success" class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-start gap-3 text-emerald-800 shadow-sm transition-all duration-300">
                    <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-900">Operasi Berhasil</h4>
                        <p class="text-sm font-medium mt-0.5">{{ session('success') }}</p>
                    </div>
                    <button type="button" onclick="document.getElementById('toast-success').remove()" class="text-emerald-500 hover:text-emerald-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                @endif

                @if(session('error'))
                <div id="toast-error" class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 flex items-start gap-3 text-rose-800 shadow-sm transition-all duration-300">
                    <div class="w-6 h-6 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-rose-900">Perhatian / Galat</h4>
                        <p class="text-sm font-medium mt-0.5">{{ session('error') }}</p>
                    </div>
                    <button type="button" onclick="document.getElementById('toast-error').remove()" class="text-rose-500 hover:text-rose-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                @endif

                @if($errors->any())
                <div id="toast-validation" class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 shadow-sm">
                    <div class="flex items-center gap-2 font-bold text-xs uppercase tracking-wider text-amber-900 mb-1">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <span>Terdapat Kesalahan Pengisian Form:</span>
                    </div>
                    <ul class="list-disc list-inside text-sm space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="py-4 px-6 bg-white border-t border-line text-center text-xs text-ink-muted">
                &copy; {{ date('Y') }} <strong class="text-ink">Sistem Informasi KAPUAS</strong> &mdash; Sistem Pengarsipan Dokumen Digital Instansi.
            </footer>
        </div>
    </div>

    <!-- Universal Delete Confirmation Modal -->
    <div id="delete-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-navy/60 backdrop-blur-sm p-4 hidden">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 border border-line transform transition-all">
            <div class="w-12 h-12 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </div>
            <h3 class="text-base font-bold text-ink" id="delete-modal-title">Konfirmasi Hapus Data</h3>
            <p class="text-sm text-ink-muted mt-2" id="delete-modal-description">Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.</p>
            <div class="mt-6 flex items-center justify-end gap-3">
                <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 text-xs font-semibold rounded-lg bg-slate-100 text-ink hover:bg-slate-200 transition-colors">
                    Batal
                </button>
                <form id="delete-modal-form" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 text-xs font-semibold rounded-lg bg-rose-600 text-white hover:bg-rose-700 shadow-sm transition-colors">
                        Ya, Hapus Data
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function isDesktop() {
            return window.innerWidth >= 768;
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('main-sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            
            if (isDesktop()) {
                const isCollapsed = sidebar.classList.toggle('desktop-collapsed');
                try {
                    localStorage.setItem('kapuas_sidebar_collapsed', isCollapsed ? 'true' : 'false');
                } catch (e) {}
            } else {
                const isClosed = sidebar.classList.toggle('-translate-x-full');
                if (backdrop) {
                    backdrop.classList.toggle('hidden', isClosed);
                }
            }
        }

        // Terapkan preferensi status sidebar desktop saat halaman dibuka
        (function() {
            try {
                if (isDesktop() && localStorage.getItem('kapuas_sidebar_collapsed') === 'true') {
                    const sidebar = document.getElementById('main-sidebar');
                    if (sidebar) sidebar.classList.add('desktop-collapsed');
                }
            } catch (e) {}
            document.documentElement.classList.remove('sidebar-init-collapsed');
        })();

        // Sembunyikan backdrop otomatis jika layar di-resize ke ukuran desktop
        window.addEventListener('resize', () => {
            const backdrop = document.getElementById('sidebar-backdrop');
            if (isDesktop() && backdrop) {
                backdrop.classList.add('hidden');
            }
        });

        function confirmDelete(url, title = 'Konfirmasi Hapus Data', description = 'Apakah Anda yakin ingin menghapus data ini?') {
            const modal = document.getElementById('delete-modal');
            const form = document.getElementById('delete-modal-form');
            const titleEl = document.getElementById('delete-modal-title');
            const descEl = document.getElementById('delete-modal-description');

            form.action = url;
            titleEl.textContent = title;
            descEl.textContent = description;

            modal.classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('delete-modal').classList.add('hidden');
        }

        // Auto hide toasts after 5 seconds
        setTimeout(() => {
            const successToast = document.getElementById('toast-success');
            if (successToast) {
                successToast.style.opacity = '0';
                setTimeout(() => successToast.remove(), 300);
            }
        }, 5000);
    </script>
    @stack('scripts')
</body>
</html>
