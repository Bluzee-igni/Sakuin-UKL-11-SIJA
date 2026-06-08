<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static> bawaan()
 * @method static \Illuminate\Database\Eloquent\Builder<static> milikPengguna(int $penggunaId)
 */
class KategoriTransaksi extends Model
{
    use HasFactory;

    protected $table = 'kategori_transaksi';

    protected $fillable = [
        'pengguna_id',
        'nama',
        'slug',
        'ikon',
        'warna',
        'adalah_default',
    ];

    protected $casts = [
        'adalah_default' => 'boolean',
    ];

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id');
    }

    public function transaksiTabungan(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TransaksiTabungan::class, 'kategori_id');
    }

    public function scopeBawaan($query)
    {
        return $query->where('adalah_default', true);
    }

    public function scopeMilikPengguna($query, $penggunaId)
    {
        return $query->where('pengguna_id', $penggunaId);
    }
}
