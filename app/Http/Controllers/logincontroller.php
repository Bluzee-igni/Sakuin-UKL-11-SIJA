<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        return view('login'); // Ini akan memanggil file login.blade.php
    }

    public function authenticate(Request $request)
    {
        // Validasi input email dan password
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Cek kecocokan di database
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/tabung');
        }

        // Jika gagal, balik lagi ke login
        return back()->with('loginError', 'Login Gagal! Cek email/password.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}