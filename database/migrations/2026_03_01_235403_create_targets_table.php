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
        Schema::create('targets', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();

    $table->string('nama'); // nama target: "Laptop", "HP"
    $table->decimal('harga_target', 15, 2);
    $table->decimal('rencana_per_hari', 15, 2)->nullable(); // optional (buat estimasi awal)
    $table->date('mulai')->nullable();
    $table->boolean('is_active')->default(false);
    $table->boolean('is_done')->default(false);

    $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('targets');
    }
};
