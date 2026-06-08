<?php

namespace App\Services;

use App\Models\PengaturanPengguna;

class PengaturanService
{
    /**
     * Default values for all settings.
     * New settings can be added here without needing a migration.
     */
    protected array $defaults = [
        // Tampilan
        'tema'              => 'light',
        'compact_mode'      => '0',
        'animasi_aktif'     => '1',

        // Dashboard widget visibility
        'tampil_heatmap'    => '1',
        'tampil_streak'     => '1',
        'tampil_target'     => '1',
        'tampil_riwayat'    => '1',
        'tampil_statistik'  => '1',

        // Dashboard widget order (JSON array)
        'urutan_widget'     => '["saldo","streak","target_form","heatmap","stats","target_list","riwayat"]',

        // Notifikasi
        'notif_menabung'    => '1',
        'notif_target'      => '1',
        'notif_gajian'      => '1',
        'notif_budget'      => '1',

        // Keuangan
        'target_default_id' => '',
        'alokasi_aktif'     => '0',
        'alokasi_persen'    => '20',

        // Privasi
        'hide_balance'      => '0',
        // Legacy keys (fallback)
        'sembunyikan_saldo' => '0',
        'mode_privasi'      => '0',
        'blur_saldo'        => '0',
    ];

    /**
     * Get a single setting value.
     */
    public function ambil(int $userId, string $kunci, $default = null)
    {
        $fallback = $default ?? ($this->defaults[$kunci] ?? null);

        $setting = PengaturanPengguna::where('pengguna_id', $userId)
            ->where('kunci', $kunci)
            ->first();

        return $setting ? $setting->nilai : $fallback;
    }

    /**
     * Save a single setting.
     */
    public function simpan(int $userId, string $kunci, $nilai): void
    {
        PengaturanPengguna::updateOrCreate(
            ['pengguna_id' => $userId, 'kunci' => $kunci],
            ['nilai' => (string) $nilai]
        );
    }

    /**
     * Save multiple settings at once.
     */
    public function simpanBanyak(int $userId, array $data): void
    {
        foreach ($data as $kunci => $nilai) {
            $this->simpan($userId, $kunci, $nilai);
        }
    }

    /**
     * Get all settings for a user, merged with defaults.
     */
    public function ambilSemua(int $userId): array
    {
        $userSettings = PengaturanPengguna::where('pengguna_id', $userId)
            ->pluck('nilai', 'kunci')
            ->toArray();

        return array_merge($this->defaults, $userSettings);
    }

    /**
     * Get the default values array.
     */
    public function getDefaults(): array
    {
        return $this->defaults;
    }
}
