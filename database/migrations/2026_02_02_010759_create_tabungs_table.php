<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tabungs', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // Nama penabung
            $table->decimal('jumlah_tabung', 15, 2); // Nominal tabungan
            $table->decimal('total_tabungan', 15, 2); // Total akumulasi
            $table->date('tanggal'); // Tanggal menabung
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tabungs');
    }
};