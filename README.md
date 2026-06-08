# Sakuin Aja

**Sakuin Aja** adalah aplikasi pelacak tabungan dan manajemen keuangan pintar yang dirancang untuk membantu Anda mengatur uang dengan lebih mudah dan menyenangkan.

---

## ✨ Fitur Utama

Berdasarkan fokus presentasi aplikasi, Sakuin Aja memiliki 4 fitur unggulan utama:

### 1. 🔍 Monitoring Saldo
Pantau arus kas Anda dengan mudah. Aplikasi akan otomatis menampilkan:
* **Hero Balance:** Total uang yang Anda miliki saat ini.
* **Pemasukan & Pengeluaran:** Ringkasan uang masuk dan keluar pada bulan berjalan.
* Semua transaksi akan otomatis memperbarui saldo utama Anda secara *real-time*.

### 2. 🎯 Target Tabungan
Wujudkan impian Anda satu per satu dengan sistem target tabungan.
* Buat target spesifik (misal: "Beli Laptop", "Liburan").
* Tetapkan nominal target dan pantau persentase progresnya melalui progress bar hijau.
* Anda bisa mengatur satu target menjadi **"Target Aktif"** agar selalu tampil di halaman utama (Dashboard) untuk memotivasi Anda menabung.

### 3. 🔥 Streak Tabungan
Gamifikasi yang dirancang untuk membangun kebiasaan (habit) menabung.
* Aplikasi melacak berapa hari berturut-turut Anda konsisten menabung.
* **Current Streak:** Jumlah hari beruntun Anda menabung saat ini.
* **Longest Streak:** Rekor terbaik hari beruntun Anda.
* Semakin sering menabung, semakin tinggi level/badge yang Anda dapatkan!

### 4. 🕒 Riwayat Transaksi
Lacak ke mana saja uang Anda pergi tanpa pusing.
* Tersedia menu khusus untuk melihat seluruh riwayat uang masuk, uang keluar, dan uang yang ditabung.
* Anda bisa memfilter riwayat berdasarkan jenisnya (Pemasukan, Pengeluaran, atau Tabungan).

---

## 🌟 Fitur Pendukung & Lanjutan (Advanced Features)

Selain 4 fitur dasar di atas, Sakuin Aja juga dilengkapi dengan berbagai fitur modern sekelas aplikasi finansial profesional:

* **📈 Manajemen Anggaran (Budgeting):** Atur batas pengeluaran bulanan per kategori. Sistem akan memberi peringatan (Aman/Waspada/Bahaya) jika pengeluaran hampir melewati batas anggaran.
* **🌐 Feed Sosial & Komunitas:** Bagikan progres tabungan Anda ke feed sosial. Anda bisa saling *like*, komentar, dan berteman dengan pengguna lain untuk saling memotivasi.
* **🤖 Transaksi Otomatis:** Punya tagihan bulanan atau jadwal menabung rutin? Atur sekali, dan sistem akan mencatatnya secara otomatis setiap bulan.
* **🏆 Sistem Level (Badge):** Semakin banyak Anda menabung, level Anda akan naik (🌱 Pemula ➔ 🌿 Konsisten ➔ 🌳 Penabung Aktif ➔ 💎 Financial Master).
* **💱 Multi-Currency:** Mendukung berbagai mata uang. Anda bisa mengubah tampilan saldo dari Rupiah (IDR) menjadi Dolar (USD) atau mata uang lainnya langsung dari pengaturan.
* **🔒 Mode Privasi (Privacy Mode):** Sedang di tempat umum? Sembunyikan nominal saldo Anda menjadi "••••" hanya dengan satu klik.
* **🔔 Sistem Notifikasi In-App:** Pengingat otomatis untuk menabung, peringatan saat budget menipis, hingga notifikasi saat ada teman yang menyukai progres Anda.
* **🎨 Tema & Kustomisasi:** Atur tampilan aplikasi sesuai selera Anda (Dark Mode / Green Theme) dan atur ulang posisi widget di dashboard.

---

## 🚀 Cara Menjalankan Aplikasi Lokal

Jika Anda perlu menjalankan ulang aplikasi ini di laptop/komputer Anda:

1. Buka terminal/CMD di dalam folder `Sakuin_aja`.
2. Install dependensi (hanya jika baru pertama kali clone):
   ```bash
   composer install
   npm install
   ```
3. Pastikan database MySQL di XAMPP sudah menyala.
4. Jalankan perintah ini jika butuh mereset database ke awal:
   ```bash
   php artisan migrate:fresh --seed
   ```
5. Nyalakan server Laravel:
   ```bash
   php artisan serve
   ```
6. Buka browser dan ketik: `http://127.0.0.1:8000`
