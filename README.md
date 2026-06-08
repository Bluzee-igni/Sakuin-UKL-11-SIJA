# Sakuin - Modern Financial & Savings Tracker

**Sakuin** adalah aplikasi manajemen keuangan berbasis web yang didesain khusus untuk membantu pengguna melacak pemasukan, pengeluaran, serta mewujudkan target tabungan impian dengan sentuhan gamifikasi yang memotivasi.

Dibangun dengan pendekatan desain UI/UX bergaya *fintech modern*, Sakuin tidak hanya sekadar alat pencatat, tetapi juga pendamping finansial yang menumbuhkan kebiasaan (habit) menabung yang konsisten.

---

## Fitur Utama (Complete)

### 1. Autentikasi
- **Register** -- Pendaftaran akun baru.
- **Login** -- Masuk ke akun.
- **Logout** -- Keluar dari sesi.
- **Middleware Auth** -- Seluruh halaman (kecuali login/register) dilindungi, memastikan hanya pengguna terautentikasi yang dapat mengakses.

### 2. Dashboard Fintech Modern
Dashboard didesain dengan sistem **Full Width 3-Column Z-Pattern Layout** yang secara optimal memanfaatkan seluruh lebar layar monitor desktop untuk menyajikan informasi krusial tanpa terasa sesak:
- **Hero Balance:** Menampilkan secara instan total saldo tersedia, beserta ringkasan pemasukan dan pengeluaran bulan ini.
- **Combined Target & Action Card:** Progres target tabungan aktif yang disandingkan langsung dengan form pencatatan tabungan yang *compact*.
- **Heatmap Aktivitas:** Visualisasi grid bulanan (GitHub-style) yang menunjukkan riwayat menabung harian -- semakin gelap warna hijau, semakin besar nominal tabungan.
- **Streak Tracker:** Menampilkan rekor hari menabung berturut-turut.
- **Widget yang Bisa Diatur Ulang:** Tata letak widget dashboard dapat disesuaikan oleh pengguna melalui menu pengaturan.
- **Target Lainnya:** Daftar target tabungan non-aktif dengan progress bar.

### 3. Manajemen Target Tabungan
- **CRUD Target:** Buat, lihat, edit, dan hapus target tabungan (misal: "Beli Laptop", "Dana Darurat", "Liburan").
- **Upload Gambar:** Setiap target dapat memiliki gambar motivasi.
- **Set Active Target:** Memprioritaskan satu target utama yang ditampilkan di dashboard.
- **Real-time Progress Bar:** Persentase pencapaian dan nominal terkumpul diperbarui langsung saat user menabung.
- **Tanggal Mulai:** Setiap target memiliki tanggal mulai untuk tracking durasi.

### 4. Manajemen Keuangan
- **Catat Pemasukan:** Tambah pemasukan dengan nama, jumlah, tanggal, dan catatan.
- **Catat Pengeluaran:** Catat pengeluaran harian dengan kategori (Makanan, Transport, Tagihan, Hiburan, Kebutuhan Lain, dll).
- **Budget Plan:** Atur batas anggaran per kategori pengeluaran; sistem menampilkan status anggaran (Aman / Waspada / Bahaya) secara real-time.
- **Transaksi Otomatis:** Buat pengeluaran/pemasukan rutin (bulanan) yang diproses otomatis setiap bulan.

### 5. Riwayat Transaksi
- **Dua Tab Intuitif:** Pemisahan **Riwayat Tabungan** dan **Riwayat Pemasukan** untuk kemudahan pemantauan.
- **Filter & Pencarian:** Filter berdasarkan tanggal, cari transaksi spesifik.

### 6. Sosial Feed
- **Feed Grid (Responsive):** 3 kolom (desktop), 2 kolom (tablet), 1 kolom (mobile).
- **Bagikan Progres:** Posting pencapaian target tabungan ke feed sosial dengan pesan motivasi.
- **Like:** Toggle suka pada postingan pengguna lain (AJAX).
- **Komentar:** Beri komentar pada postingan.
- **Hapus Postingan:** Hapus postingan milik sendiri.
- **Streak di Feed:** Setiap postingan menampilkan streak user di samping nama (🔥37).
- **Notifikasi Sosial:** Like dan komentar mengirim notifikasi ke pemilik postingan.

### 7. Public Profile (/user/{username})
- **Lihat Profil Publik:** Akses profil user melalui `/user/{username}`.
- **Jika Profil Sendiri:** Tombol Edit Profil muncul.
- **Jika Profil Orang Lain:** Read-only, tanpa tombol edit.
- **Info yang Ditampilkan:**
  - Foto profil, nama, username, badge level (🌱 Pemula / 🌿 Konsisten / 🌳 Penabung Aktif / 💎 Financial Master)
  - Total streak 🔥, rekor streak 🏆
  - Heatmap aktivitas 365 hari (GitHub-style)
  - Total target, target selesai, total tabungan
  - Achievement showcase
- **Friend Action:** Tombol Tambah Teman / Permintaan Terkirim / Terima/Tolak / Hapus Teman.

### 8. Friend System
- **Kirim Permintaan:** Kirim permintaan teman ke user lain + notifikasi.
- **Terima/Tolak:** Terima atau tolak permintaan teman masuk.
- **Auto-Accept:** Jika双方 saling mengirim permintaan, otomatis berteman.
- **Hapus Teman:** Hapus pertemanan (dari profil atau feed).
- **Pending Requests:** Daftar permintaan teman masuk di halaman feed.

### 9. User Search
- **Cari Pengguna:** Cari berdasarkan nama atau username.
- **Tampilan Card:** Avatar, nama, username, streak, badge level, tombol tambah teman.
- **Integrated:** Terintegrasi dengan sistem pertemanan.

### 10. Notifikasi
- **Notifikasi In-App:** Muncul di sidebar dengan ikon berdasarkan tipe (pencapaian, pengingat, info, peringatan).
- **Tandai Dibaca:** Tandai notifikasi satu per satu atau semua sekaligus.
- **Tipe Notifikasi:** Pencapaian target, pengingat menabung, pengingat target > 90%, notifikasi sosial.
- **Notifikasi Sosial:** Friend request, friend accepted, like, comment, share update.
- **Ikon Dinamis:** Ikon berubah berdasarkan tipe notifikasi (heart untuk like, user-plus untuk friend request, dll).
- **Hapus Notifikasi:** Hapus notifikasi yang tidak diperlukan.

### 11. Badge Level User
- **Otomatis:** Dihitung otomatis berdasarkan total tabungan (tidak perlu tabel baru).
- **Level:** 🌱 Pemula (< Rp500rb) → 🌿 Konsisten (< Rp5jt) → 🌳 Penabung Aktif (< Rp25jt) → 💎 Financial Master (≥ Rp25jt).
- **Ditampilkan di:** Profil, search card, header profil.

### 12. Pengaturan (5 Tab)
- **Tampilan:** Mode kompak, animasi.
- **Dashboard:** Atur ulang / sembunyikan widget.
- **Notifikasi:** Atur preferensi notifikasi (pengingat harian, mingguan, dll).
- **Keuangan:** Atur mata uang (IDR, USD, dll), format angka.
- **Privasi:** Mode privasi (sembunyikan nominal di layar).

### 13. Profil
- **Lihat Profil:** Statistik lengkap (total target, target tercapai, streak, streak terbaik).
- **Achievements:** Sistem pencapaian/prestasi (misal: "First Save", "Streak 7", dll).
- **Edit Profil:** Ubah nama, email, avatar (upload gambar).
- **Ganti Password:** Ubah password dengan validasi password lama.

### 14. RESTful API (V1)
Sakuin dilengkapi *internal API* yang dilindungi middleware (`web` & `auth`) untuk pengambilan data secara asinkron (AJAX/Fetch):

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/api/v1/dashboard` | Ringkasan dashboard (saldo, pemasukan, pengeluaran) |
| GET | `/api/v1/target-tabungan` | Daftar semua target tabungan |
| POST | `/api/v1/target-tabungan` | Buat target baru |
| GET | `/api/v1/target-tabungan/{id}` | Detail target |
| PUT | `/api/v1/target-tabungan/{id}` | Update target |
| DELETE | `/api/v1/target-tabungan/{id}` | Hapus target |
| PATCH | `/api/v1/target-tabungan/{id}/status` | Ubah status target |
| GET | `/api/v1/target-tabungan/{id}/ringkasan` | Ringkasan target |
| POST | `/api/v1/target-tabungan/{target}/transaksi` | Catat transaksi tabungan |
| GET | `/api/v1/transaksi` | Daftar transaksi tabungan |
| GET/PUT/DELETE | `/api/v1/transaksi/{id}` | Detail/update/hapus transaksi |
| GET/POST/PUT/DELETE | `/api/v1/kategori` | CRUD kategori transaksi |
| GET | `/api/v1/notifikasi` | Daftar notifikasi |
| PATCH | `/api/v1/notifikasi/{id}/baca` | Tandai notifikasi sudah dibaca |
| POST | `/api/v1/notifikasi/baca-semua` | Tandai semua sudah dibaca |
| DELETE | `/api/v1/notifikasi/{id}` | Hapus notifikasi |

### 15. Multi-Currency Support (Dinamis)
- Mendukung penyesuaian mata uang (IDR, USD, dll) sesuai preferensi pengguna.
- Perubahan diterapkan secara instan (*real-time*) di seluruh antarmuka aplikasi.

### 16. Gamifikasi & Motivasi
- **Streak:** Menghitung hari menabung berturut-turut secara global (per user, bukan per target).
- **Heatmap Aktivitas (GitHub Style):** Visualisasi grid bulanan; semakin besar nominal tabungan, semakin pekat warna hijau.
- **Achievements:** Sistem prestasi dengan berbagai level pencapaian yang bisa dibuka.

### 17. Fitur Tambahan
- **Sidebar Navigasi:** Sidebar responsif dengan link ke semua halaman utama.
- **Hover Tooltip Heatmap:** Detail histori saat hover di heatmap.
- **Compact Mode & Privacy Mode:** Sembunyikan nominal untuk privasi di layar.
- **Animasi Smooth:** Transisi dan animasi halus di seluruh UI.

---

## Stack Teknologi

- **Backend Framework:** [Laravel 11.x](https://laravel.com/) (PHP)
- **Frontend:** Laravel Blade Templating Engine
- **Styling:** CSS3 & [Bootstrap 5](https://getbootstrap.com/) (Custom UI/UX variables)
- **Database:** MySQL
- **Iconography:** [Phosphor Icons](https://phosphoricons.com/)

---

## Cara Menjalankan Project (Local Development)

1. **Clone Repository**
   ```bash
   git clone <url-repo-kamu>
   cd Sakuin_aja
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Jangan lupa atur kredensial database (DB_DATABASE, DB_USERNAME, DB_PASSWORD) di dalam file `.env`.*

4. **Jalankan Migrasi Database**
   ```bash
   php artisan migrate
   ```

5. **Jalankan Server**
   ```bash
   php artisan serve
   ```
   Aplikasi sekarang dapat diakses di `http://127.0.0.1:8000`.

---

## Filosofi Desain

Sakuin dirancang dengan menjauhi tampilan kaku aplikasi akuntansi tradisional. Mengusung *Soft Green Identity*, *glassmorphism hints*, *rounded corners*, dan transisi *smooth*, aplikasi ini dibuat agar proses mengelola uang terasa "ringan" dan "menyenangkan". Penempatan komponen yang sarat akan *action* diletakkan di tengah mata (*center of attention*), sementara riwayat historis diletakkan di pinggiran sebagai referensi pasif.

---

## Struktur Database

| Tabel | Deskripsi |
|-------|-----------|
| `pengguna` | Data pengguna (nama, email, password, avatar) |
| `target_tabungan` | Target tabungan (nama, target nominal, progress, gambar, status aktif) |
| `transaksi_tabungan` | Catatan menabung per target (jumlah, tanggal, catatan) |
| `pemasukan` | Pemasukan pengguna (nama, jumlah, tanggal) |
| `pengeluaran` | Pengeluaran pengguna (nama, jumlah, kategori, tanggal) |
| `budget_plans` | Anggaran per kategori pengeluaran |
| `budget_plan_items` | Detail item budget plan |
| `transaksi_otomatis` | Transaksi rutin (bulanan) otomatis |
| `kategori_transaksi` | Master kategori transaksi |
| `riwayat_progres` | Riwayat progres target (snapshot harian) |
| `notifikasi` | Notifikasi in-app (pengingat, pencapaian) |
| `pengaturan_pengguna` | Preferensi pengguna (5 tab pengaturan) |
| `saving_shares` | Postingan progres tabungan di sosial feed |
| `post_likes` | Like pada postingan |
| `post_comments` | Komentar pada postingan |
| `friend_requests` | Permintaan pertemanan (pengirim, penerima, status) |
| `friends` | Relasi pertemanan antar pengguna |

---

*Dibangun dengan hati untuk masa depan finansial yang lebih baik.*
