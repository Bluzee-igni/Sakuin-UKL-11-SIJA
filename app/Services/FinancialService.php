<?php

namespace App\Services;

use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use App\Models\TransaksiTabungan;

class FinancialService
{
    /**
     * Get total pemasukan untuk pengguna
     */
    public static function getTotalPemasukan($userId): float
    {
        return (float) Pemasukan::milikPengguna($userId)->sum('jumlah');
    }

    /**
     * Get total pengeluaran untuk pengguna
     */
    public static function getTotalPengeluaran($userId): float
    {
        return (float) Pengeluaran::milikPengguna($userId)->sum('jumlah');
    }

    /**
     * Get total tabungan (setoran) untuk pengguna
     */
    public static function getTotalTabungan($userId): float
    {
        return (float) TransaksiTabungan::setoran()
            ->whereHas('targetTabungan', function ($q) use ($userId) {
                $q->where('pengguna_id', $userId);
            })
            ->sum('jumlah');
    }

    /**
     * Get total aset (Pemasukan - Pengeluaran)
     * Uang keseluruhan tanpa memedulikan apakah itu ditabung atau tidak.
     */
    public static function getTotalAset($userId): float
    {
        $pemasukan = self::getTotalPemasukan($userId);
        $pengeluaran = self::getTotalPengeluaran($userId);
        return max(0, $pemasukan - $pengeluaran);
    }

    /**
     * Get saldo tersedia (Total Aset - Total Tabungan)
     * Uang tunai yang benar-benar liquid / bisa dipakai / ditabung lagi.
     */
    public static function getSaldoTersedia($userId): float
    {
        $aset = self::getTotalAset($userId);
        $tabungan = self::getTotalTabungan($userId);
        return max(0, $aset - $tabungan);
    }

    /**
     * Get metrics at a glance
     */
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
}
