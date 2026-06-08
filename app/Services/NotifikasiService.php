<?php

namespace App\Services;

use App\Models\Notifikasi;
use App\Models\Pengguna;
use App\Models\TransaksiTabungan;
use App\Models\TargetTabungan;
use App\Models\TransaksiOtomatis;
use App\Models\Pengeluaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class NotifikasiService
{
    /**
     * Generate daily passive notifications based on user settings.
     * This is called by middleware once per day/session.
     */
    public function generatePassiveNotifications(Pengguna $user)
    {
        try {
            $today = Carbon::today()->toDateString();
            
            // Check if we already generated for today to avoid spamming DB
            $lastGenerated = $user->getSetting('last_notif_generated');
            if ($lastGenerated === $today) {
                return;
            }

            // 1. Pengingat Menabung (Jika belum nabung hari ini)
            if ($user->getSetting('notif_menabung', '1') === '1') {
                $hasNabungToday = TransaksiTabungan::whereHas('targetTabungan', function ($q) use ($user) {
                    $q->where('pengguna_id', $user->id);
                })
                ->where('tipe', 'setor')
                ->where('tanggal_transaksi', $today)
                ->exists();

                if (!$hasNabungToday) {
                    $this->createNotif($user->id, 'pengingat', 'Tingkatkan streakmu hari ini', 'Jangan lupa menabung hari ini untuk menjaga streak konsistensimu!');
                }
            }

            // 2. Pengingat Target (Jika target > 90% tapi belum selesai)
            if ($user->getSetting('notif_target', '1') === '1') {
                $targets = TargetTabungan::milikPengguna($user->id)
                    ->where('status', 'aktif')
                    ->get();
                foreach ($targets as $target) {
                    $progress = $target->persentase_progres;
                    if ($progress >= 90 && $progress < 100) {
                        // Check if we already notified about this target today to prevent duplicates
                        $alreadyNotified = Notifikasi::where('pengguna_id', $user->id)
                            ->where('tipe', 'pencapaian')
                            ->where('data->target_id', $target->id)
                            ->whereDate('created_at', $today)
                            ->exists();

                        if (!$alreadyNotified) {
                            $sisa = 100 - $progress;
                            $this->createNotif(
                                $user->id, 
                                'pencapaian', 
                                "Target {$target->nama} hampir tercapai!", 
                                "Target {$target->nama} tinggal {$sisa}% lagi. Sedikit lagi mimpimu terwujud!",
                                ['target_id' => $target->id]
                            );
                        }
                    }
                }
            }

            // 3. Pengingat Gajian
            if ($user->getSetting('notif_gajian', '1') === '1') {
                $gajiAuto = TransaksiOtomatis::where('pengguna_id', $user->id)
                    ->where('tipe', 'pemasukan')
                    ->where('tanggal_rutin', Carbon::now()->day)
                    ->first();

                if ($gajiAuto) {
                    $this->createNotif(
                        $user->id,
                        'info',
                        'Yeay, Hari Gajian!',
                        "Pemasukan '{$gajiAuto->nama}' telah masuk. Jangan lupa alokasikan untuk ditabung ya!"
                    );
                }
            }

            // 4. Peringatan Budget Bulanan
            if ($user->getSetting('notif_budget', '1') === '1') {
                $budget = $user->anggaran_bulanan;
                if ($budget > 0) {
                    $currentMonth = Carbon::now()->format('Y-m');
                    $totalPengeluaran = Pengeluaran::where('pengguna_id', $user->id)
                        ->where('tanggal', 'like', $currentMonth . '%')
                        ->sum('jumlah');

                    $persenPengeluaran = ($totalPengeluaran / $budget) * 100;
                    
                    if ($persenPengeluaran >= 80) {
                        $this->createNotif(
                            $user->id,
                            'peringatan',
                            'Awas, Budget Menipis!',
                            'Pengeluaran bulan ini sudah mencapai ' . round($persenPengeluaran) . '% dari anggaran bulananmu. Yuk lebih hemat!'
                        );
                    }
                }
            }

            // Mark as generated for today
            app(PengaturanService::class)->simpan($user->id, 'last_notif_generated', $today);

        } catch (\Exception $e) {
            Log::error('Gagal generate notifikasi: ' . $e->getMessage());
        }
    }

    private function createNotif(int $userId, string $tipe, string $judul, string $pesan, array $data = [])
    {
        Notifikasi::create([
            'pengguna_id' => $userId,
            'tipe'        => $tipe,
            'judul'       => $judul,
            'pesan'       => $pesan,
            'data'        => $data,
        ]);
    }
}
