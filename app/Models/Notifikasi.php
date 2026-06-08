<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static> belumDibaca()
 * @method static \Illuminate\Database\Eloquent\Builder<static> berdasarkanTipe(string $tipe)
 */
class Notifikasi extends Model
{
    use HasFactory;

    protected $table = 'notifikasi';

    protected $fillable = [
        'pengguna_id',
        'judul',
        'pesan',
        'tipe',
        'data',
        'dibaca_pada',
    ];

    protected $casts = [
        'data' => 'array',
        'dibaca_pada' => 'datetime',
    ];

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id');
    }

    public function scopeBelumDibaca($query)
    {
        return $query->whereNull('dibaca_pada');
    }

    public function scopeBerdasarkanTipe($query, $tipe)
    {
        return $query->where('tipe', $tipe);
    }
}
