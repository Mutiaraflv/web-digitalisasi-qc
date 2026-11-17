# Panduan Hosting QCMS Laravel di Synology Server (Container Manager / Docker)

Panduan ini dirancang khusus untuk menjalankan aplikasi **QCMS (Laravel 12 + MariaDB/MySQL)** di server **Synology NAS** menggunakan **Container Manager**.

---

## 📁 Ringkasan Struktur Folder di Synology File Station

Nantinya di dalam Synology, kamu akan membuat struktur folder seperti ini di folder `docker/`:

```text
docker/
└── qcms/
    ├── docker-compose.yml
    ├── app/                     <-- Letakkan semua file proyek Laravel di sini
    │   ├── app/
    │   ├── bootstrap/
    │   ├── config/
    │   ├── database/
    │   ├── public/
    │   ├── resources/
    │   ├── routes/
    │   ├── storage/
    │   ├── vendor/              <-- Sertakan agar tidak perlu composer install
    │   ├── artisan
    │   ├── .env                 <-- .env yang sudah disesuaikan dengan DB Synology
    │   └── ...
    └── db_data/                 <-- Otomatis dibuat Docker untuk menyimpan database
```

---

## 🚀 Langkah-langkah Praktis

### Langkah 1: Persiapan File di Laptop

1. Di laptop, buka folder `ccr-laravel`.
2. Buat file `.env` baru khusus hosting Synology (bisa gunakan template `.env.synology`):
   * `DB_HOST=db` (ini adalah nama service container database di `docker-compose.yml`)
   * `DB_DATABASE=ccr_db`
   * `DB_USERNAME=ccr_user`
   * `DB_PASSWORD=ccrpassword123`
   * `APP_URL=http://<IP_SYNOLOGY_KAMU>:8080`
3. Kompres folder `ccr-laravel` menjadi file `.zip` (misal: `ccr-laravel.zip`).
   > **Catatan:** Folder `vendor` sebaiknya ikut di-zip agar di Synology kamu tidak perlu repot install composer via terminal.

---

### Langkah 2: Upload ke Synology (File Station)

1. Buka browser dan login ke **Synology DSM**.
2. Buka aplikasi **File Station**.
3. Masuk ke folder shared bernama `docker`.
4. Buat folder baru bernama `qcms`.
5. Di dalam `docker/qcms`, buat folder baru bernama `app`.
6. Upload file `ccr-laravel.zip` ke dalam folder `app`, lalu klik kanan dan pilih **Extract here** (Ekstrak di sini). Pastikan file-file seperti `artisan` dan folder `public` langsung berada di dalam folder `app/`.
7. Upload file `docker-compose.yml` ke dalam folder `docker/qcms/` (sejajar dengan folder `app/`).

---

### Langkah 3: Jalankan Project di Container Manager

1. Di Synology DSM, buka aplikasi **Container Manager** (ikon paus biru/docker).
2. Di menu sebelah kiri, klik **Project** (atau **Proyek**).
3. Klik tombol **Create** (Buat):
   * **Project Name**: `qcms`
   * **Path**: Klik Browse, lalu pilih folder `/docker/qcms`.
   * **Source**: Pilih **Use existing docker-compose.yml**.
4. Klik **Next** -> **Next** -> Centang opsi *Start the project after it is created* -> Klik **Done**.
5. Synology akan otomatis mendownload image Docker:
   * `webdevops/php-apache:8.2` (PHP 8.2 + Apache siap pakai untuk Laravel)
   * `mariadb:10.11` (Database ringan & kompatibel penuh dengan MySQL)
   * `phpmyadmin:latest` (Untuk akses database visual lewat browser)
6. Tunggu hingga status Project berubah menjadi **hijau (Running)**.

---

### Langkah 4: Setup Laravel di Container (Artisan)

Setelah container berjalan:
1. Di Container Manager, klik menu **Container**.
2. Klik container bernama `qcms-app` -> klik **Action** (atau klik dua kali nama container) -> pilih tab **Terminal**.
3. Klik tombol **Create** (buat terminal baru bertipe `bash` atau `sh`).
4. Jalankan perintah berikut satu per satu di terminal:

```bash
# 1. Berikan permission ke storage & bootstrap/cache
chmod -R 777 storage bootstrap/cache

# 2. Buat App Key Laravel
php artisan key:generate

# 3. Buat symbolic link untuk upload file/gambar
php artisan storage:link

# 4. Jalankan migrasi dan seeder database
php artisan migrate --seed --force
```

---

### Langkah 5: Akses Website

* **Website QCMS**:
  Buka tab baru di browser: `http://<IP_SYNOLOGY_KAMU>:8080`
* **phpMyAdmin (Kelola Database)**:
  Buka: `http://<IP_SYNOLOGY_KAMU>:8088`
  * Server: `db`
  * Username: `ccr_user` (atau `root`)
  * Password: `ccrpassword123` (atau `rootpassword123`)

---

## 🔒 Tips Jika Ingin Diakses dari Luar Kantor/Rumah

Jika website ingin bisa dibuka dari luar jaringan WiFi Synology (tanpa harus buka port router/port forwarding):
* Kamu bisa tetap memanfaatkan Cloudflare Tunnel (`cloudflared`) yang dijalankan langsung sebagai container di Synology, atau menggunakan fitur **Synology QuickConnect / DDNS / Reverse Proxy** di *Control Panel -> Login Portal -> Advanced -> Reverse Proxy*.

