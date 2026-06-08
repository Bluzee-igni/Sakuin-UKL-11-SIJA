<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\KategoriTransaksi;
use App\Http\Requests\Api\V1\SimpanKategoriTransaksiRequest;
use App\Http\Requests\Api\V1\PerbaruiKategoriTransaksiRequest;
use App\Http\Resources\KategoriTransaksiResource;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class KategoriTransaksiController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $kategori = KategoriTransaksi::where(function ($q) {
                $q->where('pengguna_id', Auth::id())
                  ->orWhere('adalah_default', true);
            })
            ->orderBy('nama')
            ->get();

        return $this->successResponse(
            KategoriTransaksiResource::collection($kategori),
            'Daftar kategori berhasil diambil'
        );
    }

    public function store(SimpanKategoriTransaksiRequest $request)
    {
        $kategori = KategoriTransaksi::create(array_merge(
            $request->validated(),
            [
                'pengguna_id' => Auth::id(),
                'slug' => Str::slug($request->nama) . '-' . uniqid(),
                'adalah_default' => false,
            ]
        ));

        return $this->successResponse(
            new KategoriTransaksiResource($kategori),
            'Kategori berhasil dibuat',
            201
        );
    }

    public function show($id)
    {
        $kategori = KategoriTransaksi::where(function ($q) {
                $q->where('pengguna_id', Auth::id())
                  ->orWhere('adalah_default', true);
            })
            ->find($id);

        if (!$kategori) {
            return $this->errorResponse('Kategori tidak ditemukan', [], 404);
        }

        return $this->successResponse(new KategoriTransaksiResource($kategori), 'Detail kategori');
    }

    public function update(PerbaruiKategoriTransaksiRequest $request, $id)
    {
        $kategori = KategoriTransaksi::milikPengguna(Auth::id())->find($id);

        if (!$kategori) {
            return $this->errorResponse('Kategori tidak ditemukan atau tidak dapat diubah', [], 404);
        }

        $data = $request->validated();
        if (isset($data['nama'])) {
            $data['slug'] = Str::slug($data['nama']) . '-' . uniqid();
        }

        $kategori->update($data);

        return $this->successResponse(new KategoriTransaksiResource($kategori), 'Kategori berhasil diperbarui');
    }

    public function destroy($id)
    {
        $kategori = KategoriTransaksi::milikPengguna(Auth::id())->find($id);

        if (!$kategori) {
            return $this->errorResponse('Kategori tidak ditemukan atau tidak dapat dihapus', [], 404);
        }

        $kategori->delete();

        return $this->successResponse(null, 'Kategori berhasil dihapus');
    }
}
