<?php

require_once __DIR__ . '/PptxGenerator.php';

$outputDir = __DIR__ . '/../public/presentasi';
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0777, true);
}
$outputPath = $outputDir . '/Presentasi_QCMS_PT_SSC_Works.pptx';
$rootOutputPath = __DIR__ . '/../Presentasi_QCMS_PT_SSC_Works.pptx';

$gen = new PptxGenerator($outputPath);

// ==========================================
// SLIDE 1: COVER (Dark Navy)
// ==========================================
$s1 = new SlideBuilder('0F172A');
$s1->addBackground();

// Decorative Header Line / Glow
$s1->addCard(800000, 700000, 2000000, 40000, '0284C7', '', 0, 0);

// Tag
$s1->addTextBox(800000, 900000, 10500000, 400000, [
    ['text' => 'PT SSC WORKS • WORKSHOP QUALITY ASSURANCE', 'size' => 1300, 'bold' => true, 'color' => '38BDF8']
]);

// Main Title
$s1->addTextBox(800000, 1400000, 10500000, 1400000, [
    ['text' => 'QCMS — Quality Control Management System', 'size' => 3200, 'bold' => true, 'color' => 'FFFFFF'],
    ['spacingAfter' => 10],
    ['text' => 'Digitalisasi Sistem Laporan Kendali Mutu Workshop & Otomasi Dokumen FORM-QC-002', 'size' => 1700, 'bold' => false, 'color' => '94A3B8']
]);

// Info Card
$s1->addCard(800000, 3200000, 10592000, 2600000, '1E293B', '334155', 12700, 3000);
$s1->addTextBox(1100000, 3450000, 10000000, 2100000, [
    ['text' => 'FOKUS IMPLEMENTASI & SPESIFIKASI SISTEM:', 'size' => 1200, 'bold' => true, 'color' => '38BDF8'],
    ['spacingAfter' => 8],
    ['runs' => [
        ['text' => '• Modul Komponen Utama: ', 'size' => 1400, 'bold' => true, 'color' => 'FFFFFF'],
        ['text' => 'OUTPUT SHAFT (Component Condition Report - FORM-QC-002)', 'size' => 1400, 'bold' => false, 'color' => 'E2E8F0']
    ]],
    ['runs' => [
        ['text' => '• Arsitektur Aplikasi: ', 'size' => 1400, 'bold' => true, 'color' => 'FFFFFF'],
        ['text' => 'Laravel 12 (PHP 8.2), MySQL / MariaDB, Native OpenXML Excel Engine, Responsive Blade UI', 'size' => 1400, 'bold' => false, 'color' => 'E2E8F0']
    ]],
    ['runs' => [
        ['text' => '• Kapabilitas Inti: ', 'size' => 1400, 'bold' => true, 'color' => 'FFFFFF'],
        ['text' => 'Dual-Pane Real-Time A4 Live Preview, Kamera Workshop Langsung, Multi-Role Approval, Ekspor Excel 100% Identik & Cetak PDF', 'size' => 1400, 'bold' => false, 'color' => 'E2E8F0']
    ]],
    ['runs' => [
        ['text' => '• Penyusun Presentasi: ', 'size' => 1400, 'bold' => true, 'color' => 'FFFFFF'],
        ['text' => 'Tim Pengembang Sistem & Quality Control PT SSC Works (2026)', 'size' => 1400, 'bold' => false, 'color' => '38BDF8']
    ]]
]);

$s1->addFooter("PT SSC Works — Engineering & Quality Control Division", "FORM-QC-002 Output Shaft", true);
$gen->addSlide($s1->toXml());

// ==========================================
// SLIDE 2: LATAR BELAKANG & MASALAH
// ==========================================
$s2 = new SlideBuilder('F8FAFC');
$s2->addBackground();
$s2->addHeader('LATAR BELAKANG & TANTANGAN', 'Masalah pada Sistem Pelaporan Manual Konvensional', 'Pencatatan laporan QC menggunakan file spreadsheet terpisah menimbulkan banyak kendala operasional.');

$cardW = 3330000;
$cardH = 4200000;
$cardY = 1900000;

// Problem 1
$s2->addCard(800000, $cardY, $cardW, $cardH, 'FFFFFF', 'FECACA', 12700, 2000);
$s2->addCard(1000000, $cardY + 250000, 800000, 350000, 'FEE2E2', '', 0, 1000);
$s2->addTextBox(1000000, $cardY + 300000, 800000, 250000, [
    ['text' => 'MASALAH 1', 'size' => 1000, 'bold' => true, 'color' => 'DC2626', 'align' => 'ctr']
]);
$s2->addTextBox(1000000, $cardY + 750000, 2930000, 3200000, [
    ['text' => 'Fragmentasi File & Human Error', 'size' => 1600, 'bold' => true, 'color' => '0F172A'],
    ['spacingAfter' => 10],
    ['bullet' => true, 'text' => 'File Excel disimpan terpisah-pisah di berbagai folder komputer teknisi tanpa database terpusat.', 'size' => 1250, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Rumus perhitungan deviasi toleransi dimensi rawan tertimpa atau terhapus secara tidak sengaja.', 'size' => 1250, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Tingginya potensi salah ketik angka ukur dan status kelulusan komponen.', 'size' => 1250, 'color' => '475569']
]);

// Problem 2
$s2->addCard(4430000, $cardY, $cardW, $cardH, 'FFFFFF', 'FED7AA', 12700, 2000);
$s2->addCard(4630000, $cardY + 250000, 800000, 350000, 'FFEDD5', '', 0, 1000);
$s2->addTextBox(4630000, $cardY + 300000, 800000, 250000, [
    ['text' => 'MASALAH 2', 'size' => 1000, 'bold' => true, 'color' => 'EA580C', 'align' => 'ctr']
]);
$s2->addTextBox(4630000, $cardY + 750000, 2930000, 3200000, [
    ['text' => 'Foto Temuan Sering Bergeser & Rusak', 'size' => 1600, 'bold' => true, 'color' => '0F172A'],
    ['spacingAfter' => 10],
    ['bullet' => true, 'text' => 'Lebih dari 60 slot foto inspeksi visual, drawing dimensi, dan NDT test sulit ditata di Excel biasa.', 'size' => 1250, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Foto sering bergeser posisi atau terdistorsi skala aspek rasionya saat dibuka di komputer lain.', 'size' => 1250, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Teknisi harus memindahkan foto dari HP ke laptop secara manual menggunakan kabel data.', 'size' => 1250, 'color' => '475569']
]);

// Problem 3
$s2->addCard(8060000, $cardY, $cardW, $cardH, 'FFFFFF', 'E2E8F0', 12700, 2000);
$s2->addCard(8260000, $cardY + 250000, 800000, 350000, 'F1F5F9', '', 0, 1000);
$s2->addTextBox(8260000, $cardY + 300000, 800000, 250000, [
    ['text' => 'MASALAH 3', 'size' => 1000, 'bold' => true, 'color' => '475569', 'align' => 'ctr']
]);
$s2->addTextBox(8260000, $cardY + 750000, 2930000, 3200000, [
    ['text' => 'Alur Approval & Audit Lambat', 'size' => 1600, 'bold' => true, 'color' => '0F172A'],
    ['spacingAfter' => 10],
    ['bullet' => true, 'text' => 'Proses paraf dan tanda tangan pengesahan oleh QC Leader sering tertunda karena menunggu lembar cetak fisik.', 'size' => 1250, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Tidak ada sistem notifikasi antrean review yang jelas bagi pimpinan.', 'size' => 1250, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Pelacakan riwayat (audit trail) revisi dan tanggal pengesahan sulit dibuktikan secara kredibel.', 'size' => 1250, 'color' => '475569']
]);

$s2->addFooter();
$gen->addSlide($s2->toXml());

// ==========================================
// SLIDE 3: SOLUSI YANG DIHADIRKAN (QCMS)
// ==========================================
$s3 = new SlideBuilder('F8FAFC');
$s3->addBackground();
$s3->addHeader('SOLUSI SISTEM', 'QCMS: Transformasi Digital Kendali Mutu Terpadu', 'Menghubungkan proses pemeriksaan bengkel langsung dengan database terpusat dan dokumen standar siap cetak.');

// 3 Solusi Utama
$s3->addCard(800000, $cardY, $cardW, $cardH, 'FFFFFF', 'BAE6FD', 12700, 2000);
$s3->addCard(1000000, $cardY + 250000, 1200000, 350000, 'E0F2FE', '', 0, 1000);
$s3->addTextBox(1000000, $cardY + 300000, 1200000, 250000, [
    ['text' => 'SATU PINTU INPUT', 'size' => 1000, 'bold' => true, 'color' => '0284C7', 'align' => 'ctr']
]);
$s3->addTextBox(1000000, $cardY + 750000, 2930000, 3200000, [
    ['text' => 'Form Terpadu 6-Tab', 'size' => 1600, 'bold' => true, 'color' => '0F172A'],
    ['spacingAfter' => 10],
    ['bullet' => true, 'text' => 'Seluruh parameter komponen (identitas, dimensi, TIR, foto, rekomendasi, pengesahan) diisi dalam 1 antarmuka web yang terorganisir.', 'size' => 1250, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Dilengkapi fitur Simpan Draft sehingga teknisi dapat melengkapi data secara bertahap.', 'size' => 1250, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Validasi otomatis batas toleransi dimensi standar industri.', 'size' => 1250, 'color' => '475569']
]);

// Solution 2
$s3->addCard(4430000, $cardY, $cardW, $cardH, 'FFFFFF', 'BBF7D0', 12700, 2000);
$s3->addCard(4630000, $cardY + 250000, 1300000, 350000, 'DCFCE7', '', 0, 1000);
$s3->addTextBox(4630000, $cardY + 300000, 1300000, 250000, [
    ['text' => 'LIVE DOKUMEN A4', 'size' => 1000, 'bold' => true, 'color' => '16A34A', 'align' => 'ctr']
]);
$s3->addTextBox(4630000, $cardY + 750000, 2930000, 3200000, [
    ['text' => 'Real-Time Live Preview', 'size' => 1600, 'bold' => true, 'color' => '0F172A'],
    ['spacingAfter' => 10],
    ['bullet' => true, 'text' => 'Panel pratinjau dokumen A4 resmi langsung terbentuk di sisi kanan layar saat teknisi mengetik data.', 'size' => 1250, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Memastikan format layout 6 halaman lembar kendali mutu 100% presisi sebelum diajukan.', 'size' => 1250, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Menghilangkan kekeliruan tata letak dokumen (WYSIWYG).', 'size' => 1250, 'color' => '475569']
]);

// Solution 3
$s3->addCard(8060000, $cardY, $cardW, $cardH, 'FFFFFF', 'C7D2FE', 12700, 2000);
$s3->addCard(8260000, $cardY + 250000, 1300000, 350000, 'E0E7FF', '', 0, 1000);
$s3->addTextBox(8260000, $cardY + 300000, 1300000, 250000, [
    ['text' => 'OTOMASI EKSPOR', 'size' => 1000, 'bold' => true, 'color' => '4F46E5', 'align' => 'ctr']
]);
$s3->addTextBox(8260000, $cardY + 750000, 2930000, 3200000, [
    ['text' => 'Excel & PDF 1-Klik', 'size' => 1600, 'bold' => true, 'color' => '0F172A'],
    ['spacingAfter' => 10],
    ['bullet' => true, 'text' => 'Ekspor ke Excel asli (.xlsx) tanpa merusak master template SSC SBY-5778, formula kalkulasi, dan 60+ foto visual.', 'size' => 1250, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Ekspor dokumen PDF multi-halaman siap cetak berstandar ISO workshop.', 'size' => 1250, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Alur approval berjenjang digital dengan penanda badge notifikasi real-time.', 'size' => 1250, 'color' => '475569']
]);

$s3->addFooter();
$gen->addSlide($s3->toXml());

// ==========================================
// SLIDE 4: ARSITEKTUR & TECH STACK
// ==========================================
$s4 = new SlideBuilder('F8FAFC');
$s4->addBackground();
$s4->addHeader('ARSITEKTUR & TEKNOLOGI', 'Pondasi Teknologi Sistem Handal & Skalabel', 'Kombinasi teknologi open-source enterprise yang teruji, cepat, aman, dan mudah dipelihara.');

$colW = 5146000;
$rowH = 2000000;

// Tech 1: Laravel
$s4->addCard(800000, 1900000, $colW, $rowH, 'FFFFFF', 'E2E8F0', 12700, 2000);
$s4->addTextBox(1050000, 2050000, $colW - 500000, $rowH - 300000, [
    ['runs' => [
        ['text' => '⚙️ Backend Framework: ', 'size' => 1500, 'bold' => true, 'color' => '0F172A'],
        ['text' => 'Laravel 12 & PHP 8.2', 'size' => 1500, 'bold' => true, 'color' => 'DC2626']
    ]],
    ['spacingAfter' => 6],
    ['bullet' => true, 'text' => 'Arsitektur MVC (Model-View-Controller) dengan Service Layer khusus.', 'size' => 1200, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Sistem autentikasi aman dengan session throttling dan CSRF protection.', 'size' => 1200, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Eloquent ORM untuk relasi data terstruktur dan performa query optimal.', 'size' => 1200, 'color' => '475569']
]);

// Tech 2: Database
$s4->addCard(6246000, 1900000, $colW, $rowH, 'FFFFFF', 'E2E8F0', 12700, 2000);
$s4->addTextBox(6496000, 2050000, $colW - 500000, $rowH - 300000, [
    ['runs' => [
        ['text' => '🗄️ Database Management: ', 'size' => 1500, 'bold' => true, 'color' => '0F172A'],
        ['text' => 'MySQL / MariaDB', 'size' => 1500, 'bold' => true, 'color' => '0284C7']
    ]],
    ['spacingAfter' => 6],
    ['bullet' => true, 'text' => 'Tabel terpusat (reports, users, activity_logs, parts, customers).', 'size' => 1200, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Penyimpanan parameter dimensi dinamis JSON & kolom relasional terindeks.', 'size' => 1200, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Jejak audit otomatis (timestamps & user action logs) untuk integritas data.', 'size' => 1200, 'color' => '475569']
]);

// Tech 3: Excel Engine
$s4->addCard(800000, 4100000, $colW, $rowH, 'FFFFFF', 'E2E8F0', 12700, 2000);
$s4->addTextBox(1050000, 4250000, $colW - 500000, $rowH - 300000, [
    ['runs' => [
        ['text' => '📊 Excel Engine: ', 'size' => 1500, 'bold' => true, 'color' => '0F172A'],
        ['text' => 'Native OpenXML ZipEngine', 'size' => 1500, 'bold' => true, 'color' => '059669']
    ]],
    ['spacingAfter' => 6],
    ['bullet' => true, 'text' => 'Memanipulasi berkas SpreadsheetML menggunakan ZipArchive PHP bawaan.', 'size' => 1200, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Tidak merusak 60+ foto gambar teknis & chartsheet dari master template asli.', 'size' => 1200, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Eksekusi sangat cepat (< 0.8 detik) tanpa membebani memory server.', 'size' => 1200, 'color' => '475569']
]);

// Tech 4: Frontend UI
$s4->addCard(6246000, 4100000, $colW, $rowH, 'FFFFFF', 'E2E8F0', 12700, 2000);
$s4->addTextBox(6496000, 4250000, $colW - 500000, $rowH - 300000, [
    ['runs' => [
        ['text' => '🎨 Modern Frontend: ', 'size' => 1500, 'bold' => true, 'color' => '0F172A'],
        ['text' => 'Blade, Tailwind & Lucide', 'size' => 1500, 'bold' => true, 'color' => '4F46E5']
    ]],
    ['spacingAfter' => 6],
    ['bullet' => true, 'text' => 'Desain responsif: nyaman diakses via PC, Laptop, Tablet, maupun HP di bengkel.', 'size' => 1200, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Fitur Tema Terang / Gelap (Dark Mode) dan Dukungan Dua Bahasa (ID / EN).', 'size' => 1200, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Integrasi HTML5 Camera API untuk pengambilan foto langsung di lapangan.', 'size' => 1200, 'color' => '475569']
]);

$s4->addFooter();
$gen->addSlide($s4->toXml());

// ==========================================
// SLIDE 5: ALUR KERJA SISTEM (WORKFLOW)
// ==========================================
$s5 = new SlideBuilder('F8FAFC');
$s5->addBackground();
$s5->addHeader('ALUR KERJA OPERASIONAL', 'Siklus Hidup Laporan Kendali Mutu Workshop', 'Alur kerja terstruktur dari input lapangan hingga pengesahan akhir oleh pimpinan QC.');

$stepW = 1950000;
$stepH = 4100000;
$stepGap = 210000;

$steps = [
    [
        'step' => 'TAHAP 1',
        'title' => 'Input Inspeksi',
        'badgeBg' => 'E0F2FE',
        'badgeColor' => '0284C7',
        'desc' => 'Teknisi mengukur fisik Output Shaft, memasukkan dimensi, TIR, dan mengambil foto bagian langsung di workshop.'
    ],
    [
        'step' => 'TAHAP 2',
        'title' => 'Simpan Draft',
        'badgeBg' => 'FEF3C7',
        'badgeColor' => 'D97706',
        'desc' => 'Bila pekerjaan belum tuntas, laporan dapat disimpan sebagai Draft tanpa mengganggu antrean review pimpinan.'
    ],
    [
        'step' => 'TAHAP 3',
        'title' => 'Submit Laporan',
        'badgeBg' => 'E0E7FF',
        'badgeColor' => '4338CA',
        'desc' => 'Setelah data lengkap, klik Submit. Laporan masuk status WAITING REVIEW dan muncul di antrean peninjau.'
    ],
    [
        'step' => 'TAHAP 4',
        'title' => 'Review Pimpinan',
        'badgeBg' => 'FCE7F3',
        'badgeColor' => 'BE185D',
        'desc' => 'QC Leader memeriksa 6 halaman dokumen preview A4, memvalidasi hasil ukur, dan mengklik Finish Review.'
    ],
    [
        'step' => 'TAHAP 5',
        'title' => 'Sah & Ekspor',
        'badgeBg' => 'DCFCE7',
        'badgeColor' => '15803D',
        'desc' => 'Status resmi REVIEWED. Laporan otomatis terkunci, tercatat di audit log, dan siap diunduh Excel / PDF.'
    ]
];

for ($i = 0; $i < 5; $i++) {
    $x = 800000 + ($i * ($stepW + $stepGap));
    $s = $steps[$i];

    $s5->addCard($x, 2000000, $stepW, $stepH, 'FFFFFF', 'E2E8F0', 12700, 2000);

    // Number Badge
    $s5->addCard($x + 200000, 2200000, $stepW - 400000, 350000, $s['badgeBg'], '', 0, 1000);
    $s5->addTextBox($x + 200000, 2250000, $stepW - 400000, 250000, [
        ['text' => $s['step'], 'size' => 1000, 'bold' => true, 'color' => $s['badgeColor'], 'align' => 'ctr']
    ]);

    // Title & Desc
    $s5->addTextBox($x + 200000, 2750000, $stepW - 400000, 3100000, [
        ['text' => $s['title'], 'size' => 1400, 'bold' => true, 'color' => '0F172A', 'align' => 'ctr'],
        ['spacingAfter' => 10],
        ['text' => $s['desc'], 'size' => 1150, 'color' => '475569', 'align' => 'ctr']
    ]);
}

$s5->addFooter();
$gen->addSlide($s5->toXml());

// ==========================================
// SLIDE 6: FITUR UTAMA: FORM 6-TAB TERPADU
// ==========================================
$s6 = new SlideBuilder('F8FAFC');
$s6->addBackground();
$s6->addHeader('FITUR UTAMA 01', 'Formulir Inspeksi Terstruktur Melalui 6 Modul Tab', 'Seluruh kebutuhan pencatatan FORM-QC-002 dibagi ke dalam alur yang logis dan tervalidasi.');

$tabW = 5146000;
$tabH = 1250000;
$tabsData = [
    ['tab' => 'Tab 1: Identitas & Job Order', 'desc' => 'Nomor SSC ID otomatis, No. Laporan, Tanggal Inspeksi, Tipe (Pre/Post), Nama Pelanggan, Model Unit, dan Job No.', 'col' => 0, 'row' => 0, 'badge' => '0284C7'],
    ['tab' => 'Tab 2: Nilai Dimensi Aktual', 'desc' => 'Titik ukur 01–06 (Area A s/d I dan Total Length L). Dilengkapi batas toleransi dan seleksi status Accept / Reject.', 'col' => 1, 'row' => 0, 'badge' => '0284C7'],
    ['tab' => 'Tab 3: Upload Foto Multi-Slot', 'desc' => 'Slot terstruktur untuk Drawing Dimensi, Finding Item 01–06, Visual Area A–I, Uji NDT Penetrant, dan Diagram TIR.', 'col' => 0, 'row' => 1, 'badge' => '16A34A'],
    ['tab' => 'Tab 4: Pengukuran Lanjutan TIR', 'desc' => 'Pencatatan runout dial indicator pada sudut 0°, 90°, 180°, dan 270° untuk titik A sampai F lengkap dengan batas toleransi.', 'col' => 1, 'row' => 1, 'badge' => '16A34A'],
    ['tab' => 'Tab 5: Rekomendasi Tindakan QC', 'desc' => 'Tabel temuan visual (Remarks) dan rekomendasi perbaikan teknis berjenjang (dapat menambah baris item dinamis).', 'col' => 0, 'row' => 2, 'badge' => '4F46E5'],
    ['tab' => 'Tab 6: Pengesahan & Tanda Tangan', 'desc' => 'Penetapan akun personil (Created by, Checked by, Reviewed by), jabatan workshop, tanggal, dan penguncian laporan.', 'col' => 1, 'row' => 2, 'badge' => '4F46E5']
];

foreach ($tabsData as $td) {
    $x = $td['col'] === 0 ? 800000 : 6246000;
    $y = 1900000 + ($td['row'] * 1450000);

    $s6->addCard($x, $y, $tabW, $tabH, 'FFFFFF', 'E2E8F0', 12700, 2000);
    $s6->addTextBox($x + 250000, $y + 180000, $tabW - 500000, $tabH - 300000, [
        ['text' => $td['tab'], 'size' => 1350, 'bold' => true, 'color' => $td['badge']],
        ['spacingAfter' => 4],
        ['text' => $td['desc'], 'size' => 1100, 'color' => '475569']
    ]);
}

$s6->addFooter();
$gen->addSlide($s6->toXml());

// ==========================================
// SLIDE 7: DUAL-PANE LIVE A4 PREVIEW
// ==========================================
$s7 = new SlideBuilder('F8FAFC');
$s7->addBackground();
$s7->addHeader('FITUR UNGGULAN 02', 'Dual-Pane View: Formulir Kiri, Dokumen A4 di Kanan', 'Konsep WYSIWYG (What You See Is What You Get) untuk menghilangkan kesalahan format cetak.');

// Left concept box
$s7->addCard(800000, 1900000, 5146000, 4200000, 'FFFFFF', 'BAE6FD', 12700, 2000);
$s7->addTextBox(1050000, 2150000, 4646000, 3700000, [
    ['text' => '📱 Panel Input Terpadu (Kiri)', 'size' => 1700, 'bold' => true, 'color' => '0284C7'],
    ['spacingAfter' => 10],
    ['bullet' => true, 'text' => 'Navigasi tab fleksibel untuk memasukkan data spesifikasi komponen.', 'size' => 1250, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Input numerik aktual langsung menghitung status deviasi terhadap toleransi.', 'size' => 1250, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Slot upload foto responsif dengan integrasi kamera workshop.', 'size' => 1250, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Toolbar cepat: Kosongkan Form, Simpan Draft, Ekspor, dan Submit.', 'size' => 1250, 'color' => '475569']
]);

// Right concept box
$s7->addCard(6246000, 1900000, 5146000, 4200000, 'FFFFFF', 'BBF7D0', 12700, 2000);
$s7->addTextBox(6496000, 2150000, 4646000, 3700000, [
    ['text' => '📄 Dokumen Resmi A4 Real-Time (Kanan)', 'size' => 1700, 'bold' => true, 'color' => '16A34A'],
    ['spacingAfter' => 10],
    ['bullet' => true, 'text' => 'Render dokumen resmi 6 halaman A4 muncul secara real-time saat teks diketik.', 'size' => 1250, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Kop resmi PT SSC Works, logo perusahaan, dan nomor kontrol dokumen otomatis terpasang.', 'size' => 1250, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Tersedia tombol pemilih halaman (Lembar 1 s/d 6) atau mode Lihat Semua Lembar.', 'size' => 1250, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Menjamin 100% kesesuaian antara data formulir web dengan hasil cetak fisik.', 'size' => 1250, 'color' => '475569']
]);

$s7->addFooter();
$gen->addSlide($s7->toXml());

// ==========================================
// SLIDE 8: INTEGRASI FOTO WORKSHOP & KAMERA
// ==========================================
$s8 = new SlideBuilder('F8FAFC');
$s8->addBackground();
$s8->addHeader('FITUR UNGGULAN 03', 'Dokumentasi Visual & Kamera Workshop Langsung', 'Mempercepat proses dokumentasi foto kondisi fisik komponen dan pengetesan NDT di bengkel.');

$s8->addCard(800000, $cardY, $cardW, $cardH, 'FFFFFF', 'E2E8F0', 12700, 2000);
$s8->addTextBox(1000000, $cardY + 300000, 2930000, 3500000, [
    ['text' => '📸 Direct Camera API', 'size' => 1600, 'bold' => true, 'color' => '0284C7'],
    ['spacingAfter' => 10],
    ['bullet' => true, 'text' => 'Teknisi cukup mengetuk ikon kamera untuk langsung memotret komponen dari HP/tablet di area workshop.', 'size' => 1250, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Tidak perlu lagi kabel data atau transfer file rumit antar perangkat.', 'size' => 1250, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Mendukung pemilihan dari galeri file gambar tersimpan (JPG, PNG, WEBP).', 'size' => 1250, 'color' => '475569']
]);

$s8->addCard(4430000, $cardY, $cardW, $cardH, 'FFFFFF', 'E2E8F0', 12700, 2000);
$s8->addTextBox(4630000, $cardY + 300000, 2930000, 3500000, [
    ['text' => '📐 Preset Blueprint Standar', 'size' => 1600, 'bold' => true, 'color' => '16A34A'],
    ['spacingAfter' => 10],
    ['bullet' => true, 'text' => 'Tersedia tombol pintas "Gunakan Standar Drawing Dimensi" dan "Standar Diagram TIR".', 'size' => 1250, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Teknisi tidak perlu mengunggah ulang cetak biru teknis berulang kali setiap membuat laporan baru.', 'size' => 1250, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Menjaga konsistensi gambar referensi engineering di seluruh laporan perusahaan.', 'size' => 1250, 'color' => '475569']
]);

$s8->addCard(8060000, $cardY, $cardW, $cardH, 'FFFFFF', 'E2E8F0', 12700, 2000);
$s8->addTextBox(8260000, $cardY + 300000, 2930000, 3500000, [
    ['text' => '🧩 Slot Terisolasi & Rapi', 'size' => 1600, 'bold' => true, 'color' => '4F46E5'],
    ['spacingAfter' => 10],
    ['bullet' => true, 'text' => 'Setiap temuan memiliki slot khusus (Finding Item 01–06, Visual Area A–I, Uji Penetran NDT).', 'size' => 1250, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Foto terkunci pada posisinya saat diekspor ke Excel maupun PDF (posisi foto tidak akan saling menumpuk).', 'size' => 1250, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Badge penghitung jumlah foto terisi di judul tab memudahkan pengecekan kelengkapan.', 'size' => 1250, 'color' => '475569']
]);

$s8->addFooter();
$gen->addSlide($s8->toXml());

// ==========================================
// SLIDE 9: DUAL EXPORT ENGINE (EXCEL & PDF)
// ==========================================
$s9 = new SlideBuilder('F8FAFC');
$s9->addBackground();
$s9->addHeader('FITUR TEKNIS 04', 'Dual Export Engine: Excel Presisi Tinggi & PDF Siap Cetak', 'Inovasi manipulasi OpenXML SpreadsheetML secara native di sisi server web.');

$s9->addCard(800000, 1900000, 5146000, 4200000, 'FFFFFF', 'E2E8F0', 12700, 2000);
$s9->addTextBox(1050000, 2150000, 4646000, 3700000, [
    ['text' => '📊 Ekspor Excel Master (.xlsx)', 'size' => 1700, 'bold' => true, 'color' => '16A34A'],
    ['spacingAfter' => 10],
    ['bullet' => true, 'text' => 'Menginjeksi data form langsung ke sel XML master template resmi SSC SBY-5778.', 'size' => 1250, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Menjaga keutuhan lebih dari 60 gambar teknis, drawing, formula Excel, dan relasi chartsheet asli.', 'size' => 1250, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Tombol "Simpan & Download Excel" mengeksekusi penyimpanan MySQL dan download file dalam 1 kali klik.', 'size' => 1250, 'color' => '475569'],
    ['bullet' => true, 'text' => '100% kompatibel dengan Microsoft Excel, LibreOffice Calc, dan WPS Office.', 'size' => 1250, 'color' => '475569']
]);

$s9->addCard(6246000, 1900000, 5146000, 4200000, 'FFFFFF', 'E2E8F0', 12700, 2000);
$s9->addTextBox(6496000, 2150000, 4646000, 3700000, [
    ['text' => '📑 Dokumen PDF Resmi Siap Cetak', 'size' => 1700, 'bold' => true, 'color' => 'DC2626'],
    ['spacingAfter' => 10],
    ['bullet' => true, 'text' => 'Tata letak halaman A4 standar internasional (ISO 216) siap cetak langsung.', 'size' => 1250, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Dilengkapi header resmi, penomoran halaman otomatis, dan kolom tanda tangan otorisasi.', 'size' => 1250, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Dapat dibuka dan dicetak langsung dari browser atau diunduh sebagai arsip digital pelanggan.', 'size' => 1250, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Cocok untuk lampiran fisik penyerahan komponen selesai (Handover Document).', 'size' => 1250, 'color' => '475569']
]);

$s9->addFooter();
$gen->addSlide($s9->toXml());

// ==========================================
// SLIDE 10: RBAC & DASHBOARD KPI
// ==========================================
$s10 = new SlideBuilder('F8FAFC');
$s10->addBackground();
$s10->addHeader('MANAJEMEN & MONITORING', 'Keamanan Hak Akses (RBAC) & Dashboard KPI Mutu', 'Pengendalian hak otoritas personil serta pemantauan indikator mutu secara real-time.');

$s10->addCard(800000, 1900000, 5146000, 4200000, 'FFFFFF', 'E2E8F0', 12700, 2000);
$s10->addTextBox(1050000, 2150000, 4646000, 3700000, [
    ['text' => '👥 Hak Akses Berjenjang (RBAC)', 'size' => 1700, 'bold' => true, 'color' => '0F172A'],
    ['spacingAfter' => 10],
    ['runs' => [
        ['text' => '• Inspector QC: ', 'size' => 1300, 'bold' => true, 'color' => '0284C7'],
        ['text' => 'Membuat laporan baru, simpan draft, kirim pengajuan (submit), unduh Excel/PDF, dan kelola profil pribadi.', 'size' => 1200, 'color' => '475569']
    ]],
    ['spacingAfter' => 6],
    ['runs' => [
        ['text' => '• QC Leader: ', 'size' => 1300, 'bold' => true, 'color' => '16A34A'],
        ['text' => 'Semua hak teknisi + menerima antrean "Waiting My Approval", verifikasi preview 6 lembar A4, dan otorisasi "Finish Review".', 'size' => 1200, 'color' => '475569']
    ]],
    ['spacingAfter' => 6],
    ['runs' => [
        ['text' => '• Administrator: ', 'size' => 1300, 'bold' => true, 'color' => '4F46E5'],
        ['text' => 'Akses sistem penuh: memantau Real-Time Activity Log seluruh user, menambah/mengedit akun personil, dan reset kata sandi.', 'size' => 1200, 'color' => '475569']
    ]]
]);

$s10->addCard(6246000, 1900000, 5146000, 4200000, 'FFFFFF', 'E2E8F0', 12700, 2000);
$s10->addTextBox(6496000, 2150000, 4646000, 3700000, [
    ['text' => '📈 Indikator Kualitas & Dashboard Eksekutif', 'size' => 1700, 'bold' => true, 'color' => '0F172A'],
    ['spacingAfter' => 10],
    ['runs' => [
        ['text' => '• Total Inspeksi Komponen: ', 'size' => 1300, 'bold' => true, 'color' => '0284C7'],
        ['text' => 'Monitoring volume pengerjaan Pre-Inspection vs Post-Inspection di bengkel.', 'size' => 1200, 'color' => '475569']
    ]],
    ['spacingAfter' => 6],
    ['runs' => [
        ['text' => '• Tingkat Kelulusan (Accept / Reject): ', 'size' => 1300, 'bold' => true, 'color' => '16A34A'],
        ['text' => 'Diagram pie visual proporsi status komponen yang memenuhi spesifikasi teknis.', 'size' => 1200, 'color' => '475569']
    ]],
    ['spacingAfter' => 6],
    ['runs' => [
        ['text' => '• First Pass Yield (FPY %): ', 'size' => 1300, 'bold' => true, 'color' => 'D97706'],
        ['text' => 'Metrik mutu standar industri manufaktur untuk memantau kualitas pengerjaan perdana.', 'size' => 1200, 'color' => '475569']
    ]],
    ['spacingAfter' => 6],
    ['runs' => [
        ['text' => '• Real-Time Activity Log: ', 'size' => 1300, 'bold' => true, 'color' => '4F46E5'],
        ['text' => 'Pencatatan riwayat setiap aksi login, pengajuan laporan, dan approval secara transparan.', 'size' => 1200, 'color' => '475569']
    ]]
]);

$s10->addFooter();
$gen->addSlide($s10->toXml());

// ==========================================
// SLIDE 11: DAMPAK & NILAI TAMBAH BISNIS
// ==========================================
$s11 = new SlideBuilder('F8FAFC');
$s11->addBackground();
$s11->addHeader('DAMPAK & MANFAAT', 'Nilai Tambah & Efisiensi Nyata Bagi PT SSC Works', 'Hasil implementasi sistem QCMS memberikan peningkatan produktivitas dan kepatuhan mutu.');

$s11->addCard(800000, 1900000, 2450000, 4200000, 'FFFFFF', 'BAE6FD', 12700, 2000);
$s11->addTextBox(950000, 2150000, 2150000, 3700000, [
    ['text' => '70%', 'size' => 3800, 'bold' => true, 'color' => '0284C7', 'align' => 'ctr'],
    ['spacingAfter' => 6],
    ['text' => 'Efisiensi Waktu Pelaporan', 'size' => 1400, 'bold' => true, 'color' => '0F172A', 'align' => 'ctr'],
    ['spacingAfter' => 10],
    ['text' => 'Waktu pembuatan dan kompilasi laporan berkurang drastis dari rata-rata 45 menit menjadi kurang dari 12 menit.', 'size' => 1150, 'color' => '475569', 'align' => 'ctr']
]);

$s11->addCard(3450000, 1900000, 2450000, 4200000, 'FFFFFF', 'BBF7D0', 12700, 2000);
$s11->addTextBox(3600000, 2150000, 2150000, 3700000, [
    ['text' => '0%', 'size' => 3800, 'bold' => true, 'color' => '16A34A', 'align' => 'ctr'],
    ['spacingAfter' => 6],
    ['text' => 'Kerusakan / Geser Format', 'size' => 1400, 'bold' => true, 'color' => '0F172A', 'align' => 'ctr'],
    ['spacingAfter' => 10],
    ['text' => 'Menghilangkan seluruh kasus foto bergeser, formula Excel tertimpa, atau berkas laporan yang korup/hilang.', 'size' => 1150, 'color' => '475569', 'align' => 'ctr']
]);

$s11->addCard(6100000, 1900000, 2450000, 4200000, 'FFFFFF', 'C7D2FE', 12700, 2000);
$s11->addTextBox(6250000, 2150000, 2150000, 3700000, [
    ['text' => '100%', 'size' => 3800, 'bold' => true, 'color' => '4F46E5', 'align' => 'ctr'],
    ['spacingAfter' => 6],
    ['text' => 'Audit Trail & Transparansi', 'size' => 1400, 'bold' => true, 'color' => '0F172A', 'align' => 'ctr'],
    ['spacingAfter' => 10],
    ['text' => 'Seluruh riwayat pembuatan, perubahan, tanggal review, dan penanggung jawab tercatat akurat di database.', 'size' => 1150, 'color' => '475569', 'align' => 'ctr']
]);

$s11->addCard(8750000, 1900000, 2642000, 4200000, 'FFFFFF', 'FED7AA', 12700, 2000);
$s11->addTextBox(8900000, 2150000, 2342000, 3700000, [
    ['text' => 'ISO', 'size' => 3800, 'bold' => true, 'color' => 'EA580C', 'align' => 'ctr'],
    ['spacingAfter' => 6],
    ['text' => 'Standar Mutu & Compliance', 'size' => 1400, 'bold' => true, 'color' => '0F172A', 'align' => 'ctr'],
    ['spacingAfter' => 10],
    ['text' => 'Mendukung pemenuhan sistem manajemen mutu ISO 9001 serta standarisasi dokumen inspeksi mitra industri.', 'size' => 1150, 'color' => '475569', 'align' => 'ctr']
]);

$s11->addFooter();
$gen->addSlide($s11->toXml());

// ==========================================
// SLIDE 12: PENUTUP, ROADMAP & TANYA JAWAB (Dark Navy)
// ==========================================
$s12 = new SlideBuilder('0F172A');
$s12->addBackground();

$s12->addCard(800000, 700000, 2000000, 40000, '38BDF8', '', 0, 0);

$s12->addTextBox(800000, 900000, 10500000, 400000, [
    ['text' => 'ROADMAP & PENUTUP PRESENTASI', 'size' => 1300, 'bold' => true, 'color' => '38BDF8']
]);

$s12->addTextBox(800000, 1350000, 10500000, 1200000, [
    ['text' => 'Siap Menjawab Standar Kualitas Masa Depan', 'size' => 3000, 'bold' => true, 'color' => 'FFFFFF'],
    ['spacingAfter' => 6],
    ['text' => 'Komitmen berkelanjutan untuk inovasi sistem operasional engineering PT SSC Works.', 'size' => 1500, 'color' => '94A3B8']
]);

// Roadmap Card
$s12->addCard(800000, 2800000, 10592000, 2100000, '1E293B', '334155', 12700, 3000);
$s12->addTextBox(1100000, 3000000, 10000000, 1700000, [
    ['text' => '🚀 RENCANA PENGEMBANGAN BERIKUTNYA (ROADMAP):', 'size' => 1300, 'bold' => true, 'color' => '38BDF8'],
    ['spacingAfter' => 8],
    ['runs' => [
        ['text' => '1. Template Komponen Tambahan: ', 'size' => 1300, 'bold' => true, 'color' => 'FFFFFF'],
        ['text' => 'Ekspansi form inspeksi untuk Hydraulic Cylinder, Pinion Gear, Hub, dan Shaft Bearing.', 'size' => 1300, 'color' => 'E2E8F0']
    ]],
    ['runs' => [
        ['text' => '2. Integrasi Hardware & IoT: ', 'size' => 1300, 'bold' => true, 'color' => 'FFFFFF'],
        ['text' => 'Sinkronisasi pembacaan digital caliper / micrometer Bluetooth dan scanner QR barcode komponen.', 'size' => 1300, 'color' => 'E2E8F0']
    ]],
    ['runs' => [
        ['text' => '3. Integrasi Sistem ERP / SAP: ', 'size' => 1300, 'bold' => true, 'color' => 'FFFFFF'],
        ['text' => 'Konektivitas otomatis dengan status Surat Perintah Kerja (SPK) dan Surat Jalan workshop.', 'size' => 1300, 'color' => 'E2E8F0']
    ]]
]);

// Thank you & Q&A Box
$s12->addTextBox(800000, 5100000, 10592000, 1000000, [
    ['text' => 'Terima Kasih atas Perhatian Anda', 'size' => 2400, 'bold' => true, 'color' => 'FFFFFF', 'align' => 'ctr'],
    ['spacingAfter' => 4],
    ['text' => 'Sesi Pertanyaan & Diskusi Dibuka (Q&A Session)', 'size' => 1500, 'color' => '38BDF8', 'align' => 'ctr']
], 'ctr');

$s12->addFooter("PT SSC Works — Quality Control Management System", "Terima Kasih", true);
$gen->addSlide($s12->toXml());

$gen->save();

// Copy to workspace root for ultra-easy user access
copy($outputPath, $rootOutputPath);

echo "Presentation successfully generated:\n- $outputPath\n- $rootOutputPath\n";

