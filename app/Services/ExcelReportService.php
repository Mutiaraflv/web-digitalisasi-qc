<?php

namespace App\Services;

use Carbon\Carbon;
use Exception;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\Drawing as SharedDrawing;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelReportService
{
    // Master 21-column grid percentages (A..U = 100.00%) matching PDF alignment
    private const COL_PERCENTS = [
        'A' => 6.00,  'B' => 3.00,  'C' => 1.00,  'D' => 7.50,  'E' => 6.50,
        'F' => 1.00,  'G' => 8.33,  'H' => 3.67,  'I' => 10.50, 'J' => 1.00,
        'K' => 1.50,  'L' => 6.00,  'M' => 5.35,  'N' => 5.32,  'O' => 1.83,
        'P' => 8.50,  'Q' => 2.00,  'R' => 1.00,  'S' => 5.50,  'T' => 8.50,
        'U' => 6.00,
    ];

    private const WIDTH_SCALE = 1.15;

    // Color Palette matching PDF Theme (SSC Royal Blue + Industrial Amber)
    private const COLOR_ROYAL_BLUE = '1E40AF';
    private const COLOR_ROYAL_BORDER = '1D4ED8';
    private const COLOR_DARK_BLUE = '1E3A8A';
    private const COLOR_LIGHT_BLUE_BG = 'EFF6FF';
    private const COLOR_BLUE_BORDER = 'BFDBFE';
    private const COLOR_SLATE_BG = 'F8FAFC';
    private const COLOR_SLATE_TEXT = '334155';
    private const COLOR_MUTED_TEXT = '94A3B8';
    private const COLOR_BORDER = 'CBD5E1';
    private const COLOR_DARK_BORDER = '475569';
    private const COLOR_AMBER_BG = 'FFFBEB';
    private const COLOR_AMBER_TEXT = '92400E';
    private const COLOR_AMBER_BORDER = 'FCD34D';
    private const COLOR_AMBER_ACCENT = 'F59E0B';
    private const COLOR_ACCEPT_BG = 'ECFDF5';
    private const COLOR_ACCEPT_TEXT = '059669';
    private const COLOR_REJECT_BG = 'FEF2F2';
    private const COLOR_REJECT_TEXT = 'DC2626';

    /** @var array<string> Temporary prepared images to delete after saving */
    private array $tempImages = [];

    /**
     * Generate clean 6-page A4 Excel file mirroring the official PDF layout & user inputs.
     *
     * @throws Exception
     */
    public function generateExcelFile($report): string
    {
        $this->tempImages = [];

        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $filename = 'CCR_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $report->ssc_id ?? 'REPORT') . '_' . time() . '_' . uniqid() . '.xlsx';
        $outputPath = $tempDir . DIRECTORY_SEPARATOR . $filename;

        $sections = is_array($report->sections) ? $report->sections : (json_decode($report->sections ?? '[]', true) ?: []);
        $tir = is_array($report->tir) ? $report->tir : (json_decode($report->tir ?? '[]', true) ?: []);
        $recs = is_array($report->recommendations) ? $report->recommendations : (json_decode($report->recommendations ?? '[]', true) ?: []);
        $sigs = is_array($report->signatures) ? $report->signatures : (json_decode($report->signatures ?? '[]', true) ?: []);
        $photos = is_array($report->photos) ? $report->photos : (json_decode($report->photos ?? '[]', true) ?: []);

        try {
            $spreadsheet = new Spreadsheet();
            $spreadsheet->getProperties()
                ->setCreator('PT. SSC WORKS - QCMS')
                ->setTitle('Component Condition Report - ' . ($report->ssc_id ?? 'FORM-QC-002'));

            $spreadsheet->getDefaultStyle()->getFont()
                ->setName('Arial')
                ->setSize(8)
                ->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF1E293B'));

            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('CCR Report');
            $sheet->setShowGridlines(false);

            // Configure master column widths
            foreach (self::COL_PERCENTS as $col => $pct) {
                $sheet->getColumnDimension($col)->setWidth(round($pct * self::WIDTH_SCALE, 2));
            }

            // Page Setup: A4 Portrait fit 1 page wide
            $pageSetup = $sheet->getPageSetup();
            $pageSetup->setPaperSize(PageSetup::PAPERSIZE_A4);
            $pageSetup->setOrientation(PageSetup::ORIENTATION_PORTRAIT);
            $pageSetup->setFitToWidth(1);
            $pageSetup->setFitToHeight(0);
            $sheet->getPageMargins()->setTop(0.35)->setBottom(0.35)->setLeft(0.35)->setRight(0.35)->setHeader(0.15)->setFooter(0.15);

            $row = 1;

            // ==================== LEMBAR 1 DARI 6 ====================
            $row = $this->renderKopHeader($sheet, $report, $row);
            $row = $this->renderBlueBar($sheet, $row, 'Drawing / Pictures / Dimension');
            $row = $this->renderFullWidthPhotoBox(
                $sheet,
                $row,
                9,
                18.0,
                $photos['drawing_dimension'] ?? null,
                '[ Gambar Drawing / Pictures / Dimension Belum Diunggah ]',
                950
            );
            $row = $this->renderBlueBar($sheet, $row, 'FINDING');
            $row = $this->renderFindingSection($sheet, $row, $report, $photos);
            $row = $this->renderArea01Section($sheet, $row, $sections['item01'] ?? [], $photos);
            $sheet->setBreak('A' . ($row - 1), Worksheet::BREAK_ROW);

            // ==================== LEMBAR 2 DARI 6 ====================
            $row = $this->renderPenetrantSection(
                $sheet,
                $row,
                'Image of Penetrant Check OD Area "A" and OD Spline',
                ['area_a_penet_1', 'area_a_penet_2', 'area_a_penet_3', 'area_a_penet_4'],
                $photos,
                $sections['item01']['penetrantRemark'] ?? 'No indication crack at OD area "A" and OD spline after penetrant check'
            );
            $row = $this->renderArea02Section($sheet, $row, $sections['item02'] ?? [], $photos);
            $row = $this->renderPenetrantSection(
                $sheet,
                $row,
                'Image of Penetrant Check OD Area "B and C"',
                ['area_bc_penet_1', 'area_bc_penet_2', 'area_bc_penet_3', 'area_bc_penet_4'],
                $photos,
                $sections['item02']['penetrantRemark'] ?? 'No indication crack at OD Area "B and C" after penetrant check'
            );
            $sheet->setBreak('A' . ($row - 1), Worksheet::BREAK_ROW);

            // ==================== LEMBAR 3 DARI 6 ====================
            $row = $this->renderArea03Section($sheet, $row, $sections['item03'] ?? [], $photos);
            $row = $this->renderPenetrantSection(
                $sheet,
                $row,
                'Image of Penetrant Check OD Area "D"',
                ['area_d_penet_1', 'area_d_penet_2', 'area_d_penet_3', 'area_d_penet_4'],
                $photos,
                $sections['item03']['penetrantRemark'] ?? 'No indication crack at OD area "D" after penetrant check'
            );
            $row = $this->renderArea04Section($sheet, $row, $sections['item04'] ?? [], $photos);
            $sheet->setBreak('A' . ($row - 1), Worksheet::BREAK_ROW);

            // ==================== LEMBAR 4 DARI 6 ====================
            $row = $this->renderPenetrantSection(
                $sheet,
                $row,
                'Image of Penetrant Check OD Area "F"',
                ['area_f_penet_1', 'area_f_penet_2', 'area_f_penet_3', 'area_f_penet_4'],
                $photos,
                $sections['item04']['penetrantRemark'] ?? 'No indication crack at area OD area "F" after penetrant check'
            );
            $row = $this->renderArea05Section($sheet, $row, $sections['item05'] ?? [], $photos);
            $row = $this->renderPenetrantSection(
                $sheet,
                $row,
                'Image of Penetrant Check OD Area "H"',
                ['area_h_penet_1', 'area_h_penet_2', 'area_h_penet_3', 'area_h_penet_4'],
                $photos,
                $sections['item05']['penetrantRemark'] ?? 'No indication crack at area OD area "H" after penetrant check'
            );
            $sheet->setBreak('A' . ($row - 1), Worksheet::BREAK_ROW);

            // ==================== LEMBAR 5 DARI 6 ====================
            $row = $this->renderArea06Section($sheet, $row, $sections['item06'] ?? [], $photos);
            $row = $this->renderPenetrantSection(
                $sheet,
                $row,
                'Image of Penetrant Check Area OD Spline',
                ['area_g_penet_1', 'area_g_penet_2', 'area_g_penet_3', 'area_g_penet_4'],
                $photos,
                $sections['item06']['penetrantRemark'] ?? 'No indication crack at area OD spline after penetrant check'
            );
            $row = $this->renderAmberBar($sheet, $row, 'Check TIR');
            $row = $this->renderFullWidthPhotoBox(
                $sheet,
                $row,
                7,
                17.5,
                $photos['tir_diagram'] ?? null,
                '[ Diagram / Sketsa Titik Ukur TIR Belum Diunggah ]',
                950
            );
            $row = $this->renderTirTable($sheet, $row, $tir);
            $sheet->setBreak('A' . ($row - 1), Worksheet::BREAK_ROW);

            // ==================== LEMBAR 6 DARI 6 ====================
            $row = $this->renderAmberBar($sheet, $row, 'RECOMENDATION');
            $row = $this->renderRecommendationsTable($sheet, $row, $recs);

            // Spacer row before signatures
            $sheet->getRowDimension($row)->setRowHeight(16);
            $row++;

            $row = $this->renderSignaturesBlock($sheet, $row, $sigs);

            // Set Print Area
            $pageSetup->setPrintArea('A1:U' . ($row - 1));

            $writer = new Xlsx($spreadsheet);
            $writer->save($outputPath);

            return $outputPath;
        } finally {
            foreach ($this->tempImages as $tmpImg) {
                if (file_exists($tmpImg)) {
                    @unlink($tmpImg);
                }
            }
            $this->tempImages = [];
        }
    }

    /**
     * Render aligned KOP header and metadata table (Rows 1..7).
     */
    private function renderKopHeader(Worksheet $sheet, $report, int $row): int
    {
        $rLogoStart = $row;
        $rLogoEnd = $row + 3;

        for ($r = $rLogoStart; $r <= $rLogoEnd + 3; $r++) {
            $sheet->getRowDimension($r)->setRowHeight(16.5);
        }
        $sheet->getRowDimension($row)->setRowHeight(14);
        $sheet->getRowDimension($row + 1)->setRowHeight(14);

        // Logo cell (0% - 37%: A1:H4)
        $sheet->mergeCells("A{$row}:H" . ($row + 3));
        $logoPath = public_path('images/doc_assets/kop_logo_perfect.png');
        if (file_exists($logoPath)) {
            $this->placeImageInBox($sheet, $logoPath, 'A', 'H', $row, $row + 3, 550, 4, true);
        }

        // Document title banner (37% - 100%: I1:U2)
        $sheet->mergeCells("I{$row}:U" . ($row + 1));
        $sheet->setCellValue("I{$row}", 'COMPONENT CONDITION REPORT');
        $sheet->getStyle("I{$row}:U" . ($row + 1))->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_ROYAL_BLUE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // Control table row 1 (Document No & Responsibility)
        $rCtrl1 = $row + 2;
        $sheet->setCellValue("I{$rCtrl1}", 'Document No');
        $sheet->setCellValue("J{$rCtrl1}", ':');
        $sheet->mergeCells("K{$rCtrl1}:O{$rCtrl1}");
        $sheet->setCellValueExplicit("K{$rCtrl1}", (string) ($report->doc_no ?? 'FORM-QC-002'), DataType::TYPE_STRING);

        $sheet->mergeCells("P{$rCtrl1}:Q{$rCtrl1}");
        $sheet->setCellValue("P{$rCtrl1}", 'Responsibility');
        $sheet->setCellValue("R{$rCtrl1}", ':');
        $sheet->mergeCells("S{$rCtrl1}:U{$rCtrl1}");
        $sheet->setCellValueExplicit("S{$rCtrl1}", (string) ($report->responsibility ?: 'QC Inspector'), DataType::TYPE_STRING);

        // Control table row 2 (Effective Date, Rev & Page)
        $rCtrl2 = $row + 3;
        $effDateStr = !empty($report->effective_date) ? Carbon::parse($report->effective_date)->format('d-M-Y') : '28-Jan-2022';
        $revStr = 'Rev. ' . ($report->rev ?: '04');
        $pageNum = $report->page ?: '1';

        $sheet->setCellValue("I{$rCtrl2}", 'Effective Date.');
        $sheet->setCellValue("J{$rCtrl2}", ':');
        $sheet->mergeCells("K{$rCtrl2}:M{$rCtrl2}");
        $sheet->setCellValueExplicit("K{$rCtrl2}", $effDateStr, DataType::TYPE_STRING);
        $sheet->mergeCells("N{$rCtrl2}:O{$rCtrl2}");
        $sheet->setCellValueExplicit("N{$rCtrl2}", $revStr, DataType::TYPE_STRING);

        $sheet->mergeCells("P{$rCtrl2}:Q{$rCtrl2}");
        $sheet->setCellValue("P{$rCtrl2}", 'Page');
        $sheet->setCellValue("R{$rCtrl2}", ':');
        $sheet->mergeCells("S{$rCtrl2}:U{$rCtrl2}");
        $sheet->setCellValueExplicit("S{$rCtrl2}", "{$pageNum}   of   6", DataType::TYPE_STRING);

        // Apply control table styling
        $this->applyAllBorders($sheet, "A{$rLogoStart}:U{$rLogoEnd}");
        $sheet->getStyle("I{$rCtrl1}:J{$rCtrl2}")->applyFromArray($this->ctrlLabelStyle());
        $sheet->getStyle("P{$rCtrl1}:R{$rCtrl2}")->applyFromArray($this->ctrlLabelStyle());
        $sheet->getStyle("J{$rCtrl1}:J{$rCtrl2}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("R{$rCtrl1}:R{$rCtrl2}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("K{$rCtrl1}:O{$rCtrl1}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 7.5],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle("S{$rCtrl1}:U{$rCtrl1}")->applyFromArray([
            'font' => ['size' => 7.5],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle("K{$rCtrl2}:O{$rCtrl2}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 7.5],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle("S{$rCtrl2}:U{$rCtrl2}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 7.5],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // Thick bottom royal blue border on KOP top block
        $sheet->getStyle("A{$rLogoEnd}:U{$rLogoEnd}")->getBorders()->getBottom()
            ->setBorderStyle(Border::BORDER_MEDIUM)->getColor()->setRGB(self::COLOR_ROYAL_BLUE);

        // Metadata Grid (Rows 5..7)
        $rMeta1 = $row + 4;
        $rMeta2 = $row + 5;
        $rMeta3 = $row + 6;
        $dateStr = !empty($report->date) ? Carbon::parse($report->date)->format('d-M-y') : date('d-M-y');
        $isPre = ($report->inspection_type ?? 'Pre') === 'Pre';

        // Meta Row 1
        $sheet->mergeCells("A{$rMeta1}:B{$rMeta1}");
        $sheet->setCellValue("A{$rMeta1}", 'No.');
        $sheet->setCellValue("C{$rMeta1}", ':');
        $sheet->setCellValueExplicit("D{$rMeta1}", $this->valOrDash($report->report_no), DataType::TYPE_STRING);
        $sheet->setCellValue("E{$rMeta1}", 'Date');
        $sheet->setCellValue("F{$rMeta1}", ':');
        $sheet->mergeCells("G{$rMeta1}:H{$rMeta1}");
        $sheet->setCellValueExplicit("G{$rMeta1}", $dateStr, DataType::TYPE_STRING);

        $sheet->setCellValue("I{$rMeta1}", 'Customer');
        $sheet->setCellValue("J{$rMeta1}", ':');
        $sheet->mergeCells("K{$rMeta1}:O{$rMeta1}");
        $sheet->setCellValueExplicit("K{$rMeta1}", $this->valOrDash($report->customer), DataType::TYPE_STRING);

        $sheet->mergeCells("P{$rMeta1}:Q{$rMeta1}");
        $sheet->setCellValue("P{$rMeta1}", 'Part Name');
        $sheet->setCellValue("R{$rMeta1}", ':');
        $sheet->mergeCells("S{$rMeta1}:U{$rMeta1}");
        $sheet->setCellValueExplicit("S{$rMeta1}", $this->valOrDash($report->part_name, 'OUTPUT SHAFT'), DataType::TYPE_STRING);

        // Increase height slightly if customer name wraps
        if (strlen((string) ($report->customer ?? '')) > 24) {
            $sheet->getRowDimension($rMeta1)->setRowHeight(23);
        }

        // Meta Row 2
        $sheet->mergeCells("A{$rMeta2}:B{$rMeta2}");
        $sheet->setCellValue("A{$rMeta2}", 'Inspection');
        $sheet->setCellValue("C{$rMeta2}", ':');
        $sheet->mergeCells("D{$rMeta2}:H{$rMeta2}");
        $sheet->setCellValue("D{$rMeta2}", ($isPre ? '☑ Pre      ☐ Post' : '☐ Pre      ☑ Post'));

        $sheet->setCellValue("I{$rMeta2}", 'Job No.');
        $sheet->setCellValue("J{$rMeta2}", ':');
        $sheet->mergeCells("K{$rMeta2}:O{$rMeta2}");
        $sheet->setCellValueExplicit("K{$rMeta2}", $this->valOrDash($report->job_no), DataType::TYPE_STRING);

        $sheet->mergeCells("P{$rMeta2}:Q{$rMeta2}");
        $sheet->setCellValue("P{$rMeta2}", 'Model/Unit');
        $sheet->setCellValue("R{$rMeta2}", ':');
        $sheet->mergeCells("S{$rMeta2}:U{$rMeta2}");
        $sheet->setCellValueExplicit("S{$rMeta2}", $this->valOrDash($report->model_unit), DataType::TYPE_STRING);

        // Meta Row 3
        $sheet->mergeCells("A{$rMeta3}:B{$rMeta3}");
        $sheet->setCellValue("A{$rMeta3}", 'SSC ID');
        $sheet->setCellValue("C{$rMeta3}", ':');
        $sheet->mergeCells("D{$rMeta3}:H{$rMeta3}");
        $sheet->setCellValueExplicit("D{$rMeta3}", $this->valOrDash($report->ssc_id), DataType::TYPE_STRING);

        $sheet->setCellValue("I{$rMeta3}", 'Quote No.');
        $sheet->setCellValue("J{$rMeta3}", ':');
        $sheet->mergeCells("K{$rMeta3}:O{$rMeta3}");
        $sheet->setCellValueExplicit("K{$rMeta3}", $this->valOrDash($report->quote_no), DataType::TYPE_STRING);

        $sheet->mergeCells("P{$rMeta3}:Q{$rMeta3}");
        $sheet->setCellValue("P{$rMeta3}", 'Part No.');
        $sheet->setCellValue("R{$rMeta3}", ':');
        $sheet->mergeCells("S{$rMeta3}:U{$rMeta3}");
        $sheet->setCellValueExplicit("S{$rMeta3}", $this->valOrDash($report->part_no), DataType::TYPE_STRING);

        // Style Meta Grid
        $this->applyAllBorders($sheet, "A{$rMeta1}:U{$rMeta3}");
        foreach (["A{$rMeta1}:C{$rMeta3}", "E{$rMeta1}:F{$rMeta1}", "I{$rMeta1}:J{$rMeta3}", "P{$rMeta1}:R{$rMeta3}"] as $lblRange) {
            $sheet->getStyle($lblRange)->applyFromArray($this->metaLabelStyle());
        }
        foreach (["C{$rMeta1}:C{$rMeta3}", "F{$rMeta1}", "J{$rMeta1}:J{$rMeta3}", "R{$rMeta1}:R{$rMeta3}"] as $colonRef) {
            $sheet->getStyle($colonRef)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        $sheet->getStyle("D{$rMeta1}:H{$rMeta3}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle("D{$rMeta1}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("G{$rMeta1}:H{$rMeta1}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("D{$rMeta3}:H{$rMeta3}")->getFont()->setBold(true);
        $sheet->getStyle("K{$rMeta1}:O{$rMeta3}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
        $sheet->getStyle("K{$rMeta1}:O{$rMeta1}")->getFont()->setBold(true)->setSize(7.5);
        $sheet->getStyle("S{$rMeta1}:U{$rMeta3}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle("S{$rMeta1}:U{$rMeta1}")->getFont()->setBold(true);

        $sheet->getStyle("A{$rMeta3}:U{$rMeta3}")->getBorders()->getBottom()
            ->setBorderStyle(Border::BORDER_MEDIUM)->getColor()->setRGB(self::COLOR_ROYAL_BLUE);

        return $row + 7;
    }

    /**
     * Render Blue Section Bar (Drawing / FINDING).
     */
    private function renderBlueBar(Worksheet $sheet, int $row, string $title): int
    {
        $sheet->getRowDimension($row)->setRowHeight(17);
        $sheet->mergeCells("A{$row}:U{$row}");
        $sheet->setCellValue("A{$row}", ' ' . $title);
        $sheet->getStyle("A{$row}:U{$row}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['rgb' => self::COLOR_DARK_BLUE]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_LIGHT_BLUE_BG]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => [
                'top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::COLOR_BLUE_BORDER]],
                'bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::COLOR_BLUE_BORDER]],
                'left' => ['borderStyle' => Border::BORDER_THICK, 'color' => ['rgb' => self::COLOR_ROYAL_BORDER]],
                'right' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::COLOR_BORDER]],
            ],
        ]);
        return $row + 1;
    }

    /**
     * Render Industrial Amber Section Banner (OD Area A, B&C, D, F, H, G, TIR, RECOMENDATION).
     */
    private function renderAmberBar(Worksheet $sheet, int $row, string $title): int
    {
        $sheet->getRowDimension($row)->setRowHeight(17);
        $sheet->mergeCells("A{$row}:U{$row}");
        $sheet->setCellValue("A{$row}", ' ' . $title);
        $sheet->getStyle("A{$row}:U{$row}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['rgb' => self::COLOR_AMBER_TEXT]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_AMBER_BG]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => [
                'top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::COLOR_AMBER_BORDER]],
                'bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::COLOR_AMBER_BORDER]],
                'left' => ['borderStyle' => Border::BORDER_THICK, 'color' => ['rgb' => self::COLOR_AMBER_ACCENT]],
                'right' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::COLOR_BORDER]],
            ],
        ]);
        return $row + 1;
    }

    /**
     * Render full-width image / blueprint diagram box across A..U.
     */
    private function renderFullWidthPhotoBox(
        Worksheet $sheet,
        int $row,
        int $numRows,
        float $rowHeight,
        ?string $photoUrl,
        string $placeholder,
        int $maxImgPx
    ): int {
        $endRow = $row + $numRows - 1;
        for ($r = $row; $r <= $endRow; $r++) {
            $sheet->getRowDimension($r)->setRowHeight($rowHeight);
        }
        $sheet->mergeCells("A{$row}:U{$endRow}");

        $localPath = $this->resolveLocalPhotoPath($photoUrl);
        if ($localPath) {
            $this->placeImageInBox($sheet, $localPath, 'A', 'U', $row, $endRow, $maxImgPx, 4);
        } else {
            $sheet->setCellValue("A{$row}", $placeholder);
            $sheet->getStyle("A{$row}:U{$endRow}")->applyFromArray([
                'font' => ['italic' => true, 'size' => 8, 'color' => ['rgb' => self::COLOR_MUTED_TEXT]],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FAFAFA']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
        }

        $sheet->getStyle("A{$row}:U{$endRow}")->getBorders()->getOutline()
            ->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB(self::COLOR_BORDER);

        return $endRow + 1;
    }

    /**
     * Render FINDING section (NO: 00 | IMAGE | DESCRIPTION).
     */
    private function renderFindingSection(Worksheet $sheet, int $row, $report, array $photos): int
    {
        $sheet->getRowDimension($row)->setRowHeight(15);
        $sheet->setCellValue("A{$row}", 'NO');
        $sheet->mergeCells("B{$row}:K{$row}");
        $sheet->setCellValue("B{$row}", 'IMAGE');
        $sheet->mergeCells("L{$row}:U{$row}");
        $sheet->setCellValue("L{$row}", 'DESCRIPTION');

        $this->applyAllBorders($sheet, "A{$row}:U{$row}");
        $sheet->getStyle("A{$row}:U{$row}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 7.5, 'color' => ['rgb' => self::COLOR_DARK_BLUE]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_LIGHT_BLUE_BG]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $rStart = $row + 1;
        $rEnd = $row + 5;
        for ($r = $rStart; $r <= $rEnd; $r++) {
            $sheet->getRowDimension($r)->setRowHeight(17);
        }

        $sheet->mergeCells("A{$rStart}:A{$rEnd}");
        $sheet->setCellValueExplicit("A{$rStart}", '00', DataType::TYPE_STRING);
        $sheet->getStyle("A{$rStart}:A{$rEnd}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 8],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $sheet->mergeCells("B{$rStart}:K{$rEnd}");
        $findingImg = $photos['finding_component'] ?? ($photos['full_component'] ?? null);
        $localPath = $this->resolveLocalPhotoPath($findingImg);
        if ($localPath) {
            $this->placeImageInBox($sheet, $localPath, 'B', 'K', $rStart, $rEnd, 550, 3);
        } else {
            $sheet->setCellValue("B{$rStart}", '[ Foto Komponen Utuh Belum Diunggah ]');
            $sheet->getStyle("B{$rStart}:K{$rEnd}")->applyFromArray([
                'font' => ['italic' => true, 'size' => 7.5, 'color' => ['rgb' => self::COLOR_MUTED_TEXT]],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FAFAFA']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
        }

        $partName = strtoupper(trim((string) ($report->part_name ?: 'OUTPUT SHAFT')));
        $sheet->mergeCells("L{$rStart}:U{$rEnd}");
        $sheet->setCellValue("L{$rStart}", "> Full image received component {$partName}");
        $sheet->getStyle("L{$rStart}:U{$rEnd}")->applyFromArray([
            'font' => ['size' => 7.5, 'color' => ['rgb' => self::COLOR_SLATE_TEXT]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_TOP, 'wrapText' => true, 'indent' => 1],
        ]);

        $this->applyAllBorders($sheet, "A{$rStart}:U{$rEnd}");

        return $rEnd + 1;
    }

    /**
     * Section 01: OD Area "A" and OD Spline (Page 1).
     */
    private function renderArea01Section(Worksheet $sheet, int $row, array $i1, array $photos): int
    {
        $row = $this->renderAmberBar($sheet, $row, $i1['title'] ?? 'OD Area "A" and OD Spline');
        $row = $this->renderSplitColumnHeaders($sheet, $row, 'Image of OD Spline', 'Actual visual & measurement');

        $bodyStart = $row;
        $this->initSplitBodyRows($sheet, $bodyStart);
        $this->renderPhotoGrid2x2($sheet, $bodyStart, ['area_a_visual_1', 'area_a_visual_2', 'area_a_visual_3', 'area_a_visual_4'], ['Foto 1', 'Foto 2', 'Foto 3', 'Foto 4'], $photos);

        // Right Side (10 rows: bodyStart .. bodyStart+9)
        $r = $bodyStart;
        $r = $this->renderSubTitle($sheet, $r, 'Actual Measurement');
        $r = $this->renderMeasurementHeaders($sheet, $r);
        $r = $this->renderMeasurementRow($sheet, $r, 'A', $i1['spec'] ?? 'Ø79 ± 0,25', $i1['x'] ?? null, $i1['y'] ?? null, $i1['z'] ?? null, $i1['ar'] ?? 'A');

        $remarks = [];
        for ($k = 1; $k <= 6; $k++) {
            if (!empty($i1["remark{$k}"])) {
                $remarks[] = $i1["remark{$k}"];
            }
        }
        if (empty($remarks)) {
            $remarks = ['Actual measurement OD area "A" in good condition'];
        }
        $this->renderBulletBox($sheet, $r, $r + 3, $remarks);
        $r += 4;

        $r = $this->renderSubTitle($sheet, $r, 'Actual Length');
        $r = $this->renderSingleResultHeaders($sheet, $r);
        $lenVal = $i1['lengthResult'] ?? ($i1['length'] ?? null);
        $this->renderSingleResultRow($sheet, $r, 'L', $i1['lengthSpec'] ?? 'N/A', $lenVal, $i1['lengthAr'] ?? '-');

        return $this->renderLegendFooter($sheet, $bodyStart + 10);
    }

    /**
     * Section 02: OD Area "B and C" (Page 2).
     */
    private function renderArea02Section(Worksheet $sheet, int $row, array $i2, array $photos): int
    {
        $row = $this->renderAmberBar($sheet, $row, $i2['title'] ?? 'OD Area "B and C"');
        $row = $this->renderSplitColumnHeaders($sheet, $row, 'Image of OD Area "B and C"', 'Actual visual & measurement');

        $bodyStart = $row;
        $this->initSplitBodyRows($sheet, $bodyStart);
        $this->renderPhotoGrid2x2($sheet, $bodyStart, ['area_bc_visual_1', 'area_bc_visual_2', 'area_bc_visual_3', 'area_bc_visual_4'], ['Foto 1', 'Foto 2', 'Foto 3', 'Foto 4'], $photos);

        $r = $bodyStart;
        $r = $this->renderSubTitle($sheet, $r, 'Actual Measurement');
        $r = $this->renderMeasurementHeaders($sheet, $r);

        $b = $i2['itemB'] ?? [];
        $r = $this->renderMultiPointGroup($sheet, $r, 'B', $b['spec'] ?? 'Ø82 ± 0,25', [
            $b['row1'] ?? [], $b['row2'] ?? [], $b['row3'] ?? [],
        ]);

        $c = $i2['itemC'] ?? [];
        $r = $this->renderMultiPointGroup($sheet, $r, 'C', $c['spec'] ?? 'Ø77 ± 0,25', [
            $c['row1'] ?? [], $c['row2'] ?? [], $c['row3'] ?? [],
        ]);

        $remarks = $this->extractRemarks($i2['remarks'] ?? [], ['Actual measurement OD area "B and C" in good condition']);
        $this->renderBulletBox($sheet, $r, $r + 1, $remarks);

        return $this->renderLegendFooter($sheet, $bodyStart + 10);
    }

    /**
     * Section 03: OD Area "D" & Distance "E" (Page 3).
     */
    private function renderArea03Section(Worksheet $sheet, int $row, array $i3, array $photos): int
    {
        $row = $this->renderAmberBar($sheet, $row, $i3['title'] ?? 'OD Area "D"');
        $row = $this->renderSplitColumnHeaders($sheet, $row, 'Image of OD Area "D"', 'Actual visual & measurement');

        $bodyStart = $row;
        $this->initSplitBodyRows($sheet, $bodyStart);
        $this->renderPhotoGrid2x2($sheet, $bodyStart, ['area_d_visual_1', 'area_d_visual_2', 'area_d_visual_3', 'area_d_visual_4'], ['Foto 1', 'Foto 2', 'Foto 3', 'Foto 4'], $photos);

        $r = $bodyStart;
        $r = $this->renderSubTitle($sheet, $r, 'Actual Measurement');
        $r = $this->renderMeasurementHeaders($sheet, $r);
        $d = $i3['itemD'] ?? [];
        $r = $this->renderMeasurementRow($sheet, $r, 'D', $d['spec'] ?? 'Ø118 ± 0,5', $d['x'] ?? null, $d['y'] ?? null, $d['z'] ?? null, $d['ar'] ?? 'A');

        $allRemarks = $this->extractRemarks($i3['remarks'] ?? [], [
            'Actual measurement OD area "D" in good condition',
            'Actual distance area "E" in good condition',
        ]);
        $this->renderBulletBox($sheet, $r, $r + 1, [$allRemarks[0] ?? 'Actual measurement OD area "D" in good condition']);
        $r += 2;

        $r = $this->renderSubTitle($sheet, $r, 'Actual Distance');
        $r = $this->renderSingleResultHeaders($sheet, $r);
        $e = $i3['distanceE'] ?? ($i3['itemE'] ?? []);
        $eVal = $e['x'] ?? ($e['result'] ?? null);
        $r = $this->renderSingleResultRow($sheet, $r, 'E', $e['spec'] ?? '13 ± 0,5', $eVal, $e['ar'] ?? 'A');

        $eRemarks = array_slice($allRemarks, 1);
        if (empty($eRemarks)) {
            $eRemarks = ['Actual distance area "E" in good condition'];
        }
        $this->renderBulletBox($sheet, $r, $r + 1, $eRemarks);

        return $this->renderLegendFooter($sheet, $bodyStart + 10);
    }

    /**
     * Section 04 / 05: Shared 3-row multi-point measurement + distance block (Area F/K & Area H/J).
     */
    private function renderMultiPointAreaSection(
        Worksheet $sheet,
        int $row,
        array $secData,
        array $photos,
        array $cfg
    ): int {
        $row = $this->renderAmberBar($sheet, $row, $secData['title'] ?? $cfg['title']);
        $row = $this->renderSplitColumnHeaders($sheet, $row, $cfg['photoHeader'], 'Actual visual & measurement');

        $bodyStart = $row;
        $this->initSplitBodyRows($sheet, $bodyStart);
        $this->renderPhotoGrid2x2($sheet, $bodyStart, $cfg['slotKeys'], ['Foto 1', 'Foto 2', 'Foto 3', 'Foto 4'], $photos);

        $r = $bodyStart;
        $r = $this->renderSubTitle($sheet, $r, 'Actual Measurement');
        $r = $this->renderMeasurementHeaders($sheet, $r);
        $group = $secData[$cfg['groupKey']] ?? [];
        $r = $this->renderMultiPointGroup($sheet, $r, $cfg['itemLabel'], $group['spec'] ?? $cfg['spec'], [
            $group['row1'] ?? [], $group['row2'] ?? [], $group['row3'] ?? [],
        ]);

        $remarks = $this->extractRemarks($secData['remarks'] ?? [], [$cfg['defaultRemark']]);
        $this->renderBulletBox($sheet, $r, $r + 1, $remarks);
        $r += 2;

        $r = $this->renderSubTitle($sheet, $r, 'Actual Distance');
        $r = $this->renderSingleResultHeaders($sheet, $r);
        $dist = $secData[$cfg['distKey']] ?? [];
        $distVal = $dist['result'] ?? ($secData[$cfg['legacyDistKey']] ?? null);
        $this->renderSingleResultRow($sheet, $r, $cfg['distLabel'], $dist['spec'] ?? 'N/A', $distVal, $dist['ar'] ?? '-');

        return $this->renderLegendFooter($sheet, $bodyStart + 10);
    }

    /**
     * Section 04: OD Area "F" & Distance "K" (Page 3).
     */
    private function renderArea04Section(Worksheet $sheet, int $row, array $i4, array $photos): int
    {
        return $this->renderMultiPointAreaSection($sheet, $row, $i4, $photos, [
            'title' => 'OD Area "F"',
            'photoHeader' => 'Image of OD Area "F"',
            'slotKeys' => ['area_f_visual_1', 'area_f_visual_2', 'area_f_visual_3', 'area_f_visual_4'],
            'groupKey' => 'itemF',
            'itemLabel' => 'F',
            'spec' => 'Ø100,01 ± 0,008',
            'defaultRemark' => 'Actual measurement OD area "F" in good condition',
            'distKey' => 'distanceK',
            'legacyDistKey' => 'distK',
            'distLabel' => 'K',
        ]);
    }

    /**
     * Section 05: OD Area "H" & Distance "J" (Page 4).
     */
    private function renderArea05Section(Worksheet $sheet, int $row, array $i5, array $photos): int
    {
        return $this->renderMultiPointAreaSection($sheet, $row, $i5, $photos, [
            'title' => 'OD Area "H"',
            'photoHeader' => 'Image of OD Area "H"',
            'slotKeys' => ['area_h_visual_1', 'area_h_visual_2', 'area_h_visual_3', 'area_h_visual_4'],
            'groupKey' => 'itemH',
            'itemLabel' => 'H',
            'spec' => 'Ø94 ± 0,013',
            'defaultRemark' => 'Actual measurement OD area "H" in good condition',
            'distKey' => 'distanceJ',
            'legacyDistKey' => 'distJ',
            'distLabel' => 'J',
        ]);
    }

    /**
     * Section 06: OD Area "G" and OD Spline & Distance "I" (Page 5).
     */
    private function renderArea06Section(Worksheet $sheet, int $row, array $i6, array $photos): int
    {
        $row = $this->renderAmberBar($sheet, $row, $i6['title'] ?? 'OD Area "G" and OD Spline');
        $row = $this->renderSplitColumnHeaders($sheet, $row, 'Image of OD Spline', 'Actual visual & measurement');

        $bodyStart = $row;
        $this->initSplitBodyRows($sheet, $bodyStart);
        $this->renderPhotoGrid2x2($sheet, $bodyStart, ['area_g_visual_1', 'area_g_visual_2', 'area_g_visual_3', 'area_g_visual_4'], ['Foto 1', 'Foto 2', 'Foto 3', 'Foto 4'], $photos);

        $r = $bodyStart;
        $r = $this->renderSubTitle($sheet, $r, 'Actual Measurement');
        $r = $this->renderMeasurementHeaders($sheet, $r);
        $g = $i6['itemG'] ?? [];
        $r = $this->renderMeasurementRow($sheet, $r, 'G', $g['spec'] ?? 'Ø84,2 ± 0,13', $g['x'] ?? null, $g['y'] ?? null, $g['z'] ?? null, $g['ar'] ?? 'A');

        $remarks = $this->extractRemarks($i6['remarks'] ?? [], ['Actual measurement OD area "G" in good condition']);
        $this->renderBulletBox($sheet, $r, $r + 3, $remarks);
        $r += 4;

        $r = $this->renderSubTitle($sheet, $r, 'Actual Distance');
        $r = $this->renderSingleResultHeaders($sheet, $r);
        $i = $i6['distanceI'] ?? [];
        $iVal = $i['result'] ?? ($i6['distI'] ?? null);
        $this->renderSingleResultRow($sheet, $r, 'I', $i['spec'] ?? 'N/A', $iVal, $i['ar'] ?? '-');

        return $this->renderLegendFooter($sheet, $bodyStart + 10);
    }

    /**
     * Shared Penetrant Check Split Section (4 photos left + Actual condition right).
     */
    private function renderPenetrantSection(
        Worksheet $sheet,
        int $row,
        string $leftHeader,
        array $slotKeys,
        array $photos,
        string $conditionText
    ): int {
        $row = $this->renderSplitColumnHeaders($sheet, $row, $leftHeader, 'Actual condition');

        $bodyStart = $row;
        $bodyEnd = $bodyStart + 7;
        for ($r = $bodyStart; $r <= $bodyEnd; $r++) {
            $sheet->getRowDimension($r)->setRowHeight(18.5);
        }

        // 2x2 Grid (4 rows per quadrant)
        $this->renderPhotoGrid2x2(
            $sheet,
            $bodyStart,
            $slotKeys,
            ['Penetrant 1', 'Penetrant 2', 'Penetrant 3', 'Penetrant 4'],
            $photos,
            4
        );

        // Right condition box
        $sheet->mergeCells("L{$bodyStart}:U{$bodyEnd}");
        $sheet->setCellValue("L{$bodyStart}", "\n> " . $conditionText);
        $sheet->getStyle("L{$bodyStart}:U{$bodyEnd}")->applyFromArray([
            'font' => ['size' => 7.5, 'color' => ['rgb' => self::COLOR_SLATE_TEXT]],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_TOP,
                'wrapText' => true,
                'indent' => 1,
            ],
        ]);
        $sheet->getStyle("L{$bodyStart}:U{$bodyEnd}")->getBorders()->getOutline()
            ->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB(self::COLOR_BORDER);

        return $this->renderLegendFooter($sheet, $bodyEnd + 1);
    }

    /**
     * Render TIR Runout Measurement Table + Datum note (Page 5).
     */
    private function renderTirTable(Worksheet $sheet, int $row, array $tir): int
    {
        $hdrRow = $row;
        $sheet->getRowDimension($hdrRow)->setRowHeight(16);

        $sheet->mergeCells("A{$hdrRow}:B{$hdrRow}");
        $sheet->setCellValue("A{$hdrRow}", 'Item');
        $sheet->mergeCells("C{$hdrRow}:G{$hdrRow}");
        $sheet->setCellValue("C{$hdrRow}", 'Spec');
        $sheet->mergeCells("H{$hdrRow}:I{$hdrRow}");
        $sheet->setCellValue("H{$hdrRow}", '0°');
        $sheet->mergeCells("J{$hdrRow}:M{$hdrRow}");
        $sheet->setCellValue("J{$hdrRow}", '90°');
        $sheet->mergeCells("N{$hdrRow}:P{$hdrRow}");
        $sheet->setCellValue("N{$hdrRow}", '180°');
        $sheet->mergeCells("Q{$hdrRow}:T{$hdrRow}");
        $sheet->setCellValue("Q{$hdrRow}", '270°');
        $sheet->setCellValue("U{$hdrRow}", 'A/R');

        $this->applyAllBorders($sheet, "A{$hdrRow}:U{$hdrRow}");
        $sheet->getStyle("A{$hdrRow}:U{$hdrRow}")->applyFromArray($this->tableHeaderStyle());

        $tirItems = $tir['items'] ?? [];
        $letters = ['A', 'B', 'C', 'D', 'E', 'F'];
        $startData = $hdrRow + 1;

        for ($idx = 0; $idx < 6; $idx++) {
            $r = $startData + $idx;
            $sheet->getRowDimension($r)->setRowHeight(15);
            $itemData = $tirItems[$idx] ?? [];
            $label = $itemData['item'] ?? $letters[$idx];
            $ar = $itemData['ar'] ?? 'A';

            $sheet->mergeCells("A{$r}:B{$r}");
            $sheet->setCellValue("A{$r}", $label);
            $sheet->mergeCells("C{$r}:G{$r}");
            $sheet->mergeCells("H{$r}:I{$r}");
            $sheet->setCellValueExplicit("H{$r}", $this->valOrDash($itemData['d0'] ?? null), DataType::TYPE_STRING);
            $sheet->mergeCells("J{$r}:M{$r}");
            $sheet->setCellValueExplicit("J{$r}", $this->valOrDash($itemData['d90'] ?? null), DataType::TYPE_STRING);
            $sheet->mergeCells("N{$r}:P{$r}");
            $sheet->setCellValueExplicit("N{$r}", $this->valOrDash($itemData['d180'] ?? null), DataType::TYPE_STRING);
            $sheet->mergeCells("Q{$r}:T{$r}");
            $sheet->setCellValueExplicit("Q{$r}", $this->valOrDash($itemData['d270'] ?? null), DataType::TYPE_STRING);
            $sheet->setCellValueExplicit("U{$r}", $this->valOrDash($ar, 'A'), DataType::TYPE_STRING);

            $this->applyAllBorders($sheet, "A{$r}:U{$r}");
            $sheet->getStyle("A{$r}:U{$r}")->applyFromArray([
                'font' => ['size' => 7.5],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getStyle("A{$r}:B{$r}")->getFont()->setBold(true);
            $this->styleArCell($sheet, "U{$r}", $ar);
        }

        // Merge Spec column C..G across all 6 rows
        $endData = $startData + 5;
        $sheet->mergeCells("C{$startData}:G{$endData}");
        $sheet->setCellValue("C{$startData}", $tir['spec'] ?? 'Max 0,2');
        $sheet->getStyle("C{$startData}:G{$endData}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 7.5, 'color' => ['rgb' => self::COLOR_DARK_BLUE]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_LIGHT_BLUE_BG]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // Datum row
        $datumRow = $endData + 1;
        $sheet->getRowDimension($datumRow)->setRowHeight(16);
        $sheet->mergeCells("A{$datumRow}:U{$datumRow}");
        $sheet->setCellValue("A{$datumRow}", '  > ' . ($tir['datum'] ?? 'Datum at item "B and E"'));
        $sheet->getStyle("A{$datumRow}:U{$datumRow}")->applyFromArray([
            'font' => ['size' => 7.5, 'color' => ['rgb' => self::COLOR_SLATE_TEXT]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::COLOR_BORDER]]],
        ]);

        return $datumRow + 1;
    }

    /**
     * Render Dynamic Recommendations Table (Page 6) with nested sub-points & vertical merge on Item.
     */
    private function renderRecommendationsTable(Worksheet $sheet, int $row, array $recs): int
    {
        $hdrRow = $row;
        $sheet->getRowDimension($hdrRow)->setRowHeight(18);
        $sheet->setCellValue("A{$hdrRow}", 'Item');
        $sheet->mergeCells("B{$hdrRow}:K{$hdrRow}");
        $sheet->setCellValue("B{$hdrRow}", 'Remarks');
        $sheet->mergeCells("L{$hdrRow}:U{$hdrRow}");
        $sheet->setCellValue("L{$hdrRow}", 'Recomendation');

        $sheet->getStyle("A{$hdrRow}:U{$hdrRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_ROYAL_BLUE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::COLOR_ROYAL_BORDER]]],
        ]);

        // Group items & split multi-line text identically to print.blade.php
        $groupedRecs = [];
        foreach ($recs as $idx => $r) {
            $itemNum = trim((string) ($r['item'] ?? sprintf('%02d', $idx + 1)));
            $rem = trim((string) ($r['remarks'] ?? ''));
            $rec = trim((string) ($r['recommendation'] ?? ($r['rec'] ?? '')));

            if ($rem === '' && $rec === '' && count($recs) > 1) {
                continue;
            }

            $remLines = array_values(array_filter(array_map('trim', explode("\n", $rem)), fn ($s) => $s !== ''));
            $recLines = array_values(array_filter(array_map('trim', explode("\n", $rec)), fn ($s) => $s !== ''));
            $maxLines = max(count($remLines), count($recLines), 1);

            if (!isset($groupedRecs[$itemNum])) {
                $groupedRecs[$itemNum] = [];
            }

            for ($i = 0; $i < $maxLines; $i++) {
                $groupedRecs[$itemNum][] = [
                    'remarks' => $remLines[$i] ?? ($maxLines === 1 ? $rem : '-'),
                    'rec' => $recLines[$i] ?? ($maxLines === 1 ? $rec : '-'),
                ];
            }
        }

        if (empty($groupedRecs)) {
            $groupedRecs['01'] = [['remarks' => '-', 'rec' => '-']];
        }

        $currRow = $hdrRow + 1;
        $zebraIndex = 0;

        foreach ($groupedRecs as $itemNum => $points) {
            $groupStart = $currRow;
            $rowCount = count($points);

            foreach ($points as $pt) {
                $remText = $pt['remarks'] !== '' ? $pt['remarks'] : '-';
                $recText = $pt['rec'] !== '' ? $pt['rec'] : '-';

                $sheet->mergeCells("B{$currRow}:K{$currRow}");
                $sheet->setCellValueExplicit("B{$currRow}", $remText, DataType::TYPE_STRING);
                $sheet->mergeCells("L{$currRow}:U{$currRow}");
                $sheet->setCellValueExplicit("L{$currRow}", $recText, DataType::TYPE_STRING);

                $linesEst = max((int) ceil(strlen($remText) / 48), (int) ceil(strlen($recText) / 52), 1);
                $sheet->getRowDimension($currRow)->setRowHeight(max(18, $linesEst * 13 + 5));

                $bg = ($zebraIndex % 2 === 1) ? self::COLOR_SLATE_BG : 'FFFFFF';
                $this->applyAllBorders($sheet, "A{$currRow}:U{$currRow}");
                $sheet->getStyle("B{$currRow}:U{$currRow}")->applyFromArray([
                    'font' => ['size' => 7.5],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_TOP, 'wrapText' => true],
                ]);

                $currRow++;
                $zebraIndex++;
            }

            $groupEnd = $groupStart + $rowCount - 1;
            if ($groupEnd > $groupStart) {
                $sheet->mergeCells("A{$groupStart}:A{$groupEnd}");
            }
            $sheet->setCellValueExplicit("A{$groupStart}", (string) $itemNum, DataType::TYPE_STRING);
            $sheet->getStyle("A{$groupStart}:A{$groupEnd}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 8],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
        }

        return $currRow;
    }

    /**
     * Render Signatures Block (Checked by | Created by | Reviewed by) on Page 6.
     */
    private function renderSignaturesBlock(Worksheet $sheet, int $row, array $sigs): int
    {
        $hdrRow = $row;
        $spaceRow = $row + 1;
        $nameRow = $row + 2;
        $metaRow = $row + 3;

        $sheet->getRowDimension($hdrRow)->setRowHeight(18);
        $sheet->getRowDimension($spaceRow)->setRowHeight(42);
        $sheet->getRowDimension($nameRow)->setRowHeight(17);
        $sheet->getRowDimension($metaRow)->setRowHeight(16);

        $blocks = [
            ['cols' => ['A', 'D', 'E', 'G'], 'label' => 'Checked by:', 'data' => $sigs['checkedBy'] ?? [], 'defTitle' => 'QC Inspector'],
            ['cols' => ['H', 'K', 'L', 'N'], 'label' => 'Created by:', 'data' => $sigs['createdBy'] ?? [], 'defTitle' => 'QC Inspector'],
            ['cols' => ['O', 'R', 'S', 'U'], 'label' => 'Reviewed by:', 'data' => $sigs['reviewedBy'] ?? [], 'defTitle' => 'Leader QC'],
        ];

        foreach ($blocks as $blk) {
            [$cStart, $cMidLeft, $cMidRight, $cEnd] = $blk['cols'];
            $d = $blk['data'];

            // Header
            $sheet->mergeCells("{$cStart}{$hdrRow}:{$cEnd}{$hdrRow}");
            $sheet->setCellValue("{$cStart}{$hdrRow}", ' ' . $blk['label']);
            $sheet->getStyle("{$cStart}{$hdrRow}:{$cEnd}{$hdrRow}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 8, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_ROYAL_BLUE]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::COLOR_ROYAL_BORDER]]],
            ]);

            // Signature blank space
            $sheet->mergeCells("{$cStart}{$spaceRow}:{$cEnd}{$spaceRow}");

            // Personnel name
            $name = !empty($d['name']) ? trim((string) $d['name']) : '............................';
            $sheet->mergeCells("{$cStart}{$nameRow}:{$cEnd}{$nameRow}");
            $sheet->setCellValueExplicit("{$cStart}{$nameRow}", $name, DataType::TYPE_STRING);
            $sheet->getStyle("{$cStart}{$nameRow}:{$cEnd}{$nameRow}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 8, 'color' => ['rgb' => self::COLOR_DARK_BLUE]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 1],
                'borders' => ['bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::COLOR_MUTED_TEXT]]],
            ]);

            // Meta (Title left, Date right)
            $title = !empty($d['title']) ? trim((string) $d['title']) : $blk['defTitle'];
            $dateRaw = $d['date'] ?? null;
            $dateFmt = !empty($dateRaw) ? Carbon::parse($dateRaw)->format('F d, Y') : date('F d, Y');

            $sheet->mergeCells("{$cStart}{$metaRow}:{$cMidLeft}{$metaRow}");
            $sheet->setCellValueExplicit("{$cStart}{$metaRow}", $title, DataType::TYPE_STRING);
            $sheet->getStyle("{$cStart}{$metaRow}:{$cMidLeft}{$metaRow}")->applyFromArray([
                'font' => ['size' => 7.2, 'color' => ['rgb' => self::COLOR_SLATE_TEXT]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 1],
            ]);

            $sheet->mergeCells("{$cMidRight}{$metaRow}:{$cEnd}{$metaRow}");
            $sheet->setCellValueExplicit("{$cMidRight}{$metaRow}", 'Date: ' . $dateFmt, DataType::TYPE_STRING);
            $sheet->getStyle("{$cMidRight}{$metaRow}:{$cEnd}{$metaRow}")->applyFromArray([
                'font' => ['size' => 7.2, 'color' => ['rgb' => self::COLOR_SLATE_TEXT]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            // Outer box border for signature block
            $sheet->getStyle("{$cStart}{$hdrRow}:{$cEnd}{$metaRow}")->getBorders()->getOutline()
                ->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB(self::COLOR_BORDER);
        }

        return $metaRow + 1;
    }

    // =========================================================================
    // SHARED SECTION BUILDER HELPERS
    // =========================================================================

    private function initSplitBodyRows(Worksheet $sheet, int $startRow): void
    {
        for ($i = 0; $i < 10; $i++) {
            $sheet->getRowDimension($startRow + $i)->setRowHeight(16.0);
        }
    }

    private function renderSplitColumnHeaders(Worksheet $sheet, int $row, string $leftText, string $rightText): int
    {
        $sheet->getRowDimension($row)->setRowHeight(16);
        $sheet->mergeCells("A{$row}:K{$row}");
        $sheet->setCellValue("A{$row}", $leftText);
        $sheet->mergeCells("L{$row}:U{$row}");
        $sheet->setCellValue("L{$row}", $rightText);

        $this->applyAllBorders($sheet, "A{$row}:U{$row}");
        $sheet->getStyle("A{$row}:U{$row}")->applyFromArray($this->tableHeaderStyle());

        return $row + 1;
    }

    private function renderPhotoGrid2x2(
        Worksheet $sheet,
        int $startRow,
        array $slotKeys,
        array $labels,
        array $photos,
        int $rowsPerQuad = 5
    ): void {
        $quads = [
            ['A', 'F', $startRow, $startRow + $rowsPerQuad - 1, $slotKeys[0], $labels[0]],
            ['G', 'K', $startRow, $startRow + $rowsPerQuad - 1, $slotKeys[1], $labels[1]],
            ['A', 'F', $startRow + $rowsPerQuad, $startRow + ($rowsPerQuad * 2) - 1, $slotKeys[2], $labels[2]],
            ['G', 'K', $startRow + $rowsPerQuad, $startRow + ($rowsPerQuad * 2) - 1, $slotKeys[3], $labels[3]],
        ];

        foreach ($quads as [$c1, $c2, $r1, $r2, $key, $lbl]) {
            $sheet->mergeCells("{$c1}{$r1}:{$c2}{$r2}");
            $localPath = $this->resolveLocalPhotoPath($photos[$key] ?? null);
            if ($localPath) {
                $this->placeImageInBox($sheet, $localPath, $c1, $c2, $r1, $r2, 420, 3);
            } else {
                $sheet->setCellValue("{$c1}{$r1}", $lbl);
                $sheet->getStyle("{$c1}{$r1}:{$c2}{$r2}")->applyFromArray([
                    'font' => ['italic' => true, 'size' => 7.5, 'color' => ['rgb' => self::COLOR_MUTED_TEXT]],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_SLATE_BG]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
            }
            $sheet->getStyle("{$c1}{$r1}:{$c2}{$r2}")->getBorders()->getOutline()
                ->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB(self::COLOR_BORDER);
        }
    }

    private function renderSubTitle(Worksheet $sheet, int $row, string $text): int
    {
        $sheet->mergeCells("L{$row}:U{$row}");
        $sheet->setCellValue("L{$row}", $text);
        $this->applyAllBorders($sheet, "L{$row}:U{$row}");
        $sheet->getStyle("L{$row}:U{$row}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 7.5, 'color' => ['rgb' => self::COLOR_SLATE_TEXT]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_SLATE_BG]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        return $row + 1;
    }

    private function renderMeasurementHeaders(Worksheet $sheet, int $row): int
    {
        $sheet->setCellValue("L{$row}", 'Item');
        $sheet->mergeCells("M{$row}:O{$row}");
        $sheet->setCellValue("M{$row}", 'Spec');
        $sheet->setCellValue("P{$row}", 'X');
        $sheet->mergeCells("Q{$row}:S{$row}");
        $sheet->setCellValue("Q{$row}", 'Y');
        $sheet->setCellValue("T{$row}", 'Z');
        $sheet->setCellValue("U{$row}", 'A/R');

        $this->applyAllBorders($sheet, "L{$row}:U{$row}");
        $sheet->getStyle("L{$row}:U{$row}")->applyFromArray($this->tableHeaderStyle());
        return $row + 1;
    }

    private function renderMeasurementRow(Worksheet $sheet, int $row, string $item, string $spec, $x, $y, $z, $ar): int
    {
        $sheet->setCellValue("L{$row}", $item);
        $sheet->mergeCells("M{$row}:O{$row}");
        $sheet->setCellValue("M{$row}", $spec);
        $sheet->setCellValueExplicit("P{$row}", $this->valOrDash($x), DataType::TYPE_STRING);
        $sheet->mergeCells("Q{$row}:S{$row}");
        $sheet->setCellValueExplicit("Q{$row}", $this->valOrDash($y), DataType::TYPE_STRING);
        $sheet->setCellValueExplicit("T{$row}", $this->valOrDash($z), DataType::TYPE_STRING);
        $sheet->setCellValueExplicit("U{$row}", $this->valOrDash($ar, 'A'), DataType::TYPE_STRING);

        $this->applyAllBorders($sheet, "L{$row}:U{$row}");
        $sheet->getStyle("L{$row}:U{$row}")->applyFromArray([
            'font' => ['size' => 7.5],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle("L{$row}")->getFont()->setBold(true);
        $this->styleArCell($sheet, "U{$row}", $ar);
        return $row + 1;
    }

    private function renderMultiPointGroup(Worksheet $sheet, int $startRow, string $item, string $spec, array $rowsData): int
    {
        $endRow = $startRow + count($rowsData) - 1;
        foreach ($rowsData as $idx => $rd) {
            $r = $startRow + $idx;
            $ar = $rd['ar'] ?? 'A';
            $sheet->setCellValueExplicit("P{$r}", $this->valOrDash($rd['x'] ?? null), DataType::TYPE_STRING);
            $sheet->mergeCells("Q{$r}:S{$r}");
            $sheet->setCellValueExplicit("Q{$r}", $this->valOrDash($rd['y'] ?? null), DataType::TYPE_STRING);
            $sheet->setCellValueExplicit("T{$r}", $this->valOrDash($rd['z'] ?? null), DataType::TYPE_STRING);
            $sheet->setCellValueExplicit("U{$r}", $this->valOrDash($ar, 'A'), DataType::TYPE_STRING);

            $this->applyAllBorders($sheet, "L{$r}:U{$r}");
            $sheet->getStyle("L{$r}:U{$r}")->applyFromArray([
                'font' => ['size' => 7.5],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $this->styleArCell($sheet, "U{$r}", $ar);
        }

        $sheet->mergeCells("L{$startRow}:L{$endRow}");
        $sheet->setCellValue("L{$startRow}", $item);
        $sheet->getStyle("L{$startRow}:L{$endRow}")->getFont()->setBold(true);

        $sheet->mergeCells("M{$startRow}:O{$endRow}");
        $sheet->setCellValue("M{$startRow}", $spec);

        return $endRow + 1;
    }

    private function renderSingleResultHeaders(Worksheet $sheet, int $row): int
    {
        $sheet->setCellValue("L{$row}", 'Item');
        $sheet->mergeCells("M{$row}:O{$row}");
        $sheet->setCellValue("M{$row}", 'Spec');
        $sheet->mergeCells("P{$row}:T{$row}");
        $sheet->setCellValue("P{$row}", 'Result of Measurement');
        $sheet->setCellValue("U{$row}", 'A/R');

        $this->applyAllBorders($sheet, "L{$row}:U{$row}");
        $sheet->getStyle("L{$row}:U{$row}")->applyFromArray($this->tableHeaderStyle());
        return $row + 1;
    }

    private function renderSingleResultRow(Worksheet $sheet, int $row, string $item, string $spec, $val, $ar): int
    {
        $sheet->setCellValue("L{$row}", $item);
        $sheet->mergeCells("M{$row}:O{$row}");
        $sheet->setCellValue("M{$row}", $spec);
        $sheet->mergeCells("P{$row}:T{$row}");
        $sheet->setCellValueExplicit("P{$row}", $this->valOrDash($val), DataType::TYPE_STRING);
        $sheet->setCellValueExplicit("U{$row}", $this->valOrDash($ar, '-'), DataType::TYPE_STRING);

        $this->applyAllBorders($sheet, "L{$row}:U{$row}");
        $sheet->getStyle("L{$row}:U{$row}")->applyFromArray([
            'font' => ['size' => 7.5],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle("L{$row}")->getFont()->setBold(true);
        if ($ar === 'A' || $ar === 'R' || $ar === 'Accept' || $ar === 'Reject') {
            $this->styleArCell($sheet, "U{$row}", $ar);
        }
        return $row + 1;
    }

    private function renderBulletBox(Worksheet $sheet, int $startRow, int $endRow, array $bullets): void
    {
        $sheet->mergeCells("L{$startRow}:U{$endRow}");
        $lines = array_map(fn ($b) => '> ' . trim((string) $b), array_filter($bullets, fn ($b) => trim((string) $b) !== ''));
        $sheet->setCellValue("L{$startRow}", implode("\n", $lines));
        $sheet->getStyle("L{$startRow}:U{$endRow}")->applyFromArray([
            'font' => ['size' => 7, 'color' => ['rgb' => self::COLOR_SLATE_TEXT]],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_TOP,
                'wrapText' => true,
                'indent' => 1,
            ],
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::COLOR_BORDER]]],
        ]);
    }

    private function renderLegendFooter(Worksheet $sheet, int $row): int
    {
        $sheet->getRowDimension($row)->setRowHeight(15);
        $sheet->mergeCells("A{$row}:Q{$row}");
        $sheet->mergeCells("R{$row}:S{$row}");
        $sheet->setCellValue("R{$row}", 'A : Accept');
        $sheet->mergeCells("T{$row}:U{$row}");
        $sheet->setCellValue("T{$row}", 'R : Reject');

        $sheet->getStyle("A{$row}:U{$row}")->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_SLATE_BG]],
            'borders' => [
                'top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::COLOR_BORDER]],
                'bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::COLOR_BORDER]],
                'left' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::COLOR_BORDER]],
                'right' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::COLOR_BORDER]],
            ],
        ]);
        $sheet->getStyle("R{$row}:S{$row}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 7.2, 'color' => ['rgb' => self::COLOR_ACCEPT_TEXT]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle("T{$row}:U{$row}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 7.2, 'color' => ['rgb' => self::COLOR_REJECT_TEXT]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        return $row + 1;
    }

    // =========================================================================
    // IMAGE RESOLUTION, NORMALIZATION & CENTERING IN MERGED BOXES
    // =========================================================================

    private function resolveLocalPhotoPath(?string $rawUrl): ?string
    {
        if (empty($rawUrl)) {
            return null;
        }
        $path = trim($rawUrl);
        if (preg_match('/^https?:\/\//i', $path)) {
            $parsed = parse_url($path, PHP_URL_PATH);
            $path = $parsed ?: $path;
        }
        $path = ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path), DIRECTORY_SEPARATOR);
        $fullPath = public_path($path);
        return file_exists($fullPath) && is_file($fullPath) ? $fullPath : null;
    }

    /**
     * Load photo via GD, fix EXIF orientation, downscale if large, and save normalized PNG/JPG.
     */
    private function prepareExcelImage(string $srcPath, int $maxPx): ?array
    {
        $info = @getimagesize($srcPath);
        if (!$info) {
            return null;
        }
        [$w, $h, $type] = $info;
        $img = match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($srcPath),
            IMAGETYPE_PNG => @imagecreatefrompng($srcPath),
            IMAGETYPE_GIF => @imagecreatefromgif($srcPath),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($srcPath) : null,
            default => null,
        };
        if (!$img) {
            return null;
        }

        // Fix mobile camera EXIF orientation for JPEG
        if ($type === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
            $exif = @exif_read_data($srcPath);
            $ort = $exif['Orientation'] ?? 1;
            $rotated = match ($ort) {
                3 => imagerotate($img, 180, 0),
                6 => imagerotate($img, -90, 0),
                8 => imagerotate($img, 90, 0),
                default => null,
            };
            if ($rotated) {
                imagedestroy($img);
                $img = $rotated;
                $w = imagesx($img);
                $h = imagesy($img);
            }
        }

        // Resize proportionally if larger than maxPx
        if ($w > $maxPx || $h > $maxPx) {
            $scale = min($maxPx / $w, $maxPx / $h);
            $nw = max(1, (int) round($w * $scale));
            $nh = max(1, (int) round($h * $scale));
            $dst = imagecreatetruecolor($nw, $nh);
            if ($type === IMAGETYPE_PNG) {
                imagealphablending($dst, false);
                imagesavealpha($dst, true);
                $trans = imagecolorallocatealpha($dst, 255, 255, 255, 127);
                imagefilledrectangle($dst, 0, 0, $nw, $nh, $trans);
            } else {
                $white = imagecolorallocate($dst, 255, 255, 255);
                imagefilledrectangle($dst, 0, 0, $nw, $nh, $white);
            }
            imagecopyresampled($dst, $img, 0, 0, 0, 0, $nw, $nh, $w, $h);
            imagedestroy($img);
            $img = $dst;
            $w = $nw;
            $h = $nh;
        }

        $tempPath = storage_path('app/temp') . DIRECTORY_SEPARATOR . 'img_' . uniqid() . ($type === IMAGETYPE_PNG ? '.png' : '.jpg');
        if ($type === IMAGETYPE_PNG) {
            imagepng($img, $tempPath, 6);
        } else {
            imagejpeg($img, $tempPath, 85);
        }
        imagedestroy($img);

        $this->tempImages[] = $tempPath;
        return ['path' => $tempPath, 'width' => $w, 'height' => $h];
    }

    /**
     * Place image inside merged cell box [colStart..colEnd, rowStart..rowEnd], keeping aspect ratio.
     */
    private function placeImageInBox(
        Worksheet $sheet,
        string $srcPath,
        string $colStart,
        string $colEnd,
        int $rowStart,
        int $rowEnd,
        int $maxPx = 500,
        int $padding = 4,
        bool $alignLeft = false
    ): void {
        $prep = $this->prepareExcelImage($srcPath, $maxPx);
        if (!$prep) {
            return;
        }

        $defaultFont = $sheet->getParent()->getDefaultStyle()->getFont();
        $c1 = Coordinate::columnIndexFromString($colStart);
        $c2 = Coordinate::columnIndexFromString($colEnd);

        $colPx = [];
        $boxW = 0;
        for ($c = $c1; $c <= $c2; $c++) {
            $colStr = Coordinate::stringFromColumnIndex($c);
            $wUnits = $sheet->getColumnDimension($colStr)->getWidth();
            $px = SharedDrawing::cellDimensionToPixels($wUnits, $defaultFont);
            $colPx[$c] = $px;
            $boxW += $px;
        }

        $rowPx = [];
        $boxH = 0;
        for ($r = $rowStart; $r <= $rowEnd; $r++) {
            $rhPt = $sheet->getRowDimension($r)->getRowHeight();
            if ($rhPt <= 0) {
                $rhPt = 16.0;
            }
            $px = SharedDrawing::pointsToPixels($rhPt);
            $rowPx[$r] = $px;
            $boxH += $px;
        }

        $availW = max(10, $boxW - ($padding * 2));
        $availH = max(10, $boxH - ($padding * 2));
        $ratio = min($availW / $prep['width'], $availH / $prep['height']);
        $renderW = max(1, (int) round($prep['width'] * $ratio));
        $renderH = max(1, (int) round($prep['height'] * $ratio));

        $offsetX = $alignLeft ? $padding : (int) round(($boxW - $renderW) / 2);
        $offsetY = (int) round(($boxH - $renderH) / 2);

        $anchorCol = $colStart;
        $remX = max(0, $offsetX);
        for ($c = $c1; $c <= $c2; $c++) {
            if ($remX < $colPx[$c] || $c === $c2) {
                $anchorCol = Coordinate::stringFromColumnIndex($c);
                break;
            }
            $remX -= $colPx[$c];
        }

        $anchorRow = $rowStart;
        $remY = max(0, $offsetY);
        for ($r = $rowStart; $r <= $rowEnd; $r++) {
            if ($remY < $rowPx[$r] || $r === $rowEnd) {
                $anchorRow = $r;
                break;
            }
            $remY -= $rowPx[$r];
        }

        $drawing = new Drawing();
        $drawing->setPath($prep['path']);
        $drawing->setCoordinates("{$anchorCol}{$anchorRow}");
        $drawing->setOffsetX((int) round($remX));
        $drawing->setOffsetY((int) round($remY));
        $drawing->setWidthAndHeight($renderW, $renderH);
        $drawing->setWorksheet($sheet);
    }

    // =========================================================================
    // FORMATTING & UTILITY HELPERS
    // =========================================================================

    private function valOrDash($val, string $fallback = '-'): string
    {
        if ($val === null || $val === '') {
            return $fallback;
        }
        return trim((string) $val);
    }

    private function extractRemarks($raw, array $fallback): array
    {
        if (!is_array($raw) || empty($raw)) {
            return $fallback;
        }
        $clean = array_values(array_filter(array_map('trim', $raw), fn ($s) => $s !== ''));
        return !empty($clean) ? $clean : $fallback;
    }

    private function styleArCell(Worksheet $sheet, string $cell, $ar): void
    {
        $isReject = ($ar === 'R' || $ar === 'Reject');
        $sheet->getStyle($cell)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 7.5,
                'color' => ['rgb' => $isReject ? self::COLOR_REJECT_TEXT : self::COLOR_ACCEPT_TEXT],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => $isReject ? self::COLOR_REJECT_BG : self::COLOR_ACCEPT_BG],
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
    }

    private function applyAllBorders(Worksheet $sheet, string $range): void
    {
        $sheet->getStyle($range)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB(self::COLOR_BORDER);
    }

    private function ctrlLabelStyle(): array
    {
        return [
            'font' => ['bold' => true, 'size' => 7, 'color' => ['rgb' => self::COLOR_SLATE_TEXT]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_SLATE_BG]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ];
    }

    private function metaLabelStyle(): array
    {
        return [
            'font' => ['bold' => true, 'size' => 7.5, 'color' => ['rgb' => self::COLOR_DARK_BLUE]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_LIGHT_BLUE_BG]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ];
    }

    private function tableHeaderStyle(): array
    {
        return [
            'font' => ['bold' => true, 'size' => 7.5, 'color' => ['rgb' => self::COLOR_DARK_BLUE]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_LIGHT_BLUE_BG]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ];
    }
}
