<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pengguna extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'pengguna';

    protected $fillable = [
        'nama',
        'email',
        'kata_sandi',
        'anggaran_bulanan',
        'avatar',
        'mata_uang',
    ];

    protected $hidden = [
        'kata_sandi',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_terverifikasi' => 'datetime',
            'kata_sandi' => 'hashed',
        ];
    }

    public function getAuthPassword()
    {
        return $this->kata_sandi;
    }

    public function targetTabungan(): HasMany
    {
        return $this->hasMany(TargetTabungan::class, 'pengguna_id');
    }

    public function kategoriTransaksi(): HasMany
    {
        return $this->hasMany(KategoriTransaksi::class, 'pengguna_id');
    }

    public function notifikasi(): HasMany
    {
        return $this->hasMany(Notifikasi::class, 'pengguna_id');
    }

    public function pemasukan(): HasMany
    {
        return $this->hasMany(Pemasukan::class, 'pengguna_id');
    }

    public function pengeluaran(): HasMany
    {
        return $this->hasMany(Pengeluaran::class, 'pengguna_id');
    }

    public function transaksiOtomatis(): HasMany
    {
        return $this->hasMany(TransaksiOtomatis::class, 'pengguna_id');
    }

    public function pengaturan(): HasMany
    {
        return $this->hasMany(PengaturanPengguna::class, 'pengguna_id');
    }

    /**
     * Get a single setting value with a default fallback.
     */
    public function getSetting(string $kunci, $default = null)
    {
        $setting = $this->pengaturan()->where('kunci', $kunci)->first();
        return $setting ? $setting->nilai : $default;
    }

    public function getFotoUrlAttribute(): ?string
    {
        return $this->avatar ? asset('storage/' . $this->avatar) : null;
    }

    public function getInisialAttribute(): string
    {
        return strtoupper(substr($this->nama ?? 'U', 0, 1));
    }
}
