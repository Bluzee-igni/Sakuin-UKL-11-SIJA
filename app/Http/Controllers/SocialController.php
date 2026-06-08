<?php

namespace App\Http\Controllers;

use App\Models\Friend;
use App\Models\FriendRequest;
use App\Models\Notifikasi;
use App\Models\Pengguna;
use App\Models\PostComment;
use App\Models\PostLike;
use App\Models\SavingShare;
use App\Models\TargetTabungan;
use App\Services\AchievementService;
use App\Services\FinancialService;
use App\Services\TargetPredictionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SocialController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $shares = SavingShare::with([
            'user',
            'target',
            'likes.user',
            'comments.user',
        ])
        ->latest()
        ->paginate(12);

        $userIds = $shares->pluck('user_id')->unique()->filter()->values();
        $streakCache = [];
        foreach ($userIds as $uid) {
            $streakCache[$uid] = FinancialService::getCurrentStreak($uid);
        }
        foreach ($shares as $share) {
            if ($share->user && isset($streakCache[$share->user_id])) {
                $share->user->cached_streak = $streakCache[$share->user_id];
            }

            if ($share->target) {
                $share->prediction = TargetPredictionService::calculatePrediction(
                    $share->target->jumlah_target,
                    $share->target->total_terkumpul,
                    $share->target->rencana_harian,
                    $share->target->id,
                    $share->user_id
                );
            } else {
                $share->prediction = null;
            }
        }

        $shareableTargets = TargetTabungan::milikPengguna($user->id)
            ->whereHas('transaksiTabungan', function ($q) {
                $q->setoran();
            })
            ->get();

        $ownedTargetCount = TargetTabungan::milikPengguna($user->id)->count();

        $pendingRequests = FriendRequest::where('penerima_id', $user->id)
            ->where('status', 'pending')
            ->with('pengirim')
            ->latest()
            ->get();

        $pendingRequestCount = $pendingRequests->count();

        return view('sosial.index', compact(
            'shares',
            'shareableTargets',
            'ownedTargetCount',
            'pendingRequests',
            'pendingRequestCount'
        ));
    }

    public function share(Request $request)
    {
        $request->validate([
            'target_tabungan_id' => 'required|exists:target_tabungan,id',
            'pesan' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $target = TargetTabungan::milikPengguna($user->id)->findOrFail($request->target_tabungan_id);

        if ($target->persentase_progres < 1) {
            return back()->with('error', 'Progres minimal 1% untuk bisa dibagikan. Terus menabung!');
        }

        $alreadyShared = SavingShare::where('user_id', $user->id)
            ->where('target_tabungan_id', $target->id)
            ->where('created_at', '>=', now()->subDay())
            ->exists();

        if ($alreadyShared) {
            return back()->with('error', 'Kamu sudah membagikan progres target ini hari ini. Bagikan lagi besok!');
        }

        $share = SavingShare::create([
            'user_id' => $user->id,
            'target_tabungan_id' => $target->id,
            'jumlah_terkumpul' => $target->total_terkumpul,
            'persentase' => $target->persentase_progres,
            'pesan' => $request->pesan,
        ]);

        // Notify friends
        $friendIds = $user->getAllFriendsIds();
        foreach ($friendIds as $friendId) {
            Notifikasi::create([
                'pengguna_id' => $friendId,
                'tipe' => 'info',
                'judul' => 'Pembaruan Progres',
                'pesan' => $user->nama . ' membagikan progres tabungan baru',
                'data' => ['post_id' => $share->id, 'user_id' => $user->id, 'jenis' => 'share'],
            ]);
        }

        return back()->with('success', 'Progres berhasil dibagikan!');
    }

    public function toggleLike(int $shareId)
    {
        $share = SavingShare::findOrFail($shareId);
        $userId = Auth::id();

        $existingLike = PostLike::where('post_id', $shareId)
            ->where('user_id', $userId)
            ->first();

        if ($existingLike) {
            $existingLike->delete();
            $liked = false;
        } else {
            PostLike::create([
                'post_id' => $shareId,
                'user_id' => $userId,
            ]);
            $liked = true;

            // Notify post owner
            if ($share->user_id !== $userId) {
                $liker = Auth::user();
                Notifikasi::create([
                    'pengguna_id' => $share->user_id,
                    'tipe' => 'info',
                    'judul' => 'Menyukai Progres Anda',
                    'pesan' => $liker->nama . ' menyukai progres tabungan Anda',
                    'data' => ['post_id' => $shareId, 'user_id' => $userId, 'jenis' => 'like'],
                ]);
            }
        }

        $likeCount = $share->likes()->count();

        if (request()->wantsJson()) {
            return response()->json([
                'liked' => $liked,
                'like_count' => $likeCount,
            ]);
        }

        return back();
    }

    public function comment(Request $request, int $shareId)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        $share = SavingShare::findOrFail($shareId);
        $user = Auth::user();

        PostComment::create([
            'post_id' => $shareId,
            'user_id' => $user->id,
            'comment' => $request->comment,
        ]);

        // Notify post owner
        if ($share->user_id !== $user->id) {
            Notifikasi::create([
                'pengguna_id' => $share->user_id,
                'tipe' => 'info',
                'judul' => 'Komentar Baru',
                'pesan' => $user->nama . ' mengomentari postingan Anda: "' . Str::limit($request->comment, 50) . '"',
                'data' => ['post_id' => $shareId, 'user_id' => $user->id, 'comment' => $request->comment, 'jenis' => 'comment'],
            ]);
        }

        return back()->with('success', 'Komentar berhasil ditambahkan.');
    }

    public function destroy(int $shareId)
    {
        $share = SavingShare::findOrFail($shareId);

        if ($share->user_id !== Auth::id()) {
            abort(403, 'Kamu tidak memiliki izin untuk menghapus postingan ini.');
        }

        $share->delete();

        return redirect()->route('sosial.index')->with('success', 'Postingan berhasil dihapus.');
    }

    public function profile(string $username)
    {
        $user = Pengguna::where('username', $username)->firstOrFail();
        $authUser = Auth::user();
        $isOwnProfile = $authUser->id === $user->id;

        $tanggalBergabung = Carbon::parse($user->created_at);
        $hariBergabung = $tanggalBergabung->diffInDays(now()) + 1;

        $totalMenabung = FinancialService::getTotalTabungan($user->id);
        $saldoSaatIni = FinancialService::getSaldoTersedia($user->id);

        $targetAktif = $user->targetTabungan()->where('status', 'aktif')->count();
        $targetTercapai = $user->targetTabungan()->where('status', 'selesai')->count();
        $totalTarget = $user->targetTabungan()->count();

        // Heatmap 365 days
        $endDate = now()->startOfDay();
        $startDate = now()->subDays(364)->startOfDay();

        $dailySavings = FinancialService::getDailySavings(
            $user->id,
            $startDate->format('Y-m-d'),
            $endDate->format('Y-m-d')
        );

        $maxDaily = $dailySavings->max() ?: 1;

        $heatmapData = [];
        $currentDate = clone $startDate;

        $startDayOfWeek = $startDate->dayOfWeek;
        for ($i = 0; $i < $startDayOfWeek; $i++) {
            $heatmapData[] = ['is_padding' => true];
        }

        $hariAktif = 0;

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
                $hariAktif++;
            }

            $heatmapData[] = [
                'is_padding' => false,
                'date' => $dateStr,
                'total' => $totalNominal,
                'level' => $level,
                'monthName' => $currentDate->translatedFormat('M'),
                'isFirstOfMonth' => $currentDate->day === 1,
            ];

            $currentDate->addDay();
        }

        // Achievements
        $achievements = AchievementService::hitungAchievement($user->id);

        // Badge
        $badge = $user->badge_level;

        // Friend status
        $friendStatus = 'none';
        if ($authUser->isFriendWith($user)) {
            $friendStatus = 'friends';
        } elseif ($authUser->hasSentRequestTo($user)) {
            $friendStatus = 'pending';
        } elseif ($authUser->hasPendingRequestFrom($user)) {
            $friendStatus = 'awaiting';
        }

        $pendingFriendRequest = $authUser->getPendingFriendRequestFrom($user);

        return view('sosial.profile', compact(
            'user', 'isOwnProfile',
            'tanggalBergabung', 'hariBergabung',
            'totalMenabung', 'saldoSaatIni',
            'targetAktif', 'targetTercapai', 'totalTarget',
            'hariAktif', 'heatmapData',
            'achievements', 'badge',
            'friendStatus', 'pendingFriendRequest'
        ));
    }

    public function sendFriendRequest(Pengguna $user)
    {
        $authUser = Auth::user();

        if ($authUser->id === $user->id) {
            return back()->with('error', 'Tidak bisa mengirim permintaan teman ke diri sendiri.');
        }

        if ($authUser->isFriendWith($user)) {
            return back()->with('error', 'Kamu sudah berteman dengan ' . $user->nama);
        }

        if ($authUser->hasSentRequestTo($user)) {
            return back()->with('error', 'Permintaan teman sudah dikirim.');
        }

        if ($authUser->hasPendingRequestFrom($user)) {
            // Auto-accept if they already sent us one
            $existingRequest = $authUser->getPendingFriendRequestFrom($user);
            $existingRequest->update(['status' => 'diterima']);

            Friend::create([
                'pengguna_id' => $authUser->id,
                'teman_id' => $user->id,
            ]);

            Notifikasi::create([
                'pengguna_id' => $user->id,
                'tipe' => 'info',
                'judul' => 'Permintaan Teman Diterima',
                'pesan' => $authUser->nama . ' menerima permintaan teman Anda',
                'data' => ['user_id' => $authUser->id, 'jenis' => 'friend_accepted'],
            ]);

            return back()->with('success', 'Sekarang kamu berteman dengan ' . $user->nama . '!');
        }

        FriendRequest::create([
            'pengirim_id' => $authUser->id,
            'penerima_id' => $user->id,
            'status' => 'pending',
        ]);

            Notifikasi::create([
                'pengguna_id' => $user->id,
                'tipe' => 'info',
                'judul' => 'Permintaan Teman',
                'pesan' => $authUser->nama . ' mengirim permintaan teman',
                'data' => ['pengirim_id' => $authUser->id, 'jenis' => 'friend_request'],
            ]);

        return back()->with('success', 'Permintaan teman berhasil dikirim ke ' . $user->nama);
    }

    public function acceptFriendRequest(FriendRequest $friendRequest)
    {
        $authUser = Auth::user();

        if ($friendRequest->penerima_id !== $authUser->id) {
            abort(403);
        }

        if ($friendRequest->status !== 'pending') {
            return back()->with('error', 'Permintaan sudah diproses sebelumnya.');
        }

        $friendRequest->update(['status' => 'diterima']);

        Friend::create([
            'pengguna_id' => $authUser->id,
            'teman_id' => $friendRequest->pengirim_id,
        ]);

        Notifikasi::create([
            'pengguna_id' => $friendRequest->pengirim_id,
            'tipe' => 'info',
            'judul' => 'Permintaan Teman Diterima',
            'pesan' => $authUser->nama . ' menerima permintaan teman Anda',
            'data' => ['user_id' => $authUser->id, 'jenis' => 'friend_accepted'],
        ]);

        return back()->with('success', 'Sekarang kamu berteman dengan ' . $friendRequest->pengirim->nama . '!');
    }

    public function rejectFriendRequest(FriendRequest $friendRequest)
    {
        $authUser = Auth::user();

        if ($friendRequest->penerima_id !== $authUser->id) {
            abort(403);
        }

        if ($friendRequest->status !== 'pending') {
            return back()->with('error', 'Permintaan sudah diproses sebelumnya.');
        }

        $friendRequest->update(['status' => 'ditolak']);

        return back()->with('success', 'Permintaan teman ditolak.');
    }

    public function removeFriend(Pengguna $user)
    {
        $authUser = Auth::user();

        Friend::where(function ($q) use ($authUser, $user) {
            $q->where('pengguna_id', $authUser->id)->where('teman_id', $user->id);
        })->orWhere(function ($q) use ($authUser, $user) {
            $q->where('pengguna_id', $user->id)->where('teman_id', $authUser->id);
        })->delete();

        return back()->with('success', 'Berhasil menghapus pertemanan dengan ' . $user->nama);
    }

    public function search(Request $request)
    {
        $query = $request->get('q');

        $users = Pengguna::where(function ($q) use ($query) {
            $q->where('nama', 'like', '%' . $query . '%')
              ->orWhere('username', 'like', '%' . $query . '%');
        })
        ->where('id', '!=', Auth::id())
        ->limit(20)
        ->get();

        $authUser = Auth::user();

        $results = $users->map(function ($user) use ($authUser) {
            $friendStatus = 'none';
            if ($authUser->isFriendWith($user)) {
                $friendStatus = 'friends';
            } elseif ($authUser->hasSentRequestTo($user)) {
                $friendStatus = 'pending';
            } elseif ($authUser->hasPendingRequestFrom($user)) {
                $friendStatus = 'awaiting';
                $pendingReq = $authUser->getPendingFriendRequestFrom($user);
                $user->pending_request_id = $pendingReq ? $pendingReq->id : null;
            }
            $user->friend_status = $friendStatus;
            $user->badge = $user->badge_level;
            return $user;
        });

        if ($request->wantsJson()) {
            return response()->json($results);
        }

        return view('sosial.search', compact('results', 'query'));
    }
}
