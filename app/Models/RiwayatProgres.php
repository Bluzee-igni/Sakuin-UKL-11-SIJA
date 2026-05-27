<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatProgres extends Model
{
    use HasFactory;

    protected $table = 'riwayat_progres';

    protected $fillable = [
        'target_tabungan_id',
        'jumlah_terkumpul',
        'persentase',
        'hari_beruntun',
        'tanggal_catat',
        'catatan',
    ];

    protected $casts = [
        'tanggal_catat' => 'date',
    ];

    public function targetTabungan(): BelongsTo
    {
        return $this->belongsTo(TargetTabungan::class, 'target_tabungan_id');
    }
}
