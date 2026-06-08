<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use App\Models\Pengguna;
use App\Models\TargetTabungan;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    use ApiResponse;

    public function ringkasan(Request $request)
    {
        $penggunaId = Auth::id();
        /** @var Pengguna $user */
        $user = Auth::user();
        
        $totalPemasukan = Pemasukan::milikPengguna($penggunaId)->sum('jumlah') ?? 0;
        $totalPengeluaran = Pengeluaran::milikPengguna($penggunaId)->sum('jumlah') ?? 0;
        
        $targetAktif = TargetTabungan::milikPengguna($penggunaId)
            ->where('status', 'aktif')
            ->with(['transaksiTabungan'])
            ->get();
            
        $totalTerkumpul = $targetAktif->map(fn($t) => $t->total_terkumpul)->sum();
        
        $saldo = $totalPemasukan - $totalPengeluaran - $totalTerkumpul;

        $pengeluaranBulanIni = Pengeluaran::milikPengguna($penggunaId)
            ->whereMonth('tanggal', now()->month)
            ->sum('jumlah') ?? 0;

        $data = [
            'saldo' => (float) $saldo,
            'total_pemasukan' => (float) $totalPemasukan,
            'total_pengeluaran' => (float) $totalPengeluaran,
            'total_ditabung' => (float) $totalTerkumpul,
            'pengeluaran_bulan_ini' => (float) $pengeluaranBulanIni,
            'anggaran_bulanan' => (float) $user->anggaran_bulanan,
            'sisa_anggaran' => (float) max(0, $user->anggaran_bulanan - $pengeluaranBulanIni),
            'jumlah_target_aktif' => $targetAktif->count(),
        ];

        return $this->successResponse($data, 'Ringkasan dashboard berhasil diambil');
    }
}
