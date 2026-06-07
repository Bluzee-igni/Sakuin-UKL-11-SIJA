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
use App\Services\FinancialService;
use Carbon\Carbon;

class ProfilController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $tanggalBergabung = Carbon::parse($user->created_at);
        $hariBergabung = $tanggalBergabung->diffInDays(now()) + 1;

        $saldoSaatIni = FinancialService::getSaldoTersedia($user->id);
        $totalMenabung = FinancialService::getTotalTabungan($user->id);

        $targetAktif = $user->targetTabungan()->aktif()->count();
        $targetTercapai = $user->targetTabungan()->selesai()->count();

        $targetTerdekat = $user->targetTabungan()->aktif()->with('transaksiTabungan')->get()
            ->sortByDesc('persentase_progres')
            ->first();

        $totalTransaksi = TransaksiTabungan::whereHas('targetTabungan', function ($q) use ($user) {
            $q->where('pengguna_id', $user->id);
        })->count();

        // ==========================================
        // GITHUB-STYLE HEATMAP LOGIC (365 DAYS)
        // ==========================================
        $endDate = now()->startOfDay();
        $startDate = now()->subDays(364)->startOfDay();

        $dailySavings = TransaksiTabungan::setoran()
            ->whereHas('targetTabungan', function ($q) use ($user) {
                $q->where('pengguna_id', $user->id);
            })
            ->whereBetween('tanggal_transaksi', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->selectRaw('DATE(tanggal_transaksi) as date, SUM(jumlah) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $maxDaily = $dailySavings->max() ?: 1;

        $heatmapData = [];
        $currentDate = clone $startDate;

        $startDayOfWeek = $startDate->dayOfWeek;
        for ($i = 0; $i < $startDayOfWeek; $i++) {
            $heatmapData[] = ['is_padding' => true];
        }

        $hariAktif = 0;
        $bestStreak = 0;
        $tempStreak = 0;

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
                'monthName' => $currentDate->translatedFormat('M'),
                'isFirstOfMonth' => $currentDate->day === 1,
            ];

            if ($totalNominal > 0) {
                $hariAktif++;
                $tempStreak++;
                if ($tempStreak > $bestStreak) $bestStreak = $tempStreak;
            } else {
                $tempStreak = 0;
            }

            $currentDate->addDay();
        }

        $streakSaatIni = 0;
        for ($i = count($heatmapData) - 1; $i >= 0; $i--) {
            if (!$heatmapData[$i]['is_padding'] && $heatmapData[$i]['total'] > 0) {
                $streakSaatIni++;
            } else {
                break;
            }
        }

        return view('profil.index', compact(
            'user', 'tanggalBergabung', 'hariBergabung',
            'saldoSaatIni', 'totalMenabung',
            'targetAktif', 'targetTercapai',
            'targetTerdekat', 'totalTransaksi',
            'hariAktif', 'streakSaatIni', 'bestStreak',
            'heatmapData'
        ));
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
