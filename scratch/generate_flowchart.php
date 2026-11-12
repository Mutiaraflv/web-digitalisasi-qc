<?php

function createFlowchartImage($w = 1100, $h = 560) {
    $im = imagecreatetruecolor($w, $h);

    // Colors
    $bg = imagecolorallocate($im, 255, 255, 255);
    $cardBg = imagecolorallocate($im, 248, 250, 252); // #F8FAFC
    $border = imagecolorallocate($im, 203, 213, 225); // #CBD5E1
    $primary = imagecolorallocate($im, 2, 132, 199); // #0284C7
    $green = imagecolorallocate($im, 16, 185, 129); // #10B981
    $amber = imagecolorallocate($im, 217, 119, 6); // #D97706
    $purple = imagecolorallocate($im, 99, 102, 241); // #6366F1
    $dark = imagecolorallocate($im, 15, 23, 42); // #0F172A
    $textMuted = imagecolorallocate($im, 100, 116, 139); // #64748B
    $white = imagecolorallocate($im, 255, 255, 255);

    imagefilledrectangle($im, 0, 0, $w, $h, $bg);

    // Flowchart Steps (4 horizontal stages)
    $steps = [
        [
            'num' => 'TAHAP 01',
            'title' => 'Input & Pengumpulan Data',
            'role' => 'Aktor: Teknisi Workshop',
            'badgeBg' => imagecolorallocate($im, 224, 242, 254),
            'badgeColor' => $primary,
            'points' => [
                '• Ukur fisik dimensi & TIR',
                '• Ambil foto temuan dari HP',
                '• Bisa Simpan Draft jika dicicil',
                'Status: [ DRAFT ]'
            ]
        ],
        [
            'num' => 'TAHAP 02',
            'title' => 'Kompilasi & Live Preview A4',
            'role' => 'Aktor: Sistem Web QCMS',
            'badgeBg' => imagecolorallocate($im, 238, 242, 255),
            'badgeColor' => $purple,
            'points' => [
                '• Data otomatis masuk lembar A4',
                '• Toleransi dicek otomatis',
                '• WYSIWYG: Wujud cetak terlihat',
                'Status: [ SIAP AJUKAN ]'
            ]
        ],
        [
            'num' => 'TAHAP 03',
            'title' => 'Review & Approval Online',
            'role' => 'Aktor: Leader QC / Atasan',
            'badgeBg' => imagecolorallocate($im, 254, 243, 199),
            'badgeColor' => $amber,
            'points' => [
                '• Masuk ke Waiting Approval',
                '• Periksa 6 halaman A4',
                '• Klik tombol Finish Review',
                'Status: [ WAITING REVIEW ]'
            ]
        ],
        [
            'num' => 'TAHAP 04',
            'title' => 'Finalisasi Dokumen Resmi',
            'role' => 'Output: Dokumen Sah Terkunci',
            'badgeBg' => imagecolorallocate($im, 220, 252, 231),
            'badgeColor' => $green,
            'points' => [
                '• Laporan sah & terkunci aman',
                '• Ekspor Excel master (.xlsx)',
                '• Cetak PDF siap serah terima',
                'Status: [ REVIEWED / SAH ]'
            ]
        ]
    ];

    $cardW = 230;
    $cardH = 430;
    $startY = 65;
    $gap = 48;
    $startX = 25;

    for ($i = 0; $i < 4; $i++) {
        $x = $startX + ($i * ($cardW + $gap));
        $s = $steps[$i];

        // Card shadow & box
        imagefilledrectangle($im, $x + 4, $startY + 4, $x + $cardW + 4, $startY + $cardH + 4, imagecolorallocate($im, 241, 245, 249));
        imagefilledrectangle($im, $x, $startY, $x + $cardW, $startY + $cardH, $cardBg);
        imagerectangle($im, $x, $startY, $x + $cardW, $startY + $cardH, $border);

        // Header accent bar
        imagefilledrectangle($im, $x, $startY, $x + $cardW, $startY + 6, $s['badgeColor']);

        // Step Badge
        imagefilledrectangle($im, $x + 16, $startY + 20, $x + 120, $startY + 45, $s['badgeBg']);
        imagestring($im, 3, $x + 24, $startY + 26, $s['num'], $s['badgeColor']);

        // Title
        imagestring($im, 4, $x + 16, $startY + 65, $s['title'], $dark);

        // Role / Aktor
        imagefilledrectangle($im, $x + 16, $startY + 95, $x + $cardW - 16, $startY + 120, imagecolorallocate($im, 241, 245, 249));
        imagestring($im, 2, $x + 22, $startY + 102, $s['role'], $dark);

        imageline($im, $x + 16, $startY + 135, $x + $cardW - 16, $startY + 135, $border);

        // Bullet Points
        $py = $startY + 155;
        foreach ($s['points'] as $p) {
            $isStatus = strpos($p, 'Status:') !== false;
            if ($isStatus) {
                imagefilledrectangle($im, $x + 16, $py + 10, $x + $cardW - 16, $py + 40, $s['badgeBg']);
                imagestring($im, 2, $x + 22, $py + 18, $p, $s['badgeColor']);
            } else {
                imagestring($im, 2, $x + 16, $py, $p, $dark);
            }
            $py += 45;
        }

        // Draw Arrow Connector to next card
        if ($i < 3) {
            $arrowX = $x + $cardW + 8;
            $arrowY = $startY + ($cardH / 2);

            // Arrow line
            imageline($im, $arrowX, $arrowY, $arrowX + 28, $arrowY, $primary);
            imageline($im, $arrowX, $arrowY - 1, $arrowX + 28, $arrowY - 1, $primary);
            imageline($im, $arrowX, $arrowY + 1, $arrowX + 28, $arrowY + 1, $primary);

            // Arrow head
            imagefilledpolygon($im, [
                $arrowX + 28, $arrowY - 8,
                $arrowX + 40, $arrowY,
                $arrowX + 28, $arrowY + 8
            ], $primary);
        }
    }

    // Top banner description
    imagestring($im, 5, 25, 20, "SKEMA ALUR KERJA: DARI DRAF MENTAH MENUJU DOKUMEN RESMI SAH", $dark);

    imagepng($im, __DIR__ . '/flowchart_finalisasi.png');
    imagepng($im, __DIR__ . '/../public/images/mockups/flowchart_finalisasi.png');
    imagedestroy($im);
    echo "Flowchart image generated successfully!\n";
}

createFlowchartImage();

