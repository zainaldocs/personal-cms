# Personal CMS (v1.0-fixed)

Sebuah Content Management System (CMS) personal sederhana dan cepat yang dibangun khusus untuk mengelola website portofolio dan blog pribadi. Proyek ini didesain agar mudah dimodifikasi, memiliki performa yang kencang tanpa overhead, serta mendukung integrasi Tailwind CSS dan tampilan responsif (termasuk Dark Mode).

## 🚀 Tech Stack
- **Backend:** PHP Native (PDO)
- **Database:** MySQL
- **Frontend Styling:** Tailwind CSS
- **Frontend Interactivity:** Vanilla JavaScript

---

## ✨ Fitur Utama
### Publik (Frontend)
1. **Beranda (Home):** Memuat section Hero, perkenalan profil ringkas, 3 portofolio terbaru, dan 3 artikel blog terbaru.
2. **About:** Menampilkan detail profil biografi lengkap, foto profil, dan info kontak.
3. **Portofolio:** Daftar galeri proyek lengkap dengan link demo eksternal.
4. **Blog:** Daftar artikel dengan sistem pagination serta halaman membaca detail artikel (`single.php`).
5. **Kontak:** Formulir kirim pesan publik terintegrasi ke database dengan validasi.
6. **Dark Mode Toggle:** Tombol switch tema terang/gelap yang tersinkronisasi menggunakan local storage.

### Panel Admin (Backend/CMS)
1. **Autentikasi Keamanan:** Sistem login dan pembatasan akses dashboard admin menggunakan PHP Session.
2. **Statistik Dashboard:** Ringkasan jumlah artikel, proyek, total inbox pesan, dan data inbox terbaru.
3. **Manajemen Pengaturan Situs:** Mengatur nama situs, isi teks hero, deskripsi profil about, link sosial media, dan upload foto profil.
4. **Blog CRUD:** Tulis, edit, list, dan hapus artikel blog beserta upload thumbnail gambar dan auto-slug generator.
5. **Portfolio CRUD:** Kelola daftar proyek portofolio, urutan tampil (*order index*), dan URL demo.
6. **Inbox Pesan:** Halaman pembaca inbox bergaya *split-view* (master-detail) yang interaktif (menandai pesan telah dibaca secara dinamis).

---

## 📁 Struktur Direktori
```text
personal-cms/
│
├── admin/                  # Halaman & logika Panel Admin
│   ├── includes/           # Layout Header & Footer Admin
│   ├── index.php           # Dashboard Admin
│   ├── login.php           # Autentikasi Login
│   ├── logout.php          # Proses Logout
│   ├── posts.php           # CRUD Artikel Blog
│   ├── portfolios.php      # CRUD Proyek Portofolio
│   └── messages.php        # Inbox Pesan Pengunjung
│
├── assets/                 # File Aset Statis
│   ├── css/                # Aset Stylesheet
│   ├── js/                 # Aset Javascript
│   └── images/             # Folder Penyimpanan Upload Gambar
│
├── includes/               # File Global Layout & Config
│   ├── config.php          # Konfigurasi PDO & BASE_URL
│   ├── functions.php       # Library Fungsi & Sanitizer XSS
│   ├── header.php          # Header Publik & Navigasi
│   └── footer.php          # Footer Publik & Dark Mode Logic
│
├── index.php               # Beranda Publik
├── about.php               # Halaman About
├── blog.php                # Daftar Artikel Blog
├── single.php              # Baca Artikel
├── portfolio.php           # Daftar Portofolio
├── contact.php             # Form Kontak
├── schema.sql              # Skema Database MySQL
└── plan.md                 # Rencana Implementasi Awal
```

---

## 🛠️ Instalasi & Setup Lokal

### 1. Prasyarat
- XAMPP / Laragon (Web Server Apache & MySQL dengan PHP versi >= 8.0)
- Akun GitHub (jika ingin melakukan versioning)

### 2. Konfigurasi Database
1. Buka phpMyAdmin (`http://localhost/phpmyadmin/`).
2. Buat database baru bernama `personal_cms`.
3. Import file `schema.sql` ke dalam database tersebut.
4. Secara default, tabel `users` akan terisi satu akun admin awal:
   - **Username:** `admin`
   - **Password:** `admin123`

### 3. Konfigurasi Proyek
1. Pindahkan folder `personal-cms` ke dalam direktori server lokal Anda (misal `C:/xampp/htdocs/personal-cms`).
2. Buka file `includes/config.php` untuk menyesuaikan koneksi database jika Anda menggunakan username/password database yang berbeda dari setelan default XAMPP (default: `username: root`, `password: `).
3. Buka browser Anda dan akses:
   - **Website Utama:** `http://localhost/personal-cms/`
   - **Panel Admin:** `http://localhost/personal-cms/admin/`

---

## 🔒 Catatan Keamanan
- Semua query database menggunakan **PDO Prepared Statements** untuk mencegah serangan *SQL Injection*.
- Output variabel dinamis di HTML dilewatkan ke dalam fungsi `sanitize()` (yang menggunakan `htmlspecialchars()`) guna mencegah celah *Cross-Site Scripting (XSS)*.
- Halaman admin dilindungi fungsi pengecekan sesi `check_login()` pada baris awal eksekusi script.
