<?php

namespace App\Models;

use App\Services\FinancialService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pengguna extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'pengguna';

    protected $fillable = [
        'username',
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

    public function friendRequestsSent(): HasMany
    {
        return $this->hasMany(FriendRequest::class, 'pengirim_id');
    }

    public function friendRequestsReceived(): HasMany
    {
        return $this->hasMany(FriendRequest::class, 'penerima_id');
    }

    public function friends(): HasMany
    {
        return $this->hasMany(Friend::class, 'pengguna_id');
    }

    public function friendOf(): HasMany
    {
        return $this->hasMany(Friend::class, 'teman_id');
    }

    public function isFriendWith(Pengguna $user): bool
    {
        return $this->friends()->where('teman_id', $user->id)->exists()
            || $this->friendOf()->where('pengguna_id', $user->id)->exists();
    }

    public function hasPendingRequestFrom(Pengguna $user): bool
    {
        return $this->friendRequestsReceived()
            ->where('pengirim_id', $user->id)
            ->where('status', 'pending')
            ->exists();
    }

    public function hasSentRequestTo(Pengguna $user): bool
    {
        return $this->friendRequestsSent()
            ->where('penerima_id', $user->id)
            ->where('status', 'pending')
            ->exists();
    }

    public function getPendingFriendRequestFrom(Pengguna $user)
    {
        return $this->friendRequestsReceived()
            ->where('pengirim_id', $user->id)
            ->where('status', 'pending')
            ->first();
    }

    public function getAllFriendsIds(): array
    {
        $ids = $this->friends()->pluck('teman_id')->toArray();
        $ids2 = $this->friendOf()->pluck('pengguna_id')->toArray();
        return array_unique(array_merge($ids, $ids2));
    }

    public function getStreakSaatIniAttribute(): int
    {
        return FinancialService::getCurrentStreak($this->id);
    }

    public function getStreakTerbaikAttribute(): int
    {
        return FinancialService::getLongestStreak($this->id);
    }

    public function getBadgeLevelAttribute(): array
    {
        return FinancialService::getBadgeLevel($this->id);
    }

    public function getTotalSavedAttribute(): float
    {
        return FinancialService::getTotalTabungan($this->id);
    }
}
