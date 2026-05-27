<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('target_tabungan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengguna_id')->constrained('pengguna')->cascadeOnDelete();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->decimal('jumlah_target', 15, 2);
            $table->decimal('rencana_harian', 15, 2)->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_target')->nullable();
            $table->enum('status', ['aktif', 'selesai', 'dijeda', 'dibatalkan'])->default('aktif');
            $table->enum('prioritas', ['rendah', 'sedang', 'tinggi'])->default('sedang');
            $table->string('ikon')->nullable();
            $table->string('warna')->nullable();
            $table->timestamps();

            $table->index(['pengguna_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('target_tabungan');
    }
};
