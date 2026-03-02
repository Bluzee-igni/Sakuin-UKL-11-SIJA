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
        Schema::create('checkins', function (Blueprint $table) {
    $table->id();
    $table->foreignId('target_id')->constrained()->cascadeOnDelete();

    $table->date('tanggal');
    $table->decimal('nominal', 15, 2);
    $table->string('catatan')->nullable();

    $table->timestamps();

    $table->unique(['target_id', 'tanggal']); // 1 target, 1 check-in per hari
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkins');
    }
};
