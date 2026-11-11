# Buku Panduan Penggunaan Sistem QCMS — PT SSC Works
**Quality Control Management System (Panduan Operasional Umum)**

---

## 1. Gambaran Umum Sistem
**QCMS (Quality Control Management System)** adalah sistem pencatatan pemeriksaan kualitas komponen workshop secara digital di **PT SSC Works** yang dirancang untuk mendukung berbagai template komponen di masa mendatang. *(Catatan rilis saat ini: modul form komponen yang tersedia penuh baru **Output Shaft**; template komponen lain akan ditambahkan bertahap sesuai kebutuhan).*

Dengan sistem ini, teknisi dan QC Leader tidak perlu lagi menyusun laporan secara manual di banyak file terpisah:
- Input ukuran komponen, foto temuan, pengukuran lanjutan, rekomendasi perbaikan, dan tanda tangan personil dilakukan dalam **satu form terpadu** sesuai jenis komponennya.
- Tampilan dokumen resmi **format A4** langsung muncul secara **real-time** di sisi kanan layar sambil mengetik.
- Laporan siap diunduh menjadi **file Excel (`.xlsx`)** maupun **PDF multi-halaman siap cetak** dalam satu klik.

---

## 2. Peran Pengguna & Hak Akses

| Peran (Role) | Siapa Penggunanya | Apa Saja yang Bisa Dilakukan |
|---|---|---|
| **Inspector QC** | Teknisi / Pemeriksa QC workshop | Membuat form inspeksi baru, menyimpan Draft, mengirim laporan (Submit), mengedit/menghapus laporan yang dibuat atau diperiksanya, mengunduh Excel/PDF, melihat aktivitas laporan pribadi di Dashboard, mengatur Profil, serta mengganti Tema & Bahasa. |
| **QC Leader** | Koordinator / Peninjau QC | Semua hak Inspector QC, ditambah menerima daftar tugas review di **Waiting My Approval**, membuka preview dokumen laporan, serta mengesahkan laporan dengan tombol **Finish Review**. |
| **Administrator** | Pengelola Sistem | Akses penuh: memantau **Log Activity Real-Time** seluruh akun di Dashboard serta menambah, mengubah role/sandi, dan menghapus akun melalui **Pengaturan Sistem > Kelola Akun**. |

> **Catatan:** Untuk mendapatkan akun baru atau reset peran/kata sandi jika lupa, silakan hubungi **Administrator** sistem.

---

## 3. Masuk ke Sistem & Mengenal Menu

### A. Cara Login
1. Buka halaman web QCMS melalui browser di komputer atau HP.
2. Masukkan **Email** dan **Kata sandi** akun Anda.
3. *(Opsional)* Centang **Ingat saya di perangkat ini** jika memakai perangkat kerja pribadi agar tidak perlu sering login ulang.
4. Klik tombol **Masuk**.

### B. Mengenal Menu Sidebar (Sebelah Kiri)
- **Dashboard**: Halaman ringkasan grafik inspeksi dan catatan aktivitas.
- **Riwayat Laporan**: Tempat melihat daftar laporan, mengajukan **Form Inspeksi Baru**, mengunduh Excel/PDF, dan memeriksa laporan yang menunggu persetujuan (*badge angka merah* menandakan ada laporan menunggu review Anda).
- **Template Komponen**: Katalog jenis formulir QC per komponen. Saat ini baru tersedia template aktif untuk **Output Shaft** *(template komponen lain akan tersedia pada pembaruan berikutnya)*.
- **Pengaturan Sistem**:
  - **Tema & Bahasa**: Mengubah warna layar (**Terang** / **Gelap**) dan bahasa tampilan (**Bahasa Indonesia** / **English**).
  - **Kelola Akun** *(khusus Administrator)*: Mengatur daftar akun pekerja dan role.
- **Profil Akun** *(pojok kiri bawah sidebar)*: Klik foto/nama Anda untuk mengganti foto profil, nama, email, atau kata sandi.
- **Tombol Keluar** *(ikon pintu di pojok kanan bawah sidebar)*: Keluar dari akun (dilengkapi dialog konfirmasi **Ya / Tidak** agar tidak keluar secara tidak sengaja).

### C. Tips Layar Komputer & HP
- **Di Komputer/Laptop:** Jika ingin tampilan form/tabel lebih lebar, klik tombol **Ciutkan Menu** di sebelah kanan logo `QCMS PT SSC Works`. Untuk membukanya kembali, klik tombol kecil di tepi kiri atas layar.
- **Di HP/Tablet:** Ketuk ikon garis di pojok kiri atas untuk membuka/menutup sidebar. Pada halaman input form, menu tambahan tersedia di tombol titik tiga (**•••**) di kanan atas, dan pratinjau dokumen dapat dibuka lewat tombol mengambang **Preview Dokumen** di kanan bawah.

---

## 4. Memahami Halaman Dashboard

1. **3 Grafik Utama:**
   - **Total Inspeksi:** Jumlah total laporan resmi (tidak termasuk Draft) lengkap dengan grafik batang perbandingan **Pre**, **Post**, **Waiting**, dan **Reviewed**.
   - **Lolos (Accept):** Jumlah laporan yang berstatus memenuhi spesifikasi dan diagram pie proporsi status *Reviewed* vs *Waiting Review*.
   - **First Pass Yield:** Persentase tingkat kelulusan mutu komponen.
2. **Panel Aktivitas:**
   - **Akun Pekerja & QC Leader:** Melihat **Aktivitas Laporan Saya** (lengkap dengan tanggal & jam mengajukan, apakah masih Draft/Waiting Review, dan kapan sudah ter-Reviewed oleh siapa) serta **Jejak Aktivitas Akun Anda**.
   - **Akun Administrator:** Melihat **Log Activity Real-Time** pergerakan semua pengguna (filter cepat: *Semua*, *Submit / Draft*, *Finish Review*, *Akun & Login*).
3. **Menu Cepat Operasional:** Tombol pintas di sebelah kanan untuk langsung membuat laporan baru, membuka arsip, membuka *Waiting My Approval*, atau membuka template.

---

## 5. Alur Kerja Laporan QC (Draft → Waiting Review → Reviewed)

```
 [1] Pekerja isi Form      ──► Pilih "Simpan Draft"   ──► Status: DRAFT
     (Tab 1 s/d Tab 6)         (masih bisa diedit/dilengkapi nanti)
                                        │
                           Jika data sudah lengkap, klik "Submit"
                                        ▼
 [2] Laporan Diajukan      ──► Status: WAITING REVIEW
                               • Masuk ke tab "Semua Laporan"
                               • Muncul di "Waiting My Approval" milik Peninjau
                               • Angka pengingat merah muncul di menu Peninjau
                                        │
                           Peninjau klik "Mulai Review" lalu "Finish Review"
                                        ▼
 [3] Selesai Diperiksa     ──► Status: REVIEWED
                               Laporan sah & tercatat selesai di riwayat sistem
```

### Aturan Penting Akses Laporan:
- **Draft** hanya tampil di tab **Pengajuan Saya** milik **Pembuat (`Created by`)** dan **Pemeriksa (`Checked by`)**. Laporan Draft tidak terlihat oleh pekerja lain dan belum masuk hitungan grafik Dashboard.
- **Yang boleh Edit atau Hapus laporan** hanyalah akun **Pembuat** dan **Pemeriksa** yang tercantum di laporan tersebut. Pekerja lain tetap dapat melihat laporan yang sudah diajukan serta mengunduh file Excel dan PDF-nya.
- **Yang boleh Finish Review** hanyalah akun yang namanya dipilih pada kolom **Peninjau (`Reviewed by`)** di Tab 6.

---

## 6. Panduan Mengisi Form Inspeksi Baru

Masuk ke menu **Riwayat Laporan**, lalu klik tombol biru **Form Inspeksi Baru** di sebelah filter. Layar terbagi menjadi dua sisi: **Panel Kiri untuk mengisi data** dan **Panel Kanan untuk melihat preview resmi dokumen A4**.

### Langkah 1 — Pilih Komponen
Pada bagian atas panel form, pilih jenis komponen yang akan diinspeksi. *(Saat ini baru tersedia komponen **Output Shaft** dengan status Aktif; komponen lainnya akan hadir di pembaruan berikutnya).*

### Langkah 2 — Isi Tab Formulir Secara Berurutan

1. **Tab 1 — `1. Identitas & Job`**
   - **Kontrol Dokumen Standar:** Secara otomatis terisi mengikuti kode dokumen, revisi, dan tanggal berlaku standar komponen terpilih (dapat disesuaikan manual bila perlu).
   - **Identitas Komponen & Order Kerja:**
     - **SSC ID `*` *(Wajib Diisi)*:** Nomor unik pekerjaan workshop (sudah terisi nomor urutan otomatis berikutnya dan dapat diubah sesuai job).
     - Isi **No. Laporan**, **Tanggal Inspeksi**, pilih **Tipe Inspeksi** (`Pre-Inspection` untuk pengecekan awal komponen masuk, atau `Post-Inspection` untuk pengecekan akhir setelah perbaikan).
     - Lengkapi **Customer / Perusahaan**, **Nama Komponen**, **Model Unit**, **Job No.**, **Quote No.**, dan **Part No.**

2. **Tab 2 — `2. Nilai Dimensi`**
   - Masukkan hasil ukur aktual (dalam mm) pada setiap titik dimensi yang tersedia sesuai standar komponen terpilih *(untuk **Output Shaft** tersedia titik ukur **`01` sampai `06`** lengkap dengan dimensi area **A–I** dan Panjang Total **L**)*.
   - Pada setiap kolom **Status**, pilih:
     - **`A` (Accept)** jika ukuran masuk toleransi standar.
     - **`R` (Reject)** jika ukuran di luar batas toleransi.
   - Isi **Catatan Visual** apabila terdapat kondisi khusus pada area pengukuran.

3. **Tab 3 — `3. Upload Foto Bagian`**
   - Unggah foto dokumentasi ke slot foto yang disediakan oleh template komponen *(pada **Output Shaft** tersedia slot terstruktur untuk *Drawing*, *Finding Item*, foto visual area, uji penetran/NDT, dan *Diagram TIR*)*.
   - Di setiap slot foto tersedia 3 tombol praktis:
     - **Kamera:** Membuka kamera HP/perangkat secara langsung untuk memotret komponen di bengkel.
     - **Galeri:** Memilih file gambar yang sudah tersimpan di HP/komputer.
     - **Ikon Tempat Sampah (Hapus):** Menghapus foto dari slot tersebut.
   - Khusus untuk slot cetak biru / diagram standar (*Drawing Dimensi* dan *Diagram TIR*), tersedia tombol pintas **Gunakan Standar...** agar tidak perlu mengunggah gambar referensi secara manual.
   - Angka badge pada judul tab `3. Upload Foto Bagian` menunjukkan jumlah slot foto yang sudah terisi.

4. **Tab 4 — Pengukuran Lanjutan (Mis. `Runout / TIR`)**
   - Masukkan angka pembacaan alat ukur tambahan sesuai form komponen *(pada **Output Shaft** tersedia pembacaan dial indicator **TIR** sudut **`0°`, `90°`, `180°`, dan `270°`** untuk titik **A sampai F** beserta acuan datum dan batas toleransi)*.
   - Pilih status **`A`** atau **`R`** pada ujung setiap baris.

5. **Tab 5 — `5. Rekomendasi`**
   - Catat temuan visual (**Remarks**) dan tindakan perbaikan yang disarankan (**Rekomendasi Tindakan QC**).
   - Klik **Tambah Item Baru** untuk menambah nomor item baru (`01`, `02`, dst.).
   - Jika satu item memiliki lebih dari satu temuan & saran perbaikan, gunakan tombol **`+ Baris`** pada item tersebut agar nomor itemnya otomatis tergabung rapi di PDF/Excel.

6. **Tab 6 — `6. Pengesahan`**
   - Pilih akun personil pada tiga kolom utama:
     - **`Checked by` (Nama Pemeriksa)** — akun terpilih otomatis memiliki akses untuk membuka dan ikut melengkapi laporan ini.
     - **`Created by` (Nama Pembuat)** — secara baku otomatis terisi akun Anda yang sedang login.
     - **`Reviewed by` (Nama Peninjau)** — laporan setelah di-Submit akan langsung masuk ke antrean **Waiting My Approval** milik akun ini.
   - Lengkapi **Jabatan** (`QC Inspector`, `Leader QC`, `Workshop Supervisor`, atau `QC Engineer`) dan **Tanggal**.

### Langkah 3 — Cek Preview, Simpan Draft, Submit, atau Unduh
Gunakan toolbar di kanan atas layar form:
- **Kosongkan:** Menghapus semua isian form dan memulai form kosong baru (akan muncul konfirmasi keamanan terlebih dahulu).
- **Simpan Draft** *(ikon jam/berkas)*: Menyimpan pekerjaan Anda sementara tanpa mengirim ke peninjau. Cocok jika pengukuran baru selesai sebagian.
- **Ekspor Dokumen** *(ikon panah unduh)*:
  - **Ekspor PDF:** Menyimpan laporan saat ini terlebih dahulu lalu membuka tampilan **Halaman A4 Resmi** siap cetak / *Save as PDF*.
  - **Unduh Excel:** Menyimpan laporan saat ini lalu langsung mengunduh file Excel resmi (`.xlsx`) sesuai template komponen.
- **Submit** *(tombol biru utama)*: Mengirim laporan ke antrean review (`Waiting Review`). Saat sedang mengedit laporan yang sudah dikirim, tombol ini berfungsi sebagai **Simpan Perubahan**.

> **Tips Preview:** Pada panel preview sebelah kanan, gunakan tombol nomor **Lembar** atau **Lihat Semua** untuk memeriksa seluruh hasil ketikan dan foto sebelum laporan dikirim.

---

## 7. Mencari, Mengelola & Mengunduh Riwayat Laporan

Buka menu **Riwayat Laporan**:
1. **Tab Filter Cepat:**
   - **Semua Laporan:** Seluruh laporan yang sudah di-Submit oleh tim (Draft tidak tampil di sini).
   - **Pengajuan Saya:** Semua laporan yang Anda buat atau periksa (termasuk yang masih berstatus **Draft**). Di tab ini tersedia filter status tambahan (**Semua Status**, **Draft**, **Dikirim**).
   - **Waiting My Approval:** Daftar laporan yang menunggu giliran Anda tinjau (*Reviewed by*).
2. **Pencarian & Filter:** Ketik **SSC ID**, nama **Customer**, **Part**, atau **Model Unit** pada kotak pencarian, lalu pilih filter **Tipe** (`Pre-Inspection` / `Post-Inspection`) sesuai kebutuhan.
3. **Tombol Aksi di Setiap Baris:**
   - Klik **`Unduh`** → pilih **`Unduh Excel (.xlsx)`** atau **`Lihat / Cetak PDF`**.
   - Klik **`Edit`** jika Anda pembuat/pemeriksa untuk memperbaiki data, foto, atau melengkapi Draft lalu menekan Submit.
   - Klik ikon **Tempat Sampah** *(merah)* untuk menghapus laporan (khusus pembuat/pemeriksa).

---

## 8. Panduan Review & Approval (Khusus Peninjau / QC Leader)

Jika akun Anda dipilih sebagai **Peninjau (`Reviewed by`)**:
1. Angka pengingat merah akan muncul di samping menu **Riwayat Laporan**, pada kartu **Waiting My Approval**, serta di **Dashboard**.
2. Buka menu **Riwayat Laporan** → pilih tab **Waiting My Approval**.
3. Klik tombol biru **Mulai Review** pada baris laporan yang ingin diperiksa.
4. Periksa seluruh isi **6 Halaman A4** (dimensi, foto tiap bagian, TIR, rekomendasi, dan tanda tangan). Anda juga dapat mencetak/mengunduh PDF melalui tombol **Cetak / PDF**.
5. Jika seluruh hasil pemeriksaan sudah sesuai, klik tombol hijau **✓ Finish Review** di bilah atas.
6. Pada jendela konfirmasi, klik **Ya, Finish Review**. Status laporan otomatis berubah menjadi **Reviewed** dan hilang dari antrean *Waiting My Approval*.

---

## 9. Mengelola Profil, Tema Gelap/Terang, Bahasa & Akun

### A. Mengubah Profil & Kata Sandi Sendiri
1. Klik **foto/nama akun Anda** di pojok kiri bawah sidebar.
2. Pada kotak **Edit Informasi Profil**:
   - Klik ikon kamera pada foto atau pilih file gambar baru untuk **mengubah foto profil** (format `JPG`, `PNG`, atau `WEBP`, ukuran maksimal **3 MB**). Klik **Hapus Foto** jika ingin kembali ke inisial nama.
   - Ubah **Nama Lengkap** atau **Alamat Email**, lalu klik **Simpan Profil**. *(Jabatan/Peran hanya dapat diubah oleh Administrator).*
3. Pada kotak **Ganti Kata Sandi**:
   - Masukkan **Kata Sandi Saat Ini**, **Kata Sandi Baru** (minimal 6 karakter), dan **Konfirmasi Kata Sandi Baru**, lalu klik **Perbarui Sandi**.

### B. Mengganti Tema (Terang / Gelap) & Bahasa (Indonesia / Inggris)
1. Buka sub-menu **Pengaturan Sistem > Tema & Bahasa** di sidebar.
2. Pilih **Tema Website**:
   - **Terang (Light Mode):** Tampilan cerah standar.
   - **Gelap (Dark Mode):** Tampilan gelap nyaman di mata dengan teks & ikon tetap jelas terbaca *(lembar preview dokumen resmi A4 tetap ditampilkan dengan latar kertas putih agar sama persis dengan hasil cetak)*.
3. Pilih **Bahasa Sistem**: **Bahasa Indonesia** atau **English** (mengubah seluruh teks menu, grafik, tabel, tombol, status, hingga notifikasi).
4. Klik tombol **Terapkan Pengaturan**. Pilihan Anda tersimpan di akun sehingga tetap aktif setiap kali login kembali.

### C. Kelola Akun *(Khusus Administrator)*
1. Buka sub-menu **Pengaturan Sistem > Kelola Akun**.
2. Untuk menambah personil baru, klik **Tambah Akun Baru** → isi Nama Lengkap, Email, Role (`Inspector QC` / `QC Leader` / `Administrator`), dan Kata Sandi Awal (min. 6 karakter) → klik **Simpan Akun Baru**.
3. Untuk memperbarui akun atau mereset kata sandi pekerja yang lupa, klik tombol **Edit** pada baris akun tersebut. Kosongkan kolom *Kata Sandi Baru* jika hanya ingin mengubah nama/email/role.
4. Untuk menonaktifkan akun yang sudah tidak terpakai, klik tombol **Hapus** *(Administrator tidak dapat menghapus akun miliknya sendiri yang sedang aktif dipakai)*.

---

## 10. Tanya Jawab Cepat (Kendala Umum di Lapangan)

| Pertanyaan / Kendala | Penyebab & Solusi |
|---|---|
| **Laporan sudah disimpan, kenapa belum muncul di tab *Semua Laporan*?** | Laporan masih berstatus **Draft** karena Anda menekan *Simpan Draft*. Buka tab **Pengajuan Saya**, klik **Edit** pada laporan tersebut, lalu klik tombol biru **Submit**. |
| **Kenapa saya tidak melihat tombol *Edit* atau *Hapus* pada sebuah laporan?** | Tombol **Edit** dan **Hapus** hanya muncul bagi akun **Pembuat (`Created by`)** dan **Pemeriksa (`Checked by`)** laporan tersebut. Anda tetap bisa mengunduh Excel/PDF dari menu **Unduh**. |
| **Laporan sudah dikirim teman, tapi tidak muncul di *Waiting My Approval* saya?** | Pastikan saat mengisi **Tab 6 (Pengesahan)**, nama akun Anda sudah dipilih di kolom **Nama Peninjau (`Reviewed by`)**. Minta pembuat/pemeriksa untuk **Edit** Tab 6 bila peninjau keliru dipilih. |
| **Kenapa menu *Kelola Akun* tidak muncul di akun saya?** | Menu **Kelola Akun** hanya tersedia bagi akun dengan role **Administrator**. |
| **Gagal unggah foto profil saat ganti avatar?** | Pastikan format file adalah **JPG, PNG, atau WEBP** dengan ukuran tidak lebih dari **3 MB**. |
| **Setelah mengganti Tema/Bahasa, tampilan terasa belum berubah?** | Pastikan Anda sudah menekan tombol **Terapkan Pengaturan**, lalu tekan refresh (`Ctrl+F5` di PC atau muat ulang halaman di HP). |

---

## 11. Checklist Cepat Sebelum Klik Submit

- [ ] **Tab 1:** Nomor **SSC ID**, **No. Laporan**, Customer, Part, dan Tipe (*Pre-Inspection* / *Post-Inspection*) sudah benar.
- [ ] **Tab 2 & 4:** Nilai ukur dimensi serta pembacaan pengukuran lanjutan (seperti *Runout / TIR* pada Output Shaft) sudah lengkap beserta status **`A`** atau **`R`**.
- [ ] **Tab 3:** Foto kondisi komponen dan pengetesan NDT terlihat jelas, serta gambar cetak biru / diagram standar komponen sudah terpasang.
- [ ] **Tab 5:** Kolom **Remarks** dan saran **Rekomendasi Tindakan QC** sudah terisi jelas.
- [ ] **Tab 6:** Nama **Pemeriksa (`Checked by`)** dan **Peninjau (`Reviewed by`)** sudah dipilih agar laporan masuk ke antrean approval.
- [ ] **Preview:** Tombol **Lihat Semua** sudah diperiksa dan tata letak seluruh lembar A4 siap cetak sudah rapi sebelum klik **Submit**.

> **Butuh Bantuan Lebih Lanjut?** Jika mengalami kendala akun, lupa kata sandi, atau masalah teknis di area workshop, hubungi **QC Leader** atau **Administrator QCMS PT SSC Works**.
