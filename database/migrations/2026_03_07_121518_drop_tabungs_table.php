<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('tabungs');
    }

    public function down(): void
    {
        Schema::create('tabungs', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->decimal('jumlah_tabung', 15, 2);
            $table->decimal('total_tabungan', 15, 2);
            $table->date('tanggal');
            $table->timestamps();
        });
    }
};
