<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\AutomatedTransaction;
use App\Models\Income;
use App\Models\Expense;
use Carbon\Carbon;

class CheckAutomatedTransactions
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $now = Carbon::now();
            $currentMonthString = $now->format('Y-m');
            $currentDay = $now->day;

            $autos = AutomatedTransaction::where('user_id', $user->id)
                ->where(function ($query) use ($currentMonthString) {
                    $query->where('last_processed_month', '<', $currentMonthString)
                          ->orWhereNull('last_processed_month');
                })
                ->where('tanggal_rutin', '<=', $currentDay)
                ->get();

            foreach ($autos as $auto) {
                try {
                    // Proses transaksi
                    if ($auto->tipe === 'income') {
                        Income::create([
                            'user_id' => $user->id,
                            'nama' => $auto->nama,
                            'nominal' => $auto->nominal,
                            'tanggal' => $now->format('Y-m') . '-' . sprintf('%02d', $auto->tanggal_rutin),
                            'catatan' => 'Sistem Otomatis: Gaji/Pemasukan Rutin',
                        ]);
                    } else {
                        Expense::create([
                            'user_id' => $user->id,
                            'nama' => $auto->nama,
                            'nominal' => $auto->nominal,
                            'kategori' => $auto->kategori ?? 'Lainnya', // Fallback jika kategori kosong
                            'tanggal' => $now->format('Y-m') . '-' . sprintf('%02d', $auto->tanggal_rutin),
                            'catatan' => 'Sistem Otomatis: Pengeluaran/Tagihan Rutin',
                        ]);
                    }

                    // Update status
                    $auto->update(['last_processed_month' => $currentMonthString]);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Automasi Gagal: ' . $e->getMessage());
                }
            }
        }

        return $next($request);
    }
}
