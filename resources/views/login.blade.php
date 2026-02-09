<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sakuin Aja</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        /* Reset Dasar */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            /* Background Gradient yang Modern & Fresh */
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #334155;
        }

        /* Container Kartu Login */
        .login-card {
            background: white;
            width: 100%;
            max-width: 400px;
            padding: 2.5rem;
            border-radius: 20px;
            /* Shadow halus biar elegan (Glassy feel) */
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            text-align: center;
        }

        .brand-logo {
            font-size: 24px;
            font-weight: 600;
            color: #166534; /* Hijau Tua */
            margin-bottom: 0.5rem;
            display: inline-block;
        }

        .brand-logo span {
            color: #22c55e; /* Hijau Muda */
        }

        h2 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: #1e293b;
        }

        p.subtitle {
            font-size: 0.9rem;
            color: #64748b;
            margin-bottom: 2rem;
        }

        /* Styling Input Form */
        .form-group {
            margin-bottom: 1.25rem;
            text-align: left;
        }

        label {
            display: block;
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #475569;
        }

        input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            outline: none;
            background-color: #f8fafc;
        }

        /* Efek saat input diklik */
        input:focus {
            border-color: #22c55e;
            background-color: white;
            box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.1);
        }

        /* Tombol Login */
        .btn-login {
            width: 100%;
            padding: 0.85rem;
            background: linear-gradient(to right, #16a34a, #15803d);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 1rem;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(22, 163, 74, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        /* Pesan Error */
        .error-msg {
            background-color: #fee2e2;
            color: #991b1b;
            padding: 0.75rem;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
            border: 1px solid #fecaca;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="brand-logo">💰 Sakuin<span>Aja</span></div>
        
        <h2>Selamat Datang!</h2>
        <p class="subtitle">Silakan masuk untuk mengelola tabunganmu.</p>

        @if(session()->has('loginError'))
            <div class="error-msg">
                {{ session('loginError') }}
            </div>
        @endif

        <form action="/login" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="contoh@email.com" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="******" required>
            </div>

            <button type="submit" class="btn-login">Masuk Sekarang</button>
        </form>
    </div>

</body>
</html>