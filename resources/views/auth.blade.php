<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $mode === 'register' ? 'Daftar' : 'Login' }} - Sakuin</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    
    <!-- Custom Dedicated Login CSS -->
    <link rel="stylesheet" href="{{ asset('css/login.css') }}?v={{ time() }}">
</head>
<body>

    {{-- AESTHETIC BACKGROUND BLOBS --}}
    <div class="bg-blobs">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
    </div>

    {{-- SPLASH SCREEN ANIMATION (Dedicated Login) --}}
    <div id="splash-screen">
        <div class="splash-logo">
            <i class="ph-fill ph-wallet" style="font-size: 3.5rem;"></i>
        </div>
        <h2 class="font-poppins fw-bold text-primary splash-text">Sakuin</h2>
        <p class="text-muted small splash-text" style="animation-delay: 0.3s;">Mengatur keuangan dengan cerdas</p>
    </div>

    {{-- MAIN LOGIN/REGISTER CONTENT --}}
    <div class="login-wrapper">
        <div class="login-card mx-auto" id="main-auth-content">
            
            {{-- KIRI: FORM LOGIN / REGISTER --}}
            <div class="login-left order-2 order-md-1">
                
                {{-- LOGO BALANCE DI ATAS FORM --}}
                <div class="logo-container">
                    <div class="icon-box">
                        <i class="ph-fill ph-wallet"></i>
                    </div>
                    <h1 class="logo-text text-primary">Sakuin</h1>
                </div>

                <div class="mb-4 text-center">
                    <h3 class="font-poppins fw-bold">
                        {{ $mode === 'register' ? 'Buat Akun Baru' : 'Selamat Datang Kembali' }}
                    </h3>
                    <p class="text-muted small">
                        {{ $mode === 'register' ? 'Mulai kelola keuanganmu dengan langkah mudah.' : 'Masuk ke akunmu untuk mengontrol anggaran hari ini.' }}
                    </p>
                </div>

                @if($mode === 'login' && session('loginError'))
                    <div class="alert bg-danger bg-opacity-10 text-danger border-0 rounded-3 py-2 small d-flex align-items-center gap-2">
                        <i class="ph-fill ph-warning-circle fs-5"></i> {{ session('loginError') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert bg-danger bg-opacity-10 text-danger border-0 rounded-3 py-2 small">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ $mode === 'register' ? route('register') : route('login') }}" method="POST">
                    @csrf
                    
                    @if($mode === 'register')
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-medium">Nama Lengkap</label>
                        <div class="input-group">
                            <span class="input-group-text input-group-text-custom">
                                <i class="ph ph-user"></i>
                            </span>
                            <input type="text" name="name" class="form-control form-control-custom border-start-0 ps-2" placeholder="John Doe" value="{{ old('name') }}" required autofocus>
                        </div>
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-medium">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text input-group-text-custom">
                                <i class="ph ph-envelope-simple"></i>
                            </span>
                            <input type="email" name="email" class="form-control form-control-custom border-start-0 ps-2" placeholder="nama@email.com" value="{{ old('email') }}" required {{ $mode === 'login' ? 'autofocus' : '' }}>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted small fw-medium">Password</label>
                        <div class="input-group">
                            <span class="input-group-text input-group-text-custom">
                                <i class="ph ph-lock-key"></i>
                            </span>
                            <input type="password" name="password" class="form-control form-control-custom border-start-0 ps-2" placeholder="••••••••" required>
                        </div>
                    </div>

                    @if($mode === 'login')
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label text-muted small" for="remember">
                                Ingat saya
                            </label>
                        </div>
                        <a href="#" class="small text-primary fw-medium text-decoration-none" style="transition: opacity 0.3s;" onmouseover="this.style.opacity=0.8" onmouseout="this.style.opacity=1">Lupa Password?</a>
                    </div>
                    @else
                    <div class="mb-4"></div>
                    @endif

                    <button type="submit" class="btn btn-primary-custom w-100 py-3 mb-2">
                        {{ $mode === 'register' ? 'Daftar Sekarang' : 'Masuk ke Dashboard' }}
                    </button>
                </form>
            </div>

            {{-- KANAN: INFO / BRANDING --}}
            <div class="login-right order-1 order-md-2 d-none d-md-flex">
                <div class="mb-4">
                    <i class="ph-fill ph-shield-check text-white" style="font-size: 4rem; opacity: 0.9;"></i>
                </div>
                
                <h2 class="font-poppins fw-bold mb-3">Keamanan Terjamin</h2>
                <p class="text-white-50 mb-0" style="max-width: 300px; line-height: 1.6;">
                    Data keuangan Anda dienkripsi dan disimpan dengan aman. Pantau pemasukan dan capai target tabungan tanpa khawatir.
                </p>
                
                <div class="mt-5 pt-4 border-top border-light border-opacity-25" style="width: 80%;">
                    @if($mode === 'login')
                        <p class="small text-white-50 mb-2">Ingin memulai perjalanan menabung?</p>
                        <a href="{{ route('register') }}" class="btn btn-outline-light rounded-pill py-2 px-4 w-100 fw-medium" style="background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.3);">Buat Akun Gratis</a>
                    @else
                        <p class="small text-white-50 mb-2">Sudah jadi pengguna Sakuin?</p>
                        <a href="{{ route('login') }}" class="btn btn-outline-light rounded-pill py-2 px-4 w-100 fw-medium" style="background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.3);">Kembali Login</a>
                    @endif
                </div>
            </div>
            
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Dedicated Login JS -->
    <script src="{{ asset('js/login.js') }}?v={{ time() }}"></script>
</body>
</html>
