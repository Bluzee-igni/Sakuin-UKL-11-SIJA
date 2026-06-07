<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use App\Models\TransaksiOtomatis;
use App\Services\FinancialService;

class ManagementController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $totalIncome = Pemasukan::milikPengguna($userId)->whereMonth('tanggal', now()->month)->sum('jumlah');
        $totalExpense = Pengeluaran::milikPengguna($userId)->whereMonth('tanggal', now()->month)->sum('jumlah');
        
        // Menggunakan FinancialService sebagai Single Source of Truth
        $metrics = FinancialService::getMetrics($userId);
        $saldoTersedia = $metrics['saldo_tersedia'];
        $totalTabungan = $metrics['total_tabungan'];
        $totalAset = $metrics['total_aset'];

        $budget = Auth::user()->anggaran_bulanan;
        $budgetPct = $budget > 0 ? min(100, round(($totalExpense / $budget) * 100)) : 0;
        $sisaBudget = max(0, $budget - $totalExpense);
        $isOverBudget = $totalExpense > $budget && $budget > 0;
        $budgetStatus = $budget > 0 ? ($budgetPct >= 80 ? 'bahaya' : ($budgetPct >= 50 ? 'waspada' : 'aman')) : 'belum';

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

        // Insights
        $daysPassed = now()->day;
        $rataHarian = $daysPassed > 0 ? round($totalExpense / $daysPassed) : 0;

        $largestCategory = $expensesByCategory->sortByDesc('total')->first();
        $kategoriTerbesar = $largestCategory ? $largestCategory->kategori : null;
        $nominalTerbesar = $largestCategory ? $largestCategory->total : 0;

        $daysInMonth = now()->daysInMonth;
        $prediksiTotalPengeluaran = $rataHarian * $daysInMonth;
        $prediksiSisaBudget = $budget - $prediksiTotalPengeluaran;

        // Fetch Automations
        $automations = TransaksiOtomatis::where('pengguna_id', $userId)->get();

        return view('management.index', compact(
            'totalIncome',
            'totalExpense',
            'saldoTersedia',
            'totalTabungan',
            'totalAset',
            'budget',
            'budgetPct',
            'sisaBudget',
            'isOverBudget',
            'budgetStatus',
            'chartKeys',
            'chartValues',
            'rataHarian',
            'kategoriTerbesar',
            'nominalTerbesar',
            'prediksiSisaBudget',
            'automations'
        ));
    }

    public function storeIncome(Request $request)
    {
        $request->merge([
            'jumlah' => $request->jumlah ? convert_to_idr($request->jumlah) : null,
        ]);

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
        $request->merge([
            'jumlah' => $request->jumlah ? convert_to_idr($request->jumlah) : null,
        ]);

        $request->validate([
            'nama' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:1',
            'kategori' => 'required|in:Kebutuhan Pokok,Mendesak,Kebutuhan Lain,Cicilan,Hiburan',
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
        $request->merge([
            'anggaran_bulanan' => $request->anggaran_bulanan ? convert_to_idr($request->anggaran_bulanan) : 0,
        ]);

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
        $request->merge([
            'jumlah' => $request->jumlah ? convert_to_idr($request->jumlah) : null,
        ]);

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
