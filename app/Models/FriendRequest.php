<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FriendRequest extends Model
{
    use HasFactory;

    protected $table = 'friend_requests';

    protected $fillable = [
        'pengirim_id',
        'penerima_id',
        'status',
    ];

    public function pengirim(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'pengirim_id');
    }

    public function penerima(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'penerima_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
