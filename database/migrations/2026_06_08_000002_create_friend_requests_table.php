<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('friend_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengirim_id')->constrained('pengguna')->cascadeOnDelete();
            $table->foreignId('penerima_id')->constrained('pengguna')->cascadeOnDelete();
            $table->enum('status', ['pending', 'diterima', 'ditolak'])->default('pending');
            $table->timestamps();

            $table->unique(['pengirim_id', 'penerima_id']);
            $table->index(['penerima_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('friend_requests');
    }
};
