<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Target extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama',
        'harga_target',
        'rencana_per_hari',
        'mulai',
        'is_active',
        'is_done',
    ];

    public function checkins()
{
    return $this->hasMany(\App\Models\Checkin::class);
}

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
    
}