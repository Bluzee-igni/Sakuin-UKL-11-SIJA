<?php

namespace App\Http\Controllers;

use App\Models\Target;
use App\Models\Checkin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TabungController extends Controller
{
    public function index()
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $targets = $user->targets()
            ->with('checkins')
            ->latest()
            ->get();

        $activeTarget = $user->targets()->where('is_active', true)->first();

        if (!$activeTarget) {
            $activeTarget = $targets->first();
        }

        $checkins = collect();
        $total = 0;
        $streak = 0;
        $estimasiTanggal = null;
        $calendarEvents = [];

        if ($activeTarget) {
            $checkins = $activeTarget->checkins()
                ->orderBy('tanggal', 'desc')
                ->get();

            $total = $activeTarget->checkins()->sum('nominal');

            $dates = $activeTarget->checkins()
                ->orderBy('tanggal', 'desc')
                ->pluck('tanggal')
                ->map(fn ($d) => Carbon::parse($d)->format('Y-m-d'))
                ->unique()
                ->values()
                ->toArray();

            $today = now()->format('Y-m-d');
            $yesterday = now()->subDay()->format('Y-m-d');

            if (!empty($dates) && ($dates[0] === $today || $dates[0] === $yesterday)) {
                $current = $dates[0];

                foreach ($dates as $date) {
                    if ($date === $current) {
                        $streak++;
                        $current = Carbon::parse($current)->subDay()->format('Y-m-d');
                    } else {
                        break;
                    }
                }
            }

            $sisa = max(0, $activeTarget->harga_target - $total);

            if ($total > 0 && $sisa > 0) {
                $rataRata = $activeTarget->checkins()
                    ->where('tanggal', '>=', now()->subDays(14)->toDateString())
                    ->avg('nominal');

                if ($rataRata > 0) {
                    $hariButuh = (int) ceil($sisa / $rataRata);
                    $estimasiTanggal = now()->copy()->addDays($hariButuh);
                }
            }

            $calendarEvents = $activeTarget->checkins()
                ->get()
                ->map(function ($item) {
                    return [
                        'title' => 'Nabung Rp ' . number_format((float) $item->nominal, 0, ',', '.'),
                        'start' => Carbon::parse($item->tanggal)->format('Y-m-d'),
                    ];
                })
                ->values()
                ->toArray();
        }

        $now = now();

        $checkinsBulanIni = Checkin::whereHas('target', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->whereMonth('tanggal', $now->month)
            ->whereYear('tanggal', $now->year);

        $totalBulanIni = (clone $checkinsBulanIni)->sum('nominal');
        $jumlahCheckinBulanIni = (clone $checkinsBulanIni)->count();
        $rata2PerCheckin = $jumlahCheckinBulanIni > 0
            ? $totalBulanIni / $jumlahCheckinBulanIni
            : 0;

        $lastCheckin = Checkin::whereHas('target', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->latest('tanggal')
            ->first();

        $lastCheckinDate = $lastCheckin
            ? Carbon::parse($lastCheckin->tanggal)->format('d M Y')
            : '-';

        $recentCheckins = Checkin::with('target')
            ->whereHas('target', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->latest('tanggal')
            ->take(5)
            ->get();

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
            'calendarEvents'
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
            'harga_target' => 'required|numeric|min:1',
            'rencana_per_hari' => 'nullable|numeric|min:0',
            'mulai' => 'nullable|date',
        ]);

        $hasTarget = Target::where('user_id', $user->id)->exists();

        Target::create([
            'user_id' => $user->id,
            'nama' => $request->nama,
            'harga_target' => $request->harga_target,
            'rencana_per_hari' => $request->rencana_per_hari,
            'mulai' => $request->mulai,
            'is_active' => $hasTarget ? 0 : 1,
            'is_done' => 0,
        ]);

        return redirect()
            ->route('tabung.index')
            ->with('success', 'Target berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $target = Target::where('user_id', Auth::id())->findOrFail($id);

        return view('tabung.edit', compact('target'));
    }

    public function update(Request $request, string $id)
    {
        $target = Target::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'harga_target' => 'required|numeric|min:1',
            'rencana_per_hari' => 'nullable|numeric|min:0',
            'mulai' => 'nullable|date',
        ]);

        $target->update([
            'nama' => $request->nama,
            'harga_target' => $request->harga_target,
            'rencana_per_hari' => $request->rencana_per_hari,
            'mulai' => $request->mulai,
        ]);

        return redirect()
            ->route('tabung.index')
            ->with('success', 'Target berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $target = Target::where('user_id', Auth::id())->findOrFail($id);

        $wasActive = (bool) $target->is_active;

        $target->delete();

        if ($wasActive) {
            $newActive = Target::where('user_id', Auth::id())->latest()->first();

            if ($newActive) {
                $newActive->update(['is_active' => 1]);
            }
        }

        return redirect()
            ->route('tabung.index')
            ->with('success', 'Target berhasil dihapus.');
    }

    public function setActive(Target $target)
    {
        if ($target->user_id !== Auth::id()) {
            abort(403);
        }

        Target::where('user_id', Auth::id())->update(['is_active' => 0]);

        $target->update(['is_active' => 1]);

        return redirect()
            ->route('tabung.index')
            ->with('success', 'Target aktif berhasil diganti.');
    }

    public function storeCheckin(Request $request)
    {
        $request->validate([
            'target_id' => 'required|exists:targets,id',
            'tanggal' => 'required|date',
            'nominal' => 'required|numeric|min:1',
            'catatan' => 'nullable|string|max:255',
        ]);

        $target = Target::where('user_id', Auth::id())
            ->findOrFail($request->target_id);

        $existing = Checkin::where('target_id', $target->id)
            ->whereDate('tanggal', $request->tanggal)
            ->first();

        if ($existing) {
            return redirect()
                ->route('tabung.index')
                ->with('success', 'Check-in di tanggal itu sudah ada. Silakan pilih tanggal lain.');
        }

        Checkin::create([
            'target_id' => $target->id,
            'tanggal' => $request->tanggal,
            'nominal' => $request->nominal,
            'catatan' => $request->catatan,
        ]);

        $totalTerkumpul = $target->checkins()->sum('nominal');

        if ($totalTerkumpul >= $target->harga_target) {
            $target->update(['is_done' => 1]);
        }

        return redirect()
            ->route('tabung.index')
            ->with('success', 'Check-in berhasil disimpan.');
    }
}