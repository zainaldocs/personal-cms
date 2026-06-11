# Panduan Deployment Personal CMS ke VPS (Ubuntu Server)

Panduan ini menjelaskan langkah-demi-langkah cara men-deploy aplikasi PHP Native + MySQL ini ke Virtual Private Server (VPS) dengan sistem operasi **Ubuntu 20.04 / 22.04 / 24.04 LTS** menggunakan web server **Apache**.

---

## 📋 Langkah 1: Konek ke VPS & Update System
Buka terminal Anda dan hubungkan ke VPS via SSH:
```bash
ssh root@IP_VPS_ANDA
```
Setelah masuk, lakukan update package server ke versi terbaru:
```bash
sudo apt update && sudo apt upgrade -y
```

---

## 🛠️ Langkah 2: Install LAMP Stack (Apache, MySQL, PHP)

Jalankan perintah berikut untuk menginstal Apache web server, database MySQL, dan engine PHP beserta modul yang dibutuhkan:

```bash
# 1. Install Apache
sudo apt install apache2 -y

# 2. Install MySQL Server
sudo apt install mysql-server -y

# 3. Install PHP & Modul PHP Pendukung
sudo apt install php libapache2-mod-php php-mysql php-gd php-mbstring php-xml php-curl php-zip -y
```

Pastikan Apache dan MySQL berjalan otomatis saat server booting:
```bash
sudo systemctl enable apache2
sudo systemctl enable mysql
```

---

## 🔒 Langkah 3: Konfigurasi Database MySQL

### 1. Amankan Instalasi MySQL
Jalankan script ini untuk mengamankan database (hapus database test, matikan login root remote, dll.):
```bash
sudo mysql_secure_installation
```
*(Ikuti petunjuk di layar, disarankan memilih opsi default/yes).*

### 2. Buat Database & User
Masuk ke prompt MySQL:
```bash
sudo mysql
```
Jalankan perintah SQL berikut di dalam console MySQL untuk membuat database dan user baru khusus untuk aplikasi CMS:
```sql
-- Buat database
CREATE DATABASE personal_cms DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Buat user baru (ganti 'PasswordKuatAnda' dengan password yang kuat)
CREATE USER 'cms_user'@'localhost' IDENTIFIED BY 'PasswordKuatAnda';

-- Berikan hak akses penuh database ke user tersebut
GRANT ALL PRIVILEGES ON personal_cms.* TO 'cms_user'@'localhost';

-- Refresh privileges dan keluar
FLUSH PRIVILEGES;
EXIT;
```

---

## 📥 Langkah 4: Deploy Source Code via Git

### 1. Bersihkan Folder Root Web Server
Secara default Apache menggunakan direktori `/var/www/html/`. Hapus file default `index.html` bawaan Apache:
```bash
sudo rm -rf /var/www/html/*
```

### 2. Clone Repositori Git
Pindah ke direktori web root, lalu clone proyek Anda dari GitHub:
```bash
cd /var/www/html
sudo git clone https://github.com/zainaldocs/personal-cms.git .
```

### 3. Atur Hak Akses Folder (Permissions)
Web server Apache (`www-data`) membutuhkan hak kepemilikan dan akses untuk membaca file php serta menulis file (upload gambar) ke dalam folder `assets/images/`:
```bash
# Set pemilik folder ke Apache user
sudo chown -R www-data:www-data /var/www/html

# Atur permission standar (folder 755, file 644)
sudo find /var/www/html -type d -exec chmod 755 {} \;
sudo find /var/www/html -type f -exec chmod 644 {} \;
```

---

## ⚙️ Langkah 5: Konfigurasi Koneksi Database Aplikasi

Buka file konfigurasi database aplikasi di server menggunakan teks editor (seperti `nano`):
```bash
sudo nano /var/www/html/includes/config.php
```

Ubah bagian kredensial database (sekitar baris 13-16) menjadi data database yang Anda buat di **Langkah 3**:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'cms_user');       // Ubah dari root
define('DB_PASS', 'PasswordKuatAnda'); // Isi password baru
define('DB_NAME', 'personal_cms');
```
*Tekan `Ctrl + O` lalu `Enter` untuk menyimpan, kemudian `Ctrl + X` untuk keluar dari nano.*

---

## 🗄️ Langkah 6: Import Skema Database (schema.sql)

Gunakan perintah `mysql` untuk mengimpor file `schema.sql` langsung ke database `personal_cms` yang telah Anda buat di server:
```bash
mysql -u cms_user -p personal_cms < /var/www/html/schema.sql
```
Masukkan password database (`PasswordKuatAnda`) saat diminta.

Restart Apache agar konfigurasi PHP diperbarui:
```bash
sudo systemctl restart apache2
```

Sekarang website Anda sudah bisa diakses secara publik menggunakan alamat **`http://IP_VPS_ANDA/`**.

---

## 🌐 Langkah 7: Hubungkan Domain & Pasang SSL (Sangat Direkomendasikan)

Jika Anda sudah memiliki nama domain (misalnya `namaanda.com`):

### 1. Arahkan DNS Record
Masuk ke penyedia domain Anda (seperti Niagahoster, Domainesia, Cloudflare, dll.), buat **A Record** baru:
- **Name/Host:** `@` (dan `www` jika perlu)
- **Value/IP:** `IP_VPS_ANDA`

### 2. Konfigurasi Apache Virtual Host
Buat file konfigurasi vhost untuk domain Anda:
```bash
sudo nano /etc/apache2/sites-available/personal-cms.conf
```
Paste konfigurasi berikut (sesuaikan nama domain):
```apache
<VirtualHost *:80>
    ServerName namaanda.com
    ServerAlias www.namaanda.com
    DocumentRoot /var/www/html

    <Directory /var/www/html>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```
Aktifkan konfigurasi website baru tersebut dan matikan konfigurasi default Apache:
```bash
sudo a2ensite personal-cms.conf
sudo a2dissite 000-default.conf
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### 3. Pasang SSL Gratis dengan Let's Encrypt (Certbot)
Gunakan Certbot untuk menginstal SSL secara otomatis agar website berjalan di bawah HTTPS (`https://namaanda.com`):
```bash
sudo apt install certbot python3-certbot-apache -y
sudo certbot --apache -d namaanda.com -d www.namaanda.com
```
*(Ikuti petunjuk di layar. Pilih opsi **Redirect** agar semua trafik HTTP dialihkan otomatis ke HTTPS).*
