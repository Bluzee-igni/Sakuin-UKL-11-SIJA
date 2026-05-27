<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotifikasiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'judul' => $this->judul,
            'pesan' => $this->pesan,
            'tipe' => $this->tipe,
            'data' => $this->data,
            'dibaca_pada' => $this->dibaca_pada ? $this->dibaca_pada->toIso8601String() : null,
            'dibuat_pada' => $this->created_at->toIso8601String(),
        ];
    }
}
