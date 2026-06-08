<!DOCTYPE html>
<html lang="id" data-theme="{{ $userTheme ?? 'light' }}">
<head>
    <script>
        // Sync theme ke localStorage agar halaman login/register bisa mengikuti
        localStorage.setItem('sakuin_theme', '{{ $userTheme ?? 'light' }}');
    </script>
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
<body class="bg-main {{ isset($compactMode) && $compactMode ? 'compact-mode' : '' }} {{ isset($animasiAktif) && !$animasiAktif ? 'no-animations' : '' }} {{ ($hideBalance ?? false) ? 'privasi-mode' : '' }}">

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

        <!-- Profil & Notifikasi (Kanan) -->
        <div class="d-flex align-items-center gap-3">
            
            {{-- Notification Dropdown --}}
            <div class="dropdown">
                <button class="btn btn-outline-modern rounded-circle p-2 position-relative border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Notifikasi" style="background: var(--bg-main); color: var(--text-main);">
                    <i class="ph ph-bell fs-5"></i>
                    @if(isset($unreadCount) && $unreadCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white" style="font-size: 0.6rem; transform: translate(-30%, 30%) !important;">
                            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                            <span class="visually-hidden">notifikasi belum dibaca</span>
                        </span>
                    @endif
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-md border-color mt-2 rounded-4 p-0" style="width: 320px; max-height: 400px; overflow-y: auto;">
                    <div class="p-3 border-bottom border-color d-flex justify-content-between align-items-center bg-light sticky-top" style="z-index: 10;">
                        <h6 class="mb-0 font-poppins fw-bold text-dark">Notifikasi</h6>
                        @if(isset($unreadCount) && $unreadCount > 0)
                            <form action="{{ route('notifikasi.readAll') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-link text-primary text-decoration-none p-0 fw-medium" style="font-size: 0.8rem;">Tandai Dibaca</button>
                            </form>
                        @endif
                    </div>
                    
                    <div class="notifikasi-list">
                        @if(isset($unreadNotifs) && count($unreadNotifs) > 0)
                            @foreach($unreadNotifs as $notif)
                                <div class="dropdown-item p-3 border-bottom border-color text-wrap position-relative">
                                    <div class="d-flex gap-3">
                                        @php
                                            $notifIcon = 'ph-info';
                                            $notifBg = 'bg-light-info text-info';
                                            $notifData = $notif->data ?? [];
                                            $notifJenis = $notifData['jenis'] ?? '';

                                            if ($notif->tipe == 'pencapaian') {
                                                $notifIcon = 'ph-trophy';
                                                $notifBg = 'bg-light-success text-success';
                                            } elseif ($notif->tipe == 'peringatan') {
                                                $notifIcon = 'ph-warning';
                                                $notifBg = 'bg-light-warning text-warning';
                                            } elseif ($notif->tipe == 'pengingat') {
                                                $notifIcon = 'ph-clock';
                                                $notifBg = 'bg-light-primary text-primary';
                                            } elseif ($notif->tipe == 'info' && $notifJenis === 'friend_request') {
                                                $notifIcon = 'ph-user-plus';
                                                $notifBg = 'bg-light-primary text-primary';
                                            } elseif ($notif->tipe == 'info' && $notifJenis === 'friend_accepted') {
                                                $notifIcon = 'ph-user-check';
                                                $notifBg = 'bg-light-success text-success';
                                            } elseif ($notif->tipe == 'info' && $notifJenis === 'share') {
                                                $notifIcon = 'ph-share-network';
                                                $notifBg = 'bg-light-success text-success';
                                            } elseif ($notif->tipe == 'info' && $notifJenis === 'like') {
                                                $notifIcon = 'ph-heart';
                                                $notifBg = 'bg-light-danger text-danger';
                                            } elseif ($notif->tipe == 'info' && $notifJenis === 'comment') {
                                                $notifIcon = 'ph-chat-circle-dots';
                                                $notifBg = 'bg-light-info text-info';
                                            }
                                        @endphp
                                        <div class="icon-container rounded-circle flex-shrink-0 {{ $notifBg }}"
                                            style="width: 36px; height: 36px;">
                                            <i class="ph-fill {{ $notifIcon }} fs-5"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 text-dark fw-bold" style="font-size: 0.85rem;">{{ $notif->judul }}</h6>
                                            <p class="text-muted mb-1" style="font-size: 0.75rem;">{{ $notif->pesan }}</p>
                                            <small class="text-muted" style="font-size: 0.65rem;">{{ $notif->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <div class="p-2 text-center bg-light border-top border-color">
                                <span class="small text-muted">Notifikasi hanya ditampilkan di sini</span>
                            </div>
                        @else
                            <div class="p-4 text-center">
                                <div class="icon-container bg-light-secondary text-muted mx-auto rounded-circle mb-2" style="width: 48px; height: 48px;">
                                    <i class="ph ph-bell-slash fs-4"></i>
                                </div>
                                <p class="text-muted small mb-0">Belum ada notifikasi baru.</p>
                            </div>
                        @endif
                    </div>
                </ul>
            </div>

            <div class="dropdown">
                @php $navUser = auth()->user(); @endphp
                <button class="btn btn-light rounded-pill d-flex align-items-center gap-2 px-3 py-2 border border-color dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="background: var(--bg-card); color: var(--text-main);">
                    @if($navUser->foto_url)
                        <img src="{{ $navUser->foto_url }}" alt="{{ $navUser->inisial }}" class="rounded-circle" style="width: 28px; height: 28px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.8rem; font-weight: bold;">
                            {{ $navUser->inisial }}
                        </div>
                    @endif
                    <span class="d-none d-md-block fw-medium small">{{ $navUser->nama ?? 'User' }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2 rounded-4 p-2">
                    <li><a class="dropdown-item rounded-3 mb-1" href="{{ route('profil.index') }}"><i class="ph ph-user me-2"></i>Profil</a></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" class="m-0" id="logout-form-nav">
                            @csrf
                            <button type="button" class="dropdown-item rounded-3 text-danger" onclick="confirmLogout('logout-form-nav')">
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
                    <a href="{{ route('riwayat.index') }}" class="nav-link {{ request()->routeIs('riwayat.index') ? 'active' : '' }}">
                        <i class="ph ph-clock-counter-clockwise"></i>
                        <span>Riwayat</span>
                    </a>
                    <a href="{{ route('sosial.index') }}" class="nav-link {{ request()->routeIs('sosial.*') ? 'active' : '' }}">
                        <i class="ph ph-users-three"></i>
                        <span>Sosial</span>
                    </a>
                </nav>

                <div class="mt-4">
                    <h6 class="font-poppins text-muted small fw-semibold text-uppercase mb-3 px-2">Sistem</h6>
                    <nav class="nav flex-column gap-2 sidebar-nav">
                        <a href="{{ route('pengaturan.index') }}" class="nav-link {{ request()->routeIs('pengaturan.*') ? 'active' : '' }}">
                            <i class="ph ph-gear"></i>
                            <span>Pengaturan</span>
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="m-0 mt-2" id="logout-form-sidebar">
                            @csrf
                            <button type="button" class="nav-link text-danger w-100 text-start border-0 bg-transparent" onclick="confirmLogout('logout-form-sidebar')">
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
            <div class="p-4 p-md-5 page-transition">
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
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmLogout(formId) {
            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Anda akan keluar dari sesi ini.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Keluar!',
                cancelButtonText: 'Batal',
                showClass: {
                    popup: 'animate__animated animate__fadeInDown animate__faster'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp animate__faster'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }
    </script>

    <!-- Custom JS -->
    <script src="{{ asset('js/dashboard.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/auth-toggle.js') }}?v={{ time() }}"></script>

</body>
</html>
