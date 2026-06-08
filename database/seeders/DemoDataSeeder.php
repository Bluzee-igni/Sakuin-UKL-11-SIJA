<?php

namespace Database\Seeders;

use App\Models\Friend;
use App\Models\FriendRequest;
use App\Models\Notifikasi;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use App\Models\Pengguna;
use App\Models\PengaturanPengguna;
use App\Models\PostComment;
use App\Models\PostLike;
use App\Models\SavingShare;
use App\Models\TargetTabungan;
use App\Models\TransaksiTabungan;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================================
        // 1. Create 3 demo users
        // ============================================================
        $atha = Pengguna::create([
            'username' => 'atha',
            'nama' => 'Atha',
            'email' => 'admin@gmail.com',
            'kata_sandi' => Hash::make('password'),
            'anggaran_bulanan' => 3000000,
            'mata_uang' => 'IDR',
        ]);

        $rina = Pengguna::create([
            'username' => 'rina',
            'nama' => 'Rina Amelia',
            'email' => 'rina@gmail.com',
            'kata_sandi' => Hash::make('password'),
            'anggaran_bulanan' => 2000000,
            'mata_uang' => 'IDR',
        ]);

        $budi = Pengguna::create([
            'username' => 'budi',
            'nama' => 'Budi Hartono',
            'email' => 'budi@gmail.com',
            'kata_sandi' => Hash::make('password'),
            'anggaran_bulanan' => 5000000,
            'mata_uang' => 'IDR',
        ]);

        // ============================================================
        // 2. Settings for each user
        // ============================================================
        $settings = [
            ['kunci' => 'theme', 'nilai' => 'light'],
            ['kunci' => 'notif_menabung', 'nilai' => '1'],
            ['kunci' => 'notif_target', 'nilai' => '1'],
            ['kunci' => 'notif_anggaran', 'nilai' => '1'],
            ['kunci' => 'compact_mode', 'nilai' => '0'],
            ['kunci' => 'animasi', 'nilai' => '1'],
            ['kunci' => 'mode_privasi', 'nilai' => '0'],
            ['kunci' => 'urutan_widget', 'nilai' => '["streak","target","aktivitas","riwayat"]'],
        ];

        foreach ([$atha, $rina, $budi] as $user) {
            foreach ($settings as $s) {
                PengaturanPengguna::create([
                    'pengguna_id' => $user->id,
                    'kunci' => $s['kunci'],
                    'nilai' => $s['nilai'],
                ]);
            }
        }

        // ============================================================
        // 3. Create default transaction categories
        // ============================================================
        $categories = ['Makanan', 'Transportasi', 'Hiburan', 'Belanja', 'Tagihan', 'Kesehatan', 'Pendidikan', 'Lainnya'];
        foreach ([$atha, $rina, $budi] as $user) {
            foreach ($categories as $cat) {
                $user->kategoriTransaksi()->create([
                    'nama' => $cat,
                    'slug' => str($cat)->slug() . '-' . $user->id,
                    'ikon' => match ($cat) {
                        'Makanan' => 'ph-hamburger',
                        'Transportasi' => 'ph-bus',
                        'Hiburan' => 'ph-game-controller',
                        'Belanja' => 'ph-shopping-bag',
                        'Tagihan' => 'ph-receipt',
                        'Kesehatan' => 'ph-first-aid',
                        'Pendidikan' => 'ph-book',
                        default => 'ph-coin',
                    },
                    'warna' => match ($cat) {
                        'Makanan' => '#ef4444',
                        'Transportasi' => '#3b82f6',
                        'Hiburan' => '#f59e0b',
                        'Belanja' => '#ec4899',
                        'Tagihan' => '#8b5cf6',
                        'Kesehatan' => '#10b981',
                        'Pendidikan' => '#06b6d4',
                        default => '#6b7280',
                    },
                    'adalah_default' => true,
                ]);
            }
        }

        // ============================================================
        // 4. Income & Expense records (last 3 months)
        // ============================================================
        $athasIncome = [
            ['nama' => 'Gaji Bulanan', 'jumlah' => 5000000, 'tanggal' => '2026-05-01'],
            ['nama' => 'Gaji Bulanan', 'jumlah' => 5000000, 'tanggal' => '2026-06-01'],
        ];
        $rinasIncome = [
            ['nama' => 'Gaji', 'jumlah' => 3500000, 'tanggal' => '2026-05-01'],
            ['nama' => 'Gaji', 'jumlah' => 3500000, 'tanggal' => '2026-06-01'],
        ];
        $budisIncome = [
            ['nama' => 'Gaji Bulanan', 'jumlah' => 8000000, 'tanggal' => '2026-05-01'],
            ['nama' => 'Gaji Bulanan', 'jumlah' => 8000000, 'tanggal' => '2026-06-01'],
            ['nama' => 'Freelance', 'jumlah' => 2000000, 'tanggal' => '2026-05-15'],
        ];

        foreach ($athasIncome as $inc) {
            $atha->pemasukan()->create($inc);
        }
        foreach ($rinasIncome as $inc) {
            $rina->pemasukan()->create($inc);
        }
        foreach ($budisIncome as $inc) {
            $budi->pemasukan()->create($inc);
        }

        // Expenses
        $athasExpenses = [
            ['nama' => 'Makan siang', 'jumlah' => 150000, 'kategori' => 'Makanan', 'tanggal' => '2026-05-02'],
            ['nama' => 'Transportasi', 'jumlah' => 20000, 'kategori' => 'Transportasi', 'tanggal' => '2026-05-03'],
            ['nama' => 'Belanja bulanan', 'jumlah' => 350000, 'kategori' => 'Belanja', 'tanggal' => '2026-05-05'],
            ['nama' => 'Listrik & Air', 'jumlah' => 500000, 'kategori' => 'Tagihan', 'tanggal' => '2026-05-10'],
            ['nama' => 'Nonton bioskop', 'jumlah' => 75000, 'kategori' => 'Hiburan', 'tanggal' => '2026-05-12'],
        ];
        foreach ($athasExpenses as $exp) {
            $atha->pengeluaran()->create($exp);
        }

        $rinasExpenses = [
            ['nama' => 'Makan', 'jumlah' => 100000, 'kategori' => 'Makanan', 'tanggal' => '2026-05-02'],
            ['nama' => 'Belanja', 'jumlah' => 200000, 'kategori' => 'Belanja', 'tanggal' => '2026-05-06'],
            ['nama' => 'Kosan', 'jumlah' => 300000, 'kategori' => 'Tagihan', 'tanggal' => '2026-05-08'],
        ];
        foreach ($rinasExpenses as $exp) {
            $rina->pengeluaran()->create($exp);
        }

        $budisExpenses = [
            ['nama' => 'Makan keluarga', 'jumlah' => 200000, 'kategori' => 'Makanan', 'tanggal' => '2026-05-02'],
            ['nama' => 'Sewa rumah', 'jumlah' => 1500000, 'kategori' => 'Tagihan', 'tanggal' => '2026-05-01'],
            ['nama' => 'Belanja bulanan', 'jumlah' => 500000, 'kategori' => 'Belanja', 'tanggal' => '2026-05-04'],
            ['nama' => 'Cek kesehatan', 'jumlah' => 250000, 'kategori' => 'Kesehatan', 'tanggal' => '2026-05-10'],
        ];
        foreach ($budisExpenses as $exp) {
            $budi->pengeluaran()->create($exp);
        }

        // ============================================================
        // 5. Savings targets
        // ============================================================
        $athaTarget1 = TargetTabungan::create([
            'pengguna_id' => $atha->id,
            'nama' => 'Liburan ke Bali',
            'jumlah_target' => 3000000,
            'rencana_harian' => 50000,
            'tanggal_mulai' => '2026-04-01',
            'status' => 'aktif',
        ]);

        $athaTarget2 = TargetTabungan::create([
            'pengguna_id' => $atha->id,
            'nama' => 'PS5 Baru',
            'jumlah_target' => 8000000,
            'rencana_harian' => 30000,
            'tanggal_mulai' => '2026-05-01',
            'status' => 'aktif',
        ]);

        $rinaTarget1 = TargetTabungan::create([
            'pengguna_id' => $rina->id,
            'nama' => 'Laptop Kuliah',
            'jumlah_target' => 5000000,
            'rencana_harian' => 25000,
            'tanggal_mulai' => '2026-05-15',
            'status' => 'aktif',
        ]);

        $rinaTarget2 = TargetTabungan::create([
            'pengguna_id' => $rina->id,
            'nama' => 'Buku & Alat Tulis',
            'jumlah_target' => 500000,
            'rencana_harian' => 10000,
            'tanggal_mulai' => '2026-06-01',
            'status' => 'aktif',
        ]);

        $budiTarget1 = TargetTabungan::create([
            'pengguna_id' => $budi->id,
            'nama' => 'Mobil Baru',
            'jumlah_target' => 50000000,
            'rencana_harian' => 100000,
            'tanggal_mulai' => '2026-01-01',
            'status' => 'aktif',
        ]);

        $budiTargetSelesai = TargetTabungan::create([
            'pengguna_id' => $budi->id,
            'nama' => 'Liburan Akhir Tahun',
            'jumlah_target' => 5000000,
            'rencana_harian' => 40000,
            'tanggal_mulai' => '2025-12-01',
            'status' => 'selesai',
        ]);

        // Completed target stays as 'selesai' status

        // ============================================================
        // 6. Saving transactions (for heatmap + streak)
        // Build daily transactions for last 60 days
        // ============================================================
        $now = Carbon::now();

        // Atha: consistent saver, saves ~5-6 days a week
        for ($day = 60; $day >= 0; $day--) {
            $date = $now->copy()->subDays($day);
            // Skip some days (weekends sometimes)
            if ($date->isSaturday() && fake()->boolean(40)) continue;
            if ($date->isSunday() && fake()->boolean(60)) continue;
            if (fake()->boolean(15)) continue; // random skip

            $amount = fake()->numberBetween(10000, 75000);
            TransaksiTabungan::create([
                'target_tabungan_id' => fake()->boolean(60) ? $athaTarget1->id : $athaTarget2->id,
                'tipe' => 'setor',
                'tanggal_transaksi' => $date->format('Y-m-d'),
                'jumlah' => $amount,
                'catatan' => 'Tabungan harian',
            ]);
        }

        // Rina: new saver, started 30 days ago, less consistent
        for ($day = 30; $day >= 0; $day--) {
            $date = $now->copy()->subDays($day);
            if (fake()->boolean(40)) continue; // skips many days
            $amount = fake()->numberBetween(5000, 50000);
            TransaksiTabungan::create([
                'target_tabungan_id' => fake()->boolean(70) ? $rinaTarget1->id : $rinaTarget2->id,
                'tipe' => 'setor',
                'tanggal_transaksi' => $date->format('Y-m-d'),
                'jumlah' => $amount,
                'catatan' => 'Menabung',
            ]);
        }

        // Budi: dedicated saver, saves almost daily for 90 days
        for ($day = 90; $day >= 0; $day--) {
            $date = $now->copy()->subDays($day);
            if (fake()->boolean(5)) continue; // rarely skips
            $amount = fake()->numberBetween(50000, 150000);
            TransaksiTabungan::create([
                'target_tabungan_id' => $budiTarget1->id,
                'tipe' => 'setor',
                'tanggal_transaksi' => $date->format('Y-m-d'),
                'jumlah' => $amount,
                'catatan' => 'Rutin menabung',
            ]);
        }

        // Add some withdrawal transactions
        TransaksiTabungan::create([
            'target_tabungan_id' => $athaTarget1->id,
            'tipe' => 'tarik',
            'tanggal_transaksi' => $now->copy()->subDays(10)->format('Y-m-d'),
            'jumlah' => 200000,
            'catatan' => ' DP hotel',
        ]);

        TransaksiTabungan::create([
            'target_tabungan_id' => $rinaTarget1->id,
            'tipe' => 'tarik',
            'tanggal_transaksi' => $now->copy()->subDays(5)->format('Y-m-d'),
            'jumlah' => 50000,
            'catatan' => 'Keperluan mendadak',
        ]);

        // Also mark completed target as fully funded (setoran yang mencapai target)
        TransaksiTabungan::create([
            'target_tabungan_id' => $budiTargetSelesai->id,
            'tipe' => 'setor',
            'tanggal_transaksi' => '2026-03-10',
            'jumlah' => 5000000,
            'catatan' => 'Pelunasan target liburan',
        ]);

        // ============================================================
        // 7. Social feed: SavingShares, Likes, Comments
        // ============================================================
        // Atha shares his Bali target
        $share1 = SavingShare::create([
            'user_id' => $atha->id,
            'target_tabungan_id' => $athaTarget1->id,
            'jumlah_terkumpul' => $athaTarget1->total_terkumpul,
            'persentase' => $athaTarget1->persentase_progres,
            'pesan' => 'Semakin dekat ke pantai! 🏖️', // keeping this emoji as it's user-generated content
            'created_at' => $now->copy()->subDays(3),
        ]);

        // Budi shares his car target
        $share2 = SavingShare::create([
            'user_id' => $budi->id,
            'target_tabungan_id' => $budiTarget1->id,
            'jumlah_terkumpul' => $budiTarget1->total_terkumpul,
            'persentase' => $budiTarget1->persentase_progres,
            'pesan' => 'Konsisten menabung setiap hari!',
            'created_at' => $now->copy()->subDays(1),
        ]);

        // Budi also shares his completed target
        $share3 = SavingShare::create([
            'user_id' => $budi->id,
            'target_tabungan_id' => $budiTargetSelesai->id,
            'jumlah_terkumpul' => 5000000,
            'persentase' => 100,
            'pesan' => 'Liburan akhir tahun tercapai! 🎉',
            'created_at' => $now->copy()->subDays(7),
        ]);

        // Rina shares her laptop target
        $share4 = SavingShare::create([
            'user_id' => $rina->id,
            'target_tabungan_id' => $rinaTarget1->id,
            'jumlah_terkumpul' => $rinaTarget1->total_terkumpul,
            'persentase' => $rinaTarget1->persentase_progres,
            'pesan' => 'Mulai menabung untuk laptop impian!',
            'created_at' => $now->copy()->subDays(2),
        ]);

        // ============================================================
        // 8. Likes & Comments on shares
        // ============================================================
        // Budi likes Atha's Bali post
        PostLike::create([
            'post_id' => $share1->id,
            'user_id' => $budi->id,
            'created_at' => $now->copy()->subDays(3)->addHour(),
        ]);

        // Rina likes Atha's Bali post
        PostLike::create([
            'post_id' => $share1->id,
            'user_id' => $rina->id,
            'created_at' => $now->copy()->subDays(3)->addHours(2),
        ]);

        // Atha likes Budi's car post
        PostLike::create([
            'post_id' => $share2->id,
            'user_id' => $atha->id,
            'created_at' => $now->copy()->subDay()->addHour(),
        ]);

        // Rina likes Budi's completed target
        PostLike::create([
            'post_id' => $share3->id,
            'user_id' => $rina->id,
            'created_at' => $now->copy()->subDays(7)->addHour(),
        ]);

        // Comments
        PostComment::create([
            'post_id' => $share1->id,
            'user_id' => $budi->id,
            'comment' => 'Mantap! Semoga cepet tercapai 🔥',
            'created_at' => $now->copy()->subDays(3)->addHours(3),
        ]);

        PostComment::create([
            'post_id' => $share1->id,
            'user_id' => $rina->id,
            'comment' => 'Keren banget! Bali favoritku juga',
            'created_at' => $now->copy()->subDays(3)->addHours(5),
        ]);

        PostComment::create([
            'post_id' => $share2->id,
            'user_id' => $atha->id,
            'comment' => 'Salut! Konsisten banget 👍',
            'created_at' => $now->copy()->subDay()->addHours(2),
        ]);

        PostComment::create([
            'post_id' => $share3->id,
            'user_id' => $atha->id,
            'comment' => 'Selamat! Liburan di mana nih?',
            'created_at' => $now->copy()->subDays(7)->addHours(2),
        ]);

        PostComment::create([
            'post_id' => $share4->id,
            'user_id' => $atha->id,
            'comment' => 'Semangat! Laptop apa yang mau dibeli?',
            'created_at' => $now->copy()->subDays(2)->addHour(),
        ]);

        // ============================================================
        // 9. Friend relationships
        // ============================================================
        // Atha & Budi are friends
        Friend::create(['pengguna_id' => $atha->id, 'teman_id' => $budi->id]);

        // Atha sent friend request to Rina (pending)
        FriendRequest::create([
            'pengirim_id' => $atha->id,
            'penerima_id' => $rina->id,
            'status' => 'pending',
        ]);

        // ============================================================
        // 10. Notifications
        // ============================================================
        // When Budi liked Atha's post
        Notifikasi::create([
            'pengguna_id' => $atha->id,
            'tipe' => 'info',
            'judul' => 'Menyukai Progres Anda',
            'pesan' => 'Budi Hartono menyukai progres tabungan Anda',
            'data' => ['post_id' => $share1->id, 'user_id' => $budi->id, 'jenis' => 'like'],
            'dibaca_pada' => null,
            'created_at' => $now->copy()->subDays(3)->addHour(),
        ]);

        // When Budi commented on Atha's post
        Notifikasi::create([
            'pengguna_id' => $atha->id,
            'tipe' => 'info',
            'judul' => 'Komentar Baru',
            'pesan' => 'Budi Hartono mengomentari postingan Anda: "Mantap! Semoga cepet tercapai 🔥"',
            'data' => ['post_id' => $share1->id, 'user_id' => $budi->id, 'comment' => 'Mantap! Semoga cepet tercapai 🔥', 'jenis' => 'comment'],
            'dibaca_pada' => null,
            'created_at' => $now->copy()->subDays(3)->addHours(3),
        ]);

        // When Atha sent friend request to Rina
        Notifikasi::create([
            'pengguna_id' => $rina->id,
            'tipe' => 'info',
            'judul' => 'Permintaan Teman',
            'pesan' => 'Atha mengirim permintaan teman',
            'data' => ['pengirim_id' => $atha->id, 'jenis' => 'friend_request'],
            'dibaca_pada' => null,
            'created_at' => $now->copy()->subDay(),
        ]);

        $this->command->info('Demo data created successfully!');
        $this->command->info('Users: atha (admin@gmail.com), rina (rina@gmail.com), budi (budi@gmail.com)');
        $this->command->info('Password for all: password');
    }
}
