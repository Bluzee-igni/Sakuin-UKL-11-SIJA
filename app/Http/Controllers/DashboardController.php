<?php

namespace App\Http\Controllers;

use App\Models\Target;
use App\Models\Checkin;
use App\Models\Income;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // =========================
        // ===== TARGET ============
        // =========================
        $targets = Target::with('checkins')
            ->where('user_id', $userId)
            ->get();

        $activeTarget = Target::where('user_id', $userId)
            ->latest()
            ->first();

        // =========================
        // ===== CHECKIN ===========
        // =========================
        $bulanIniQuery = Checkin::where('user_id', $userId)
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year);

        $checkins = (clone $bulanIniQuery)->get();

        // Streak sederhana
        $streak = Checkin::where('user_id', $userId)
            ->whereDate('tanggal', today())
            ->exists() ? 1 : 0;

        $estimasiTanggal = null;

        // =========================
        // ===== PANEL BAWAH =======
        // =========================
        $recentCheckins = Checkin::with('target')
            ->where('user_id', $userId)
            ->latest('tanggal')
            ->take(7)
            ->get();

        $totalBulanIni = (clone $bulanIniQuery)->sum('nominal');
        $jumlahCheckinBulanIni = (clone $bulanIniQuery)->count();

        $rata2PerCheckin = $jumlahCheckinBulanIni > 0
            ? (int)($totalBulanIni / $jumlahCheckinBulanIni)
            : 0;

        $last = Checkin::where('user_id', $userId)
            ->latest('tanggal')
            ->first();

        $lastCheckinDate = $last
            ? Carbon::parse($last->tanggal)->format('d M Y')
            : null;

        // =========================
        // ===== FULLCALENDAR ======
        // =========================
        $allCheckins = Checkin::with('target')
            ->where('user_id', $userId)
            ->orderByDesc('tanggal')
            ->get();

        $calendarEvents = $allCheckins->map(function ($c) {
            return [
                'title' => 'Nabung: Rp ' . number_format($c->nominal, 0, ',', '.'),
                'start' => Carbon::parse($c->tanggal)->format('Y-m-d'),
                'allDay' => true,
            ];
        })->values();

        // =========================
        // ===== INCOME ============
        // =========================
        $totalIncome = Income::forUser($userId)
            ->sum('nominal');

        $recentIncomes = Income::forUser($userId)
            ->latest('tanggal')
            ->take(5)
            ->get();

        // =========================
        // ===== SALDO ============
        // =========================
        $usedForSaving = Checkin::where('user_id', $userId)
            ->sum('nominal');

        $usedForExpense = Expense::where('user_id', $userId)
            ->sum('nominal');

        $saldo = $totalIncome - $usedForSaving - $usedForExpense;

        // =========================
        // ===== RETURN VIEW =======
        // =========================
        return view('tabung.index', compact(
            'targets',
            'checkins',
            'activeTarget',
            'streak',
            'estimasiTanggal',
            'recentCheckins',
            'totalBulanIni',
            'jumlahCheckinBulanIni',
            'rata2PerCheckin',
            'lastCheckinDate',
            'calendarEvents',

            // income & saldo
            'totalIncome',
            'recentIncomes',
            'usedForSaving',
            'usedForExpense',
            'saldo'
        ));
    }
}
