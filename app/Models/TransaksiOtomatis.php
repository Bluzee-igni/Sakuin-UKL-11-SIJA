<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaksiOtomatis extends Model
{
    use HasFactory;

    protected $table = 'transaksi_otomatis';

    protected $fillable = [
        'pengguna_id',
        'tipe',
        'nama',
        'jumlah',
        'kategori',
        'tanggal_rutin',
        'bulan_terakhir_proses',
    ];

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id');
    }
}
