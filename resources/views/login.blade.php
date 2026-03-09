<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sakuin Aja</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/auth-diprella.css') }}">
</head>
<body>

<div class="auth-wrap">
    <div class="left">
        <div class="brand">
            <div class="brand-badge">💰</div>
            <div>SakuinAja</div>
        </div>

        <h1>Welcome Back!</h1>
        <p>Untuk tetap terhubung, silakan login menggunakan informasi akunmu.</p>

        <a class="ghost-btn" href="{{ route('register') }}">SIGN UP</a>
    </div>

    <div class="right">
        <div class="form-box">
            <h2>Sign In</h2>

            <div class="social">
                <a href="#" title="Facebook">f</a>
                <a href="#" title="Google">G+</a>
                <a href="#" title="LinkedIn">in</a>
            </div>

            <div class="or">atau gunakan akun email kamu</div>

            @if(session('loginError'))
                <div class="error-msg">
                    {{ session('loginError') }}
                </div>
            @endif

            @if($errors->any())
                <div class="error-msg">
                    <ul style="margin:0; padding-left:18px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf

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
                    @error('email')
                        <small style="color:#e74c3c;">{{ $message }}</small>
                    @enderror
                </div>

                <div class="field">
                    <input
                        type="password"
                        name="password"
                        placeholder="Password"
                        required
                        autocomplete="current-password"
                    >
                    @error('password')
                        <small style="color:#e74c3c;">{{ $message }}</small>
                    @enderror
                </div>

                <button type="submit" class="primary-btn">SIGN IN</button>

                <div class="small-link">
                    Belum punya akun? <a href="{{ route('register') }}">Daftar</a>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>