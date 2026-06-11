# Rencana Ekstensi: Modul Manajemen Pengguna (User Profile & Security)
*(Ditambahkan pada revisi terbaru)*

## Tujuan Ekstensi
Menambahkan fitur manajemen akun di panel admin agar Anda selaku pemilik website dapat mengubah informasi profil seperti *username*, *email*, serta *password* secara mandiri tanpa perlu mengakses database secara langsung.

## Rencana Implementasi (Fase 6)
- [ ] **UI/UX Halaman Profil (`admin/profile.php`):** Membuat desain formulir menggunakan Tailwind CSS. Akan ada dua bagian: Formulir Update Profil (Username & Email) dan Formulir Update Password (Password Baru & Konfirmasi Password).
- [ ] **Modifikasi Sidebar Admin (`includes/header.php` khusus admin):** Menambahkan menu navigasi baru "Profil" atau "Akun" agar halaman ini bisa diakses.
- [ ] **Logika Backend (PHP):**
  - Menerapkan *Prepared Statements* untuk UPDATE data user.
  - Jika password tidak diisi, maka hanya *username* dan *email* yang diperbarui.
  - Jika password diisi, lakukan validasi pencocokan dengan "Konfirmasi Password" dan lakukan enkripsi ulang menggunakan `password_hash()`.
- [ ] **Keamanan:** Memastikan formulir pembaruan profil dan password ini dilindungi penuh oleh token CSRF (`csrf_field()`).

---

# Personal CMS Implementation Plan (Rencana Awal)

## 1. Project Overview
Project ini adalah pembuatan Personal Content Management System (CMS) sederhana yang dirancang khusus untuk mengelola website portfolio dan blog pribadi. 
Fokus utamanya adalah kemudahan penggunaan, kustomisasi konten melalui panel admin, dan performa yang cepat tanpa overhead dari CMS besar seperti WordPress.

**Tech Stack:**
- **Backend:** PHP Native (versi 8.x direkomendasikan)
- **Database:** MySQL
- **Frontend CSS:** Tailwind CSS (via CDN atau CLI untuk build)
- **Frontend JS:** Vanilla JavaScript

## 2. Panduan Desain UI/UX (Design Guidelines)
- **Tema Utama (Light Theme):** Kombinasi warna putih (background bersih) dan biru muda modern (aksen, tombol, link). Desain harus terasa *simple*, *clean*, dan profesional.
- **Dark Mode Support:** Menyediakan *toggle* untuk beralih ke mode gelap (dark mode), menggunakan nuansa abu-abu gelap (bukan hitam pekat) dengan aksen biru yang disesuaikan agar nyaman di mata.
- **Minimalisme:** Hindari elemen dekoratif yang berlebihan. Fokus pada tipografi yang mudah dibaca (misalnya keluarga font Sans-serif modern seperti Inter, Plus Jakarta Sans, atau Roboto) dan *whitespace* yang lega.
- **Komponen UI:** Gunakan desain komponen bersudut agak membulat (*rounded-md* atau *rounded-lg* di Tailwind), bayangan halus (*soft shadows*), dan transisi yang mulus (*smooth hover effects*).

---

## 3. Fitur Utama
### Publik (Frontend)
1. **Beranda (Home):** Hero section, ringkasan profil, cuplikan portofolio terbaru, dan cuplikan blog terbaru.
2. **About:** Halaman detail profil, perjalanan karir, dan keahlian (skills).
3. **Blog:** Daftar artikel dengan pagination, dan halaman detail artikel (single post).
4. **Portofolio:** Galeri/daftar karya atau project yang pernah dibuat beserta detailnya.
5. **Kontak:** Formulir untuk mengirim pesan yang akan masuk ke database/email, serta informasi kontak.

### Admin Panel (Backend)
1. **Otentikasi:** Login/Logout aman untuk admin.
2. **Dashboard:** Ringkasan statistik (jumlah post, portofolio, pesan masuk).
3. **Manajemen Pengaturan Situs (Settings):** Mengubah teks hero beranda, logo, informasi kontak, dan social media links.
4. **Manajemen Blog:** CRUD (Create, Read, Update, Delete) artikel, publish/draft status, thumbnail artikel.
5. **Manajemen Portofolio:** CRUD project, upload gambar project, deskripsi, dan link eksternal.
6. **Manajemen Pesan:** Melihat dan menghapus pesan yang masuk dari halaman kontak.
7. **Manajemen Halaman Statis:** Mengedit konten untuk halaman About.

---

## 4. Struktur Database (High-Level Schema)
Berikut adalah rancangan awal tabel-tabel di MySQL:

- `users`: id, username, password_hash, email, created_at
- `settings`: id, setting_key, setting_value (menyimpan data dinamis seperti teks beranda, deskripsi about, link sosmed)
- `posts`: id, title, slug, content, image, status (published/draft), created_at, updated_at
- `portfolios`: id, title, description, image, project_url, order_index, created_at
- `messages`: id, name, email, subject, message, is_read, created_at

---

## 5. Struktur Direktori
Struktur folder dasar untuk kemudahan maintainability:

```text
personal-cms/
│
├── admin/                  # Semua halaman dan logika khusus Panel Admin
│   ├── index.php           # Dashboard Admin
│   ├── login.php           # Halaman Login
│   ├── posts.php           # CRUD Blog
│   ├── portfolios.php      # CRUD Portofolio
│   ├── settings.php        # Pengaturan Web & Beranda
│   └── messages.php        # Inbox Kontak
│
├── assets/                 # File statis (Publik)
│   ├── css/                # Tailwind output (style.css)
│   ├── js/                 # Vanilla JS (main.js)
│   └── images/             # Uploads dan aset gambar statis (logo, favicon)
│
├── includes/               # File yang bisa di-reuse / di-include
│   ├── config.php          # Konfigurasi Database (PDO/MySQLi)
│   ├── functions.php       # Fungsi-fungsi helper (sanitasi, upload gambar, dll)
│   ├── header.php          # Bagian atas HTML (Navbar)
│   └── footer.php          # Bagian bawah HTML (Footer)
│
├── index.php               # Halaman Beranda
├── about.php               # Halaman About
├── blog.php                # Daftar Blog
├── single.php              # Detail Artikel Blog
├── portfolio.php           # Halaman Portofolio
└── contact.php             # Halaman Kontak
```

---

## 6. Rencana Fase Implementasi (Untuk Junior Dev / LLM)

### Fase 1: Setup Lingkungan & Database (Estimasi: 1 Hari)
- [x] (Sudah Dieksekusi) Buat database MySQL dan jalankan query pembuatan tabel-tabel sesuai skema.
- [x] (Sudah Dieksekusi) Setup struktur direktori proyek.
- [x] (Sudah Dieksekusi) Buat file `includes/config.php` untuk koneksi database (Sangat disarankan menggunakan **PDO** untuk keamanan dari SQL Injection).
- [x] (Sudah Dieksekusi) Setup Tailwind CSS (jika menggunakan CLI, inisiasi `tailwind.config.js` dan buat build script sederhana).

### Fase 2: Autentikasi & Dasar Admin Panel (Estimasi: 2 Hari)
- [x] (Sudah Dieksekusi) Buat desain login admin (`admin/login.php`) dengan Tailwind.
- [x] (Sudah Dieksekusi) Implementasi proses validasi login dan pembuatan Session.
- [x] (Sudah Dieksekusi) Buat layout dasar admin (Sidebar, Header) dan batasi akses (hanya user yang sudah login yang bisa akses folder `admin/`).
- [x] (Sudah Dieksekusi) Buat halaman Dashboard sederhana yang menampilkan pesan selamat datang.

### Fase 3: CRUD Modul Admin (Estimasi: 3-4 Hari)
- [x] (Sudah Dieksekusi) **Settings:** Halaman untuk update value di tabel `settings` (mengatur teks hero, profil singkat).
- [x] (Sudah Dieksekusi) **Blog:** Form tambah, edit, list, dan hapus artikel blog. Implementasikan sistem upload gambar dan generator 'slug' dari judul.
- [x] (Sudah Dieksekusi) **Portofolio:** Sama seperti blog, tambahkan fitur upload gambar portofolio.
- [x] (Sudah Dieksekusi) **Messages:** Halaman list pesan masuk dengan opsi tandai sudah dibaca atau hapus.

### Fase 4: Integrasi Frontend (Estimasi: 3 Hari)
- [x] (Sudah Dieksekusi) Buat `includes/header.php` (termasuk navigasi responsif) dan `includes/footer.php`.
- [x] (Sudah Dieksekusi) **Beranda (`index.php`):** Tarik data dari tabel `settings`, tampilkan 3 portofolio terbaru, dan 3 artikel terbaru.
- [x] (Sudah Dieksekusi) **About (`about.php`):** Tarik data profil/about dari tabel `settings`.
- [x] (Sudah Dieksekusi) **Blog (`blog.php` & `single.php`):** Tampilkan daftar artikel dan routing sederhana untuk membaca detail artikel.
- [x] (Sudah Dieksekusi) **Portofolio (`portfolio.php`):** Grid layout untuk menampilkan semua portofolio.
- [x] (Sudah Dieksekusi) **Kontak (`contact.php`):** Buat form HTML, dan proses submit (INSERT ke tabel `messages`).

### Fase 5: Finalisasi & Keamanan (Estimasi: 1-2 Hari)
- [x] (Sudah Dieksekusi) **Security Review:** Pastikan semua query ke database menggunakan *Prepared Statements*.
- [x] (Sudah Dieksekusi) **Sanitasi Data:** Gunakan `htmlspecialchars()` saat me-render data ke HTML untuk mencegah XSS.
- [x] (Sudah Dieksekusi) **Validasi Form:** Validasi input baik di sisi frontend (JS) maupun backend (PHP).
- [x] (Sudah Dieksekusi) **Responsiveness:** Pastikan semua tampilan (Admin maupun Frontend) nyaman dilihat di layar HP.
- [x] (Sudah Dieksekusi) **SEO Basic:** Tambahkan tag meta title dan description dinamis di `header.php`.

---

## 7. Standar & Guideline Teknis
Pesan untuk Junior Programmer / LLM yang akan mengeksekusi:
1. **Keamanan Utama:** Jangan pernah menggabungkan input user langsung ke dalam query string SQL. Selalu gunakan `PDO::prepare` dan `execute()`.
2. **Kerapian Kode:** Pisahkan logika bisnis (query DB) dari logika presentasi (HTML) sebisa mungkin. 
3. **Penamaan File/Variabel:** Gunakan *snake_case* untuk nama tabel dan kolom database. Gunakan *camelCase* atau *snake_case* secara konsisten untuk variabel PHP.
4. **Tailwind Class:** Manfaatkan utility class dari Tailwind sebaik-baiknya. Hindari membuat custom CSS di file terpisah kecuali sangat terpaksa.
5. **Upload File:** Berikan validasi yang ketat pada fitur upload gambar (cek ekstensi file, MIME type, dan ukuran maksimal). Jangan lupa untuk me-rename file gambar yang diupload dengan string unik (misal: `uniqid()`) untuk mencegah konflik nama file.
