<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('friends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengguna_id')->constrained('pengguna')->cascadeOnDelete();
            $table->foreignId('teman_id')->constrained('pengguna')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['pengguna_id', 'teman_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('friends');
    }
};
