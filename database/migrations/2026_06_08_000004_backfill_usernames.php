<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $users = DB::table('pengguna')->whereNull('username')->orWhere('username', '')->get();

        foreach ($users as $user) {
            $baseUsername = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', explode('@', $user->email)[0]));
            $username = $baseUsername;
            $counter = 1;
            while (DB::table('pengguna')->where('username', $username)->where('id', '!=', $user->id)->exists()) {
                $username = $baseUsername . $counter;
                $counter++;
            }
            DB::table('pengguna')->where('id', $user->id)->update(['username' => $username]);
        }
    }

    public function down(): void
    {
        // No rollback for data migration
    }
};
