<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function index()
    {
        return view('auth', ['mode' => 'register']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:pengguna,email',
            'password' => 'required|min:6',
        ]);

        $baseUsername = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', explode('@', $validated['email'])[0]));
        $username = $baseUsername;
        $counter = 1;
        while (Pengguna::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        $user = Pengguna::create([
            'username' => $username,
            'nama' => $validated['name'],
            'email' => $validated['email'],
            'kata_sandi' => $validated['password'],
        ]);

        Auth::login($user);

        return redirect()->route('tabung.index')->with('success', 'Registrasi berhasil, selamat datang!');
    }
}
