<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop foreign keys di expenses
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['expense_category_id']);
            $table->dropForeign(['budget_plan_id']);
            $table->dropColumn(['expense_category_id', 'budget_plan_id']);
            
            // Tambahkan kolom yang dibutuhkan user
            $table->string('nama')->after('user_id');
            $table->enum('kategori', ['Kebutuhan Pokok', 'Mendesak', 'Kebutuhan Lain'])->after('nominal');
        });

        // 2. Modifikasi incomes
        Schema::table('incomes', function (Blueprint $table) {
            $table->dropColumn(['tipe', 'sumber']);
            $table->string('nama')->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            $table->dropColumn('nama');
            $table->enum('tipe', ['gaji', 'uang_bulanan', 'freelance', 'bonus', 'lainnya'])->default('lainnya');
            $table->string('sumber')->nullable();
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn(['nama', 'kategori']);
            $table->foreignId('expense_category_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('budget_plan_id')->nullable()->constrained()->nullOnDelete();
        });
    }
};
