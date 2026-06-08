<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static> milikPengguna(int $penggunaId)
 * @method static \Illuminate\Database\Eloquent\Builder<static> kunci(string $kunci)
 */
class PengaturanPengguna extends Model
{
    use HasFactory;

    protected $table = 'pengaturan_pengguna';

    protected $fillable = [
        'pengguna_id',
        'kunci',
        'nilai',
    ];

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id');
    }

    public function scopeMilikPengguna($query, $penggunaId)
    {
        return $query->where('pengguna_id', $penggunaId);
    }

    public function scopeKunci($query, $kunci)
    {
        return $query->where('kunci', $kunci);
    }
}
