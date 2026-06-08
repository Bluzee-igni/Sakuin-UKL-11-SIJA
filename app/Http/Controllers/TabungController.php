<?php

namespace App\Http\Controllers;

use App\Models\TargetTabungan;
use App\Models\TransaksiTabungan;
use App\Models\Pemasukan;
use App\Models\Pengguna;
use App\Services\FinancialService;
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

        $activeTarget = TargetTabungan::milikPengguna($user->id)
            ->where('status', 'aktif')
            ->first();

        if (!$activeTarget) {
            $activeTarget = $targets->first();
        }

        $checkins = collect();
        $total = 0;
        $streak = 0;
        $estimasiTanggal = null;

        if ($activeTarget) {
            $checkins = $activeTarget->transaksiTabungan()
                ->where('tipe', 'setor')
                ->orderBy('tanggal_transaksi', 'desc')
                ->get();

            $total = $activeTarget->transaksiTabungan()->where('tipe', 'setor')->sum('jumlah');



            $sisa = max(0, $activeTarget->jumlah_target - $total);

            if ($total > 0 && $sisa > 0) {
                $rataRata = $activeTarget->transaksiTabungan()
                    ->where('tipe', 'setor')
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

        $totalIncome = FinancialService::getTotalPemasukan($user->id);

        $recentIncomes = Pemasukan::milikPengguna($user->id)
            ->latest('tanggal')
            ->take(5)
            ->get();

        $usedForSaving = FinancialService::getTotalTabungan($user->id);
        $usedForExpense = FinancialService::getTotalPengeluaran($user->id);

        $totalAset = FinancialService::getTotalAset($user->id);
        $saldoTersedia = FinancialService::getSaldoTersedia($user->id);

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
        
        $hasSavedToday = count($allDatesDesc) > 0 && $allDatesDesc[0] === now()->format('Y-m-d');

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
            'totalAset',
            'saldoTersedia',
            'heatmapData',
            'heatmapPrevMonth',
            'heatmapNextMonth',
            'heatmapCurrentMonthName',
            'isCurrentMonth',
            'longestStreak',
            'hasSavedToday'
        ));
    }

    public function create()
    {
        return view('tabung.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->merge([
            'jumlah_target' => $request->jumlah_target ? convert_to_idr($request->jumlah_target) : null,
            'rencana_harian' => $request->rencana_harian ? convert_to_idr($request->rencana_harian) : null,
        ]);

        $request->validate([
            'nama' => 'required|string|max:255',
            'jumlah_target' => 'required|numeric|min:1',
            'rencana_harian' => 'nullable|numeric|min:0',
            'tanggal_mulai' => 'nullable|date',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('targets', 'public');
        }

        $hasTarget = TargetTabungan::milikPengguna($user->id)->exists();

        TargetTabungan::create([
            'pengguna_id' => $user->id,
            'nama' => $request->nama,
            'jumlah_target' => $request->jumlah_target,
            'rencana_harian' => $request->rencana_harian,
            'tanggal_mulai' => $request->tanggal_mulai,
            'status' => $hasTarget ? 'dijeda' : 'aktif',
            'gambar' => $gambarPath,
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

        $request->merge([
            'jumlah_target' => $request->jumlah_target ? convert_to_idr($request->jumlah_target) : null,
            'rencana_harian' => $request->rencana_harian ? convert_to_idr($request->rencana_harian) : null,
        ]);

        $request->validate([
            'nama' => 'required|string|max:255',
            'jumlah_target' => 'required|numeric|min:1',
            'rencana_harian' => 'nullable|numeric|min:0',
            'tanggal_mulai' => 'nullable|date',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $updateData = [
            'nama' => $request->nama,
            'jumlah_target' => $request->jumlah_target,
            'rencana_harian' => $request->rencana_harian,
            'tanggal_mulai' => $request->tanggal_mulai,
        ];

        if ($request->hasFile('gambar')) {
            if ($target->gambar) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($target->gambar);
            }
            $updateData['gambar'] = $request->file('gambar')->store('targets', 'public');
        }

        $target->update($updateData);

        return redirect()
            ->route('tabung.index')
            ->with('success', 'Target berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $target = TargetTabungan::milikPengguna(Auth::id())->findOrFail($id);

        $wasActive = $target->status === 'aktif';
        
        if ($target->gambar) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($target->gambar);
        }

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
        $request->merge([
            'jumlah' => $request->jumlah ? convert_to_idr($request->jumlah) : null,
        ]);

        $request->validate([
            'target_tabungan_id' => 'required|exists:target_tabungan,id',
            'tanggal_transaksi' => 'required|date',
            'jumlah' => 'required|numeric|min:1',
            'catatan' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();

        // Validasi Saldo: Tidak boleh nabung lebih dari saldo yang dimiliki
        $saldoAktif = FinancialService::getSaldoTersedia($user->id);

        if ($request->jumlah > $saldoAktif) {
            $pesanError = $saldoAktif <= 0 
                ? 'Tidak dapat menabung karena saldo tersedia sedang kosong.' 
                : 'Saldo tidak cukup! Sisa saldo tersedia Anda saat ini hanya ' . format_currency($saldoAktif);
                
            return back()->with('error', $pesanError)->withInput();
        }

        $target = TargetTabungan::milikPengguna($user->id)
            ->findOrFail($request->target_tabungan_id);

        TransaksiTabungan::create([
            'target_tabungan_id' => $target->id,
            'tipe' => 'setor',
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
