# Sakuin - Modern Financial & Savings Tracker

**Sakuin** adalah aplikasi manajemen keuangan berbasis web yang didesain khusus untuk membantu pengguna melacak pemasukan, pengeluaran, serta mewujudkan target tabungan impian dengan sentuhan gamifikasi yang memotivasi. 

Dibangun dengan pendekatan desain UI/UX bergaya *fintech modern*, Sakuin tidak hanya sekadar alat pencatat, tetapi juga pendamping finansial yang menumbuhkan kebiasaan (habit) menabung yang konsisten.

---

## ✨ Fitur Utama

### 1. 📊 Dashboard Fintech Modern
Dashboard didesain dengan sistem **Full Width 3-Column Z-Pattern Layout** yang secara optimal memanfaatkan seluruh lebar layar monitor desktop untuk menyajikan informasi krusial tanpa terasa sesak:
- **Hero Balance:** Menampilkan secara instan total saldo tersedia, beserta ringkasan pemasukan dan pengeluaran bulan ini.
- **Combined Target & Action Card:** Progres target tabungan aktif yang disandingkan langsung dengan form pencatatan tabungan yang *compact*, mempermudah alur kerja (lihat progres -> langsung nabung).

### 2. 🎮 Gamifikasi & Motivasi
Aplikasi ini meminjam elemen *habit tracker* untuk menjaga konsistensi pengguna:
- **🔥 Rekor Streak:** Menghitung hari menabung secara berturut-turut. Streak dihitung secara global per *user* (bukan per target), memberikan kebebasan menabung di berbagai target tanpa merusak kedisiplinan.
- **🟩 Heatmap Aktivitas (GitHub Style):** Menggantikan kalender tradisional yang membosankan dengan visualisasi grid berbasis bulan (Monthly View). Semakin besar nominal yang ditabung dalam sehari, semakin pekat warna hijau pada kotak di hari tersebut. Dilengkapi fitur *hover tooltip* untuk detail histori.

### 3. 🎯 Manajemen Target Impian
- Pengguna dapat membuat berbagai target tabungan (misal: "Beli Laptop Baru", "Dana Darurat", "Liburan").
- Menampilkan persentase pencapaian (*progress bar*) dan nominal yang telah terkumpul secara *real-time*.
- Fitur *Set Active Target* untuk memprioritaskan satu tujuan utama pada satu waktu.

### 4. 🗄️ Histori & Transaksi
- Pemisahan riwayat transaksi menjadi dua tab intuitif: **Riwayat Tabungan** dan **Riwayat Pemasukan** untuk kemudahan pemantauan.

### 5. 🔌 Internal API Endpoint
Sakuin dilengkapi dengan *internal API* yang dilindungi oleh *middleware* (`web` & `auth`) untuk pengambilan data secara asinkron (AJAX/Fetch):
- `GET /api/saldo` — Mengambil sisa saldo secara cepat dan teroptimasi melalui *query* SQL yang di-join langsung, menghindari operasi koleksi PHP yang berat.

### 6. 💱 Multi-Currency Support (Dinamis)
- Mendukung penyesuaian mata uang (seperti IDR, USD, dll) sesuai preferensi pengguna.
- Perubahan mata uang diterapkan secara instan (*real-time*) di seluruh antarmuka aplikasi termasuk dashboard, riwayat transaksi, dan profil pengguna.

---

## 🛠️ Stack Teknologi

Sakuin dibangun di atas fondasi teknologi modern dan stabil:

- **Backend Framework:** [Laravel 11.x](https://laravel.com/) (PHP)
- **Frontend:** Laravel Blade Templating Engine
- **Styling:** CSS3 & [Bootstrap 5](https://getbootstrap.com/) (Custom UI/UX variables)
- **Database:** MySQL
- **Iconography:** [Phosphor Icons](https://phosphoricons.com/)

---

## 🚀 Cara Menjalankan Project (Local Development)

Ikuti langkah-langkah berikut untuk menjalankan Sakuin di komputer lokal Anda:

1. **Clone Repository**
   ```bash
   git clone <url-repo-kamu>
   cd Sakuin_aja
   ```

2. **Install Dependencies**
   Pastikan Anda sudah menginstal PHP dan Composer.
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment**
   Salin file konfigurasi dan hasilkan *app key*.
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Jangan lupa atur kredensial database (DB_DATABASE, DB_USERNAME, DB_PASSWORD) di dalam file `.env`.*

4. **Jalankan Migrasi Database**
   Membuat struktur tabel yang diperlukan.
   ```bash
   php artisan migrate
   ```

5. **Jalankan Server**
   ```bash
   php artisan serve
   ```
   Aplikasi sekarang dapat diakses di `http://127.0.0.1:8000`.

---

## 💡 Filosofi Desain

Sakuin dirancang dengan menjauhi tampilan kaku aplikasi akuntansi tradisional. Mengusung *Soft Green Identity*, *glassmorphism hints*, *rounded corners*, dan transisi *smooth*, aplikasi ini dibuat agar proses mengelola uang terasa "ringan" dan "menyenangkan". Penempatan komponen yang sarat akan *action* diletakkan di tengah mata (*center of attention*), sementara riwayat historis diletakkan di pinggiran sebagai referensi pasif.

---
*Dibangun dengan ❤️ untuk masa depan finansial yang lebih baik.*
