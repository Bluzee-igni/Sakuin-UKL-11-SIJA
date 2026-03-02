<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Target;
use App\Models\Checkin;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // ===== Ambil semua target user =====
        $targets = Target::with('checkins')
            ->where('user_id', $userId)
            ->get();

        // ===== Target aktif (misal yang paling terakhir dibuat) =====
        $activeTarget = Target::where('user_id', $userId)
            ->latest()
            ->first();

        // ===== Query bulan ini (buat panel ringkasan) =====
        $bulanIniQuery = Checkin::where('user_id', $userId)
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year);

        // ===== Checkin bulan ini (kalau masih mau dipakai di tempat lain) =====
        $checkins = (clone $bulanIniQuery)->get();

        // ===== Hitung streak sederhana (punyamu masih 1/0) =====
        $streak = Checkin::where('user_id', $userId)
            ->whereDate('tanggal', today())
            ->exists() ? 1 : 0;

        // ===== Estimasi sederhana (dummy dulu) =====
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
        // Ambil semua check-in user (biar pindah bulan tetap muncul event)
        $allCheckins = Checkin::with('target')
            ->where('user_id', $userId)
            ->orderByDesc('tanggal')
            ->get();

        // Buat format events untuk FullCalendar
        $calendarEvents = $allCheckins->map(function ($c) {
            return [
                'title' => 'Nabung: Rp ' . number_format($c->nominal, 0, ',', '.'),
                'start' => Carbon::parse($c->tanggal)->format('Y-m-d'),
                'allDay' => true,
            ];
        })->values();

        return view('dashboard', compact(
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
            'calendarEvents'
        ));
    }
}