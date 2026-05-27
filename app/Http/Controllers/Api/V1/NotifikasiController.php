<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use App\Http\Resources\NotifikasiResource;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $notifikasi = Notifikasi::where('pengguna_id', Auth::id())
            ->latest()
            ->paginate($request->get('per_page', 15));

        return $this->paginatedResponse($notifikasi, 'Daftar notifikasi berhasil diambil');
    }

    public function tandaiBaca($id)
    {
        $notifikasi = Notifikasi::where('pengguna_id', Auth::id())->find($id);

        if (!$notifikasi) {
            return $this->errorResponse('Notifikasi tidak ditemukan', [], 404);
        }

        $notifikasi->update(['dibaca_pada' => now()]);

        return $this->successResponse(new NotifikasiResource($notifikasi), 'Notifikasi ditandai sudah dibaca');
    }

    public function tandaiSemuaBaca()
    {
        Notifikasi::where('pengguna_id', Auth::id())
            ->belumDibaca()
            ->update(['dibaca_pada' => now()]);

        return $this->successResponse(null, 'Semua notifikasi ditandai sudah dibaca');
    }

    public function destroy($id)
    {
        $notifikasi = Notifikasi::where('pengguna_id', Auth::id())->find($id);

        if (!$notifikasi) {
            return $this->errorResponse('Notifikasi tidak ditemukan', [], 404);
        }

        $notifikasi->delete();

        return $this->successResponse(null, 'Notifikasi berhasil dihapus');
    }
}
