<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\TransaksiTabungan;
use App\Models\TargetTabungan;
use App\Http\Requests\Api\V1\SimpanTransaksiTabunganRequest;
use App\Http\Resources\TransaksiTabunganResource;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransaksiTabunganController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = TransaksiTabungan::with(['kategori', 'targetTabungan'])
            ->whereHas('targetTabungan', function ($q) {
                $q->where('pengguna_id', Auth::id());
            })
            ->latest('tanggal_transaksi');

        if ($request->has('target_id')) {
            $query->where('target_tabungan_id', $request->target_id);
        }

        if ($request->has('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        $transaksi = $query->paginate($request->get('per_page', 15));

        return $this->paginatedResponse($transaksi, 'Daftar transaksi berhasil diambil');
    }

    public function store(SimpanTransaksiTabunganRequest $request, $targetId)
    {
        $target = TargetTabungan::milikPengguna(Auth::id())->find($targetId);

        if (!$target) {
            return $this->errorResponse('Target tabungan tidak ditemukan', [], 404);
        }

        $transaksi = TransaksiTabungan::create(array_merge(
            $request->validated(),
            ['target_tabungan_id' => $target->id]
        ));

        // Jika transaksi ini membuat target selesai
        if ($target->total_terkumpul >= $target->jumlah_target && $target->status !== 'selesai') {
            $target->update(['status' => 'selesai']);
        }

        return $this->successResponse(
            new TransaksiTabunganResource($transaksi->load(['kategori', 'targetTabungan'])),
            'Transaksi berhasil dicatat',
            201
        );
    }

    public function update(Request $request, $id)
    {
        $transaksi = TransaksiTabungan::whereHas('targetTabungan', function ($q) {
            $q->where('pengguna_id', Auth::id());
        })->find($id);

        if (!$transaksi) {
            return $this->errorResponse('Transaksi tidak ditemukan', [], 404);
        }

        $transaksi->update($request->only(['jumlah', 'catatan', 'tanggal_transaksi']));

        return $this->successResponse(
            new TransaksiTabunganResource($transaksi->load(['kategori', 'targetTabungan'])),
            'Transaksi berhasil diperbarui'
        );
    }

    public function show($id)
    {
        $transaksi = TransaksiTabungan::with(['kategori', 'targetTabungan'])
            ->whereHas('targetTabungan', function ($q) {
                $q->where('pengguna_id', Auth::id());
            })
            ->find($id);

        if (!$transaksi) {
            return $this->errorResponse('Transaksi tidak ditemukan', [], 404);
        }

        return $this->successResponse(new TransaksiTabunganResource($transaksi), 'Detail transaksi');
    }

    public function destroy($id)
    {
        $transaksi = TransaksiTabungan::whereHas('targetTabungan', function ($q) {
            $q->where('pengguna_id', Auth::id());
        })->find($id);

        if (!$transaksi) {
            return $this->errorResponse('Transaksi tidak ditemukan', [], 404);
        }

        $transaksi->delete();

        return $this->successResponse(null, 'Transaksi berhasil dihapus');
    }
}
