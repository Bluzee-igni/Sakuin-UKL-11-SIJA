<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $mode === 'register' ? 'Register' : 'Login' }} - Sakuin Aja</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/auth-diprella.css') }}">
</head>
<body>
    <div class="auth-shell">
        <div class="auth-container {{ $mode === 'register' ? 'active' : '' }}">
            <div class="form-panel sign-up-panel">
                <form action="{{ route('register') }}" method="POST" novalidate>
                    @csrf

                    <div class="brand brand-mobile">
                        <div class="brand-badge">SA</div>
                        <div>SakuinAja</div>
                    </div>

                    <h1>Create Account</h1>
                    <p class="form-copy">Buat akun baru untuk mulai mengelola tabunganmu.</p>

                    @if($mode === 'register' && $errors->any())
                        <div class="error-msg">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="field">
                        <input type="text" name="name" placeholder="Name" value="{{ old('name') }}" required>
                    </div>

                    <div class="field">
                        <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
                    </div>

                    <div class="field password-field">
                        <input type="password" name="password" placeholder="Password (min 6)" required data-password-input>
                        <button type="button" class="password-toggle" data-password-toggle aria-label="Tampilkan password" aria-pressed="false">
                            <svg class="eye-icon eye-open" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M1.5 12s3.8-6.5 10.5-6.5S22.5 12 22.5 12 18.7 18.5 12 18.5 1.5 12 1.5 12Z" />
                                <circle cx="12" cy="12" r="3.2" />
                            </svg>
                            <svg class="eye-icon eye-closed" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M3 3l18 18" />
                                <path d="M10.6 5.7A10.9 10.9 0 0 1 12 5.5C18.7 5.5 22.5 12 22.5 12a18.2 18.2 0 0 1-3.6 4.3" />
                                <path d="M14.8 14.9A3.2 3.2 0 0 1 9.1 9.2" />
                                <path d="M6.5 6.6A18.7 18.7 0 0 0 1.5 12s3.8 6.5 10.5 6.5a10.7 10.7 0 0 0 4.1-.8" />
                            </svg>
                        </button>
                    </div>

                    <button type="submit" class="primary-btn">SIGN UP</button>

                    <p class="small-link">
                        Sudah punya akun?
                        <a href="{{ route('login') }}" data-auth-toggle="login">Login</a>
                    </p>
                </form>
            </div>

            <div class="form-panel sign-in-panel">
                <form action="{{ route('login') }}" method="POST" novalidate>
                    @csrf

                    <div class="brand brand-mobile">
                        <div class="brand-badge">SA</div>
                        <div>SakuinAja</div>
                    </div>

                    <h1>Sign In</h1>
                    <p class="form-copy">Masuk dan lanjutkan perjalanan nabungmu.</p>

                    @if($mode === 'login' && session('loginError'))
                        <div class="error-msg">
                            {{ session('loginError') }}
                        </div>
                    @endif

                    @if($mode === 'login' && $errors->any())
                        <div class="error-msg">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="field">
                        <input
                            type="email"
                            name="email"
                            placeholder="Email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="email"
                        >
                    </div>

                    <div class="field password-field">
                        <input
                            type="password"
                            name="password"
                            placeholder="Password"
                            required
                            autocomplete="current-password"
                            data-password-input
                        >
                        <button type="button" class="password-toggle" data-password-toggle aria-label="Tampilkan password" aria-pressed="false">
                            <svg class="eye-icon eye-open" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M1.5 12s3.8-6.5 10.5-6.5S22.5 12 22.5 12 18.7 18.5 12 18.5 1.5 12 1.5 12Z" />
                                <circle cx="12" cy="12" r="3.2" />
                            </svg>
                            <svg class="eye-icon eye-closed" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M3 3l18 18" />
                                <path d="M10.6 5.7A10.9 10.9 0 0 1 12 5.5C18.7 5.5 22.5 12 22.5 12a18.2 18.2 0 0 1-3.6 4.3" />
                                <path d="M14.8 14.9A3.2 3.2 0 0 1 9.1 9.2" />
                                <path d="M6.5 6.6A18.7 18.7 0 0 0 1.5 12s3.8 6.5 10.5 6.5a10.7 10.7 0 0 0 4.1-.8" />
                            </svg>
                        </button>
                    </div>

                    <label class="remember-row">
                        <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                        <span>Remember me</span>
                    </label>

                    <button type="submit" class="primary-btn">SIGN IN</button>

                    <p class="small-link">
                        Belum punya akun?
                        <a href="{{ route('register') }}" data-auth-toggle="register">Daftar</a>
                    </p>
                </form>
            </div>

            <div class="toggle-panel">
                <div class="toggle-track">
                    <div class="toggle-side toggle-left">
                        <div class="brand">
                            <div class="brand-badge">SA</div>
                            <div>SakuinAja</div>
                        </div>

                        <h2>Welcome Back!</h2>
                        <p>Untuk tetap terhubung, silakan login menggunakan informasi akunmu.</p>
                        <button class="ghost-btn login-btn" type="button" data-auth-toggle="login">SIGN IN</button>
                    </div>

                    <div class="toggle-side toggle-right">
                        <div class="brand">
                            <div class="brand-badge">SA</div>
                            <div>SakuinAja</div>
                        </div>

                        <h2>Hello, Friend!</h2>
                        <p>Buat akun baru untuk mulai mengelola tabunganmu dan pantau target dengan lebih rapi.</p>
                        <button class="ghost-btn register-btn" type="button" data-auth-toggle="register">SIGN UP</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/auth-toggle.js') }}" defer></script>
</body>
</html>
