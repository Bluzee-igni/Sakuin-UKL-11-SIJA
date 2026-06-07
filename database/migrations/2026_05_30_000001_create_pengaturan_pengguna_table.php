<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturan_pengguna', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengguna_id')->constrained('pengguna')->cascadeOnDelete();
            $table->string('kunci', 100); // e.g. 'tema', 'compact_mode', 'blur_saldo'
            $table->text('nilai')->nullable(); // Stores string, boolean as '1'/'0', or JSON
            $table->timestamps();

            $table->unique(['pengguna_id', 'kunci']);
            $table->index('kunci');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan_pengguna');
    }
};
