<?php

class VisualPptxGenerator {
    private ZipArchive $zip;
    private string $filePath;
    private array $slides = [];
    private array $slideImages = [];
    private array $mediaFiles = [];

    public function __construct(string $filePath) {
        $this->filePath = $filePath;
        $this->zip = new ZipArchive();
        if ($this->zip->open($this->filePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new Exception("Cannot create file at $filePath");
        }
    }

    public function addSlide(string $xmlContent, array $images = []) {
        $slideIdx = count($this->slides);
        $this->slides[] = $xmlContent;
        $this->slideImages[$slideIdx] = $images;

        foreach ($images as $img) {
            $this->mediaFiles[$img['target']] = $img['path'];
        }
    }

    public function save() {
        $slideCount = count($this->slides);

        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $contentTypes .= '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">' . "\n";
        $contentTypes .= '  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>' . "\n";
        $contentTypes .= '  <Default Extension="xml" ContentType="application/xml"/>' . "\n";
        $contentTypes .= '  <Default Extension="png" ContentType="image/png"/>' . "\n";
        $contentTypes .= '  <Default Extension="jpg" ContentType="image/jpeg"/>' . "\n";
        $contentTypes .= '  <Override PartName="/ppt/presentation.xml" ContentType="application/vnd.openxmlformats-officedocument.presentationml.presentation.main+xml"/>' . "\n";
        $contentTypes .= '  <Override PartName="/ppt/slideMasters/slideMaster1.xml" ContentType="application/vnd.openxmlformats-officedocument.presentationml.slideMaster+xml"/>' . "\n";
        $contentTypes .= '  <Override PartName="/ppt/slideLayouts/slideLayout1.xml" ContentType="application/vnd.openxmlformats-officedocument.presentationml.slideLayout+xml"/>' . "\n";
        $contentTypes .= '  <Override PartName="/ppt/theme/theme1.xml" ContentType="application/vnd.openxmlformats-officedocument.theme+xml"/>' . "\n";

        for ($i = 1; $i <= $slideCount; $i++) {
            $contentTypes .= "  <Override PartName=\"/ppt/slides/slide{$i}.xml\" ContentType=\"application/vnd.openxmlformats-officedocument.presentationml.slide+xml\"/>\n";
        }
        $contentTypes .= '</Types>';
        $this->zip->addFromString('[Content_Types].xml', $contentTypes);

        $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="ppt/presentation.xml"/>
</Relationships>';
        $this->zip->addFromString('_rels/.rels', $rels);

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

        $theme = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<a:theme xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" name="Clean Modern">
  <a:themeElements>
    <a:clrScheme name="Modern">
      <a:dk1><a:srgbClr val="0F172A"/></a:dk1><a:lt1><a:srgbClr val="FFFFFF"/></a:lt1>
      <a:dk2><a:srgbClr val="1E293B"/></a:dk2><a:lt2><a:srgbClr val="F8FAFC"/></a:lt2>
      <a:accent1><a:srgbClr val="0284C7"/></a:accent1><a:accent2><a:srgbClr val="2563EB"/></a:accent2>
      <a:accent3><a:srgbClr val="16A34A"/></a:accent3><a:accent4><a:srgbClr val="D97706"/></a:accent4>
      <a:accent5><a:srgbClr val="64748B"/></a:accent5><a:accent6><a:srgbClr val="4F46E5"/></a:accent6>
      <a:hlink><a:srgbClr val="0284C7"/></a:hlink><a:folHlink><a:srgbClr val="2563EB"/></a:folHlink>
    </a:clrScheme>
    <a:fontScheme name="Modern Fonts">
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

        foreach ($this->mediaFiles as $target => $localPath) {
            if (file_exists($localPath)) {
                $this->zip->addFile($localPath, "ppt/media/{$target}");
            }
        }

        for ($i = 1; $i <= $slideCount; $i++) {
            $this->zip->addFromString("ppt/slides/slide{$i}.xml", $this->slides[$i - 1]);

            $slideRel = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
            $slideRel .= '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' . "\n";
            $slideRel .= '  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/slideLayout" Target="../slideLayouts/slideLayout1.xml"/>' . "\n";

            if (isset($this->slideImages[$i - 1])) {
                foreach ($this->slideImages[$i - 1] as $img) {
                    $slideRel .= "  <Relationship Id=\"{$img['id']}\" Type=\"http://schemas.openxmlformats.org/officeDocument/2006/relationships/image\" Target=\"../media/{$img['target']}\"/>\n";
                }
            }

            $slideRel .= '</Relationships>';
            $this->zip->addFromString("ppt/slides/_rels/slide{$i}.xml.rels", $slideRel);
        }

        $this->zip->close();
    }
}

class VisualSlideBuilder {
    private array $shapes = [];
    private int $shapeId = 2;
    private string $bgHex = 'FFFFFF';

    public function __construct(string $bgHex = 'FFFFFF') {
        $this->bgHex = $bgHex;
    }

    public function addBackground() {
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

    public function addPicture(int $x, int $y, int $w, int $h, string $rId) {
        $xml = '<p:pic>
  <p:nvPicPr>
    <p:cNvPr id="' . ($this->shapeId++) . '" name="Picture"/>
    <p:cNvPicPr><a:picLocks noChangeAspect="1"/></p:cNvPicPr>
    <p:nvPr/>
  </p:nvPicPr>
  <p:blipFill>
    <a:blip r:embed="' . $rId . '"/>
    <a:stretch><a:fillRect/></a:stretch>
  </p:blipFill>
  <p:spPr>
    <a:xfrm><a:off x="' . $x . '" y="' . $y . '"/><a:ext cx="' . $w . '" cy="' . $h . '"/></a:xfrm>
    <a:prstGeom prst="rect"><a:avLst/></a:prstGeom>
  </p:spPr>
</p:pic>';
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

// BUILD THE NEW 5-SLIDE VISUAL PRESENTATION WITH FLOWCHART
$outputPath = __DIR__ . '/../public/presentasi/Presentasi_QCMS_PT_SSC_Works.pptx';
$rootOutputPath = __DIR__ . '/../Presentasi_QCMS_PT_SSC_Works.pptx';
$gen = new VisualPptxGenerator($outputPath);

// ==========================================
// SLIDE 1: COVER (Konsep Finalisasi Dokumen QC)
// ==========================================
$s1 = new VisualSlideBuilder('FFFFFF');
$s1->addBackground();

$s1->addCard(0, 0, 300000, 6858000, '0284C7', '', 0, 0);

$logoPath = __DIR__ . '/../public/images/doc_assets/kop_dual_logo_banner.png';
$s1->addPicture(900000, 700000, 4800000, 1000000, 'rId2');

$s1->addCard(900000, 2100000, 3400000, 380000, 'EFF6FF', 'BAE6FD', 12700, 1000);
$s1->addTextBox(900000, 2150000, 3400000, 300000, [
    ['text' => 'SISTEM FINALISASI DOKUMEN QC', 'size' => 1100, 'bold' => true, 'color' => '0284C7', 'align' => 'ctr']
]);

$s1->addTextBox(900000, 2600000, 10500000, 1600000, [
    ['text' => 'Digitalisasi & Finalisasi Laporan QC', 'size' => 3800, 'bold' => true, 'color' => '0F172A'],
    ['spacingAfter' => 6],
    ['text' => 'Mengubah data mentah pemeriksaan bengkel menjadi dokumen resmi yang sah dan siap cetak.', 'size' => 1600, 'color' => '64748B']
]);

$s1->addCard(900000, 4500000, 10500000, 1500000, 'F8FAFC', 'E2E8F0', 12700, 2000);
$s1->addTextBox(1150000, 4700000, 10000000, 1100000, [
    ['runs' => [
        ['text' => '✓ Terstruktur & Rapi: ', 'size' => 1350, 'bold' => true, 'color' => '0F172A'],
        ['text' => 'Input data ukuran, toleransi, dan foto langsung tersusun otomatis ke format lembar A4.', 'size' => 1350, 'color' => '475569']
    ]],
    ['spacingAfter' => 6],
    ['runs' => [
        ['text' => '✓ Verifikasi Real-Time: ', 'size' => 1350, 'bold' => true, 'color' => '0F172A'],
        ['text' => 'Live preview memastikan dokumen 100% presisi sebelum diajukan ke atasan.', 'size' => 1350, 'color' => '475569']
    ]],
    ['runs' => [
        ['text' => '✓ Pengesahan & Output Sah: ', 'size' => 1350, 'bold' => true, 'color' => '0F172A'],
        ['text' => 'Approval online via tombol Finish Review, dokumen terkunci & siap unduh Excel/PDF.', 'size' => 1350, 'color' => '475569']
    ]]
]);

$gen->addSlide($s1->toXml(), [
    ['id' => 'rId2', 'target' => 'logo_kop.png', 'path' => $logoPath]
]);

// ==========================================
// SLIDE 2: KONSEP & LATAR BELAKANG
// ==========================================
$s2 = new VisualSlideBuilder('FFFFFF');
$s2->addBackground();

$s2->addTextBox(800000, 600000, 10500000, 900000, [
    ['text' => 'KONSEP & LATAR BELAKANG', 'size' => 1100, 'bold' => true, 'color' => '0284C7'],
    ['spacingAfter' => 4],
    ['text' => 'Kenapa Kita Butuh Sistem Finalisasi Ini?', 'size' => 2800, 'bold' => true, 'color' => '0F172A']
]);

$s2->addCard(800000, 1700000, 4800000, 2100000, 'FEF2F2', 'FECACA', 12700, 2000);
$s2->addTextBox(1050000, 1850000, 4300000, 1800000, [
    ['text' => '❌ Dulu (Manual & Tercecer):', 'size' => 1450, 'bold' => true, 'color' => 'DC2626'],
    ['spacingAfter' => 6],
    ['bullet' => true, 'text' => 'Data mentah dicatat di kertas / file Excel lepas.', 'size' => 1250, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Menata foto & rumus butuh waktu lama.', 'size' => 1250, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Dokumen final sering tertunda karena tanda tangan fisik.', 'size' => 1250, 'color' => '475569']
]);

$s2->addCard(800000, 4000000, 4800000, 2100000, 'F0FDF4', 'BBF7D0', 12700, 2000);
$s2->addTextBox(1050000, 4150000, 4300000, 1800000, [
    ['text' => '✓ Sekarang (Sistem Finalisasi Web):', 'size' => 1450, 'bold' => true, 'color' => '16A34A'],
    ['spacingAfter' => 6],
    ['bullet' => true, 'text' => 'Data langsung terkompilasi ke lembar standar resmi.', 'size' => 1250, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Live preview menampilkan wujud akhir dokumen.', 'size' => 1250, 'color' => '475569'],
    ['bullet' => true, 'text' => 'Pengesahan online, dokumen langsung sah & terkunci.', 'size' => 1250, 'color' => '475569']
]);

$mockupDash = __DIR__ . '/mockup_dashboard.png';
$s2->addPicture(5900000, 1700000, 5500000, 4400000, 'rId2');

$gen->addSlide($s2->toXml(), [
    ['id' => 'rId2', 'target' => 'mockup_dash.png', 'path' => $mockupDash]
]);

// ==========================================
// SLIDE 3: SKEMA FLOWCHART ALUR FINALISASI DOKUMEN (SLIDE UTAMA)
// ==========================================
$s3 = new VisualSlideBuilder('FFFFFF');
$s3->addBackground();

$s3->addTextBox(800000, 400000, 10500000, 950000, [
    ['text' => 'SKEMA ALUR KERJA (FLOWCHART)', 'size' => 1100, 'bold' => true, 'color' => '0284C7'],
    ['spacingAfter' => 4],
    ['text' => 'Skema Alur Kerja: Dari Data Mentah Menuju Dokumen Sah', 'size' => 2800, 'bold' => true, 'color' => '0F172A'],
    ['spacingAfter' => 2],
    ['text' => 'Alur 4 tahap finalisasi dokumen: Input ➔ Kompilasi A4 ➔ Review Atasan ➔ Dokumen Sah Terkunci.', 'size' => 1350, 'color' => '64748B']
]);

$flowPath = __DIR__ . '/flowchart_finalisasi.png';
$s3->addPicture(800000, 1500000, 10592000, 5000000, 'rId2');

$gen->addSlide($s3->toXml(), [
    ['id' => 'rId2', 'target' => 'flowchart.png', 'path' => $flowPath]
]);

// ==========================================
// SLIDE 4: FITUR PENJAMIN AKURASI DOKUMEN (LIVE A4 PREVIEW)
// ==========================================
$s4 = new VisualSlideBuilder('FFFFFF');
$s4->addBackground();

$s4->addTextBox(800000, 600000, 10500000, 900000, [
    ['text' => 'FITUR PENUNJANG FINALISASI', 'size' => 1100, 'bold' => true, 'color' => '0284C7'],
    ['spacingAfter' => 4],
    ['text' => 'Memastikan Dokumen Final 100% Presisi', 'size' => 2800, 'bold' => true, 'color' => '0F172A']
]);

$s4->addCard(800000, 1700000, 4800000, 4400000, 'F8FAFC', 'E2E8F0', 12700, 2000);
$s4->addTextBox(1050000, 1950000, 4300000, 3900000, [
    ['text' => '📄 Live Assembly Kertas A4 (WYSIWYG)', 'size' => 1550, 'bold' => true, 'color' => '0284C7'],
    ['spacingAfter' => 6],
    ['text' => 'Setiap angka dan foto langsung tersusun ke lembar resmi A4 saat diketik, memastikan hasil akhir rapi sebelum disubmit.', 'size' => 1250, 'color' => '475569'],
    ['spacingAfter' => 14],
    ['text' => '⚖️ Pengecekan Toleransi Otomatis', 'size' => 1550, 'bold' => true, 'color' => '16A34A'],
    ['spacingAfter' => 6],
    ['text' => 'Status Accept / Reject dihitung otomatis sesuai standar ukuran komponen, mencegah kesalahan evaluasi mutu.', 'size' => 1250, 'color' => '475569'],
    ['spacingAfter' => 14],
    ['text' => '🔒 Penguncian Dokumen Resmi', 'size' => 1550, 'bold' => true, 'color' => '4F46E5'],
    ['spacingAfter' => 6],
    ['text' => 'Setelah disahkan oleh pimpinan (Finish Review), dokumen terkunci permanen untuk mencegah manipulasi data.', 'size' => 1250, 'color' => '475569']
]);

$mockupDual = __DIR__ . '/mockup_dualpane.png';
$s4->addPicture(5900000, 1700000, 5500000, 4400000, 'rId2');

$gen->addSlide($s4->toXml(), [
    ['id' => 'rId2', 'target' => 'mockup_dual.png', 'path' => $mockupDual]
]);

// ==========================================
// SLIDE 5: HASIL FINAL & AJAKAN DEMO LANGSUNG
// ==========================================
$s5 = new VisualSlideBuilder('FFFFFF');
$s5->addBackground();

$s5->addTextBox(800000, 600000, 10500000, 900000, [
    ['text' => 'OUTPUT & PENUTUP', 'size' => 1100, 'bold' => true, 'color' => '0284C7'],
    ['spacingAfter' => 4],
    ['text' => 'Dokumen Siap Pakai & Mari Kita Coba!', 'size' => 2800, 'bold' => true, 'color' => '0F172A']
]);

$s5->addCard(800000, 1700000, 4800000, 4400000, 'F8FAFC', 'E2E8F0', 12700, 2000);
$s5->addTextBox(1050000, 1950000, 4300000, 3900000, [
    ['text' => '⚡ Waktu Finalisasi Cepat', 'size' => 1550, 'bold' => true, 'color' => '0284C7'],
    ['spacingAfter' => 4],
    ['text' => 'Dari data mentah hingga dokumen sah hanya butuh < 12 menit (sebelumnya butuh 45 menit).', 'size' => 1250, 'color' => '475569'],
    ['spacingAfter' => 14],
    ['text' => '📥 Ekspor Master Excel & PDF Cetak', 'size' => 1550, 'bold' => true, 'color' => '16A34A'],
    ['spacingAfter' => 4],
    ['text' => 'File Excel 100% sama dengan template master pabrik, dan PDF siap serah terima ke pelanggan.', 'size' => 1250, 'color' => '475569'],
    ['spacingAfter' => 14],
    ['text' => '🚀 Masuk ke Sesi Live Demo', 'size' => 1600, 'bold' => true, 'color' => '2563EB'],
    ['spacingAfter' => 4],
    ['text' => 'Izin saya perlihatkan langsung proses finalisasi dokumennya di layar web ya Pak/Bu.', 'size' => 1250, 'color' => '0F172A']
]);

$mockupExp = __DIR__ . '/mockup_export.png';
$s5->addPicture(5900000, 1700000, 5500000, 4400000, 'rId2');

$gen->addSlide($s5->toXml(), [
    ['id' => 'rId2', 'target' => 'mockup_exp.png', 'path' => $mockupExp]
]);

$gen->save();
copy($outputPath, $rootOutputPath);

echo "Updated 5-slide PPTX with Flowchart generated successfully!\n";
