<?php

namespace App\Services;

use App\Models\TransaksiTabungan;

class TargetPredictionService
{
    public static function calculatePrediction(
        float $jumlahTarget,
        float $totalTerkumpul,
        ?float $rencanaHarian,
        ?int $targetId = null,
        ?int $userId = null
    ): array {
        $sisaTarget = max(0, $jumlahTarget - $totalTerkumpul);

        if ($sisaTarget <= 0) {
            return [
                'sisa_target' => 0,
                'hari_prediksi' => null,
                'formatted_prediksi' => 'Target Tercapai',
                'tanggal_prediksi' => null,
                'status' => 'selesai',
                'message' => 'Target Tercapai',
            ];
        }

        if ($rencanaHarian && $rencanaHarian > 0) {
            $hariPrediksi = (int) ceil($sisaTarget / $rencanaHarian);
            $tanggalPrediksi = now()->addDays($hariPrediksi);

            return [
                'sisa_target' => $sisaTarget,
                'hari_prediksi' => $hariPrediksi,
                'formatted_prediksi' => formatPredictionTime($hariPrediksi),
                'tanggal_prediksi' => $tanggalPrediksi,
                'status' => 'on_track',
                'message' => null,
            ];
        }

        if ($targetId && $userId) {
            $rataRata = TransaksiTabungan::where('target_tabungan_id', $targetId)
                ->where('tipe', 'setor')
                ->where('tanggal_transaksi', '>=', now()->subDays(30))
                ->avg('jumlah');

            if ($rataRata && $rataRata > 0) {
                $hariPrediksi = (int) ceil($sisaTarget / $rataRata);
                $tanggalPrediksi = now()->addDays($hariPrediksi);

                return [
                    'sisa_target' => $sisaTarget,
                    'hari_prediksi' => $hariPrediksi,
                    'formatted_prediksi' => formatPredictionTime($hariPrediksi),
                    'tanggal_prediksi' => $tanggalPrediksi,
                    'status' => 'on_track',
                    'message' => null,
                ];
            }
        }

        return [
            'sisa_target' => $sisaTarget,
            'hari_prediksi' => null,
            'formatted_prediksi' => 'Belum cukup data',
            'tanggal_prediksi' => null,
            'status' => 'no_data',
            'message' => 'Belum cukup data untuk membuat prediksi',
        ];
    }
}
