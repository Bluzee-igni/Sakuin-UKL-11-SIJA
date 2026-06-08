<?php

namespace App\Services;

use App\Models\TargetTabungan;
use App\Models\TransaksiTabungan;
use Carbon\Carbon;

class AchievementService
{
    /**
     * Define all achievements with their metadata.
     */
    public static function semuaAchievement(): array
    {
        return [
            'target_pertama_dibuat' => [
                'id' => 'target_pertama_dibuat',
                'ikon' => 'ph-target ph-fill',
                'warna' => '#059669',
                'judul' => 'Target Pertama Dibuat',
                'deskripsi' => 'Buat target tabungan pertamamu',
                'kategori' => 'target',
            ],
            'target_pertama_selesai' => [
                'id' => 'target_pertama_selesai',
                'ikon' => 'ph-trophy ph-fill',
                'warna' => '#F59E0B',
                'judul' => 'Target Pertama Selesai',
                'deskripsi' => 'Berhasil menyelesaikan target tabungan',
                'kategori' => 'target',
            ],
            'nabung_sejuta' => [
                'id' => 'nabung_sejuta',
                'ikon' => 'ph-piggy-bank ph-fill',
                'warna' => '#10B981',
                'judul' => 'Menabung Rp1.000.000 Total',
                'deskripsi' => 'Akumulasi tabungan mencapai Rp1.000.000',
                'kategori' => 'tabungan',
            ],
            'streak_7_hari' => [
                'id' => 'streak_7_hari',
                'ikon' => 'ph-fire ph-fill',
                'warna' => '#EF4444',
                'judul' => 'Menabung 7 Hari Berturut-turut',
                'deskripsi' => 'Konsisten menabung selama 7 hari beruntun',
                'kategori' => 'konsistensi',
            ],
        ];
    }

    /**
     * Calculate which achievements are unlocked for a given user,
     * and return them with status ('tercapai' or 'belum').
     */
    public static function hitungAchievement(int $userId): array
    {
        $semua = self::semuaAchievement();
        $hasil = [];

        // 1. Target Pertama Dibuat
        $totalTarget = TargetTabungan::milikPengguna($userId)->count();
        $hasil['target_pertama_dibuat'] = [
            'status' => $totalTarget >= 1 ? 'tercapai' : 'belum',
            'tercapai_pada' => $totalTarget >= 1 ? self::getTargetPertamaDate($userId) : null,
        ];

        // 2. Target Pertama Selesai
        $targetSelesai = TargetTabungan::milikPengguna($userId)
            ->where('status', 'selesai')
            ->count();
        $hasil['target_pertama_selesai'] = [
            'status' => $targetSelesai >= 1 ? 'tercapai' : 'belum',
            'tercapai_pada' => $targetSelesai >= 1 ? self::getTargetSelesaiPertamaDate($userId) : null,
        ];

        // 3. Menabung Rp1.000.000 Total
        $totalTabungan = (float) TransaksiTabungan::setoran()
            ->whereHas('targetTabungan', function ($q) use ($userId) {
                $q->where('pengguna_id', $userId);
            })
            ->sum('jumlah');

        $hasil['nabung_sejuta'] = [
            'status' => $totalTabungan >= 1000000 ? 'tercapai' : 'belum',
            'tercapai_pada' => $totalTabungan >= 1000000 ? self::getSejutaPertamaDate($userId) : null,
        ];

        // 4. Menabung 7 Hari Berturut-turut
        $pernahStreak7 = self::pernahStreak7Hari($userId);
        $hasil['streak_7_hari'] = [
            'status' => $pernahStreak7 ? 'tercapai' : 'belum',
            'tercapai_pada' => $pernahStreak7 ? self::getStreak7Date($userId) : null,
        ];

        // Gabungkan dengan metadata
        $achievements = [];
        foreach ($semua as $key => $meta) {
            $achievements[] = array_merge($meta, $hasil[$key]);
        }

        // Hitung ringkasan
        $totalTercapai = count(array_filter($hasil, fn($h) => $h['status'] === 'tercapai'));
        $totalSemua = count($semua);

        return [
            'daftar' => $achievements,
            'total_tercapai' => $totalTercapai,
            'total_semua' => $totalSemua,
            'persentase' => $totalSemua > 0 ? round(($totalTercapai / $totalSemua) * 100) : 0,
        ];
    }

    private static function getTargetPertamaDate(int $userId): ?string
    {
        $first = TargetTabungan::milikPengguna($userId)
            ->orderBy('created_at', 'asc')
            ->first();
        return $first ? $first->created_at->format('Y-m-d') : null;
    }

    private static function getTargetSelesaiPertamaDate(int $userId): ?string
    {
        $first = TargetTabungan::milikPengguna($userId)
            ->where('status', 'selesai')
            ->orderBy('updated_at', 'asc')
            ->first();
        return $first ? $first->updated_at->format('Y-m-d') : null;
    }

    private static function getSejutaPertamaDate(int $userId): ?string
    {
        // Cari transaksi pertama yang membuat total >= 1.000.000
        $runningTotal = 0;
        $transaksi = TransaksiTabungan::setoran()
            ->whereHas('targetTabungan', function ($q) use ($userId) {
                $q->where('pengguna_id', $userId);
            })
            ->orderBy('tanggal_transaksi', 'asc')
            ->orderBy('created_at', 'asc')
            ->get(['tanggal_transaksi', 'jumlah', 'created_at']);

        foreach ($transaksi as $t) {
            $runningTotal += (float) $t->jumlah;
            if ($runningTotal >= 1000000) {
                return $t->tanggal_transaksi instanceof Carbon
                    ? $t->tanggal_transaksi->format('Y-m-d')
                    : Carbon::parse($t->tanggal_transaksi)->format('Y-m-d');
            }
        }

        return null;
    }

    private static function pernahStreak7Hari(int $userId): bool
    {
        $allDates = TransaksiTabungan::setoran()
            ->whereHas('targetTabungan', function ($q) use ($userId) {
                $q->where('pengguna_id', $userId);
            })
            ->orderBy('tanggal_transaksi', 'desc')
            ->pluck('tanggal_transaksi')
            ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
            ->unique()
            ->values()
            ->toArray();

        if (count($allDates) < 7) return false;

        // Cari streak terpanjang
        $longest = 1;
        $current = 1;
        for ($i = 0; $i < count($allDates) - 1; $i++) {
            $diff = Carbon::parse($allDates[$i])->diffInDays(Carbon::parse($allDates[$i + 1]));
            if ($diff == 1) {
                $current++;
                if ($current > $longest) $longest = $current;
            } else {
                $current = 1;
            }
        }

        return $longest >= 7;
    }

    private static function getStreak7Date(int $userId): ?string
    {
        $allDates = TransaksiTabungan::setoran()
            ->whereHas('targetTabungan', function ($q) use ($userId) {
                $q->where('pengguna_id', $userId);
            })
            ->orderBy('tanggal_transaksi', 'desc')
            ->pluck('tanggal_transaksi')
            ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
            ->unique()
            ->values()
            ->toArray();

        if (count($allDates) < 7) return null;

        // Cari streak pertama yang mencapai 7 hari
        $current = 1;
        for ($i = 0; $i < count($allDates) - 1; $i++) {
            $diff = Carbon::parse($allDates[$i])->diffInDays(Carbon::parse($allDates[$i + 1]));
            if ($diff == 1) {
                $current++;
                if ($current >= 7) {
                    // Streak 7 hari tercapai di tanggal ke-7 (hari paling awal dalam streak ini)
                    return $allDates[$i + 6]; // index $i+6 = hari ke-7 dalam streak
                }
            } else {
                $current = 1;
            }
        }

        return null;
    }
}
