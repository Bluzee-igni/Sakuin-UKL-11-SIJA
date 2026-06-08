<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static> setoran()
 * @method static \Illuminate\Database\Eloquent\Builder<static> penarikan()
 * @method static \Illuminate\Database\Eloquent\Builder<static> rentangTanggal($mulai, $selesai)
 *
 * @property int $id
 * @property int $target_tabungan_id
 * @property string $tipe
 * @property float|int $jumlah
 */
class TransaksiTabungan extends Model
{
    use HasFactory;

    protected $table = 'transaksi_tabungan';

    protected $fillable = [
        'target_tabungan_id',
        'kategori_id',
        'tipe',
        'jumlah',
        'tanggal_transaksi',
        'catatan',
        'sumber',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'date',
    ];

    public function targetTabungan(): BelongsTo
    {
        return $this->belongsTo(TargetTabungan::class, 'target_tabungan_id');
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriTransaksi::class, 'kategori_id');
    }

    public function scopeSetoran($query)
    {
        return $query->where('tipe', 'setor');
    }

    public function scopePenarikan($query)
    {
        return $query->where('tipe', 'tarik');
    }

    public function scopeRentangTanggal($query, $mulai, $selesai)
    {
        return $query->whereBetween('tanggal_transaksi', [$mulai, $selesai]);
    }
}
