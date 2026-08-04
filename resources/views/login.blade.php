<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sakuin - Login</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo-sakuin.jpg') }}">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}?v={{ time() }}">
    
    <style>
        body {
            background: radial-gradient(circle at top left, rgba(5,150,105,0.24) !important, transparent 18%) !important;
            min-height: 100vh !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 2rem !important;
            background-color: #e7fbf2 !important;
        }
        .login-card {
            background: rgba(255,255,255,0.94) !important;
            border: 1px solid rgba(5,150,105,0.14) !important;
            backdrop-filter: blur(26px) !important;
            border-radius: 2rem !important;
            box-shadow: 0 36px 120px rgba(5,150,105,0.18) !important;
            overflow: hidden !important;
            width: 100% !important;
            max-width: 980px !important;
            display: flex !important;
            flex-direction: column !important;
            position: relative !important;
        }
        .login-card::before,
        .login-card::after {
            content: '' !important;
            position: absolute !important;
            border-radius: 50% !important;
            filter: blur(30px) !important;
            opacity: 0.5 !important;
            pointer-events: none !important;
        }
        .login-card::before {
            width: 260px !important;
            height: 260px !important;
            top: -70px !important;
            right: -70px !important;
            background: rgba(14,165,233,0.28) !important;
        }
        .login-card::after {
            width: 200px !important;
            height: 200px !important;
            bottom: -50px !important;
            left: -50px !important;
            background: rgba(5,150,105,0.38) !important;
        }
        @media (min-width: 768px) {
            .login-card {
                flex-direction: row !important;
            }
        }
        .login-left {
            background: linear-gradient(135deg, rgba(5,150,105,0.95) 0%, rgba(13,81,98,0.95) 100%) !important;
            padding: 3rem !important;
            color: white !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            flex: 1 !important;
            position: relative !important;
        }
        .login-left::after {
            content: '' !important;
            position: absolute !important;
            width: 180px !important;
            height: 180px !important;
            bottom: -45px !important;
            left: 30px !important;
            border-radius: 50% !important;
            background: rgba(255,255,255,0.1) !important;
            filter: blur(10px) !important;
        }
        .login-right {
            padding: 3rem !important;
            flex: 1.2 !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            background: rgba(255,255,255,0.96) !important;
            box-shadow: inset 0 0 0 1px rgba(5,150,105,0.1) !important;
        }
        .login-heading {
            font-size: clamp(2rem, 2.5vw, 3rem) !important;
            line-height: 1.05 !important;
            letter-spacing: -0.03em !important;
            color: var(--text-main) !important;
        }
        .login-copy {
            color: rgba(15,23,42,0.8) !important;
            max-width: 28rem !important;
            margin-top: 1rem !important;
            line-height: 1.85 !important;
        }
        .feature-pill {
            display: inline-flex !important;
            align-items: center !important;
            gap: 0.5rem !important;
            background: rgba(255,255,255,0.16) !important;
            color: rgba(255,255,255,0.95) !important;
            padding: 0.75rem 1rem !important;
            border-radius: 9999px !important;
            margin-top: 1.5rem !important;
            font-size: 0.9rem !important;
            font-weight: 600 !important;
            box-shadow: inset 0 0 0 1px rgba(255,255,255,0.12) !important;
        }
        .login-right .form-control {
            background: rgba(245,249,250,0.98) !important;
            border: 1px solid rgba(5,150,105,0.14) !important;
            border-radius: 1rem !important;
            padding: 1rem 1.1rem !important;
            transition: all 0.2s ease !important;
            color: var(--text-main) !important;
        }
        .login-right .form-control:focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 4px rgba(5,150,105,0.16) !important;
        }
        .login-right .btn-primary-modern {
            width: 100% !important;
            padding: 1rem 1.2rem !important;
            font-size: 1rem !important;
        }
        .input-group-text {
            background: rgba(255,255,255,0.95) !important;
            border: 1px solid rgba(5,150,105,0.14) !important;
            border-right: none !important;
            color: var(--text-muted) !important;
        }
        .input-group .form-control {
            border-left: none !important;
        }
        .form-check-input:checked {
            background-color: var(--primary) !important;
            border-color: var(--primary) !important;
        }
        .login-note {
            color: var(--text-muted) !important;
        }
    </style>
</head>
<body>

    {{-- SPLASH SCREEN --}}
    <div id="splash-screen" class="d-flex flex-column justify-content-center align-items-center" style="display: none;">
        <img src="{{ asset('images/logo-sakuin.jpg') }}" alt="SakuinAja" class="mb-3" style="width: 120px; height: auto; object-fit: contain; animation: bounceIn 1s ease-out forwards;">
        <p class="text-muted small" style="animation: fadeInUp 1s ease-out 0.4s forwards;">Mengatur keuangan dengan mudah</p>
    </div>

    <div class="container p-3 p-md-0" id="main-login-content" style="opacity: 0; transition: opacity 0.5s ease-in;">
        <div class="login-card mx-auto">
            
            {{-- LEFT SECTION --}}
            <div class="login-left text-center text-md-start">
                <div class="d-flex align-items-center justify-content-center justify-content-md-start mb-4">
                    <img src="{{ asset('images/logo-sakuin.jpg') }}" alt="SakuinAja" style="height: 56px; width: auto; object-fit: contain; filter: brightness(0) invert(1);">
                </div>
                
                <h2 class="font-poppins fw-bold mb-3">Selamat Datang!</h2>
                <p class="text-white-50 mb-5">Atur keuangan bulanan, catat pengeluaran, dan capai target tabunganmu dengan lebih mudah dan terencana bersama Sakuin.</p>
                
                <p class="small text-white-50 mb-2">Belum punya akun?</p>
                <a href="{{ route('register') }}" class="btn btn-outline-light rounded-pill py-2 px-4 w-100 fw-medium">Buat Akun Sekarang</a>
            </div>

            {{-- RIGHT SECTION --}}
            <div class="login-right bg-card">
                <div class="mb-4 text-center text-md-start">
                    <h3 class="font-poppins fw-bold text-main">Masuk ke Akunmu</h3>
                    <p class="text-muted small">Silakan masukkan email dan password untuk melanjutkan.</p>
                </div>

                @if(session('loginError'))
                    <div class="alert bg-light-danger text-danger border border-danger border-opacity-25 rounded-4 py-2 small d-flex align-items-center gap-2">
                        <i class="ph-fill ph-warning-circle fs-5"></i> {{ session('loginError') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert bg-light-danger text-danger border-0 rounded-4 py-2 small">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-medium">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0 text-muted" style="border-radius: var(--radius-md) 0 0 var(--radius-md);">
                                <i class="ph ph-envelope-simple"></i>
                            </span>
                            <input type="email" name="email" class="form-control form-control-modern border-start-0 ps-0" placeholder="nama@email.com" value="{{ old('email') }}" required autofocus>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted small fw-medium">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0 text-muted" style="border-radius: var(--radius-md) 0 0 var(--radius-md);">
                                <i class="ph ph-lock-key"></i>
                            </span>
                            <input type="password" name="password" class="form-control form-control-modern border-start-0 ps-0" placeholder="••••••••" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember">
                            <label class="form-check-label text-muted small" for="remember">
                                Ingat saya
                            </label>
                        </div>
                        <a href="#" class="small text-primary fw-medium text-decoration-none">Lupa Password?</a>
                    </div>

                    <button type="submit" class="btn btn-primary-modern w-100 py-3 mb-3 fw-semibold">
                        Masuk
                    </button>

                    <div class="text-center">
                        <p class="small text-muted mb-0 d-md-none">Belum punya akun? <a href="{{ route('register') }}" class="text-primary fw-medium text-decoration-none">Daftar</a></p>
                    </div>
                </form>
            </div>
            
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Splash Screen Logic -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const splashScreen = document.getElementById('splash-screen');
            const mainContent = document.getElementById('main-login-content');

            // Cek apakah animasi sudah pernah dijalankan di sesi ini
            if (!sessionStorage.getItem('splashPlayed')) {
                // Tampilkan splash screen
                splashScreen.style.display = 'flex';
                
                // Tunggu 2 detik untuk animasi selesai, lalu fade out
                setTimeout(() => {
                    splashScreen.classList.add('fade-out');
                    splashScreen.style.pointerEvents = 'none'; // Jangan block UI
                    
                    // Tampilkan konten utama
                    mainContent.style.opacity = '1';

                    // Remove element completely
                    setTimeout(() => {
                        splashScreen.remove();
                    }, 500); // 500ms adalah durasi CSS transition fade-out
                    
                }, 2000);

                // Tandai bahwa animasi sudah diputar
                sessionStorage.setItem('splashPlayed', 'true');
            } else {
                // Jika sudah pernah diputar, langsung hilangkan splash dan tampilkan konten
                if (splashScreen) splashScreen.remove();
                mainContent.style.opacity = '1';
            }
        });
    </script>
</body>
</html>