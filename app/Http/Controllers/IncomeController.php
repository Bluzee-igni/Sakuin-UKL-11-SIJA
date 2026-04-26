<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncomeController extends Controller
{
    public function create()
    {
        return view('Incomes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:1',
            'tanggal' => 'required|date',
            'catatan' => 'nullable|string|max:255',
        ]);

        Auth::user()->incomes()->create($validated);

        return redirect()->route('incomes.create')->with('success', 'Pemasukan berhasil ditambahkan');
    }
}
