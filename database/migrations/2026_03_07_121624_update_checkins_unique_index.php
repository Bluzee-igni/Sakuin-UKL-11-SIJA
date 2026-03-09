<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('checkins', function (Blueprint $table) {
            // buat index baru untuk foreign key target_id dulu
            $table->index('target_id', 'checkins_target_id_index');
        });

        Schema::table('checkins', function (Blueprint $table) {
            // baru hapus unique lama
            $table->dropUnique('checkins_target_id_tanggal_unique');
        });

        Schema::table('checkins', function (Blueprint $table) {
            // ganti dengan index biasa
            $table->index(['target_id', 'tanggal'], 'checkins_target_id_tanggal_index');
        });
    }

    public function down(): void
    {
        Schema::table('checkins', function (Blueprint $table) {
            $table->dropIndex('checkins_target_id_tanggal_index');
        });

        Schema::table('checkins', function (Blueprint $table) {
            $table->unique(['target_id', 'tanggal'], 'checkins_target_id_tanggal_unique');
        });

        Schema::table('checkins', function (Blueprint $table) {
            $table->dropIndex('checkins_target_id_index');
        });
    }
};