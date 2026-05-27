<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Checkin;

class SaldoController extends Controller
{
    public function getSaldo(Request $request)
    {
        // Karena API dipanggil menggunakan middleware web, kita bisa menggunakan $request->user()
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        $userId = $user->id;

        // Optimasi: Menghitung total langsung di database dengan SUM (hindari query ->get() yang berat)
        $totalIncome = Income::where('user_id', $userId)->sum('nominal') ?? 0;
        $totalExpense = Expense::where('user_id', $userId)->sum('nominal') ?? 0;
        
        // Optimasi: Menggunakan JOIN lebih cepat daripada whereHas karena langsung di level query MySQL
        $totalTabungan = Checkin::join('targets', 'checkins.target_id', '=', 'targets.id')
            ->where('targets.user_id', $userId)
            ->sum('checkins.nominal') ?? 0;

        $saldo = $totalIncome - $totalExpense - $totalTabungan;

        return response()->json([
            'status' => 'success',
            'saldo' => (int) $saldo
        ]);
    }
}
