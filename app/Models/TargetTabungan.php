<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static> aktif()
 * @method static \Illuminate\Database\Eloquent\Builder<static> selesai()
 * @method static \Illuminate\Database\Eloquent\Builder<static> milikPengguna(int $penggunaId)
 *
 * @property int $id
 * @property int $pengguna_id
 * @property string $nama
 * @property float|int $jumlah_target
 * @property string $status
 * @property float|int $total_terkumpul
 * @property float|int $persentase_progres
 */
class TargetTabungan extends Model
{
    use HasFactory;

    protected $table = 'target_tabungan';

    protected $fillable = [
        'pengguna_id',
        'nama',
        'deskripsi',
        'jumlah_target',
        'rencana_harian',
        'tanggal_mulai',
        'tanggal_target',
        'status',
        'prioritas',
        'ikon',
        'warna',
        'gambar',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_target' => 'date',
    ];

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id');
    }

    public function transaksiTabungan(): HasMany
    {
        return $this->hasMany(TransaksiTabungan::class, 'target_tabungan_id');
    }

    public function riwayatProgres(): HasMany
    {
        return $this->hasMany(RiwayatProgres::class, 'target_tabungan_id');
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeSelesai($query)
    {
        return $query->where('status', 'selesai');
    }

    public function scopeMilikPengguna($query, $penggunaId)
    {
        return $query->where('pengguna_id', $penggunaId);
    }

    public function getTotalTerkumpulAttribute()
    {
        $setoran = $this->transaksiTabungan()->where('tipe', 'setor')->sum('jumlah');
        $penarikan = $this->transaksiTabungan()->where('tipe', 'tarik')->sum('jumlah');
        return max(0, $setoran - $penarikan);
    }

    public function getPersentaseProgresAttribute()
    {
        if ($this->jumlah_target <= 0) return 0;
        return round(min(100, ($this->total_terkumpul / $this->jumlah_target) * 100), 2);
    }

    public function getSudahSelesaiAttribute()
    {
        return $this->total_terkumpul >= $this->jumlah_target;
    }
}
