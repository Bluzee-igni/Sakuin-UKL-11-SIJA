<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tabung extends Model
{
    use HasFactory;

    // Menentukan kolom mana saja yang boleh diisi oleh user
    protected $fillable = [
        'nama',
        'jumlah_tabung',
        'total_tabungan',
        'tanggal'
    ];
}