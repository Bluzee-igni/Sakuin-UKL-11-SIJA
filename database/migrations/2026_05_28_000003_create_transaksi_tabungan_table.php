<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_tabungan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('target_tabungan_id')->constrained('target_tabungan')->cascadeOnDelete();
            $table->foreignId('kategori_id')->nullable()->constrained('kategori_transaksi')->nullOnDelete();
            $table->enum('tipe', ['setor', 'tarik'])->default('setor');
            $table->decimal('jumlah', 15, 2);
            $table->date('tanggal_transaksi');
            $table->string('catatan')->nullable();
            $table->string('sumber')->nullable();
            $table->timestamps();

            $table->index(['target_tabungan_id', 'tanggal_transaksi']);
            $table->index('tipe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_tabungan');
    }
};
