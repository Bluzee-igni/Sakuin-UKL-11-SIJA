<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_otomatis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengguna_id')->constrained('pengguna')->cascadeOnDelete();
            $table->enum('tipe', ['pemasukan', 'pengeluaran']);
            $table->string('nama');
            $table->decimal('jumlah', 15, 2);
            $table->string('kategori')->nullable();
            $table->integer('tanggal_rutin'); // 1-31
            $table->string('bulan_terakhir_proses')->nullable(); // format: YYYY-MM
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_otomatis');
    }
};
