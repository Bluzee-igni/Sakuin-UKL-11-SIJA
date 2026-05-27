<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TargetTabunganResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'deskripsi' => $this->deskripsi,
            'jumlah_target' => (float) $this->jumlah_target,
            'total_terkumpul' => (float) $this->total_terkumpul,
            'persentase_progres' => (float) $this->persentase_progres,
            'rencana_harian' => $this->rencana_harian ? (float) $this->rencana_harian : null,
            'tanggal_mulai' => $this->tanggal_mulai ? $this->tanggal_mulai->format('Y-m-d') : null,
            'tanggal_target' => $this->tanggal_target ? $this->tanggal_target->format('Y-m-d') : null,
            'status' => $this->status,
            'prioritas' => $this->prioritas,
            'ikon' => $this->ikon,
            'warna' => $this->warna,
            'jumlah_transaksi' => $this->whenCounted('transaksiTabungan'),
            'dibuat_pada' => $this->created_at->toIso8601String(),
        ];
    }
}
