<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutomatedTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tipe',
        'nama',
        'nominal',
        'kategori',
        'tanggal_rutin',
        'last_processed_month',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
