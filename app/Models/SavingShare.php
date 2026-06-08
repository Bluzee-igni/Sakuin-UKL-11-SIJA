<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static> latest()
 */
class SavingShare extends Model
{
    use HasFactory;

    protected $table = 'saving_shares';

    protected $fillable = [
        'user_id',
        'target_tabungan_id',
        'jumlah_terkumpul',
        'persentase',
        'pesan',
    ];

    protected $casts = [
        'jumlah_terkumpul' => 'decimal:2',
        'persentase' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'user_id');
    }

    public function target(): BelongsTo
    {
        return $this->belongsTo(TargetTabungan::class, 'target_tabungan_id');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(PostLike::class, 'post_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(PostComment::class, 'post_id');
    }

    public function isLikedByUser(int $userId): bool
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }

    public function scopeLatest(\Illuminate\Database\Eloquent\Builder $query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
