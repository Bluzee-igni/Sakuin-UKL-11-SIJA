<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Pengguna;
use App\Models\TransaksiTabungan;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use Carbon\Carbon;

class ProfilController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Menghitung hari bergabung
        $tanggalBergabung = Carbon::parse($user->created_at);
        $hariBergabung = $tanggalBergabung->diffInDays(now()) + 1; // +1 karena hari H dihitung 1
        
        // Menghitung Saldo
        $totalIncome = Pemasukan::milikPengguna($user->id)->sum('jumlah');
        $usedForSaving = TransaksiTabungan::setoran()
            ->whereHas('targetTabungan', function ($q) use ($user) {
                $q->where('pengguna_id', $user->id);
            })
            ->sum('jumlah');
        $usedForExpense = Pengeluaran::milikPengguna($user->id)->sum('jumlah');
        $saldo = $totalIncome - $usedForSaving - $usedForExpense;
        
        // ==========================================
        // GITHUB-STYLE HEATMAP LOGIC (365 DAYS)
        // ==========================================
        // Kita butuh data tabungan selama 1 tahun ke belakang
        $endDate = now()->startOfDay();
        $startDate = now()->subDays(364)->startOfDay(); // 365 hari termasuk hari ini
        
        $dailySavings = TransaksiTabungan::setoran()
            ->whereHas('targetTabungan', function ($q) use ($user) {
                $q->where('pengguna_id', $user->id);
            })
            ->whereBetween('tanggal_transaksi', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->selectRaw('DATE(tanggal_transaksi) as date, SUM(jumlah) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $maxDaily = $dailySavings->max() ?: 1;

        // Bikin array kotak-kotak untuk kalender (7 baris, ~52 kolom)
        // Github menyusun kolom per minggu. Minggu (0) sampai Sabtu (6).
        // Kita isi dari tanggal mulai sampai tanggal selesai.
        $heatmapData = [];
        $currentDate = clone $startDate;
        
        // Padding agar hari pertama cocok dengan urutan minggu (Minggu=0, dst)
        $startDayOfWeek = $startDate->dayOfWeek; // 0 for Sunday
        for ($i = 0; $i < $startDayOfWeek; $i++) {
            $heatmapData[] = ['is_padding' => true];
        }

        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $totalNominal = $dailySavings->get($dateStr, 0);
            
            $level = 0;
            if ($totalNominal > 0) {
                $percentage = $totalNominal / $maxDaily;
                if ($percentage <= 0.25) $level = 1;
                elseif ($percentage <= 0.50) $level = 2;
                elseif ($percentage <= 0.75) $level = 3;
                else $level = 4;
            }

            $heatmapData[] = [
                'is_padding' => false,
                'date' => $dateStr,
                'total' => $totalNominal,
                'level' => $level,
                'monthName' => $currentDate->translatedFormat('M'), // Untuk label bulan di atas
                'isFirstOfMonth' => $currentDate->day === 1
            ];
            $currentDate->addDay();
        }

        return view('profil.index', compact('user', 'tanggalBergabung', 'hariBergabung', 'saldo', 'heatmapData'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:pengguna,email,' . $user->id,
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password_lama' => 'nullable|string',
            'password_baru' => 'nullable|string|min:6|confirmed',
        ]);

        $dataUpdate = [
            'nama' => $request->nama,
            'email' => $request->email,
        ];

        // Handle Avatar Upload
        if ($request->hasFile('avatar')) {
            // Hapus avatar lama jika ada
            if ($user->avatar && Storage::exists('public/' . $user->avatar)) {
                Storage::delete('public/' . $user->avatar);
            }
            // Simpan yang baru
            $path = $request->file('avatar')->store('avatars', 'public');
            $dataUpdate['avatar'] = $path;
        }

        // Handle ganti password
        if ($request->filled('password_lama') && $request->filled('password_baru')) {
            if (!Hash::check($request->password_lama, $user->kata_sandi)) {
                return back()->with('error', 'Password lama tidak sesuai.');
            }
            $dataUpdate['kata_sandi'] = Hash::make($request->password_baru);
        }

        Pengguna::where('id', $user->id)->update($dataUpdate);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}
