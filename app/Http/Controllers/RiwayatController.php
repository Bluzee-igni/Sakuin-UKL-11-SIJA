<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TransaksiTabungan;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $filter = $request->query('filter', 'semua'); // semua, masuk, keluar, nabung

        // 1. Fetch Pemasukan
        $incomes = collect();
        if (in_array($filter, ['semua', 'masuk'])) {
            $incomes = Pemasukan::where('pengguna_id', $user->id)
                ->get()
                ->map(function ($item) {
                    return (object) [
                        'id' => 'inc_' . $item->id,
                        'kategori' => 'Pemasukan',
                        'judul' => $item->nama,
                        'jumlah' => $item->jumlah,
                        'tanggal' => $item->tanggal,
                        'tipe_badge' => 'success',
                        'ikon' => 'ph-arrow-circle-down',
                        'raw_date' => $item->tanggal
                    ];
                });
        }

        // 2. Fetch Pengeluaran
        $expenses = collect();
        if (in_array($filter, ['semua', 'keluar'])) {
            $expenses = Pengeluaran::where('pengguna_id', $user->id)
                ->get()
                ->map(function ($item) {
                    return (object) [
                        'id' => 'exp_' . $item->id,
                        'kategori' => 'Pengeluaran',
                        'judul' => $item->nama,
                        'jumlah' => $item->jumlah,
                        'tanggal' => $item->tanggal,
                        'tipe_badge' => 'danger',
                        'ikon' => 'ph-arrow-circle-up',
                        'raw_date' => $item->tanggal
                    ];
                });
        }

        // 3. Fetch Tabungan
        $savings = collect();
        if (in_array($filter, ['semua', 'nabung'])) {
            $savings = TransaksiTabungan::with('targetTabungan')
                ->whereHas('targetTabungan', function ($q) use ($user) {
                    $q->where('pengguna_id', $user->id);
                })
                ->get()
                ->map(function ($item) {
                    $isSetor = $item->tipe === 'setor' || $item->tipe === 'setoran';
                    return (object) [
                        'id' => 'sav_' . $item->id,
                        'kategori' => $isSetor ? 'Setoran Tabungan' : 'Penarikan Tabungan',
                        'judul' => $item->targetTabungan->nama ?? 'Tabungan',
                        'jumlah' => $item->jumlah,
                        'tanggal' => $item->tanggal_transaksi,
                        'tipe_badge' => $isSetor ? 'success' : 'warning',
                        'ikon' => 'ph-piggy-bank',
                        'raw_date' => $item->tanggal_transaksi
                    ];
                });
        }

        // Combine all and sort by date desc
        $allTransactions = $incomes->concat($expenses)->concat($savings)
            ->sortByDesc('raw_date')
            ->values();

        // Manual Pagination
        $perPage = 15;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentPageItems = $allTransactions->slice(($currentPage - 1) * $perPage, $perPage)->all();
        
        $transactions = new LengthAwarePaginator(
            $currentPageItems,
            $allTransactions->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        return view('riwayat.index', compact('transactions', 'filter'));
    }
}
