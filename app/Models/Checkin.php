<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkin extends Model
{
    use HasFactory;

    protected $fillable = [
        'target_id',
        'tanggal',
        'nominal',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // ✅ Checkin milik Target
    public function target()
    {
        return $this->belongsTo(\App\Models\Target::class);
    }
}