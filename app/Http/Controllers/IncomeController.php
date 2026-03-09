<?php

namespace App\Http\Controllers;

use App\Models\Income;
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
        $request->validate([
            'tipe' => 'required',
            'sumber' => 'nullable|string|max:255',
            'nominal' => 'required|numeric|min:1',
            'tanggal' => 'required|date',
            'catatan' => 'nullable|string|max:255',
        ]);

        Income::create([
            'user_id' => Auth::id(),
            'tipe' => $request->tipe,
            'sumber' => $request->sumber,
            'nominal' => $request->nominal,
            'tanggal' => $request->tanggal,
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('incomes.create')->with('success', 'Pemasukan berhasil ditambahkan');
    }
}
