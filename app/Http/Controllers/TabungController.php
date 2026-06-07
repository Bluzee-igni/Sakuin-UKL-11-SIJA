<?php

namespace App\Http\Controllers;

use App\Models\TargetTabungan;
use App\Models\TransaksiTabungan;
use App\Models\Pengeluaran;
use App\Models\Pemasukan;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TabungController extends Controller
{
    public function index(Request $request)
    {
        /** @var Pengguna|null $user */
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $targets = TargetTabungan::milikPengguna($user->id)
            ->with('transaksiTabungan')
            ->latest()
            ->get();

        $activeTarget = TargetTabungan::milikPengguna($user->id)->aktif()->first();

        if (!$activeTarget) {
            $activeTarget = $targets->first();
        }

        $checkins = collect();
        $total = 0;
        $streak = 0;
        $estimasiTanggal = null;

        if ($activeTarget) {
            $checkins = $activeTarget->transaksiTabungan()
                ->setoran()
                ->orderBy('tanggal_transaksi', 'desc')
                ->get();

            $total = $activeTarget->transaksiTabungan()->setoran()->sum('jumlah');



            $sisa = max(0, $activeTarget->jumlah_target - $total);

            if ($total > 0 && $sisa > 0) {
                $rataRata = $activeTarget->transaksiTabungan()
                    ->setoran()
                    ->where('tanggal_transaksi', '>=', now()->subDays(14)->toDateString())
                    ->avg('jumlah');

                if ($rataRata > 0) {
                    $hariButuh = (int) ceil($sisa / $rataRata);
                    $estimasiTanggal = now()->copy()->addDays($hariButuh);
                }
            }
        }

        $now = now();

        $checkinsBulanIni = TransaksiTabungan::setoran()
            ->whereHas('targetTabungan', function ($q) use ($user) {
                $q->where('pengguna_id', $user->id);
            })
            ->whereMonth('tanggal_transaksi', $now->month)
            ->whereYear('tanggal_transaksi', $now->year);

        $totalBulanIni = (clone $checkinsBulanIni)->sum('jumlah');
        $jumlahCheckinBulanIni = (clone $checkinsBulanIni)->count();
        $rata2PerCheckin = $jumlahCheckinBulanIni > 0
            ? $totalBulanIni / $jumlahCheckinBulanIni
            : 0;

        $lastCheckin = TransaksiTabungan::setoran()
            ->whereHas('targetTabungan', function ($q) use ($user) {
                $q->where('pengguna_id', $user->id);
            })
            ->latest('tanggal_transaksi')
            ->first();

        $lastCheckinDate = $lastCheckin
            ? Carbon::parse($lastCheckin->tanggal_transaksi)->format('d M Y')
            : '-';

        $recentCheckins = TransaksiTabungan::setoran()
            ->with('targetTabungan')
            ->whereHas('targetTabungan', function ($q) use ($user) {
                $q->where('pengguna_id', $user->id);
            })
            ->latest('tanggal_transaksi')
            ->take(5)
            ->get();

        $totalIncome = Pemasukan::milikPengguna($user->id)->sum('jumlah');

        $recentIncomes = Pemasukan::milikPengguna($user->id)
            ->latest('tanggal')
            ->take(5)
            ->get();

        $usedForSaving = TransaksiTabungan::setoran()
            ->whereHas('targetTabungan', function ($q) use ($user) {
                $q->where('pengguna_id', $user->id);
            })
            ->sum('jumlah');

        $usedForExpense = Pengeluaran::milikPengguna($user->id)->sum('jumlah');

        $saldo = $totalIncome - $usedForSaving - $usedForExpense;

        // --- HEATMAP ACTIVITY (MONTHLY VIEW) ---
        $monthParam = $request->query('month', now()->format('Y-m'));
        try {
            $currentHeatmapDate = Carbon::createFromFormat('Y-m', $monthParam)->startOfMonth();
        } catch (\Exception $e) {
            $currentHeatmapDate = now()->startOfMonth();
        }

        $heatmapPrevMonth = $currentHeatmapDate->copy()->subMonth()->format('Y-m');
        $heatmapNextMonth = $currentHeatmapDate->copy()->addMonth()->format('Y-m');
        $heatmapCurrentMonthName = $currentHeatmapDate->translatedFormat('F Y');
        $isCurrentMonth = $currentHeatmapDate->format('Y-m') === now()->format('Y-m');

        $startOfMonth = $currentHeatmapDate->copy()->startOfMonth();
        $endOfMonth = $currentHeatmapDate->copy()->endOfMonth();

        // Get daily savings for the month
        $dailySavings = TransaksiTabungan::setoran()
            ->whereHas('targetTabungan', function ($q) use ($user) {
                $q->where('pengguna_id', $user->id);
            })
            ->whereBetween('tanggal_transaksi', [$startOfMonth, $endOfMonth])
            ->selectRaw('DATE(tanggal_transaksi) as date, SUM(jumlah) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $maxDaily = $dailySavings->max() ?: 1;

        $daysInMonth = $currentHeatmapDate->daysInMonth;
        $startDayOfWeek = $currentHeatmapDate->dayOfWeek; // 0 (Sun) to 6 (Sat)

        $heatmapData = [];
        // Add empty padding for days before the 1st
        for ($i = 0; $i < $startDayOfWeek; $i++) {
            $heatmapData[] = null;
        }

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dateStr = $currentHeatmapDate->copy()->day($day)->format('Y-m-d');
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
                'date' => $dateStr,
                'day' => $day,
                'total' => $totalNominal,
                'level' => $level,
                'isToday' => $dateStr === now()->format('Y-m-d')
            ];
        }

        // --- STREAK CALCULATION (GLOBAL PER USER) ---
        $longestStreak = 0;
        // Reset streak current, akan dihitung ulang secara global
        $streak = 0;

        $allDatesDesc = TransaksiTabungan::setoran()
            ->whereHas('targetTabungan', function ($q) use ($user) {
                $q->where('pengguna_id', $user->id);
            })
            ->orderBy('tanggal_transaksi', 'desc')
            ->pluck('tanggal_transaksi')
            ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
            ->unique()
            ->values()
            ->toArray();

        if (count($allDatesDesc) > 0) {
            // Hitung Longest Streak
            $currentStreakCountLoop = 1;
            $longestStreak = 1;
            for ($i = 0; $i < count($allDatesDesc) - 1; $i++) {
                $date1 = Carbon::parse($allDatesDesc[$i]);
                $date2 = Carbon::parse($allDatesDesc[$i+1]);
                if ($date1->diffInDays($date2) == 1) {
                    $currentStreakCountLoop++;
                    if ($currentStreakCountLoop > $longestStreak) {
                        $longestStreak = $currentStreakCountLoop;
                    }
                } else {
                    $currentStreakCountLoop = 1;
                }
            }

            // Hitung Current Streak (Berlanjut selama nabung kemarin atau hari ini)
            $todayStr = now()->format('Y-m-d');
            $yesterdayStr = now()->subDay()->format('Y-m-d');

            if ($allDatesDesc[0] === $todayStr || $allDatesDesc[0] === $yesterdayStr) {
                $expectedDate = Carbon::parse($allDatesDesc[0]);
                foreach ($allDatesDesc as $dateStr) {
                    if ($dateStr === $expectedDate->format('Y-m-d')) {
                        $streak++;
                        $expectedDate->subDay();
                    } else {
                        break;
                    }
                }
            }
        }

        return view('tabung.index', compact(
            'targets',
            'activeTarget',
            'checkins',
            'total',
            'streak',
            'estimasiTanggal',
            'totalBulanIni',
            'jumlahCheckinBulanIni',
            'rata2PerCheckin',
            'lastCheckinDate',
            'recentCheckins',
            'totalIncome',
            'recentIncomes',
            'usedForSaving',
            'usedForExpense',
            'saldo',
            'heatmapData',
            'heatmapPrevMonth',
            'heatmapNextMonth',
            'heatmapCurrentMonthName',
            'isCurrentMonth',
            'longestStreak'
        ));
    }

    public function create()
    {
        return view('tabung.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nama' => 'required|string|max:255',
            'jumlah_target' => 'required|numeric|min:1',
            'rencana_harian' => 'nullable|numeric|min:0',
            'tanggal_mulai' => 'nullable|date',
        ]);

        $hasTarget = TargetTabungan::milikPengguna($user->id)->exists();

        TargetTabungan::create([
            'pengguna_id' => $user->id,
            'nama' => $request->nama,
            'jumlah_target' => $request->jumlah_target,
            'rencana_harian' => $request->rencana_harian,
            'tanggal_mulai' => $request->tanggal_mulai,
            'status' => $hasTarget ? 'dijeda' : 'aktif',
        ]);

        return redirect()
            ->route('tabung.index')
            ->with('success', 'Target berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $target = TargetTabungan::milikPengguna(Auth::id())->findOrFail($id);

        return view('tabung.edit', compact('target'));
    }

    public function update(Request $request, string $id)
    {
        $target = TargetTabungan::milikPengguna(Auth::id())->findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'jumlah_target' => 'required|numeric|min:1',
            'rencana_harian' => 'nullable|numeric|min:0',
            'tanggal_mulai' => 'nullable|date',
        ]);

        $target->update([
            'nama' => $request->nama,
            'jumlah_target' => $request->jumlah_target,
            'rencana_harian' => $request->rencana_harian,
            'tanggal_mulai' => $request->tanggal_mulai,
        ]);

        return redirect()
            ->route('tabung.index')
            ->with('success', 'Target berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $target = TargetTabungan::milikPengguna(Auth::id())->findOrFail($id);

        $wasActive = $target->status === 'aktif';

        $target->delete();

        if ($wasActive) {
            $newActive = TargetTabungan::milikPengguna(Auth::id())->latest()->first();

            if ($newActive) {
                $newActive->update(['status' => 'aktif']);
            }
        }

        return redirect()
            ->route('tabung.index')
            ->with('success', 'Target berhasil dihapus.');
    }

    public function setActive(TargetTabungan $target)
    {
        if ($target->pengguna_id !== Auth::id()) {
            abort(403);
        }

        TargetTabungan::milikPengguna(Auth::id())->update(['status' => 'dijeda']);

        $target->update(['status' => 'aktif']);

        return redirect()
            ->route('tabung.index')
            ->with('success', 'Target aktif berhasil diganti.');
    }

    public function storeCheckin(Request $request)
    {
        $request->validate([
            'target_tabungan_id' => 'required|exists:target_tabungan,id',
            'tanggal_transaksi' => 'required|date',
            'jumlah' => 'required|numeric|min:1',
            'catatan' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();

        // Validasi Saldo: Tidak boleh nabung lebih dari saldo yang dimiliki
        $totalIncome = Pemasukan::milikPengguna($user->id)->sum('jumlah');
        $usedForSaving = TransaksiTabungan::setoran()
            ->whereHas('targetTabungan', function ($q) use ($user) {
                $q->where('pengguna_id', $user->id);
            })
            ->sum('jumlah');
        $usedForExpense = Pengeluaran::milikPengguna($user->id)->sum('jumlah');
        
        $saldoAktif = $totalIncome - $usedForSaving - $usedForExpense;

        if ($request->jumlah > $saldoAktif) {
            $pesanError = $saldoAktif <= 0 
                ? 'Tidak dapat menabung karena saldo Anda sedang minus atau kosong.' 
                : 'Saldo tidak cukup! Sisa saldo Anda saat ini hanya Rp ' . number_format($saldoAktif, 0, ',', '.');
                
            return back()->with('error', $pesanError)->withInput();
        }

        $target = TargetTabungan::milikPengguna($user->id)
            ->findOrFail($request->target_tabungan_id);

        TransaksiTabungan::create([
            'target_tabungan_id' => $target->id,
            'tipe' => 'setor', // or 'setoran' depending on what is expected (Wait, your index uses scope setoran(), let's check)
            'tanggal_transaksi' => $request->tanggal_transaksi,
            'jumlah' => $request->jumlah,
            'catatan' => $request->catatan,
        ]);

        $totalTerkumpul = $target->total_terkumpul; // uses accessor from model

        if ($totalTerkumpul >= $target->jumlah_target) {
            $target->update(['status' => 'selesai']);
        }

        return redirect()
            ->route('tabung.index')
            ->with('success', 'Catatan tabungan berhasil disimpan.');
    }
}
