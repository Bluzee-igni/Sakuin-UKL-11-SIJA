<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use App\Models\TransaksiOtomatis;

class ManagementController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $totalIncome = Pemasukan::milikPengguna($userId)->whereMonth('tanggal', now()->month)->sum('jumlah');
        $totalExpense = Pengeluaran::milikPengguna($userId)->whereMonth('tanggal', now()->month)->sum('jumlah');
        
        $totalAllIncome = Pemasukan::milikPengguna($userId)->sum('jumlah');
        $totalAllExpense = Pengeluaran::milikPengguna($userId)->sum('jumlah');
        $saldo = $totalAllIncome - $totalAllExpense;

        $budget = Auth::user()->anggaran_bulanan;
        $sisaBudget = max(0, $budget - $totalExpense);
        $isOverBudget = $totalExpense > $budget && $budget > 0;

        // Group expenses by category
        $expensesByCategory = Pengeluaran::milikPengguna($userId)
            ->whereMonth('tanggal', now()->month)
            ->selectRaw('kategori, SUM(jumlah) as total')
            ->groupBy('kategori')
            ->get();
        
        $chartData = $expensesByCategory->mapWithKeys(function ($item) {
            return [$item->kategori => $item->total];
        })->toArray();
        
        $chartKeys = array_keys($chartData);
        $chartValues = array_values($chartData);

        // Fetch Automations
        $automations = TransaksiOtomatis::where('pengguna_id', $userId)->get();

        return view('management.index', compact(
            'totalIncome', 
            'totalExpense', 
            'saldo',
            'budget',
            'sisaBudget',
            'isOverBudget',
            'chartKeys',
            'chartValues',
            'automations'
        ));
    }

    public function storeIncome(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:1',
            'tanggal' => 'required|date',
        ]);

        Pemasukan::create([
            'pengguna_id' => Auth::id(),
            'nama' => $request->nama,
            'jumlah' => $request->jumlah,
            'tanggal' => $request->tanggal,
        ]);

        return redirect()->route('management.index')->with('success', 'Pemasukan berhasil dicatat.');
    }

    public function storeExpense(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:1',
            'kategori' => 'required|in:Kebutuhan Pokok,Mendesak,Kebutuhan Lain',
            'tanggal' => 'required|date',
        ]);

        Pengeluaran::create([
            'pengguna_id' => Auth::id(),
            'nama' => $request->nama,
            'jumlah' => $request->jumlah,
            'kategori' => $request->kategori,
            'tanggal' => $request->tanggal,
        ]);

        return redirect()->route('management.index')->with('success', 'Pengeluaran berhasil dicatat.');
    }

    public function setBudget(Request $request)
    {
        $request->validate([
            'anggaran_bulanan' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();
        $user->anggaran_bulanan = $request->anggaran_bulanan;
        $user->save();

        return redirect()->route('management.index')->with('success', 'Anggaran bulanan berhasil diperbarui.');
    }

    public function storeAutomation(Request $request)
    {
        $request->validate([
            'tipe' => 'required|in:pemasukan,pengeluaran',
            'nama' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:1',
            'tanggal_rutin' => 'required|integer|min:1|max:31',
            'kategori' => 'nullable|string|max:255',
        ]);

        TransaksiOtomatis::create([
            'pengguna_id' => Auth::id(),
            'tipe' => $request->tipe,
            'nama' => $request->nama,
            'jumlah' => $request->jumlah,
            'tanggal_rutin' => $request->tanggal_rutin,
            'kategori' => $request->kategori,
        ]);

        return redirect()->route('management.index')->with('success', 'Automasi transaksi berhasil ditambahkan.');
    }

    public function destroyAutomation($id)
    {
        $automation = TransaksiOtomatis::where('pengguna_id', Auth::id())->findOrFail($id);
        $automation->delete();

        return redirect()->route('management.index')->with('success', 'Automasi dihapus.');
    }
}
