<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    public function readAll()
    {
        Notifikasi::where('pengguna_id', Auth::id())
            ->belumDibaca()
            ->update(['dibaca_pada' => now()]);

        return back()->with('success', 'Semua notifikasi telah ditandai sudah dibaca.');
    }
}
