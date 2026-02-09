<?php

namespace App\Http\Controllers;

use App\Models\Tabung; // <-- PENTING: Koneksi ke Model Database
use Illuminate\Http\Request;

class TabungController extends Controller
{
    /**
     * Menampilkan daftar tabungan (READ)
     */
    public function index()
    {
        // Ambil semua data, urutkan dari yang terbaru (id terbesar)
        $data = Tabung::latest()->get();
        return view('tabung.index', compact('data'));
    }

    /**
     * Menampilkan form tambah data (View Create)
     */
    public function create()
    {
        return view('tabung.create');
    }

    /**
     * Menyimpan data baru (CREATE)
     * Fitur: Otomatis hitung Total & Tanggal Hari Ini
     */
    public function store(Request $request)
    {
        // Validasi: Cuma butuh Nama & Jumlah (Total & Tanggal diurus sistem)
        $request->validate([
            'nama'           => 'required|string|max:255',
            'jumlah_tabung'  => 'required|numeric',
        ]);

        // Hitung total dari semua uang yang sudah ada sebelumnya
        $total_sebelumnya = Tabung::sum('jumlah_tabung');
        
        // Tambah dengan uang yang baru disetor
        $total_baru = $total_sebelumnya + $request->jumlah_tabung;

        // Simpan
        Tabung::create([
            'nama'           => $request->nama,
            'jumlah_tabung'  => $request->jumlah_tabung,
            'total_tabungan' => $total_baru, // <-- Hasil hitungan
            'tanggal'        => now(),       // <-- Otomatis hari ini
        ]);

        return redirect()->route('tabung.index')
                         ->with('success', 'Data berhasil ditambahkan!');
    }

    /**
     * Menampilkan form edit (View Edit)
     */
    public function edit(Tabung $tabung)
    {
        return view('tabung.edit', compact('tabung'));
    }

    /**
     * Mengupdate data (UPDATE)
     * Fitur: Otomatis Hitung Ulang Total jika jumlah diubah
     */
    public function update(Request $request, Tabung $tabung)
    {
        // 1. Validasi: Cuma Nama & Jumlah (Tanggal dihapus karena otomatis)
        $request->validate([
            'nama'           => 'required|string|max:255',
            'jumlah_tabung'  => 'required|numeric',
        ]);

        // 2. Logika Hitung Ulang Total
        $total_sebelumnya = Tabung::where('id', '<', $tabung->id)->sum('jumlah_tabung');
        $total_baru = $total_sebelumnya + $request->jumlah_tabung;

        // 3. Update ke database
        $tabung->update([
            'nama'           => $request->nama,
            'jumlah_tabung'  => $request->jumlah_tabung,
            'total_tabungan' => $total_baru,
            'tanggal'        => now(), // <-- OTOMATIS TANGGAL HARI INI
        ]);

        return redirect()->route('tabung.index')
                         ->with('success', 'Data berhasil diperbarui! Tanggal tercatat hari ini.');
    }

    /**
     * Menghapus data (DELETE)
     */
    public function destroy(Tabung $tabung)
    {
        $tabung->delete();
        return redirect()->route('tabung.index')
                         ->with('success', 'Data berhasil dihapus!');
    }
}