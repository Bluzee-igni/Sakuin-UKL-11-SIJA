<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransaksiTabunganResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tipe' => $this->tipe,
            'jumlah' => (float) $this->jumlah,
            'tanggal_transaksi' => $this->tanggal_transaksi->format('Y-m-d'),
            'catatan' => $this->catatan,
            'sumber' => $this->sumber,
            'kategori' => new KategoriTransaksiResource($this->whenLoaded('kategori')),
            'target' => [
                'id' => $this->target_tabungan_id,
                'nama' => $this->whenLoaded('targetTabungan', fn() => $this->targetTabungan->nama)
            ],
            'dibuat_pada' => $this->created_at->toIso8601String(),
        ];
    }
}
