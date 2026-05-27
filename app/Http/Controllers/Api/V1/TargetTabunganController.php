<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\TargetTabungan;
use App\Http\Requests\Api\V1\SimpanTargetTabunganRequest;
use App\Http\Requests\Api\V1\PerbaruiTargetTabunganRequest;
use App\Http\Resources\TargetTabunganResource;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TargetTabunganController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = TargetTabungan::milikPengguna(Auth::id())
            ->withCount('transaksiTabungan')
            ->latest();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $targets = $query->paginate($request->get('per_page', 10));

        return $this->paginatedResponse($targets, 'Daftar target tabungan berhasil diambil');
    }

    public function store(SimpanTargetTabunganRequest $request)
    {
        $target = TargetTabungan::create(array_merge(
            $request->validated(),
            ['pengguna_id' => Auth::id()]
        ));

        return $this->successResponse(
            new TargetTabunganResource($target),
            'Target tabungan berhasil dibuat',
            201
        );
    }

    public function show($id)
    {
        $target = TargetTabungan::milikPengguna(Auth::id())
            ->withCount('transaksiTabungan')
            ->find($id);

        if (!$target) {
            return $this->errorResponse('Target tabungan tidak ditemukan', [], 404);
        }

        return $this->successResponse(new TargetTabunganResource($target), 'Detail target tabungan');
    }

    public function update(PerbaruiTargetTabunganRequest $request, $id)
    {
        $target = TargetTabungan::milikPengguna(Auth::id())->find($id);

        if (!$target) {
            return $this->errorResponse('Target tabungan tidak ditemukan', [], 404);
        }

        $target->update($request->validated());

        return $this->successResponse(new TargetTabunganResource($target), 'Target tabungan berhasil diperbarui');
    }

    public function destroy($id)
    {
        $target = TargetTabungan::milikPengguna(Auth::id())->find($id);

        if (!$target) {
            return $this->errorResponse('Target tabungan tidak ditemukan', [], 404);
        }

        $target->delete();

        return $this->successResponse(null, 'Target tabungan berhasil dihapus');
    }

    public function ubahStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:aktif,selesai,dijeda,dibatalkan']);

        $target = TargetTabungan::milikPengguna(Auth::id())->find($id);

        if (!$target) {
            return $this->errorResponse('Target tabungan tidak ditemukan', [], 404);
        }

        $target->update(['status' => $request->status]);

        return $this->successResponse(new TargetTabunganResource($target), 'Status target tabungan berhasil diubah');
    }

    public function ringkasan($id)
    {
        $target = TargetTabungan::milikPengguna(Auth::id())->find($id);

        if (!$target) {
            return $this->errorResponse('Target tabungan tidak ditemukan', [], 404);
        }

        $summary = [
            'total_target' => (float) $target->jumlah_target,
            'total_terkumpul' => (float) $target->total_terkumpul,
            'persentase' => (float) $target->persentase_progres,
            'sisa_target' => max(0, $target->jumlah_target - $target->total_terkumpul),
            'jumlah_transaksi' => $target->transaksiTabungan()->count(),
            'rata_rata_setoran' => $target->transaksiTabungan()->setoran()->avg('jumlah') ?? 0,
        ];

        return $this->successResponse($summary, 'Ringkasan target tabungan');
    }
}
