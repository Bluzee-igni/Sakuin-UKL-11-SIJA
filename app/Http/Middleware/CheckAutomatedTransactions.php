<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengguna;
use App\Models\TransaksiOtomatis;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use Carbon\Carbon;

class CheckAutomatedTransactions
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            /** @var Pengguna $user */
            $user = Auth::user();
            $now = Carbon::now();
            $currentMonthString = $now->format('Y-m');
            $currentDay = $now->day;

            $autos = TransaksiOtomatis::where('pengguna_id', $user->id)
                ->where(function ($query) use ($currentMonthString) {
                    $query->where('bulan_terakhir_proses', '<', $currentMonthString)
                          ->orWhereNull('bulan_terakhir_proses');
                })
                ->where('tanggal_rutin', '<=', $currentDay)
                ->get();

            foreach ($autos as $auto) {
                try {
                    // Proses transaksi
                    if ($auto->tipe === 'pemasukan') {
                        \Illuminate\Support\Facades\DB::beginTransaction();
                        try {
                            // 1. Catat pemasukan utuh
                            Pemasukan::create([
                                'pengguna_id' => $user->id,
                                'nama' => $auto->nama,
                                'jumlah' => $auto->jumlah,
                                'tanggal' => $now->format('Y-m') . '-' . sprintf('%02d', $auto->tanggal_rutin),
                                'catatan' => 'Sistem Otomatis: Gaji/Pemasukan Rutin',
                            ]);

                            // 2. Cek apakah auto-alokasi aktif dan ada target default
                            $alokasiAktif = $user->getSetting('alokasi_aktif', '0') === '1';
                            $targetDefaultId = $user->getSetting('target_default_id');
                            $persenAlokasi = (int) $user->getSetting('alokasi_persen', '20');

                            if ($alokasiAktif && $targetDefaultId && $persenAlokasi > 0) {
                                $target = \App\Models\TargetTabungan::find($targetDefaultId);
                                
                                if ($target && $target->status !== 'selesai' && $target->pengguna_id === $user->id) {
                                    $nominalPotong = ($persenAlokasi / 100) * $auto->jumlah;
                                    
                                    // Masukkan ke tabungan
                                    \App\Models\TransaksiTabungan::create([
                                        'target_tabungan_id' => $target->id,
                                        'jumlah' => $nominalPotong,
                                        'tipe' => 'setor',
                                        'tanggal_transaksi' => $now->format('Y-m') . '-' . sprintf('%02d', $auto->tanggal_rutin),
                                        'catatan' => 'Alokasi Otomatis dari ' . $auto->nama,
                                    ]);
                                }
                            }

                            \Illuminate\Support\Facades\DB::commit();
                        } catch (\Exception $ex) {
                            \Illuminate\Support\Facades\DB::rollBack();
                            throw $ex; // Lempar lagi biar ditangkap catch luar
                        }
                    } else {
                        Pengeluaran::create([
                            'pengguna_id' => $user->id,
                            'nama' => $auto->nama,
                            'jumlah' => $auto->jumlah,
                            'kategori' => $auto->kategori ?? 'Lainnya', // Fallback jika kategori kosong
                            'tanggal' => $now->format('Y-m') . '-' . sprintf('%02d', $auto->tanggal_rutin),
                            'catatan' => 'Sistem Otomatis: Pengeluaran/Tagihan Rutin',
                        ]);
                    }

                    // Update status
                    $auto->update(['bulan_terakhir_proses' => $currentMonthString]);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Automasi Gagal: ' . $e->getMessage());
                }
            }
        }

        return $next($request);
    }
}
