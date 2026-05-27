<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_progres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('target_tabungan_id')->constrained('target_tabungan')->cascadeOnDelete();
            $table->decimal('jumlah_terkumpul', 15, 2);
            $table->decimal('persentase', 5, 2);
            $table->integer('hari_beruntun')->default(0);
            $table->date('tanggal_catat');
            $table->string('catatan')->nullable();
            $table->timestamps();

            $table->index(['target_tabungan_id', 'tanggal_catat']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_progres');
    }
};
