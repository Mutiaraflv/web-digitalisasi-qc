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
// SLIDE 1: JUDUL & PEMBUKA (Dark Navy)
// ==========================================
$s1 = new SlideBuilder('0F172A');
$s1->addBackground();

// Cyan Accent line
$s1->addCard(800000, 900000, 1800000, 45000, '0284C7', '', 0, 0);

$s1->addTextBox(800000, 1100000, 10500000, 400000, [
    ['text' => 'DEMO APLIKASI WEB • PT SSC WORKS', 'size' => 1300, 'bold' => true, 'color' => '38BDF8']
]);

$s1->addTextBox(800000, 1600000, 10500000, 1500000, [
    ['text' => 'Sistem Laporan QC Digital (QCMS)', 'size' => 3600, 'bold' => true, 'color' => 'FFFFFF'],
    ['spacingAfter' => 10],
    ['text' => 'Digitalisasi Form Kendali Mutu Komponen Output Shaft (FORM-QC-002)', 'size' => 1800, 'bold' => false, 'color' => '94A3B8']
]);

// Info Box
$s1->addCard(800000, 3500000, 10592000, 2200000, '1E293B', '334155', 12700, 2000);
$s1->addTextBox(1100000, 3750000, 10000000, 1700000, [
    ['text' => 'TUJUAN DEMO HARI INI:', 'size' => 1300, 'bold' => true, 'color' => '38BDF8'],
    ['spacingAfter' => 8],
    ['runs' => [
        ['text' => '• Mengenalkan sistem baru pencatatan laporan QC yang terpusat di web & database.', 'size' => 1400, 'bold' => false, 'color' => 'E2E8F0']
    ]],
    ['runs' => [
        ['text' => '• Menunjukkan bagaimana sistem ini mempermudah teknisi menginput ukuran, foto & approval.', 'size' => 1400, 'bold' => false, 'color' => 'E2E8F0']
    ]],
    ['runs' => [
        ['text' => '• Menampilkan otomasi ekspor hasil laporan ke Excel asli & dokumen cetak PDF.', 'size' => 1400, 'bold' => false, 'color' => 'E2E8F0']
    ]]
]);

$s1->addFooter("PT SSC Works — Demo Sistem QCMS", "Slide 1 / 5", true);
$gen->addSlide($s1->toXml());

// ==========================================
// SLIDE 2: LATAR BELAKANG: DULU VS SEKARANG
// ==========================================
$s2 = new SlideBuilder('F8FAFC');
$s2->addBackground();
$s2->addHeader('LATAR BELAKANG', 'Kenapa Kita Perlu Beralih ke Sistem Web?', 'Membandingkan cara lama yang manual dengan sistem digital terpusat.');

$colW = 5146000;
$cardH = 4300000;
$cardY = 1850000;

// Cara Lama
$s2->addCard(800000, $cardY, $colW, $cardH, 'FFFFFF', 'FECACA', 12700, 2000);
$s2->addCard(1050000, $cardY + 250000, 1500000, 350000, 'FEE2E2', '', 0, 1000);
$s2->addTextBox(1050000, $cardY + 300000, 1500000, 250000, [
    ['text' => '❌ CARA LAMA (EXCEL MANUAL)', 'size' => 950, 'bold' => true, 'color' => 'DC2626', 'align' => 'ctr']
]);
$s2->addTextBox(1050000, $cardY + 750000, $colW - 500000, 3300000, [
    ['text' => 'Kendala yang Sering Terjadi:', 'size' => 1600, 'bold' => true, 'color' => '0F172A'],
    ['spacingAfter' => 10],
    ['bullet' => true, 'text' => 'File Excel terpisah di laptop masing-masing, rawan hilang atau tertimpa.', 'size' => 1300, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Rumus perhitungan deviasi dimensi sering tidak sengaja terhapus.', 'size' => 1300, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Menata 60+ foto visual di Excel sangat lama dan foto sering bergeser.', 'size' => 1300, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Approval tertunda karena harus nunggu dokumen dicetak fisik.', 'size' => 1300, 'color' => '475569']
]);

// Cara Baru
$s2->addCard(6246000, $cardY, $colW, $cardH, 'FFFFFF', 'BBF7D0', 12700, 2000);
$s2->addCard(6496000, $cardY + 250000, 1500000, 350000, 'DCFCE7', '', 0, 1000);
$s2->addTextBox(6496000, $cardY + 300000, 1500000, 250000, [
    ['text' => '✓ CARA BARU (WEB QCMS)', 'size' => 950, 'bold' => true, 'color' => '16A34A', 'align' => 'ctr']
]);
$s2->addTextBox(6496000, $cardY + 750000, $colW - 500000, 3300000, [
    ['text' => 'Keuntungan dengan Sistem Web:', 'size' => 1600, 'bold' => true, 'color' => '0F172A'],
    ['spacingAfter' => 10],
    ['bullet' => true, 'text' => 'Data tersimpan terpusat di database, mudah dicari kapan saja.', 'size' => 1300, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Toleransi Accept / Reject dicek otomatis sesuai standar ukuran.', 'size' => 1300, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Bisa langsung foto dari HP di bengkel, foto langsung masuk ke slotnya.', 'size' => 1300, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Approval online langsung di web, pimpinan dapat notifikasi real-time.', 'size' => 1300, 'color' => '475569']
]);

$s2->addFooter("PT SSC Works — Demo Sistem QCMS", "Slide 2 / 5");
$gen->addSlide($s2->toXml());

// ==========================================
// SLIDE 3: 4 FITUR UTAMA
// ==========================================
$s3 = new SlideBuilder('F8FAFC');
$s3->addBackground();
$s3->addHeader('FITUR UTAMA', '4 Fitur Unggulan yang Mempermudah Kerja', 'Dirancang agar praktis digunakan teknisi bengkel maupun peninjau mutu.');

$gridW = 5146000;
$gridH = 2000000;

// Fitur 1
$s3->addCard(800000, 1900000, $gridW, $gridH, 'FFFFFF', 'E2E8F0', 12700, 2000);
$s3->addTextBox(1050000, 2100000, $gridW - 500000, $gridH - 300000, [
    ['text' => '📝 1. Form Input Terstruktur & Rapi', 'size' => 1600, 'bold' => true, 'color' => '0284C7'],
    ['spacingAfter' => 8],
    ['text' => 'Input dibagi 6 tab praktis (Identitas, Ukuran Dimensi, Foto, Runout TIR, Saran Perbaikan, dan Pengesahan). Nilai toleransi dan status Accept/Reject terisi otomatis.', 'size' => 1250, 'color' => '475569']
]);

// Fitur 2
$s3->addCard(6246000, 1900000, $gridW, $gridH, 'FFFFFF', 'E2E8F0', 12700, 2000);
$s3->addTextBox(6496000, 2100000, $gridW - 500000, $gridH - 300000, [
    ['text' => '📸 2. Upload Foto & Kamera Langsung', 'size' => 1600, 'bold' => true, 'color' => '16A34A'],
    ['spacingAfter' => 8],
    ['text' => 'Teknisi bisa langsung foto komponen dari HP di bengkel tanpa kabel USB. Ada tombol cepat untuk pasang gambar standar drawing teknik tanpa upload ulang.', 'size' => 1250, 'color' => '475569']
]);

// Fitur 3
$s3->addCard(800000, 4100000, $gridW, $gridH, 'FFFFFF', 'E2E8F0', 12700, 2000);
$s3->addTextBox(1050000, 4300000, $gridW - 500000, $gridH - 300000, [
    ['text' => '📄 3. Live Preview Dokumen A4 Real-Time', 'size' => 1600, 'bold' => true, 'color' => '4F46E5'],
    ['spacingAfter' => 8],
    ['text' => 'Ketik di sebelah kiri, tampilan resmi kertas A4 langsung muncul di sebelah kanan. Hasil cetak dijamin sama persis dengan yang ada di layar (anti berantakan).', 'size' => 1250, 'color' => '475569']
]);

// Fitur 4
$s3->addCard(6246000, 4100000, $gridW, $gridH, 'FFFFFF', 'E2E8F0', 12700, 2000);
$s3->addTextBox(6496000, 4300000, $gridW - 500000, $gridH - 300000, [
    ['text' => '📥 4. Ekspor Excel Asli & Cetak PDF 1-Klik', 'size' => 1600, 'bold' => true, 'color' => 'DC2626'],
    ['spacingAfter' => 8],
    ['text' => 'Data otomatis tersimpan ke MySQL dan bisa langsung diunduh ke format Excel resmi SSC SBY-5778 atau dicetak ke format PDF siap serah terima ke pelanggan.', 'size' => 1250, 'color' => '475569']
]);

$s3->addFooter("PT SSC Works — Demo Sistem QCMS", "Slide 3 / 5");
$gen->addSlide($s3->toXml());

// ==========================================
// SLIDE 4: ALUR KERJA (WORKFLOW APPROVAL)
// ==========================================
$s4 = new SlideBuilder('F8FAFC');
$s4->addBackground();
$s4->addHeader('ALUR KERJA', 'Alur Pengisian & Persetujuan (Approval)', 'Proses bertahap yang mudah diikuti dari awal pemeriksaan sampai laporan disahkan.');

$stepW = 2450000;
$stepH = 4300000;
$stepY = 1850000;
$gap = 260000;

$flow = [
    [
        'step' => 'LANGKAH 1',
        'badgeBg' => 'E0F2FE',
        'badgeColor' => '0284C7',
        'title' => 'Input Ukuran & Foto',
        'actor' => 'Teknisi / Inspector QC',
        'desc' => 'Ukur komponen fisik Output Shaft, masukkan angka dimensi, TIR runout, dan ambil foto temuan di workshop.'
    ],
    [
        'step' => 'LANGKAH 2',
        'badgeBg' => 'FEF3C7',
        'badgeColor' => 'D97706',
        'title' => 'Simpan Draft / Kirim',
        'actor' => 'Fleksibel & Aman',
        'desc' => 'Bisa disimpan sebagai Draft jika baru sebagian selesai. Jika sudah lengkap, klik Submit untuk diajukan ke atasan.'
    ],
    [
        'step' => 'LANGKAH 3',
        'badgeBg' => 'FCE7F3',
        'badgeColor' => 'BE185D',
        'title' => 'Review Dokumen A4',
        'actor' => 'Leader QC / Atasan',
        'desc' => 'Atasan menerima pengingat di menu "Waiting My Approval", membuka 6 lembar pratinjau A4, lalu klik "Finish Review".'
    ],
    [
        'step' => 'LANGKAH 4',
        'badgeBg' => 'DCFCE7',
        'badgeColor' => '15803D',
        'title' => 'Laporan Sah & Selesai',
        'actor' => 'Siap Download',
        'desc' => 'Status berubah menjadi REVIEWED. Laporan terkunci aman, siap diunduh menjadi file Excel atau dicetak PDF.'
    ]
];

for ($i = 0; $i < 4; $i++) {
    $x = 800000 + ($i * ($stepW + $gap));
    $item = $flow[$i];

    $s4->addCard($x, $stepY, $stepW, $stepH, 'FFFFFF', 'E2E8F0', 12700, 2000);

    // Badge
    $s4->addCard($x + 200000, $stepY + 250000, $stepW - 400000, 350000, $item['badgeBg'], '', 0, 1000);
    $s4->addTextBox($x + 200000, $stepY + 300000, $stepW - 400000, 250000, [
        ['text' => $item['step'], 'size' => 1000, 'bold' => true, 'color' => $item['badgeColor'], 'align' => 'ctr']
    ]);

    // Title & Actor & Desc
    $s4->addTextBox($x + 200000, $stepY + 800000, $stepW - 400000, 3200000, [
        ['text' => $item['title'], 'size' => 1500, 'bold' => true, 'color' => '0F172A', 'align' => 'ctr'],
        ['spacingAfter' => 4],
        ['text' => '(' . $item['actor'] . ')', 'size' => 1100, 'bold' => true, 'color' => '0284C7', 'align' => 'ctr'],
        ['spacingAfter' => 12],
        ['text' => $item['desc'], 'size' => 1250, 'color' => '475569', 'align' => 'ctr']
    ]);
}

$s4->addFooter("PT SSC Works — Demo Sistem QCMS", "Slide 4 / 5");
$gen->addSlide($s4->toXml());

// ==========================================
// SLIDE 5: MANFAAT & AJAKAN DEMO LANGSUNG (Dark Navy)
// ==========================================
$s5 = new SlideBuilder('0F172A');
$s5->addBackground();

$s5->addCard(800000, 800000, 1800000, 45000, '38BDF8', '', 0, 0);

$s5->addTextBox(800000, 1000000, 10500000, 400000, [
    ['text' => 'RINGKASAN & DEMO APLIKASI', 'size' => 1300, 'bold' => true, 'color' => '38BDF8']
]);

$s5->addTextBox(800000, 1500000, 10500000, 1200000, [
    ['text' => 'Manfaat Utama Bagi Perusahaan', 'size' => 3200, 'bold' => true, 'color' => 'FFFFFF'],
    ['spacingAfter' => 6],
    ['text' => 'Pekerjaan lebih cepat, dokumen lebih rapi, dan data tersimpan aman.', 'size' => 1600, 'color' => '94A3B8']
]);

// 3 Value Bullets
$s5->addCard(800000, 2800000, 10592000, 2100000, '1E293B', '334155', 12700, 2000);
$s5->addTextBox(1100000, 3050000, 10000000, 1600000, [
    ['runs' => [
        ['text' => '⚡ Efisiensi Waktu: ', 'size' => 1400, 'bold' => true, 'color' => '38BDF8'],
        ['text' => 'Pembuatan laporan turun dari 45 menit menjadi kurang dari 12 menit.', 'size' => 1400, 'color' => 'E2E8F0']
    ]],
    ['spacingAfter' => 8],
    ['runs' => [
        ['text' => '🔒 Bebas Format Rusak: ', 'size' => 1400, 'bold' => true, 'color' => '34D399'],
        ['text' => 'Foto dan rumus tidak akan pernah bergeser atau tertimpa lagi.', 'size' => 1400, 'color' => 'E2E8F0']
    ]],
    ['spacingAfter' => 8],
    ['runs' => [
        ['text' => '📊 Riwayat Rapi & Siap Audit: ', 'size' => 1400, 'bold' => true, 'color' => 'FCD34D'],
        ['text' => 'Semua riwayat laporan tercatat di database dan dapat dicari dalam hitungan detik.', 'size' => 1400, 'color' => 'E2E8F0']
    ]]
]);

// Call to action
$s5->addTextBox(800000, 5200000, 10592000, 900000, [
    ['text' => '🚀 Mari Kita Masuk ke Sesi Live Demo Aplikasi', 'size' => 2400, 'bold' => true, 'color' => '38BDF8', 'align' => 'ctr'],
    ['spacingAfter' => 4],
    ['text' => 'Silakan bapak/ibu jika ingin melihat langsung alur penginputan form di web.', 'size' => 1400, 'color' => 'E2E8F0', 'align' => 'ctr']
], 'ctr');

$s5->addFooter("PT SSC Works — Demo Sistem QCMS", "Slide 5 / 5", true);
$gen->addSlide($s5->toXml());

$gen->save();
copy($outputPath, $rootOutputPath);

echo "Simple 5-slide PPTX generated successfully!\n";

