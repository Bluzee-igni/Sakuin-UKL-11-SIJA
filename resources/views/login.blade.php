<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sakuin - Login</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}?v={{ time() }}">
    
    <style>
        body {
            background-color: var(--bg-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background-color: var(--bg-card);
            border-radius: 1.5rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            overflow: hidden;
            width: 100%;
            max-width: 900px;
            display: flex;
            flex-direction: column;
        }
        @media (min-width: 768px) {
            .login-card {
                flex-direction: row;
            }
        }
        .login-left {
            background: linear-gradient(135deg, var(--primary) 0%, #022C22 100%);
            padding: 3rem;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            flex: 1;
        }
        .login-right {
            padding: 3rem;
            flex: 1.2;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
    </style>
</head>
<body>

    {{-- SPLASH SCREEN --}}
    <div id="splash-screen" class="d-flex flex-column justify-content-center align-items-center" style="display: none;">
        <div class="icon-container bg-light-primary text-primary mb-3" style="width: 80px; height: 80px; animation: bounceIn 1s ease-out forwards;">
            <i class="ph-fill ph-wallet" style="font-size: 3rem;"></i>
        </div>
        <h2 class="font-poppins fw-bold text-primary" style="animation: fadeInUp 1s ease-out 0.2s forwards;">Sakuin</h2>
        <p class="text-muted small" style="animation: fadeInUp 1s ease-out 0.4s forwards;">Mengatur keuangan dengan mudah</p>
    </div>

    <div class="container p-3 p-md-0" id="main-login-content" style="opacity: 0; transition: opacity 0.5s ease-in;">
        <div class="login-card mx-auto">
            
            {{-- LEFT SECTION --}}
            <div class="login-left text-center text-md-start">
                <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-2 mb-4">
                    <div class="icon-container bg-white text-primary" style="width: 48px; height: 48px;">
                        <i class="ph-fill ph-wallet fs-3"></i>
                    </div>
                    <span class="fs-3 fw-bold font-poppins">Sakuin</span>
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
                    <div class="alert bg-light-danger text-danger border-0 rounded-4 py-2 small d-flex align-items-center gap-2">
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