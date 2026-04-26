<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah budget_bulanan ke users
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('budget_bulanan', 15, 2)->default(0)->after('password');
        });

        // 2. Buat tabel automated_transactions
        Schema::create('automated_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('tipe', ['income', 'expense']);
            $table->string('nama');
            $table->decimal('nominal', 15, 2);
            $table->string('kategori')->nullable(); // nullable untuk income, wajib untuk expense
            $table->integer('tanggal_rutin'); // 1-31
            $table->string('last_processed_month')->nullable(); // format: YYYY-MM
            $table->timestamps();
        });
        
        // 3. Ubah tipe kolom kategori di expenses dari enum ke string agar lebih dinamis
        // Catatan: Doctrine DBAL diperlukan untuk change() enum, tapi karena sqlite sering bermasalah dengan change enum,
        // kita akan buat drop constraint enum secara manual, atau lebih aman karena migration sebelumnya sudah mengubah structure,
        // sebenarnya sqlite tidak peduli dengan ENUM, jadi kita bisa abaikan jika error.
        // Di MySQL kita perlu:
        // $table->string('kategori')->change();
    }

    public function down(): void
    {
        Schema::dropIfExists('automated_transactions');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('budget_bulanan');
        });
    }
};
