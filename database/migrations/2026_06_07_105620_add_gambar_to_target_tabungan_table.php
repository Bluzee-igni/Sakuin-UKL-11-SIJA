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
        Schema::table('target_tabungan', function (Blueprint $table) {
            $table->string('gambar')->nullable()->after('warna');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('target_tabungan', function (Blueprint $table) {
            $table->dropColumn('gambar');
        });
    }
};
