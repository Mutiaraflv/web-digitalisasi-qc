<?php

class CleanVectorPptx {
    private ZipArchive $zip;
    private string $filePath;
    private array $slides = [];

    public function __construct(string $filePath) {
        $this->filePath = $filePath;
        $this->zip = new ZipArchive();
        if ($this->zip->open($this->filePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new Exception("Cannot create file at $filePath");
        }
    }

    public function addSlide(string $xmlContent) {
        $this->slides[] = $xmlContent;
    }

    public function save() {
        $slideCount = count($this->slides);

        // 1. [Content_Types].xml
        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $contentTypes .= '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">' . "\n";
        $contentTypes .= '  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>' . "\n";
        $contentTypes .= '  <Default Extension="xml" ContentType="application/xml"/>' . "\n";
        $contentTypes .= '  <Override PartName="/ppt/presentation.xml" ContentType="application/vnd.openxmlformats-officedocument.presentationml.presentation.main+xml"/>' . "\n";
        $contentTypes .= '  <Override PartName="/ppt/slideMasters/slideMaster1.xml" ContentType="application/vnd.openxmlformats-officedocument.presentationml.slideMaster+xml"/>' . "\n";
        $contentTypes .= '  <Override PartName="/ppt/slideLayouts/slideLayout1.xml" ContentType="application/vnd.openxmlformats-officedocument.presentationml.slideLayout+xml"/>' . "\n";
        $contentTypes .= '  <Override PartName="/ppt/theme/theme1.xml" ContentType="application/vnd.openxmlformats-officedocument.theme+xml"/>' . "\n";

        for ($i = 1; $i <= $slideCount; $i++) {
            $contentTypes .= "  <Override PartName=\"/ppt/slides/slide{$i}.xml\" ContentType=\"application/vnd.openxmlformats-officedocument.presentationml.slide+xml\"/>\n";
        }
        $contentTypes .= '</Types>';
        $this->zip->addFromString('[Content_Types].xml', $contentTypes);

        // 2. _rels/.rels
        $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="ppt/presentation.xml"/>
</Relationships>';
        $this->zip->addFromString('_rels/.rels', $rels);

        // 3. ppt/presentation.xml & ppt/_rels/presentation.xml.rels
        $presRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $presRels .= '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' . "\n";
        $presRels .= '  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/slideMaster" Target="slideMasters/slideMaster1.xml"/>' . "\n";

        $presXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $presXml .= '<p:presentation xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" xmlns:p="http://schemas.openxmlformats.org/presentationml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">' . "\n";
        $presXml .= '  <p:sldMasterIdLst><p:sldMasterId id="2147483648" r:id="rId1"/></p:sldMasterIdLst>' . "\n";
        $presXml .= '  <p:sldIdLst>' . "\n";

        for ($i = 1; $i <= $slideCount; $i++) {
            $relId = "rId" . ($i + 1);
            $presRels .= "  <Relationship Id=\"{$relId}\" Type=\"http://schemas.openxmlformats.org/officeDocument/2006/relationships/slide\" Target=\"slides/slide{$i}.xml\"/>\n";
            $slideId = 255 + $i;
            $presXml .= "    <p:sldId id=\"{$slideId}\" r:id=\"{$relId}\"/>\n";
        }
        $presRels .= '</Relationships>';
        $presXml .= '  </p:sldIdLst>' . "\n";
        $presXml .= '  <p:sldSz cx="12192000" cy="6858000"/>' . "\n";
        $presXml .= '  <p:notesSz cx="6858000" cy="9144000"/>' . "\n";
        $presXml .= '</p:presentation>';

        $this->zip->addFromString('ppt/_rels/presentation.xml.rels', $presRels);
        $this->zip->addFromString('ppt/presentation.xml', $presXml);

        // 4. ppt/theme/theme1.xml
        $theme = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<a:theme xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" name="Office Theme">
  <a:themeElements>
    <a:clrScheme name="Clean">
      <a:dk1><a:srgbClr val="0F172A"/></a:dk1><a:lt1><a:srgbClr val="FFFFFF"/></a:lt1>
      <a:dk2><a:srgbClr val="1E293B"/></a:dk2><a:lt2><a:srgbClr val="F8FAFC"/></a:lt2>
      <a:accent1><a:srgbClr val="1312B6"/></a:accent1><a:accent2><a:srgbClr val="0284C7"/></a:accent2>
      <a:accent3><a:srgbClr val="15803D"/></a:accent3><a:accent4><a:srgbClr val="D97706"/></a:accent4>
      <a:accent5><a:srgbClr val="64748B"/></a:accent5><a:accent6><a:srgbClr val="4F46E5"/></a:accent6>
      <a:hlink><a:srgbClr val="1312B6"/></a:hlink><a:folHlink><a:srgbClr val="0284C7"/></a:folHlink>
    </a:clrScheme>
    <a:fontScheme name="Modern">
      <a:majorFont><a:latin typeface="Segoe UI Semibold"/></a:majorFont>
      <a:minorFont><a:latin typeface="Segoe UI"/></a:minorFont>
    </a:fontScheme>
    <a:fmtScheme name="Clean">
      <a:fillStyleLst><a:solidFill><a:srgbClr val="FFFFFF"/></a:solidFill></a:fillStyleLst>
      <a:lnStyleLst><a:ln><a:solidFill><a:srgbClr val="E2E8F0"/></a:solidFill></a:ln></a:lnStyleLst>
      <a:effectStyleLst><a:effectStyle><a:effectLst/></a:effectStyle></a:effectStyleLst>
      <a:bgFillStyleLst><a:solidFill><a:srgbClr val="F8FAFC"/></a:solidFill></a:bgFillStyleLst>
    </a:fmtScheme>
  </a:themeElements>
</a:theme>';
        $this->zip->addFromString('ppt/theme/theme1.xml', $theme);

        // 5. Slide Master & Layout
        $slideMaster = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<p:sldMaster xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" xmlns:p="http://schemas.openxmlformats.org/presentationml/2006/main">
  <p:cSld><p:spTree><p:nvGrpSpPr><p:cNvPr id="1" name=""/><p:cNvGrpSpPr/><p:nvPr/></p:nvGrpSpPr><p:grpSpPr><a:xfrm><a:off x="0" y="0"/><a:ext cx="0" cy="0"/><a:chOff x="0" y="0"/><a:chExt cx="0" cy="0"/></a:xfrm></p:grpSpPr></p:spTree></p:cSld>
  <p:clrMap bg1="lt1" tx1="dk1" bg2="lt2" tx2="dk2" accent1="accent1" accent2="accent2" accent3="accent3" accent4="accent4" accent5="accent5" accent6="accent6" hlink="hlink" folHlink="folHlink"/>
  <p:sldLayoutIdLst><p:sldLayoutId id="2147483649" r:id="rId1" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"/></p:sldLayoutIdLst>
</p:sldMaster>';
        $this->zip->addFromString('ppt/slideMasters/slideMaster1.xml', $slideMaster);

        $masterRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/slideLayout" Target="../slideLayouts/slideLayout1.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/theme" Target="../theme/theme1.xml"/>
</Relationships>';
        $this->zip->addFromString('ppt/slideMasters/_rels/slideMaster1.xml.rels', $masterRels);

        $slideLayout = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<p:sldLayout xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" xmlns:p="http://schemas.openxmlformats.org/presentationml/2006/main" type="blank">
  <p:cSld><p:spTree><p:nvGrpSpPr><p:cNvPr id="1" name=""/><p:cNvGrpSpPr/><p:nvPr/></p:nvGrpSpPr><p:grpSpPr><a:xfrm><a:off x="0" y="0"/><a:ext cx="0" cy="0"/><a:chOff x="0" y="0"/><a:chExt cx="0" cy="0"/></a:xfrm></p:grpSpPr></p:spTree></p:cSld>
</p:sldLayout>';
        $this->zip->addFromString('ppt/slideLayouts/slideLayout1.xml', $slideLayout);

        $layoutRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/slideMaster" Target="../slideMasters/slideMaster1.xml"/>
</Relationships>';
        $this->zip->addFromString('ppt/slideLayouts/_rels/slideLayout1.xml.rels', $layoutRels);

        for ($i = 1; $i <= $slideCount; $i++) {
            $this->zip->addFromString("ppt/slides/slide{$i}.xml", $this->slides[$i - 1]);
            $slideRel = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/slideLayout" Target="../slideLayouts/slideLayout1.xml"/>
</Relationships>';
            $this->zip->addFromString("ppt/slides/_rels/slide{$i}.xml.rels", $slideRel);
        }

        $this->zip->close();
    }
}

class VectorSlide {
    private array $shapes = [];
    private int $shapeId = 2;
    private string $bgHex = 'FFFFFF';

    public function __construct(string $bgHex = 'FFFFFF') {
        $this->bgHex = $bgHex;
        $this->addBackground();
    }

    private function addBackground() {
        $xml = '<p:sp>
  <p:nvSpPr><p:cNvPr id="' . ($this->shapeId++) . '" name="Bg"/><p:cNvSpPr><a:spLocks noMove="1" noResize="1"/></p:cNvSpPr><p:nvPr/></p:nvSpPr>
  <p:spPr>
    <a:xfrm><a:off x="0" y="0"/><a:ext cx="12192000" cy="6858000"/></a:xfrm>
    <a:prstGeom prst="rect"><a:avLst/></a:prstGeom>
    <a:solidFill><a:srgbClr val="' . $this->bgHex . '"/></a:solidFill>
    <a:ln><a:noFill/></a:ln>
  </p:spPr>
</p:sp>';
        $this->shapes[] = $xml;
    }

    public function addCard(int $x, int $y, int $w, int $h, string $bgFill = 'F8FAFC', string $borderHex = 'E2E8F0', int $borderW = 12700, int $cornerRound = 2000) {
        $roundAdj = $cornerRound > 0 ? '<a:avLst><a:gd name="adj" fmla="val ' . $cornerRound . '"/></a:avLst>' : '<a:avLst/>';
        $geom = $cornerRound > 0 ? 'roundRect' : 'rect';
        $lnXml = $borderHex ? '<a:ln w="' . $borderW . '"><a:solidFill><a:srgbClr val="' . $borderHex . '"/></a:solidFill></a:ln>' : '<a:ln><a:noFill/></a:ln>';

        $xml = '<p:sp>
  <p:nvSpPr><p:cNvPr id="' . ($this->shapeId++) . '" name="Card"/><p:cNvSpPr/><p:nvPr/></p:nvSpPr>
  <p:spPr>
    <a:xfrm><a:off x="' . $x . '" y="' . $y . '"/><a:ext cx="' . $w . '" cy="' . $h . '"/></a:xfrm>
    <a:prstGeom prst="' . $geom . '">' . $roundAdj . '</a:prstGeom>
    <a:solidFill><a:srgbClr val="' . $bgFill . '"/></a:solidFill>
    ' . $lnXml . '
  </p:spPr>
</p:sp>';
        $this->shapes[] = $xml;
    }

    public function addArrow(int $x, int $y, int $w, int $h, string $color = '0284C7') {
        $xml = '<p:sp>
  <p:nvSpPr><p:cNvPr id="' . ($this->shapeId++) . '" name="Arrow"/><p:cNvSpPr/><p:nvPr/></p:nvSpPr>
  <p:spPr>
    <a:xfrm><a:off x="' . $x . '" y="' . $y . '"/><a:ext cx="' . $w . '" cy="' . $h . '"/></a:xfrm>
    <a:prstGeom prst="rightArrow"><a:avLst/></a:prstGeom>
    <a:solidFill><a:srgbClr val="' . $color . '"/></a:solidFill>
    <a:ln><a:noFill/></a:ln>
  </p:spPr>
</p:sp>';
        $this->shapes[] = $xml;
    }

    public function addTextBox(int $x, int $y, int $w, int $h, array $paragraphs, string $align = 'l') {
        $pXml = '';
        foreach ($paragraphs as $p) {
            $runs = isset($p['runs']) ? $p['runs'] : [$p];
            $pAlign = isset($p['align']) ? $p['align'] : $align;
            $bullet = !empty($p['bullet']) ? ' buChar="•"' : '';
            $spcAft = isset($p['spacingAfter']) ? '<a:spcAft><a:spcPts val="' . $p['spacingAfter'] . '"/></a:spcAft>' : '';

            $pXml .= '<a:p><a:pPr algn="' . $pAlign . '"' . $bullet . '>' . $spcAft . '</a:pPr>';
            foreach ($runs as $r) {
                $text = htmlspecialchars($r['text'] ?? '', ENT_XML1, 'UTF-8');
                $size = $r['size'] ?? 1400;
                $bold = !empty($r['bold']) ? ' b="1"' : '';
                $italic = !empty($r['italic']) ? ' i="1"' : '';
                $color = $r['color'] ?? '0F172A';

                $pXml .= '<a:r><a:rPr lang="en-US" sz="' . $size . '"' . $bold . $italic . '><a:solidFill><a:srgbClr val="' . $color . '"/></a:solidFill></a:rPr><a:t>' . $text . '</a:t></a:r>';
            }
            $pXml .= '</a:p>';
        }

        $xml = '<p:sp>
  <p:nvSpPr><p:cNvPr id="' . ($this->shapeId++) . '" name="Text"/><p:cNvSpPr><a:spLocks noGrp="1"/></p:cNvSpPr><p:nvPr/></p:nvSpPr>
  <p:spPr>
    <a:xfrm><a:off x="' . $x . '" y="' . $y . '"/><a:ext cx="' . $w . '" cy="' . $h . '"/></a:xfrm>
    <a:prstGeom prst="rect"><a:avLst/></a:prstGeom>
  </p:spPr>
  <p:txBody>
    <a:bodyPr vert="horz" lIns="20000" tIns="20000" rIns="20000" bIns="20000" rtlCol="0"/>
    <a:lstStyle/>
    ' . $pXml . '
  </p:txBody>
</p:sp>';
        $this->shapes[] = $xml;
    }

    public function toXml(): string {
        $spTree = implode("\n", $this->shapes);
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<p:sld xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" xmlns:p="http://schemas.openxmlformats.org/presentationml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <p:cSld>
    <p:spTree>
      <p:nvGrpSpPr><p:cNvPr id="1" name=""/><p:cNvGrpSpPr/><p:nvPr/></p:nvGrpSpPr>
      <p:grpSpPr><a:xfrm><a:off x="0" y="0"/><a:ext cx="0" cy="0"/><a:chOff x="0" y="0"/><a:chExt cx="0" cy="0"/></a:xfrm></p:grpSpPr>
      ' . $spTree . '
    </p:spTree>
  </p:cSld>
</p:sld>';
    }
}

// ------------------------------------------------------------
// BUILD 5 SLIDES - 100% CLEAN NATIVE VECTOR POWERPOINT
// ------------------------------------------------------------
$outputPath = __DIR__ . '/../public/presentasi/Presentasi_QCMS_PT_SSC_Works.pptx';
$rootOutputPath = __DIR__ . '/../Presentasi_QCMS_PT_SSC_Works.pptx';
$gen = new CleanVectorPptx($outputPath);

// ==========================================
// SLIDE 1: COVER
// ==========================================
$s1 = new VectorSlide('FFFFFF');

// Cobalt vertical accent on the left
$s1->addCard(0, 0, 240000, 6858000, '1312B6', '', 0, 0);

// Top Tag Pill
$s1->addCard(850000, 800000, 3600000, 420000, 'EEF2FF', 'C7D2FE', 12700, 1000);
$s1->addTextBox(850000, 850000, 3600000, 350000, [
    ['text' => 'SISTEM FINALISASI DOKUMEN • PT SSC WORKS', 'size' => 1100, 'bold' => true, 'color' => '1312B6', 'align' => 'ctr']
]);

// Main Title
$s1->addTextBox(850000, 1450000, 10500000, 1800000, [
    ['text' => 'Digitalisasi & Finalisasi Laporan QC', 'size' => 3800, 'bold' => true, 'color' => '0F172A'],
    ['spacingAfter' => 8],
    ['text' => 'Mengubah data mentah pemeriksaan bengkel menjadi dokumen resmi yang sah, tervalidasi, dan siap cetak.', 'size' => 1700, 'color' => '475569']
]);

// 3 Feature Cards Across Bottom
$cardW = 3300000;
$cardH = 2600000;
$cardY = 3500000;

// Card 1
$s1->addCard(850000, $cardY, $cardW, $cardH, 'F8FAFC', 'E2E8F0', 12700, 2000);
$s1->addCard(1050000, $cardY + 250000, 1300000, 350000, 'EEF2FF', '', 0, 1000);
$s1->addTextBox(1050000, $cardY + 300000, 1300000, 250000, [
    ['text' => 'TAHAP AWAL', 'size' => 950, 'bold' => true, 'color' => '1312B6', 'align' => 'ctr']
]);
$s1->addTextBox(1050000, $cardY + 750000, 2900000, 1600000, [
    ['text' => 'Input Terstruktur', 'size' => 1700, 'bold' => true, 'color' => '0F172A'],
    ['spacingAfter' => 6],
    ['text' => 'Pengisian data ukuran dimensi, runout TIR, dan foto langsung tersusun otomatis ke format lembar A4.', 'size' => 1250, 'color' => '475569']
]);

// Card 2
$s1->addCard(4450000, $cardY, $cardW, $cardH, 'F8FAFC', 'E2E8F0', 12700, 2000);
$s1->addCard(4650000, $cardY + 250000, 1300000, 350000, 'E0F2FE', '', 0, 1000);
$s1->addTextBox(4650000, $cardY + 300000, 1300000, 250000, [
    ['text' => 'VERIFIKASI', 'size' => 950, 'bold' => true, 'color' => '0284C7', 'align' => 'ctr']
]);
$s1->addTextBox(4650000, $cardY + 750000, 2900000, 1600000, [
    ['text' => 'Live Preview A4', 'size' => 1700, 'bold' => true, 'color' => '0F172A'],
    ['spacingAfter' => 6],
    ['text' => 'Tampilan dokumen resmi muncul seketika di panel kanan layar (WYSIWYG), bebas risiko tata letak terpotong.', 'size' => 1250, 'color' => '475569']
]);

// Card 3
$s1->addCard(8050000, $cardY, $cardW, $cardH, 'F8FAFC', 'E2E8F0', 12700, 2000);
$s1->addCard(8250000, $cardY + 250000, 1400000, 350000, 'DCFCE7', '', 0, 1000);
$s1->addTextBox(8250000, $cardY + 300000, 1400000, 250000, [
    ['text' => 'FINALISASI', 'size' => 950, 'bold' => true, 'color' => '15803D', 'align' => 'ctr']
]);
$s1->addTextBox(8250000, $cardY + 750000, 2900000, 1600000, [
    ['text' => 'Otorisasi & Ekspor', 'size' => 1700, 'bold' => true, 'color' => '0F172A'],
    ['spacingAfter' => 6],
    ['text' => 'Approval online via tombol Finish Review, dokumen terkunci sah & siap unduh Excel asli serta PDF siap cetak.', 'size' => 1250, 'color' => '475569']
]);

$gen->addSlide($s1->toXml());

// ==========================================
// SLIDE 2: LATAR BELAKANG (DULU VS SEKARANG)
// ==========================================
$s2 = new VectorSlide('FFFFFF');

// Header
$s2->addTextBox(800000, 600000, 10500000, 900000, [
    ['text' => 'KONSEP & LATAR BELAKANG', 'size' => 1100, 'bold' => true, 'color' => '1312B6'],
    ['spacingAfter' => 4],
    ['text' => 'Kenapa Kita Butuh Sistem Finalisasi Ini?', 'size' => 2800, 'bold' => true, 'color' => '0F172A']
]);

$splitW = 5150000;
$splitH = 4600000;
$splitY = 1650000;

// Sisi Kiri: Cara Lama (Merah Soft)
$s2->addCard(800000, $splitY, $splitW, $splitH, 'FEF2F2', 'FECACA', 12700, 2000);
$s2->addCard(1100000, $splitY + 300000, 2200000, 380000, 'FEE2E2', '', 0, 1000);
$s2->addTextBox(1100000, $splitY + 350000, 2200000, 300000, [
    ['text' => '❌ DULU (MANUAL & TERCECER)', 'size' => 1000, 'bold' => true, 'color' => 'DC2626', 'align' => 'ctr']
]);
$s2->addTextBox(1100000, $splitY + 850000, 4500000, 3500000, [
    ['text' => 'Kendala Proses Konvensional:', 'size' => 1600, 'bold' => true, 'color' => '0F172A'],
    ['spacingAfter' => 10],
    ['bullet' => true, 'text' => 'Data mentah dicatat di kertas formulir atau file Excel lepas masing-masing teknisi.', 'size' => 1300, 'color' => '475569'],
    ['spacingAfter' => 8],
    ['bullet' => true, 'text' => 'Menata 60+ foto visual dan drawing di Excel memakan waktu lama & posisinya sering bergeser.', 'size' => 1300, 'color' => '475569'],
    ['spacingAfter' => 8],
    ['bullet' => true, 'text' => 'Rumus perhitungan deviasi toleransi rawan tertimpa atau tidak sengaja terhapus.', 'size' => 1300, 'color' => '475569'],
    ['spacingAfter' => 8],
    ['bullet' => true, 'text' => 'Finalisasi dokumen sering tertunda berhari-hari karena menunggu tanda tangan fisik.', 'size' => 1300, 'color' => '475569']
]);

// Sisi Kanan: Cara Baru (Hijau Soft)
$s2->addCard(6240000, $splitY, $splitW, $splitH, 'F0FDF4', 'BBF7D0', 12700, 2000);
$s2->addCard(6540000, $splitY + 300000, 2400000, 380000, 'DCFCE7', '', 0, 1000);
$s2->addTextBox(6540000, $splitY + 350000, 2400000, 300000, [
    ['text' => '✓ SEKARANG (SISTEM FINALISASI WEB)', 'size' => 1000, 'bold' => true, 'color' => '15803D', 'align' => 'ctr']
]);
$s2->addTextBox(6540000, $splitY + 850000, 4500000, 3500000, [
    ['text' => 'Kemudahan dengan Sistem Baru:', 'size' => 1600, 'bold' => true, 'color' => '0F172A'],
    ['spacingAfter' => 10],
    ['bullet' => true, 'text' => 'Data mentah langsung terkompilasi ke lembar standar resmi FORM-QC-002 secara terpusat.', 'size' => 1300, 'color' => '475569'],
    ['spacingAfter' => 8],
    ['bullet' => true, 'text' => 'Toleransi Accept / Reject divalidasi otomatis oleh sistem untuk setiap titik ukur.', 'size' => 1300, 'color' => '475569'],
    ['spacingAfter' => 8],
    ['bullet' => true, 'text' => 'Foto dokumentasi langsung dijepret dari HP di bengkel, terkunci rapi pada posisinya.', 'size' => 1300, 'color' => '475569'],
    ['spacingAfter' => 8],
    ['bullet' => true, 'text' => 'Dokumen disahkan online oleh Leader QC dan langsung terkunci aman, siap diunduh.', 'size' => 1300, 'color' => '475569']
]);

$gen->addSlide($s2->toXml());

// ==========================================
// SLIDE 3: FLOWCHART ALUR FINALISASI DOKUMEN (⭐ THE HERO SLIDE)
// ==========================================
$s3 = new VectorSlide('FFFFFF');

// Header
$s3->addTextBox(800000, 500000, 10500000, 950000, [
    ['text' => 'SKEMA ALUR KERJA (FLOWCHART)', 'size' => 1100, 'bold' => true, 'color' => '1312B6'],
    ['spacingAfter' => 4],
    ['text' => 'Skema Alur Kerja: Dari Data Mentah Menuju Dokumen Sah', 'size' => 2800, 'bold' => true, 'color' => '0F172A'],
    ['spacingAfter' => 2],
    ['text' => 'Alur 4 tahap finalisasi dokumen: Input ➔ Kompilasi A4 ➔ Review Atasan ➔ Dokumen Sah Terkunci.', 'size' => 1350, 'color' => '64748B']
]);

// 4 Process Cards across the slide
$fcW = 2300000;
$fcH = 4700000;
$fcY = 1600000;
$arrowW = 420000;
$arrowH = 450000;
$arrowY = 3700000;

// --- BOX 1: INPUT DATA ---
$s3->addCard(800000, $fcY, $fcW, $fcH, 'F8FAFC', 'BAE6FD', 12700, 2000);
$s3->addCard(950000, $fcY + 250000, 1100000, 350000, 'E0F2FE', '', 0, 1000);
$s3->addTextBox(950000, $fcY + 300000, 1100000, 250000, [
    ['text' => 'TAHAP 01', 'size' => 950, 'bold' => true, 'color' => '0284C7', 'align' => 'ctr']
]);
$s3->addTextBox(950000, $fcY + 700000, 2000000, 1200000, [
    ['text' => 'Input Lapangan', 'size' => 1600, 'bold' => true, 'color' => '0F172A'],
    ['spacingAfter' => 2],
    ['text' => '(Teknisi Workshop)', 'size' => 1100, 'bold' => true, 'color' => '0284C7']
]);
$s3->addTextBox(950000, $fcY + 1600000, 2000000, 2100000, [
    ['bullet' => true, 'text' => 'Ukur dimensi fisik & runout TIR.', 'size' => 1200, 'color' => '475569'],
    ['spacingAfter' => 6],
    ['bullet' => true, 'text' => 'Jepret foto temuan cacat dari HP.', 'size' => 1200, 'color' => '475569'],
    ['spacingAfter' => 6],
    ['bullet' => true, 'text' => 'Bisa Simpan Draft jika dicicil.', 'size' => 1200, 'color' => '475569']
]);
$s3->addCard(950000, $fcY + 390000, 2000000, 450000, 'FEF3C7', 'FDE68A', 12700, 1000);
$s3->addTextBox(950000, $fcY + 400000, 2000000, 350000, [
    ['text' => 'STATUS: [ DRAFT ]', 'size' => 1050, 'bold' => true, 'color' => 'D97706', 'align' => 'ctr']
]);

// ARROW 1
$s3->addArrow(3160000, $arrowY, $arrowW, $arrowH, '0284C7');

// --- BOX 2: KOMPILASI A4 ---
$s3->addCard(3640000, $fcY, $fcW, $fcH, 'F8FAFC', 'C7D2FE', 12700, 2000);
$s3->addCard(3790000, $fcY + 250000, 1100000, 350000, 'EEF2FF', '', 0, 1000);
$s3->addTextBox(3790000, $fcY + 300000, 1100000, 250000, [
    ['text' => 'TAHAP 02', 'size' => 950, 'bold' => true, 'color' => '4F46E5', 'align' => 'ctr']
]);
$s3->addTextBox(3790000, $fcY + 700000, 2000000, 1200000, [
    ['text' => 'Kompilasi A4', 'size' => 1600, 'bold' => true, 'color' => '0F172A'],
    ['spacingAfter' => 2],
    ['text' => '(Sistem Web QCMS)', 'size' => 1100, 'bold' => true, 'color' => '4F46E5']
]);
$s3->addTextBox(3790000, $fcY + 1600000, 2000000, 2100000, [
    ['bullet' => true, 'text' => 'Auto-mapping ke lembar A4.', 'size' => 1200, 'color' => '475569'],
    ['spacingAfter' => 6],
    ['bullet' => true, 'text' => 'Pengecekan toleransi otomatis.', 'size' => 1200, 'color' => '475569'],
    ['spacingAfter' => 6],
    ['bullet' => true, 'text' => 'Live preview wujud dokumen.', 'size' => 1200, 'color' => '475569']
]);
$s3->addCard(3790000, $fcY + 390000, 2000000, 450000, 'EEF2FF', 'C7D2FE', 12700, 1000);
$s3->addTextBox(3790000, $fcY + 400000, 2000000, 350000, [
    ['text' => 'STATUS: [ SIAP AJUKAN ]', 'size' => 1050, 'bold' => true, 'color' => '4F46E5', 'align' => 'ctr']
]);

// ARROW 2
$s3->addArrow(6000000, $arrowY, $arrowW, $arrowH, '4F46E5');

// --- BOX 3: REVIEW ATASAN ---
$s3->addCard(6480000, $fcY, $fcW, $fcH, 'F8FAFC', 'FED7AA', 12700, 2000);
$s3->addCard(6630000, $fcY + 250000, 1100000, 350000, 'FFEDD5', '', 0, 1000);
$s3->addTextBox(6630000, $fcY + 300000, 1100000, 250000, [
    ['text' => 'TAHAP 03', 'size' => 950, 'bold' => true, 'color' => 'D97706', 'align' => 'ctr']
]);
$s3->addTextBox(6630000, $fcY + 700000, 2000000, 1200000, [
    ['text' => 'Review Dokumen', 'size' => 1600, 'bold' => true, 'color' => '0F172A'],
    ['spacingAfter' => 2],
    ['text' => '(Leader QC / Atasan)', 'size' => 1100, 'bold' => true, 'color' => 'D97706']
]);
$s3->addTextBox(6630000, $fcY + 1600000, 2000000, 2100000, [
    ['bullet' => true, 'text' => 'Notifikasi Waiting Approval.', 'size' => 1200, 'color' => '475569'],
    ['spacingAfter' => 6],
    ['bullet' => true, 'text' => 'Periksa 6 halaman cetak A4.', 'size' => 1200, 'color' => '475569'],
    ['spacingAfter' => 6],
    ['bullet' => true, 'text' => 'Klik tombol Finish Review.', 'size' => 1200, 'color' => '475569']
]);
$s3->addCard(6630000, $fcY + 390000, 2000000, 450000, 'FEE2E2', 'FECACA', 12700, 1000);
$s3->addTextBox(6630000, $fcY + 400000, 2000000, 350000, [
    ['text' => 'STATUS: [ WAITING REVIEW ]', 'size' => 1000, 'bold' => true, 'color' => 'DC2626', 'align' => 'ctr']
]);

// ARROW 3
$s3->addArrow(8840000, $arrowY, $arrowW, $arrowH, '15803D');

// --- BOX 4: FINALISASI DOKUMEN ---
$s3->addCard(9320000, $fcY, $fcW, $fcH, 'F8FAFC', 'BBF7D0', 12700, 2000);
$s3->addCard(9470000, $fcY + 250000, 1100000, 350000, 'DCFCE7', '', 0, 1000);
$s3->addTextBox(9470000, $fcY + 300000, 1100000, 250000, [
    ['text' => 'TAHAP 04', 'size' => 950, 'bold' => true, 'color' => '15803D', 'align' => 'ctr']
]);
$s3->addTextBox(9470000, $fcY + 700000, 2000000, 1200000, [
    ['text' => 'Dokumen Sah', 'size' => 1600, 'bold' => true, 'color' => '0F172A'],
    ['spacingAfter' => 2],
    ['text' => '(Output Terkunci)', 'size' => 1100, 'bold' => true, 'color' => '15803D']
]);
$s3->addTextBox(9470000, $fcY + 1600000, 2000000, 2100000, [
    ['bullet' => true, 'text' => 'Laporan sah & terkunci aman.', 'size' => 1200, 'color' => '475569'],
    ['spacingAfter' => 6],
    ['bullet' => true, 'text' => 'Ekspor master Excel (.xlsx).', 'size' => 1200, 'color' => '475569'],
    ['spacingAfter' => 6],
    ['bullet' => true, 'text' => 'Cetak PDF serah terima klien.', 'size' => 1200, 'color' => '475569']
]);
$s3->addCard(9470000, $fcY + 390000, 2000000, 450000, 'DCFCE7', 'BBF7D0', 12700, 1000);
$s3->addTextBox(9470000, $fcY + 400000, 2000000, 350000, [
    ['text' => 'STATUS: [ REVIEWED / SAH ]', 'size' => 1000, 'bold' => true, 'color' => '15803D', 'align' => 'ctr']
]);

$gen->addSlide($s3->toXml());

// ==========================================
// SLIDE 4: FITUR PENJAMIN PRESI DOKUMEN
// ==========================================
$s4 = new VectorSlide('FFFFFF');

// Header
$s4->addTextBox(800000, 600000, 10500000, 900000, [
    ['text' => 'FITUR PENUNJANG FINALISASI', 'size' => 1100, 'bold' => true, 'color' => '1312B6'],
    ['spacingAfter' => 4],
    ['text' => 'Memastikan Dokumen Final 100% Presisi', 'size' => 2800, 'bold' => true, 'color' => '0F172A'],
    ['spacingAfter' => 2],
    ['text' => 'Teknologi penunjang agar dokumen resmi tidak pernah berantakan atau salah rumus.', 'size' => 1350, 'color' => '64748B']
]);

// 3 Kolom Fitur Presisi
$colW = 3330000;
$colH = 4600000;
$colY = 1650000;

// Fitur 1
$s4->addCard(800000, $colY, $colW, $colH, 'F8FAFC', 'BAE6FD', 12700, 2000);
$s4->addTextBox(1050000, $colY + 400000, 2830000, 3800000, [
    ['text' => '📄 Live Assembly Kertas A4', 'size' => 1700, 'bold' => true, 'color' => '0284C7'],
    ['spacingAfter' => 12],
    ['bullet' => true, 'text' => 'Ketik di panel kiri, tampilan resmi kertas A4 langsung terbentuk seketika di panel kanan (prinsip WYSIWYG).', 'size' => 1300, 'color' => '475569'],
    ['spacingAfter' => 10],
    ['bullet' => true, 'text' => 'Memastikan foto temuan dan teks tidak pernah terpotong sebelum dokumen disubmit.', 'size' => 1300, 'color' => '475569'],
    ['spacingAfter' => 10],
    ['bullet' => true, 'text' => 'Tersedia tombol pemilih halaman (Lembar 1 s/d 6) atau mode Lihat Semua Lembar.', 'size' => 1300, 'color' => '475569']
]);

// Fitur 2
$s4->addCard(4430000, $colY, $colW, $colH, 'F8FAFC', 'BBF7D0', 12700, 2000);
$s4->addTextBox(4680000, $colY + 400000, 2830000, 3800000, [
    ['text' => '⚖️ Auto-Validation Toleransi', 'size' => 1700, 'bold' => true, 'color' => '15803D'],
    ['spacingAfter' => 12],
    ['bullet' => true, 'text' => 'Angka hasil ukur aktual langsung diuji terhadap batas toleransi standar komponen Output Shaft.', 'size' => 1300, 'color' => '475569'],
    ['spacingAfter' => 10],
    ['bullet' => true, 'text' => 'Status kelulusan Accept atau Reject terisi otomatis tanpa risiko salah rumus.', 'size' => 1300, 'color' => '475569'],
    ['spacingAfter' => 10],
    ['bullet' => true, 'text' => 'TIR runout pada 4 sudut rotasi (0°, 90°, 180°, 270°) otomatis dihitung deviasinya.', 'size' => 1300, 'color' => '475569']
]);

// Fitur 3
$s4->addCard(8060000, $colY, $colW, $colH, 'F8FAFC', 'C7D2FE', 12700, 2000);
$s4->addTextBox(8310000, $colY + 400000, 2830000, 3800000, [
    ['text' => '🔒 Penguncian & Ekspor Asli', 'size' => 1700, 'bold' => true, 'color' => '4F46E5'],
    ['spacingAfter' => 12],
    ['bullet' => true, 'text' => 'Setelah disahkan melalui Finish Review oleh Leader QC, data terkunci permanen untuk integritas audit.', 'size' => 1300, 'color' => '475569'],
    ['spacingAfter' => 10],
    ['bullet' => true, 'text' => 'Injeksi data ke master template Excel asli tanpa merusak relasi 60+ gambar teknik & formula.', 'size' => 1300, 'color' => '475569'],
    ['spacingAfter' => 10],
    ['bullet' => true, 'text' => 'Ekspor PDF multi-halaman berstandar ISO A4 siap diserahterimakan ke pelanggan.', 'size' => 1300, 'color' => '475569']
]);

$gen->addSlide($s4->toXml());

// ==========================================
// SLIDE 5: HASIL NYATA & LIVE DEMO
// ==========================================
$s5 = new VectorSlide('FFFFFF');

// Header
$s5->addTextBox(800000, 600000, 10500000, 900000, [
    ['text' => 'OUTPUT & PENUTUP', 'size' => 1100, 'bold' => true, 'color' => '1312B6'],
    ['spacingAfter' => 4],
    ['text' => 'Dokumen Siap Pakai & Mari Kita Coba!', 'size' => 2800, 'bold' => true, 'color' => '0F172A'],
    ['spacingAfter' => 2],
    ['text' => 'Proses finalisasi cepat, dokumen rapi, dan siap didemokan langsung.', 'size' => 1350, 'color' => '64748B']
]);

// 3 Metric Cards
$mCardW = 3330000;
$mCardH = 2400000;
$mCardY = 1650000;

// Metric 1
$s5->addCard(800000, $mCardY, $mCardW, $mCardH, 'F8FAFC', 'BAE6FD', 12700, 2000);
$s5->addTextBox(1050000, $mCardY + 300000, 2830000, 1800000, [
    ['text' => '⚡ Hemat Waktu 70%', 'size' => 1800, 'bold' => true, 'color' => '0284C7'],
    ['spacingAfter' => 8],
    ['text' => 'Waktu penyusunan dan finalisasi dokumen turun drastis dari 45 menit menjadi kurang dari 12 menit.', 'size' => 1300, 'color' => '475569']
]);

// Metric 2
$s5->addCard(4430000, $mCardY, $mCardW, $mCardH, 'F8FAFC', 'BBF7D0', 12700, 2000);
$s5->addTextBox(4680000, $mCardY + 300000, 2830000, 1800000, [
    ['text' => '📥 Ekspor Master 1-Klik', 'size' => 1800, 'bold' => true, 'color' => '15803D'],
    ['spacingAfter' => 8],
    ['text' => 'File Excel 100% identik dengan master template SSC SBY-5778, dan PDF siap cetak kapan saja.', 'size' => 1300, 'color' => '475569']
]);

// Metric 3
$s5->addCard(8060000, $mCardY, $mCardW, $mCardH, 'F8FAFC', 'C7D2FE', 12700, 2000);
$s5->addTextBox(8310000, $mCardY + 300000, 2830000, 1800000, [
    ['text' => '🔒 Dokumen Sah Terkunci', 'size' => 1800, 'bold' => true, 'color' => '4F46E5'],
    ['spacingAfter' => 8],
    ['text' => 'Tersimpan aman di database terpusat, riwayat approval jelas dan bebas risiko berkas hilang.', 'size' => 1300, 'color' => '475569']
]);

// Big Bottom CTA Banner
$s5->addCard(800000, 4400000, 10592000, 1800000, 'EEF2FF', 'C7D2FE', 12700, 2000);
$s5->addTextBox(1100000, 4700000, 10000000, 1200000, [
    ['text' => '🚀 Mari Kita Masuk ke Sesi Live Demo Aplikasi Web', 'size' => 2200, 'bold' => true, 'color' => '1312B6', 'align' => 'ctr'],
    ['spacingAfter' => 6],
    ['text' => 'Izin saya perlihatkan langsung proses pengisian form hingga finalisasi dokumennya di browser ya Pak/Bu.', 'size' => 1400, 'color' => '334155', 'align' => 'ctr']
], 'ctr');

$gen->addSlide($s5->toXml());

$gen->save();
copy($outputPath, $rootOutputPath);

echo "100% Clean Vector PPTX generated successfully!\n";

