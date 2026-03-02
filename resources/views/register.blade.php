<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - Sakuin Aja</title>
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

      <h1>Hello, Friend!</h1>
      <p>Buat akun baru untuk mulai mengelola tabunganmu.</p>

      <a class="ghost-btn" href="{{ route('login') }}">SIGN IN</a>
    </div>

    <div class="right">
      <div class="form-box">
        <h2>Create Account</h2>

        <div class="social">
          <a href="#" title="Facebook">f</a>
          <a href="#" title="Google">G+</a>
          <a href="#" title="LinkedIn">in</a>
        </div>

        <div class="or">atau gunakan email untuk registrasi</div>

        @if($errors->any())
          <div class="error-msg">
            {{ $errors->first() }}
          </div>
        @endif

        <form action="{{ route('register') }}" method="POST">
          @csrf

          <div class="field">
            <input type="text" name="name" placeholder="Name" value="{{ old('name') }}" required>
          </div>

          <div class="field">
            <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
          </div>

          <div class="field">
            <input type="password" name="password" placeholder="Password (min 6)" required>
          </div>

          <button type="submit" class="primary-btn">SIGN UP</button>

          <div class="small-link">
            Sudah punya akun? <a href="{{ route('login') }}">Login</a>
          </div>
        </form>
      </div>
    </div>
  </div>

</body>
</html>