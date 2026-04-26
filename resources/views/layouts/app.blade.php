<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sakuin - @yield('title', 'Aplikasi Keuangan')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Fullcalendar CSS (Yielded conditionally if needed) -->
    @stack('styles')
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}?v={{ time() }}">
</head>
<body class="bg-main">

    {{-- TOP NAVBAR --}}
    <nav class="top-navbar d-flex justify-content-between align-items-center px-4 shadow-sm">
        <!-- Hamburger Menu untuk Mobile -->
        <button id="sidebar-toggle-btn" class="btn btn-outline-modern rounded-circle d-md-none p-2" title="Toggle Menu">
            <i class="ph ph-list fs-5"></i>
        </button>

        <!-- Brand/Logo (Kiri) -->
        <div class="d-flex align-items-center gap-2">
            <div class="icon-container bg-light-primary text-primary" style="width: 36px; height: 36px;">
                <i class="ph-fill ph-wallet fs-4"></i>
            </div>
            <span class="fs-5 fw-bold font-poppins text-primary d-none d-sm-block">Sakuin</span>
        </div>

        <!-- Profil & Dark Mode (Kanan) -->
        <div class="d-flex align-items-center gap-3">
            <button id="theme-toggle" class="btn btn-outline-modern rounded-circle d-flex p-2" title="Toggle Dark Mode">
                <i id="theme-icon" class="ph ph-moon fs-5"></i>
            </button>
            <div class="dropdown">
                <button class="btn btn-light rounded-pill d-flex align-items-center gap-2 px-3 py-2 border border-color dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="background: var(--bg-card); color: var(--text-main);">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.8rem; font-weight: bold;">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <span class="d-none d-md-block fw-medium small">{{ auth()->user()->name ?? 'User' }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2 rounded-4 p-2">
                    <li><a class="dropdown-item rounded-3 mb-1" href="#"><i class="ph ph-user me-2"></i>Profil</a></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="dropdown-item rounded-3 text-danger">
                                <i class="ph ph-sign-out me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    {{-- LAYOUT WRAPPER --}}
    <div class="app-wrapper">

        {{-- LEFT SIDEBAR --}}
        <aside class="left-sidebar shadow-sm" id="leftSidebar">
            <div class="p-4 h-100 d-flex flex-column">
                
                <div class="d-flex justify-content-between align-items-center mb-4 d-md-none">
                    <span class="fs-5 fw-bold font-poppins text-primary">Menu</span>
                    <button id="sidebar-close-btn" class="btn btn-outline-modern rounded-circle p-2">
                        <i class="ph ph-x fs-5"></i>
                    </button>
                </div>

                <h6 class="font-poppins text-muted small fw-semibold text-uppercase mb-3 px-2 mt-md-3">Menu Utama</h6>
                
                <nav class="nav flex-column gap-2 mb-auto sidebar-nav">
                    <a href="{{ route('tabung.index') }}" class="nav-link {{ request()->routeIs('tabung.index') ? 'active' : '' }}">
                        <i class="ph ph-squares-four"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('management.index') }}" class="nav-link {{ request()->routeIs('management.index') ? 'active' : '' }}">
                        <i class="ph ph-wallet"></i>
                        <span>Manajemen Keuangan</span>
                    </a>
                    <a href="{{ route('tabung.create') }}" class="nav-link {{ request()->routeIs('tabung.create', 'tabung.edit') ? 'active' : '' }}">
                        <i class="ph ph-target"></i>
                        <span>Target Tabungan</span>
                    </a>
                    <a href="#" class="nav-link">
                        <i class="ph ph-clock-counter-clockwise"></i>
                        <span>Riwayat Transaksi</span>
                    </a>
                </nav>

                <div class="mt-4">
                    <h6 class="font-poppins text-muted small fw-semibold text-uppercase mb-3 px-2">Sistem</h6>
                    <nav class="nav flex-column gap-2 sidebar-nav">
                        <a href="#" class="nav-link">
                            <i class="ph ph-gear"></i>
                            <span>Pengaturan</span>
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="m-0 mt-2">
                            @csrf
                            <button type="submit" class="nav-link text-danger w-100 text-start border-0 bg-transparent">
                                <i class="ph ph-sign-out"></i>
                                <span>Keluar</span>
                            </button>
                        </form>
                    </nav>
                </div>
            </div>
        </aside>

        {{-- MAIN CONTENT (Kanan) --}}
        <main class="main-content">
            <div class="p-4 p-md-5">
                @yield('content')
            </div>
        </main>

        <!-- Overlay Mobile -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

    </div>

    {{-- SCRIPTS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Optional Scripts -->
    @stack('scripts')
    
    <!-- Custom JS -->
    <script src="{{ asset('js/dashboard.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/auth-toggle.js') }}?v={{ time() }}"></script>

</body>
</html>
