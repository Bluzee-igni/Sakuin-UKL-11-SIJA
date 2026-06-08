<?php

namespace App\Http\Controllers;

use App\Models\TargetTabungan;
use App\Models\TransaksiOtomatis;
use App\Services\PengaturanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengaturanController extends Controller
{
    protected PengaturanService $pengaturanService;

    public function __construct(PengaturanService $pengaturanService)
    {
        $this->pengaturanService = $pengaturanService;
    }

    /**
     * Render the settings page.
     */
    public function index()
    {
        $user = Auth::user();
        $settings = $this->pengaturanService->ambilSemua($user->id);

        // Fetch user's targets for "Target Default" dropdown
        $targets = TargetTabungan::milikPengguna($user->id)
            ->where('status', '!=', 'selesai')
            ->latest()
            ->get(['id', 'nama']);

        // Fetch existing automated salary transaction (if any)
        $gajiOtomatis = TransaksiOtomatis::where('pengguna_id', $user->id)
            ->where('tipe', 'pemasukan')
            ->where('nama', 'like', '%Gaji%')
            ->first();

        return view('pengaturan.index', compact('settings', 'targets', 'gajiOtomatis', 'user'));
    }

    /**
     * Update Tampilan settings.
     */
    public function updateTampilan(Request $request)
    {
        $request->validate([
            'tema'          => 'required|in:light,dark,green',
            'compact_mode'  => 'nullable|in:0,1',
            'animasi_aktif' => 'nullable|in:0,1',
        ]);

        $userId = Auth::id();

        $this->pengaturanService->simpanBanyak($userId, [
            'tema'          => $request->tema,
            'compact_mode'  => $request->has('compact_mode') ? '1' : '0',
            'animasi_aktif' => $request->has('animasi_aktif') ? '1' : '0',
        ]);

        return redirect()->route('pengaturan.index', '#tampilan')
            ->with('success', 'Pengaturan tampilan berhasil disimpan.');
    }

    /**
     * Update Dashboard settings.
     */
    public function updateDashboard(Request $request)
    {
        $request->validate([
            'urutan_widget' => 'nullable|string',
        ]);

        $userId = Auth::id();

        $this->pengaturanService->simpanBanyak($userId, [
            'tampil_heatmap'    => $request->has('tampil_heatmap') ? '1' : '0',
            'tampil_streak'     => $request->has('tampil_streak') ? '1' : '0',
            'tampil_target'     => $request->has('tampil_target') ? '1' : '0',
            'tampil_riwayat'    => $request->has('tampil_riwayat') ? '1' : '0',
            'tampil_statistik'  => $request->has('tampil_statistik') ? '1' : '0',
        ]);

        if ($request->filled('urutan_widget')) {
            $this->pengaturanService->simpan($userId, 'urutan_widget', $request->urutan_widget);
        }

        return redirect()->route('pengaturan.index', '#dashboard')
            ->with('success', 'Pengaturan dashboard berhasil disimpan.');
    }

    /**
     * Update Notifikasi settings.
     */
    public function updateNotifikasi(Request $request)
    {
        $userId = Auth::id();

        $this->pengaturanService->simpanBanyak($userId, [
            'notif_menabung' => $request->has('notif_menabung') ? '1' : '0',
            'notif_target'   => $request->has('notif_target') ? '1' : '0',
            'notif_gajian'   => $request->has('notif_gajian') ? '1' : '0',
            'notif_budget'   => $request->has('notif_budget') ? '1' : '0',
        ]);

        return redirect()->route('pengaturan.index', '#notifikasi')
            ->with('success', 'Pengaturan notifikasi berhasil disimpan.');
    }

    /**
     * Update Keuangan settings.
     */
    public function updateKeuangan(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'mata_uang'         => 'required|in:IDR,USD,EUR,GBP,JPY,MYR,SGD,AUD,SAR,KRW',
            'target_default_id' => 'nullable|exists:target_tabungan,id',
            'alokasi_persen'    => 'nullable|integer|min:0|max:100',
            'gaji_tanggal'      => 'nullable|integer|min:1|max:31',
            'gaji_nominal'      => 'nullable|numeric|min:0',
        ]);

        // Update currency on user table
        $user->update(['mata_uang' => $request->mata_uang]);

        // Save settings
        $this->pengaturanService->simpanBanyak($user->id, [
            'target_default_id' => $request->target_default_id ?? '',
            'alokasi_aktif'     => $request->has('alokasi_aktif') ? '1' : '0',
            'alokasi_persen'    => $request->alokasi_persen ?? '20',
        ]);

        // Handle automated salary (upsert into transaksi_otomatis)
        if ($request->filled('gaji_tanggal') && $request->filled('gaji_nominal') && $request->gaji_nominal > 0) {
            TransaksiOtomatis::updateOrCreate(
                [
                    'pengguna_id' => $user->id,
                    'tipe'        => 'pemasukan',
                    'nama'        => 'Gaji Bulanan',
                ],
                [
                    'jumlah'        => $request->gaji_nominal,
                    'tanggal_rutin' => $request->gaji_tanggal,
                ]
            );
        }

        return redirect()->route('pengaturan.index', '#keuangan')
            ->with('success', 'Pengaturan keuangan berhasil disimpan.');
    }

    /**
     * Update Privasi settings.
     */
    public function updatePrivasi(Request $request)
    {
        $userId = Auth::id();

        $this->pengaturanService->simpanBanyak($userId, [
            'hide_balance' => $request->has('hide_balance') ? '1' : '0',
        ]);

        return redirect()->route('pengaturan.index', '#privasi')
            ->with('success', 'Pengaturan privasi berhasil disimpan.');
    }
}
