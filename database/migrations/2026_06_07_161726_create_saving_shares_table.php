<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saving_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('pengguna')->cascadeOnDelete();
            $table->foreignId('target_tabungan_id')->constrained('target_tabungan')->cascadeOnDelete();
            $table->decimal('jumlah_terkumpul', 15, 2);
            $table->decimal('persentase', 5, 2);
            $table->string('pesan')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saving_shares');
    }
};