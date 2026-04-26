<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Income;
use App\Models\Expense;

class ManagementController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $totalIncome = Income::where('user_id', $userId)->whereMonth('tanggal', now()->month)->sum('nominal');
        $totalExpense = Expense::where('user_id', $userId)->whereMonth('tanggal', now()->month)->sum('nominal');
        
        $totalAllIncome = Income::where('user_id', $userId)->sum('nominal');
        $totalAllExpense = Expense::where('user_id', $userId)->sum('nominal');
        $saldo = $totalAllIncome - $totalAllExpense;

        $budget = Auth::user()->budget_bulanan;
        $sisaBudget = max(0, $budget - $totalExpense);
        $isOverBudget = $totalExpense > $budget && $budget > 0;

        // Group expenses by category
        $expensesByCategory = Expense::where('user_id', $userId)
            ->whereMonth('tanggal', now()->month)
            ->selectRaw('kategori, SUM(nominal) as total')
            ->groupBy('kategori')
            ->get();
        
        $chartData = $expensesByCategory->mapWithKeys(function ($item) {
            return [$item->kategori => $item->total];
        })->toArray();
        
        $chartKeys = array_keys($chartData);
        $chartValues = array_values($chartData);

        // Fetch Automations
        $automations = \App\Models\AutomatedTransaction::where('user_id', $userId)->get();

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
            'nominal' => 'required|numeric|min:1',
            'tanggal' => 'required|date',
        ]);

        Income::create([
            'user_id' => Auth::id(),
            'nama' => $request->nama,
            'nominal' => $request->nominal,
            'tanggal' => $request->tanggal,
        ]);

        return redirect()->route('management.index')->with('success', 'Pemasukan berhasil dicatat.');
    }

    public function storeExpense(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:1',
            'kategori' => 'required|in:Kebutuhan Pokok,Mendesak,Kebutuhan Lain',
            'tanggal' => 'required|date',
        ]);

        Expense::create([
            'user_id' => Auth::id(),
            'nama' => $request->nama,
            'nominal' => $request->nominal,
            'kategori' => $request->kategori,
            'tanggal' => $request->tanggal,
        ]);

        return redirect()->route('management.index')->with('success', 'Pengeluaran berhasil dicatat.');
    }

    public function setBudget(Request $request)
    {
        $request->validate([
            'budget_bulanan' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();
        $user->budget_bulanan = $request->budget_bulanan;
        $user->save();

        return redirect()->route('management.index')->with('success', 'Budget bulanan berhasil diperbarui.');
    }

    public function storeAutomation(Request $request)
    {
        $request->validate([
            'tipe' => 'required|in:income,expense',
            'nama' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:1',
            'tanggal_rutin' => 'required|integer|min:1|max:31',
            'kategori' => 'nullable|string|max:255',
        ]);

        \App\Models\AutomatedTransaction::create([
            'user_id' => Auth::id(),
            'tipe' => $request->tipe,
            'nama' => $request->nama,
            'nominal' => $request->nominal,
            'tanggal_rutin' => $request->tanggal_rutin,
            'kategori' => $request->kategori,
        ]);

        return redirect()->route('management.index')->with('success', 'Automasi transaksi berhasil ditambahkan.');
    }

    public function destroyAutomation($id)
    {
        $automation = \App\Models\AutomatedTransaction::where('user_id', Auth::id())->findOrFail($id);
        $automation->delete();

        return redirect()->route('management.index')->with('success', 'Automasi dihapus.');
    }
}
