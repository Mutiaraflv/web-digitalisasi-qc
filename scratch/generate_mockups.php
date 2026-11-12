<?php

function createBrowserMockup($w, $h, $url, $title) {
    $im = imagecreatetruecolor($w, $h);
    
    // Colors
    $bg = imagecolorallocate($im, 248, 250, 252); // #F8FAFC
    $topBarBg = imagecolorallocate($im, 241, 245, 249); // #F1F5F9
    $border = imagecolorallocate($im, 226, 232, 240); // #E2E8F0
    $dotRed = imagecolorallocate($im, 239, 68, 68);
    $dotYellow = imagecolorallocate($im, 245, 158, 11);
    $dotGreen = imagecolorallocate($im, 16, 185, 129);
    $urlBg = imagecolorallocate($im, 255, 255, 255);
    $textDark = imagecolorallocate($im, 30, 41, 59);
    $textMuted = imagecolorallocate($im, 100, 116, 139);

    imagefilledrectangle($im, 0, 0, $w, $h, $bg);
    imagefilledrectangle($im, 0, 0, $w, 48, $topBarBg);
    imageline($im, 0, 48, $w, 48, $border);
    imagerectangle($im, 0, 0, $w - 1, $h - 1, $border);

    // Window control dots
    imagefilledellipse($im, 24, 24, 12, 12, $dotRed);
    imagefilledellipse($im, 44, 24, 12, 12, $dotYellow);
    imagefilledellipse($im, 64, 24, 12, 12, $dotGreen);

    // URL bar
    imagefilledrectangle($im, 90, 12, $w - 30, 36, $urlBg);
    imagerectangle($im, 90, 12, $w - 30, 36, $border);
    imagestring($im, 3, 105, 16, "🔒 https://qcms.pt-sscworks.com" . $url, $textMuted);

    return $im;
}

// 1. MOCKUP DASHBOARD
$imDash = createBrowserMockup(1000, 640, "/dashboard", "Dashboard");
$cWhite = imagecolorallocate($imDash, 255, 255, 255);
$cBorder = imagecolorallocate($imDash, 226, 232, 240);
$cBlue = imagecolorallocate($imDash, 2, 132, 199);
$cGreen = imagecolorallocate($imDash, 16, 185, 129);
$cDark = imagecolorallocate($imDash, 15, 23, 42);
$cMuted = imagecolorallocate($imDash, 100, 116, 139);
$cSideBg = imagecolorallocate($imDash, 15, 23, 42);

// Sidebar
imagefilledrectangle($imDash, 0, 49, 180, 639, $cSideBg);
imagestring($imDash, 4, 20, 75, "QCMS WORKS", imagecolorallocate($imDash, 56, 189, 248));
imagestring($imDash, 3, 20, 120, "> Dashboard", imagecolorallocate($imDash, 255, 255, 255));
imagestring($imDash, 3, 20, 160, "  Riwayat Laporan", imagecolorallocate($imDash, 148, 163, 184));
imagestring($imDash, 3, 20, 200, "  Form Input Baru", imagecolorallocate($imDash, 148, 163, 184));
imagestring($imDash, 3, 20, 240, "  Waiting Review (2)", imagecolorallocate($imDash, 248, 113, 113));
imagestring($imDash, 3, 20, 280, "  Pengaturan", imagecolorallocate($imDash, 148, 163, 184));

// Dashboard Content
imagestring($imDash, 5, 210, 75, "DASHBOARD KENDALI MUTU (QC)", $cDark);
imagestring($imDash, 3, 210, 100, "Ringkasan data inspeksi workshop secara real-time", $cMuted);

// Stat Card 1
imagefilledrectangle($imDash, 210, 135, 430, 235, $cWhite);
imagerectangle($imDash, 210, 135, 430, 235, $cBorder);
imagestring($imDash, 3, 230, 150, "TOTAL INSPEKSI", $cMuted);
imagestring($imDash, 5, 230, 175, "48 Komponen", $cBlue);
imagestring($imDash, 2, 230, 205, "+12 job minggu ini (Pre & Post)", $cGreen);

// Stat Card 2
imagefilledrectangle($imDash, 450, 135, 670, 235, $cWhite);
imagerectangle($imDash, 450, 135, 670, 235, $cBorder);
imagestring($imDash, 3, 470, 150, "TINGKAT LOLOS (ACCEPT)", $cMuted);
imagestring($imDash, 5, 470, 175, "95.8 %", $cGreen);
imagestring($imDash, 2, 470, 205, "46 Lolos / 2 Rekondisi", $cMuted);

// Stat Card 3
imagefilledrectangle($imDash, 690, 135, 960, 235, $cWhite);
imagerectangle($imDash, 690, 135, 960, 235, $cBorder);
imagestring($imDash, 3, 710, 150, "FIRST PASS YIELD (FPY)", $cMuted);
imagestring($imDash, 5, 710, 175, "91.2 %", imagecolorallocate($imDash, 217, 119, 6));
imagestring($imDash, 2, 710, 205, "Kualitas pengerjaan prima", $cMuted);

// Table Area
imagefilledrectangle($imDash, 210, 260, 960, 590, $cWhite);
imagerectangle($imDash, 210, 260, 960, 590, $cBorder);
imagestring($imDash, 4, 230, 280, "Aktivitas Laporan Terbaru:", $cDark);

$tableHeaders = ["SSC ID", "No. Laporan", "Komponen", "Tipe", "Status", "Aksi"];
$headerX = [230, 340, 480, 650, 780, 890];
for ($i = 0; $i < count($tableHeaders); $i++) {
    imagestring($imDash, 3, $headerX[$i], 320, $tableHeaders[$i], $cMuted);
}
imageline($imDash, 230, 345, 940, 345, $cBorder);

$rows = [
    ["SSC-5778", "QC-2026-089", "OUTPUT SHAFT", "Pre-Inspect", "ACCEPT", "Review"],
    ["SSC-5779", "QC-2026-090", "OUTPUT SHAFT", "Post-Inspect", "WAITING", "Lihat"],
    ["SSC-5780", "QC-2026-091", "OUTPUT SHAFT", "Pre-Inspect", "DRAFT", "Edit"],
    ["SSC-5781", "QC-2026-092", "OUTPUT SHAFT", "Post-Inspect", "ACCEPT", "Review"]
];

$rowY = 365;
foreach ($rows as $r) {
    imagestring($imDash, 3, 230, $rowY, $r[0], $cDark);
    imagestring($imDash, 3, 340, $rowY, $r[1], $cDark);
    imagestring($imDash, 3, 480, $rowY, $r[2], $cDark);
    imagestring($imDash, 3, 650, $rowY, $r[3], $cDark);
    
    // Status badge
    $stColor = $r[4] === 'ACCEPT' ? $cGreen : ($r[4] === 'WAITING' ? $cBlue : $cMuted);
    imagestring($imDash, 3, 780, $rowY, "[" . $r[4] . "]", $stColor);
    imagestring($imDash, 3, 890, $rowY, "Lihat PDF", $cBlue);
    imageline($imDash, 230, $rowY + 28, 940, $rowY + 28, imagecolorallocate($imDash, 241, 245, 249));
    $rowY += 45;
}

imagepng($imDash, __DIR__ . '/mockup_dashboard.png');
imagedestroy($imDash);

// 2. MOCKUP FORM + LIVE PREVIEW DUAL PANE
$imDual = createBrowserMockup(1000, 640, "/inspection/new", "Input Form & Live Preview");
$cWhite = imagecolorallocate($imDual, 255, 255, 255);
$cBorder = imagecolorallocate($imDual, 226, 232, 240);
$cBlue = imagecolorallocate($imDual, 2, 132, 199);
$cGreen = imagecolorallocate($imDual, 16, 185, 129);
$cDark = imagecolorallocate($imDual, 15, 23, 42);
$cMuted = imagecolorallocate($imDual, 100, 116, 139);

// Left Pane: Form Input (width 460)
imagefilledrectangle($imDual, 20, 65, 480, 615, $cWhite);
imagerectangle($imDual, 20, 65, 480, 615, $cBorder);
imagestring($imDual, 4, 35, 80, "INPUT FORM INSPEKSI (KIRI)", $cBlue);

// Tab buttons
$tabs = ["1.Identitas", "2.Dimensi", "3.Foto", "4.TIR", "5.Saran", "6.Paraf"];
for ($i = 0; $i < count($tabs); $i++) {
    $tx = 35 + ($i * 72);
    $tBg = $i === 1 ? imagecolorallocate($imDual, 2, 132, 199) : imagecolorallocate($imDual, 241, 245, 249);
    $tTxt = $i === 1 ? $cWhite : $cMuted;
    imagefilledrectangle($imDual, $tx, 115, $tx + 68, 140, $tBg);
    imagestring($imDual, 2, $tx + 5, 122, $tabs[$i], $tTxt);
}

// Form Inputs
imagestring($imDual, 3, 35, 160, "Dimensi Titik 01 (Area A - C):", $cDark);
imagefilledrectangle($imDual, 35, 185, 460, 215, imagecolorallocate($imDual, 248, 250, 252));
imagerectangle($imDual, 35, 185, 460, 215, $cBorder);
imagestring($imDual, 3, 45, 193, "Ukuran Aktual: 145.02 mm  [Status: ACCEPT]", $cGreen);

imagestring($imDual, 3, 35, 235, "Dimensi Titik 02 (Area D - F):", $cDark);
imagefilledrectangle($imDual, 35, 260, 460, 290, imagecolorallocate($imDual, 248, 250, 252));
imagerectangle($imDual, 35, 260, 460, 290, $cBorder);
imagestring($imDual, 3, 45, 268, "Ukuran Aktual: 110.01 mm  [Status: ACCEPT]", $cGreen);

imagestring($imDual, 3, 35, 310, "Upload Foto Cacat / NDT Lapangan:", $cDark);
imagefilledrectangle($imDual, 35, 335, 230, 435, imagecolorallocate($imDual, 241, 245, 249));
imagerectangle($imDual, 35, 335, 230, 435, $cBorder);
imagestring($imDual, 3, 50, 375, "[📸 Ambil Foto]", $cBlue);
imagestring($imDual, 2, 60, 400, "Kamera HP Langsung", $cMuted);

imagefilledrectangle($imDual, 250, 335, 460, 435, imagecolorallocate($imDual, 241, 245, 249));
imagerectangle($imDual, 250, 335, 460, 435, $cBorder);
imagestring($imDual, 3, 270, 375, "[📐 Pakai Standar]", $cGreen);
imagestring($imDual, 2, 280, 400, "Drawing Cetak Biru", $cMuted);

// Action buttons at bottom left
imagefilledrectangle($imDual, 35, 550, 180, 595, imagecolorallocate($imDual, 241, 245, 249));
imagerectangle($imDual, 35, 550, 180, 595, $cBorder);
imagestring($imDual, 3, 50, 565, "Simpan Draft", $cDark);

imagefilledrectangle($imDual, 200, 550, 460, 595, imagecolorallocate($imDual, 2, 132, 199));
imagestring($imDual, 3, 240, 565, "🚀 Submit Laporan", $cWhite);

// Right Pane: Live A4 Preview (width 470)
imagefilledrectangle($imDual, 510, 65, 980, 615, imagecolorallocate($imDual, 255, 255, 255));
imagerectangle($imDual, 510, 65, 980, 615, imagecolorallocate($imDual, 203, 213, 225));
imagestring($imDual, 4, 530, 80, "LIVE PREVIEW DOKUMEN CETAK A4 (KANAN)", imagecolorallocate($imDual, 16, 185, 129));

// Mini A4 paper sheet inside preview
imagefilledrectangle($imDual, 530, 110, 960, 595, imagecolorallocate($imDual, 255, 255, 255));
imagerectangle($imDual, 530, 110, 960, 595, imagecolorallocate($imDual, 148, 163, 184));

// KOP banner inside preview
imagestring($imDual, 3, 550, 125, "PT SSC WORKS - QUALITY CONTROL REPORT", $cDark);
imagestring($imDual, 2, 550, 145, "FORM-QC-002: COMPONENT CONDITION REPORT (OUTPUT SHAFT)", $cMuted);
imageline($imDual, 550, 165, 940, 165, $cDark);

// Mini Drawing placement
$drawPath = __DIR__ . '/../public/images/doc_assets/shaft_dimension_drawing.png';
if (file_exists($drawPath)) {
    $drawImg = imagecreatefrompng($drawPath);
    if ($drawImg) {
        imagecopyresampled($imDual, $drawImg, 550, 180, 0, 0, 390, 130, imagesx($drawImg), imagesy($drawImg));
        imagedestroy($drawImg);
    }
} else {
    imagefilledrectangle($imDual, 550, 180, 940, 310, imagecolorallocate($imDual, 241, 245, 249));
    imagestring($imDual, 3, 620, 240, "[ GAMBAR BLUEPRINT OUTPUT SHAFT ]", $cMuted);
}

// Mini table inside preview
imagestring($imDual, 3, 550, 330, "Hasil Pengukuran Aktual (Otomatis Masuk):", $cDark);
imagefilledrectangle($imDual, 550, 355, 940, 450, imagecolorallocate($imDual, 248, 250, 252));
imagerectangle($imDual, 550, 355, 940, 450, $cBorder);
imagestring($imDual, 2, 560, 370, "Item 01 (Dimensi Area A-C): 145.02 mm  --> ACCEPT", $cGreen);
imagestring($imDual, 2, 560, 395, "Item 02 (Dimensi Area D-F): 110.01 mm  --> ACCEPT", $cGreen);
imagestring($imDual, 2, 560, 420, "TIR Runout Center (0-180 deg): 0.02 mm  --> ACCEPT", $cGreen);

// Mini Sign box
imagefilledrectangle($imDual, 550, 480, 720, 560, $cWhite);
imagerectangle($imDual, 550, 480, 720, 560, $cBorder);
imagestring($imDual, 2, 560, 490, "Created by:", $cMuted);
imagestring($imDual, 2, 560, 535, "Inspector QC", $cDark);

imagefilledrectangle($imDual, 770, 480, 940, 560, $cWhite);
imagerectangle($imDual, 770, 480, 940, 560, $cBorder);
imagestring($imDual, 2, 780, 490, "Reviewed by:", $cMuted);
imagestring($imDual, 2, 780, 535, "Leader QC", $cGreen);

imagepng($imDual, __DIR__ . '/mockup_dualpane.png');
imagedestroy($imDual);

// 3. MOCKUP APPROVAL REVIEW
$imAppr = createBrowserMockup(1000, 640, "/reports/5778/review", "Peninjauan Dokumen (QC Leader)");
$cWhite = imagecolorallocate($imAppr, 255, 255, 255);
$cBorder = imagecolorallocate($imAppr, 226, 232, 240);
$cBlue = imagecolorallocate($imAppr, 2, 132, 199);
$cGreen = imagecolorallocate($imAppr, 16, 185, 129);
$cDark = imagecolorallocate($imAppr, 15, 23, 42);
$cMuted = imagecolorallocate($imAppr, 100, 116, 139);

// Review Toolbar
imagefilledrectangle($imAppr, 20, 65, 980, 125, $cWhite);
imagerectangle($imAppr, 20, 65, 980, 125, $cBorder);
imagestring($imAppr, 4, 40, 85, "Review Laporan: SSC SBY-5778 (Output Shaft)", $cDark);
imagestring($imAppr, 3, 40, 105, "Status saat ini: Menunggu Persetujuan Atasan", imagecolorallocate($imAppr, 217, 119, 6));

// Button Finish Review
imagefilledrectangle($imAppr, 740, 75, 960, 115, $cGreen);
imagestring($imAppr, 4, 760, 88, "✓ FINISH REVIEW", $cWhite);

// Document Preview inside
imagefilledrectangle($imAppr, 20, 145, 980, 600, $cWhite);
imagerectangle($imAppr, 20, 145, 980, 600, $cBorder);
imagestring($imAppr, 4, 40, 165, "Pemeriksaan Lembar 1 s/d 6 (Siap Cetak)", $cBlue);

// 4 thumbnails of pages
for ($p = 1; $p <= 4; $p++) {
    $px = 40 + (($p - 1) * 235);
    imagefilledrectangle($imAppr, $px, 200, $px + 215, 520, imagecolorallocate($imAppr, 248, 250, 252));
    imagerectangle($imAppr, $px, 200, $px + 215, 520, $cBorder);
    imagestring($imAppr, 3, $px + 20, 215, "Lembar " . $p . " (A4)", $cDark);
    imageline($imAppr, $px + 10, 240, $px + 205, 240, $cBorder);
    imagestring($imAppr, 2, $px + 20, 270, "• Identitas & Dimensi", $cMuted);
    imagestring($imAppr, 2, $px + 20, 310, "• Foto Kondisi Fisik", $cMuted);
    imagestring($imAppr, 2, $px + 20, 350, "• Hasil Ukur TIR", $cMuted);
    imagestring($imAppr, 2, $px + 20, 390, "• Rekomendasi QC", $cMuted);
    imagestring($imAppr, 2, $px + 20, 480, "[Status: Terverifikasi]", $cGreen);
}

imagestring($imAppr, 3, 40, 555, "Tekan 'Finish Review' untuk mengesahkan laporan secara resmi dan mengunci data.", $cMuted);

imagepng($imAppr, __DIR__ . '/mockup_approval.png');
imagedestroy($imAppr);

// 4. MOCKUP EXCEL & PDF EXPORT
$imExp = createBrowserMockup(1000, 640, "/reports/export", "Hasil Ekspor Excel & PDF");
$cWhite = imagecolorallocate($imExp, 255, 255, 255);
$cBorder = imagecolorallocate($imExp, 226, 232, 240);
$cGreen = imagecolorallocate($imExp, 16, 185, 129);
$cRed = imagecolorallocate($imExp, 239, 68, 68);
$cDark = imagecolorallocate($imExp, 15, 23, 42);
$cMuted = imagecolorallocate($imExp, 100, 116, 139);

// Left Box: Excel .xlsx
imagefilledrectangle($imExp, 50, 100, 480, 560, $cWhite);
imagerectangle($imExp, 50, 100, 480, 560, $cBorder);
imagestring($imExp, 5, 80, 130, "FILE EXCEL ASLI (.XLSX)", $cGreen);
imagestring($imExp, 3, 80, 160, "100% Identik dengan Master Template Pabrik", $cMuted);

imagefilledrectangle($imExp, 80, 200, 450, 420, imagecolorallocate($imExp, 240, 253, 244));
imagerectangle($imExp, 80, 200, 450, 420, imagecolorallocate($imExp, 187, 247, 208));
imagestring($imExp, 4, 110, 230, "📊 SSC SBY-5778 Output Shaft.xlsx", $cDark);
imagestring($imExp, 3, 110, 275, "✓ 60+ Foto & Drawing tetap utuh", $cGreen);
imagestring($imExp, 3, 110, 315, "✓ Rumus toleransi tidak tertimpa", $cGreen);
imagestring($imExp, 3, 110, 355, "✓ Kompatibel MS Excel & WPS Office", $cGreen);

imagefilledrectangle($imExp, 80, 460, 450, 510, $cGreen);
imagestring($imExp, 4, 150, 478, "📥 Download File Excel", $cWhite);

// Right Box: PDF Ready to Print
imagefilledrectangle($imExp, 520, 100, 950, 560, $cWhite);
imagerectangle($imExp, 520, 100, 950, 560, $cBorder);
imagestring($imExp, 5, 550, 130, "DOKUMEN PDF SIAP CETAK", $cRed);
imagestring($imExp, 3, 550, 160, "Format Resmi A4 Siap Serah Terima Pelanggan", $cMuted);

imagefilledrectangle($imExp, 550, 200, 920, 420, imagecolorallocate($imExp, 254, 242, 242));
imagerectangle($imExp, 550, 200, 920, 420, imagecolorallocate($imExp, 254, 202, 202));
imagestring($imExp, 4, 580, 230, "📑 Laporan QC Resmi (6 Halaman A4).pdf", $cDark);
imagestring($imExp, 3, 580, 275, "✓ Kop & logo resmi PT SSC Works", $cRed);
imagestring($imExp, 3, 580, 315, "✓ Resolusi tinggi siap print fisik", $cRed);
imagestring($imExp, 3, 580, 355, "✓ Tanda tangan digital Inspector & Leader", $cRed);

imagefilledrectangle($imExp, 550, 460, 920, 510, $cRed);
imagestring($imExp, 4, 630, 478, "🖨️ Cetak / Unduh PDF", $cWhite);

imagepng($imExp, __DIR__ . '/mockup_export.png');
imagedestroy($imExp);

echo "All 4 realistic UI mockups created successfully!\n";

