# Personal CMS Walkthrough

We have successfully built and verified the core components of the Personal CMS using native PHP, MySQL, Tailwind CSS, and Vanilla JavaScript.

## Changes Made

### 1. Database Setup
- Created [schema.sql](file:///c:/xampp/htdocs/personal-cms/schema.sql) which sets up tables for `users`, `settings`, `posts`, `portfolios`, and `messages`.
- Automatically executed the schema to configure the `personal_cms` database with a default admin user and initial web settings.

### 2. Core Abstractions & Helpers
- Built [config.php](file:///c:/xampp/htdocs/personal-cms/includes/config.php) to manage secure connection strings via PDO and initialize the base URL.
- Created [functions.php](file:///c:/xampp/htdocs/personal-cms/includes/functions.php) containing essential helpers for:
  - Input/Output sanitization (`sanitize()`) to guard against XSS.
  - URL Slug generation (`slugify()`).
  - Access control authentication locks (`check_login()`).
  - Fetching settings from database (`get_setting()`).
  - File upload restrictions (`upload_image()`).

### 3. Admin Panel Layout & Features
- **Authentication:** Created [login.php](file:///c:/xampp/htdocs/personal-cms/admin/login.php) and [logout.php](file:///c:/xampp/htdocs/personal-cms/admin/logout.php).
- **Core Layout:** Setup [header.php](file:///c:/xampp/htdocs/personal-cms/admin/includes/header.php) and [footer.php](file:///c:/xampp/htdocs/personal-cms/admin/includes/footer.php) featuring a responsive desktop/mobile sidebar with theme toggles.
- **Modules:**
  - **Dashboard ([index.php](file:///c:/xampp/htdocs/personal-cms/admin/index.php)):** Displays aggregated site stats and recent messages.
  - **Settings ([settings.php](file:///c:/xampp/htdocs/personal-cms/admin/settings.php)):** Allows editing titles, bio content, social links, and profile pictures.
  - **Blog CRUD ([posts.php](file:///c:/xampp/htdocs/personal-cms/admin/posts.php)):** Full CRUD with image attachments, slug auto-generation, and draft controls.
  - **Portfolio CRUD ([portfolios.php](file:///c:/xampp/htdocs/personal-cms/admin/portfolios.php)):** Manage project portfolios and display ordering.
  - **Messages ([messages.php](file:///c:/xampp/htdocs/personal-cms/admin/messages.php)):** Split-layout email-style message reader that marks messages as read upon viewing.

### 4. Frontend Integration
- Set up a clean, modern responsive layout using [header.php](file:///c:/xampp/htdocs/personal-cms/includes/header.php) and [footer.php](file:///c:/xampp/htdocs/personal-cms/includes/footer.php).
- Configured Tailwind CSS with colors supporting the requested **White + Light Blue Theme** as well as **Dark Mode** support via standard toggle class sync.
- Created:
  - [index.php](file:///c:/xampp/htdocs/personal-cms/index.php) (Hero intro, latest posts and portfolios).
  - [about.php](file:///c:/xampp/htdocs/personal-cms/about.php) (Bio detail and profile picture).
  - [portfolio.php](file:///c:/xampp/htdocs/personal-cms/portfolio.php) (Grid listing of all active project links).
  - [blog.php](file:///c:/xampp/htdocs/personal-cms/blog.php) & [single.php](file:///c:/xampp/htdocs/personal-cms/single.php) (Daftar artikel and reading details with custom SEO metadata tags).
  - [contact.php](file:///c:/xampp/htdocs/personal-cms/contact.php) (Message submission page with input validation).

---

## What Was Tested

### Automated Web Testing (Browser Subagent)
Kami telah menjalankan pengujian web otomatis menggunakan subagent browser untuk memverifikasi fungsionalitas web:
- **Admin Login & Redirection:** Memverifikasi pengalihan login `admin/index.php` dan fungsionalitas sidebar.
- **Public Navigation & Active State:** Beranda, About, Portofolio, Blog, dan Kontak dimuat dengan UI responsif.
- **Navbar Bug Fix (Active State):** Memperbaiki pengecekan menu aktif untuk "Blog" agar tidak merender angka `1` di string CSS class, memastikan warna teks link "Blog" terbaca dengan warna abu-abu (`text-gray-300`) dalam Dark Mode.
- **Live VPS & Cloudflare Tunnel Verification:** Memverifikasi fungsionalitas penuh web secara publik melalui domain Cloudflare Tunnel: `https://vps.zain.biz.id/` serta melakukan pengetesan login admin secara live di server VPS.

### Rekaman Hasil Pengujian Browser
Anda dapat melihat jalannya pengujian dan hasil perbaikan melalui galeri di bawah ini:

````carousel
![CMS Web Redirection & Admin Panel Test](C:/Users/Zainal Arifin/.gemini/antigravity-ide/brain/fb905778-1a8c-467f-a4d9-830a8720d830/cms_url_fix_test_1781196181720.webp)
<!-- slide -->
![CMS Navbar Dark Mode Active State Fix Test](C:/Users/Zainal Arifin/.gemini/antigravity-ide/brain/fb905778-1a8c-467f-a4d9-830a8720d830/cms_dark_mode_blog_color_test_1781196834059.webp)
<!-- slide -->
![CMS Live VPS Cloudflare Tunnel Test](C:/Users/Zainal Arifin/.gemini/antigravity-ide/brain/fb905778-1a8c-467f-a4d9-830a8720d830/cms_live_vps_test_1781198644123.webp)
````
