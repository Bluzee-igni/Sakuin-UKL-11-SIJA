<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemasukan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengguna_id')->constrained('pengguna')->cascadeOnDelete();
            $table->string('nama');
            $table->decimal('jumlah', 15, 2);
            $table->date('tanggal');
            $table->string('catatan')->nullable();
            $table->timestamps();

            $table->index(['pengguna_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemasukan');
    }
};
