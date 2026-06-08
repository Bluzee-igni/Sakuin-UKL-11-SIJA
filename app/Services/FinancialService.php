<?php

namespace App\Services;

use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use App\Models\TransaksiTabungan;
use Carbon\Carbon;

class FinancialService
{
    public static function getTotalPemasukan($userId): float
    {
        return (float) Pemasukan::milikPengguna($userId)->sum('jumlah');
    }

    public static function getTotalPengeluaran($userId): float
    {
        return (float) Pengeluaran::milikPengguna($userId)->sum('jumlah');
    }

    public static function getTotalTabungan($userId): float
    {
        return (float) TransaksiTabungan::setoran()
            ->whereHas('targetTabungan', function ($q) use ($userId) {
                $q->where('pengguna_id', $userId);
            })
            ->sum('jumlah');
    }

    public static function getTotalAset($userId): float
    {
        $pemasukan = self::getTotalPemasukan($userId);
        $pengeluaran = self::getTotalPengeluaran($userId);
        return max(0, $pemasukan - $pengeluaran);
    }

    public static function getSaldoTersedia($userId): float
    {
        $aset = self::getTotalAset($userId);
        $tabungan = self::getTotalTabungan($userId);
        return max(0, $aset - $tabungan);
    }

    public static function getMetrics($userId): array
    {
        return [
            'total_pemasukan' => self::getTotalPemasukan($userId),
            'total_pengeluaran' => self::getTotalPengeluaran($userId),
            'total_tabungan' => self::getTotalTabungan($userId),
            'total_aset' => self::getTotalAset($userId),
            'saldo_tersedia' => self::getSaldoTersedia($userId),
        ];
    }

    public static function getAllUniqueDates($userId, int $maxDays = 0): array
    {
        $query = TransaksiTabungan::setoran()
            ->whereHas('targetTabungan', function ($q) use ($userId) {
                $q->where('pengguna_id', $userId);
            })
            ->orderBy('tanggal_transaksi', 'desc');

        if ($maxDays > 0) {
            $query->where('tanggal_transaksi', '>=', now()->subDays($maxDays)->format('Y-m-d'));
        }

        return $query
            ->pluck('tanggal_transaksi')
            ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
            ->unique()
            ->values()
            ->toArray();
    }

    public static function getCurrentStreak($userId): int
    {
        $allDatesDesc = self::getAllUniqueDates($userId, 730);
        if (empty($allDatesDesc)) return 0;

        $todayStr = now()->format('Y-m-d');
        $yesterdayStr = now()->subDay()->format('Y-m-d');

        if ($allDatesDesc[0] !== $todayStr && $allDatesDesc[0] !== $yesterdayStr) {
            return 0;
        }

        $streak = 0;
        $expectedDate = Carbon::parse($allDatesDesc[0]);
        foreach ($allDatesDesc as $dateStr) {
            if ($dateStr === $expectedDate->format('Y-m-d')) {
                $streak++;
                $expectedDate->subDay();
            } else {
                break;
            }
        }

        return $streak;
    }

    public static function getLongestStreak($userId): int
    {
        $allDatesDesc = self::getAllUniqueDates($userId);
        if (count($allDatesDesc) < 2) return count($allDatesDesc);

        $longest = 1;
        $current = 1;
        for ($i = 0; $i < count($allDatesDesc) - 1; $i++) {
            $date1 = Carbon::parse($allDatesDesc[$i]);
            $date2 = Carbon::parse($allDatesDesc[$i + 1]);
            if ($date1->diffInDays($date2) == 1) {
                $current++;
                if ($current > $longest) $longest = $current;
            } else {
                $current = 1;
            }
        }

        return $longest;
    }

    public static function hasSavedToday($userId): bool
    {
        $allDatesDesc = self::getAllUniqueDates($userId);
        return !empty($allDatesDesc) && $allDatesDesc[0] === now()->format('Y-m-d');
    }

    public static function getBadgeLevel($userId): array
    {
        $total = self::getTotalTabungan($userId);

        if ($total > 25000000) {
            return ['level' => 4, 'nama' => 'Financial Master', 'ikon' => '💎', 'warna' => '#6366f1'];
        } elseif ($total > 5000000) {
            return ['level' => 3, 'nama' => 'Penabung Aktif', 'ikon' => '🌳', 'warna' => '#059669'];
        } elseif ($total > 500000) {
            return ['level' => 2, 'nama' => 'Konsisten', 'ikon' => '🌿', 'warna' => '#10B981'];
        } else {
            return ['level' => 1, 'nama' => 'Pemula', 'ikon' => '🌱', 'warna' => '#6b7280'];
        }
    }

    public static function getDailySavings($userId, $startDate, $endDate): \Illuminate\Support\Collection
    {
        return TransaksiTabungan::setoran()
            ->whereHas('targetTabungan', function ($q) use ($userId) {
                $q->where('pengguna_id', $userId);
            })
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->selectRaw('DATE(tanggal_transaksi) as date, SUM(jumlah) as total')
            ->groupBy('date')
            ->pluck('total', 'date');
    }
}
