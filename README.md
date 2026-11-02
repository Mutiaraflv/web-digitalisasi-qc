# CCR PRE OUTPUT SHAFT - Laravel & MySQL (FORM-QC-002)

Aplikasi web manajemen laporan kendali mutu (**Component Condition Report - FORM-QC-002**) khusus komponen **OUTPUT SHAFT** berbasis framework **Laravel 12 (PHP 8.2)** dengan database **MySQL (MariaDB)**.

Setiap data yang di-submit melalui antarmuka web CRUD ini langsung dapat diunduh dalam format Excel asli (`.xlsx`) lengkap dengan drawing, foto temuan inspeksi, chart, dan tata letak asli yang **100% identik** dengan file template `SSC SBY-5778 CCR PRE OUTPUT SHAFT.xlsx`.

---

## 🛠️ Tech Stack

- **Framework**: Laravel 12.x (PHP 8.2)
- **Database**: MySQL / MariaDB (XAMPP Port 3306, Database: `ccr_db`)
- **Excel Engine**: `App\Services\ExcelReportService` (PHP Native `ZipArchive` & OpenXML SpreadsheetML)
- **Frontend**: Blade Templating + Custom Responsive QC Dashboard CSS + Lucide Icons

---

## 🚀 Fitur Utama

1. **CRUD Penuh berbasis MySQL**:
   - **Create**: Tambah laporan baru dengan data spesifikasi standar Output Shaft yang otomatis terisi.
   - **Read**: Tabel daftar seluruh laporan QC lengkap dengan filter tipe inspeksi (*Pre / Post*), pencarian (*search*), dan indikator status *ACCEPT / REJECT*.
   - **Update**: Edit seluruh parameter dimensi (Item 01 - 06), runout (*Check TIR*), temuan visual, hingga tanda tangan persetujuan (*Inspector & Leader QC*).
   - **Delete**: Hapus laporan dengan konfirmasi.
   - **Duplicate**: Gandakan laporan yang sudah ada sebagai basis untuk nomor komponen atau SSC ID baru.

2. **Ekspor Excel Sesuai Format Template Asli**:
   - File template master tersimpan di `storage/app/templates/template.xlsx`.
   - Modul `ExcelReportService` menginjeksi data baru langsung ke dalam sel XML lembar kerja tanpa merusak relasi 60+ foto visual, drawing teknis, chartsheet, maupun rumus.
   - Tombol **"🚀 Simpan & Download Excel (.xlsx)"** menyimpan data ke MySQL sekaligus langsung men-download file Excel dalam satu kali klik.

---

## 💻 Cara Menjalankan Aplikasi

1. Pastikan service **MySQL** di XAMPP aktif (Port 3306).
2. Buka PowerShell atau terminal di direktori proyek ini:
   ```powershell
   cd "C:\Users\mutiarafz\.gemini\antigravity\scratch\ccr-laravel"
   ```
3. Jalankan server lokal Laravel:
   ```bash
   php artisan serve --port=8000
   ```
4. Buka browser dan akses:
   ```
   http://localhost:8000
   ```
