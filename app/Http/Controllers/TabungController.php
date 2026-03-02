<?php

namespace App\Http\Controllers;

use App\Models\Target;
use App\Models\Checkin;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TabungController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Ambil semua target user
        $targets = $user->targets()->get();

        // Ambil target aktif
        $activeTarget = $user->targets()->where('is_active', true)->first();

        if (!$activeTarget) {
            $activeTarget = $targets->first();
        }

        $checkins = collect();
        $total = 0;
        $streak = 0;
        $estimasiTanggal = null;

        if ($activeTarget) {

            // Ambil checkin bulan ini
            $checkins = $activeTarget->checkins()
                ->whereMonth('tanggal', now()->month)
                ->whereYear('tanggal', now()->year)
                ->get();

            // Total terkumpul
            $total = $activeTarget->checkins()->sum('nominal');

            // Hitung streak
            $dates = $activeTarget->checkins()
                ->orderBy('tanggal', 'desc')
                ->pluck('tanggal')
                ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
                ->toArray();

            $today = now()->format('Y-m-d');
            $current = $today;

            foreach ($dates as $date) {
                if ($date === $current) {
                    $streak++;
                    $current = Carbon::parse($current)->subDay()->format('Y-m-d');
                } else {
                    break;
                }
            }

            // Estimasi selesai
            $sisa = $activeTarget->harga_target - $total;

            if ($total > 0) {
                $rataRata = $activeTarget->checkins()
                    ->where('tanggal', '>=', now()->subDays(14))
                    ->avg('nominal');

                if ($rataRata > 0) {
                    $hariButuh = ceil($sisa / $rataRata);
                    $estimasiTanggal = now()->addDays($hariButuh);
                }
            }
        }

        return view('tabung.index', compact(
            'targets',
            'activeTarget',
            'checkins',
            'total',
            'streak',
            'estimasiTanggal'
        ));
    }
}