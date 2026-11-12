<?php

class PptxGenerator {
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
    <a:clrScheme name="Modern Tech">
      <a:dk1><a:srgbClr val="0F172A"/></a:dk1>
      <a:lt1><a:srgbClr val="FFFFFF"/></a:lt1>
      <a:dk2><a:srgbClr val="1E293B"/></a:dk2>
      <a:lt2><a:srgbClr val="F8FAFC"/></a:lt2>
      <a:accent1><a:srgbClr val="1E40AF"/></a:accent1>
      <a:accent2><a:srgbClr val="0284C7"/></a:accent2>
      <a:accent3><a:srgbClr val="059669"/></a:accent3>
      <a:accent4><a:srgbClr val="D97706"/></a:accent4>
      <a:accent5><a:srgbClr val="64748B"/></a:accent5>
      <a:accent6><a:srgbClr val="4F46E5"/></a:accent6>
      <a:hlink><a:srgbClr val="2563EB"/></a:hlink>
      <a:folHlink><a:srgbClr val="7C3AED"/></a:folHlink>
    </a:clrScheme>
    <a:fontScheme name="Modern Sans">
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

        // 5. ppt/slideMasters/slideMaster1.xml
        $slideMaster = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<p:sldMaster xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" xmlns:p="http://schemas.openxmlformats.org/presentationml/2006/main">
  <p:cSld>
    <p:spTree>
      <p:nvGrpSpPr><p:cNvPr id="1" name=""/><p:cNvGrpSpPr/><p:nvPr/></p:nvGrpSpPr>
      <p:grpSpPr><a:xfrm><a:off x="0" y="0"/><a:ext cx="0" cy="0"/><a:chOff x="0" y="0"/><a:chExt cx="0" cy="0"/></a:xfrm></p:grpSpPr>
    </p:spTree>
  </p:cSld>
  <p:clrMap bg1="lt1" tx1="dk1" bg2="lt2" tx2="dk2" accent1="accent1" accent2="accent2" accent3="accent3" accent4="accent4" accent5="accent5" accent6="accent6" hlink="hlink" folHlink="folHlink"/>
  <p:sldLayoutIdLst>
    <p:sldLayoutId id="2147483649" r:id="rId1" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"/>
  </p:sldLayoutIdLst>
</p:sldMaster>';
        $this->zip->addFromString('ppt/slideMasters/slideMaster1.xml', $slideMaster);

        // 6. ppt/slideMasters/_rels/slideMaster1.xml.rels
        $masterRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/slideLayout" Target="../slideLayouts/slideLayout1.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/theme" Target="../theme/theme1.xml"/>
</Relationships>';
        $this->zip->addFromString('ppt/slideMasters/_rels/slideMaster1.xml.rels', $masterRels);

        // 7. ppt/slideLayouts/slideLayout1.xml
        $slideLayout = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<p:sldLayout xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" xmlns:p="http://schemas.openxmlformats.org/presentationml/2006/main" type="blank">
  <p:cSld>
    <p:spTree>
      <p:nvGrpSpPr><p:cNvPr id="1" name=""/><p:cNvGrpSpPr/><p:nvPr/></p:nvGrpSpPr>
      <p:grpSpPr><a:xfrm><a:off x="0" y="0"/><a:ext cx="0" cy="0"/><a:chOff x="0" y="0"/><a:chExt cx="0" cy="0"/></a:xfrm></p:grpSpPr>
    </p:spTree>
  </p:cSld>
</p:sldLayout>';
        $this->zip->addFromString('ppt/slideLayouts/slideLayout1.xml', $slideLayout);

        // 8. ppt/slideLayouts/_rels/slideLayout1.xml.rels
        $layoutRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/slideMaster" Target="../slideMasters/slideMaster1.xml"/>
</Relationships>';
        $this->zip->addFromString('ppt/slideLayouts/_rels/slideLayout1.xml.rels', $layoutRels);

        // 9. Add slides
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

class SlideBuilder {
    private array $shapes = [];
    private int $shapeId = 2;
    private string $bgHex = 'F8FAFC';

    public function __construct(string $bgHex = 'F8FAFC') {
        $this->bgHex = $bgHex;
    }

    public function addBackground() {
        $xml = '<p:sp>
  <p:nvSpPr><p:cNvPr id="' . ($this->shapeId++) . '" name="Background"/><p:cNvSpPr><a:spLocks noMove="1" noResize="1"/></p:cNvSpPr><p:nvPr/></p:nvSpPr>
  <p:spPr>
    <a:xfrm><a:off x="0" y="0"/><a:ext cx="12192000" cy="6858000"/></a:xfrm>
    <a:prstGeom prst="rect"><a:avLst/></a:prstGeom>
    <a:solidFill><a:srgbClr val="' . $this->bgHex . '"/></a:solidFill>
    <a:ln><a:noFill/></a:ln>
  </p:spPr>
</p:sp>';
        $this->shapes[] = $xml;
    }

    public function addCard(int $x, int $y, int $w, int $h, string $bgFill = 'FFFFFF', string $borderHex = 'E2E8F0', int $borderW = 12700, int $cornerRound = 2000) {
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

    public function addHeader(string $tag, string $title, string $subtitle = '', bool $isDark = false) {
        $tagColor = $isDark ? '38BDF8' : '0284C7';
        $titleColor = $isDark ? 'FFFFFF' : '0F172A';
        $subColor = $isDark ? '94A3B8' : '64748B';

        // Tag / Badge
        $this->addTextBox(800000, 450000, 10500000, 300000, [
            ['text' => strtoupper($tag), 'size' => 1200, 'bold' => true, 'color' => $tagColor]
        ]);

        // Main Title
        $this->addTextBox(800000, 780000, 10500000, 600000, [
            ['text' => $title, 'size' => 2600, 'bold' => true, 'color' => $titleColor]
        ]);

        // Subtitle
        if ($subtitle) {
            $this->addTextBox(800000, 1380000, 10500000, 350000, [
                ['text' => $subtitle, 'size' => 1350, 'bold' => false, 'color' => $subColor]
            ]);
        }
    }

    public function addFooter(string $leftText = "PT SSC Works — Quality Control Management System", string $rightText = "QCMS Form-QC-002", bool $isDark = false) {
        $color = $isDark ? '64748B' : '94A3B8';
        $lineColor = $isDark ? '334155' : 'E2E8F0';

        // Thin separator line
        $xml = '<p:sp>
  <p:nvSpPr><p:cNvPr id="' . ($this->shapeId++) . '" name="FooterLine"/><p:cNvSpPr/><p:nvPr/></p:nvSpPr>
  <p:spPr>
    <a:xfrm><a:off x="800000" y="6350000"/><a:ext cx="10592000" cy="12700"/></a:xfrm>
    <a:prstGeom prst="rect"><a:avLst/></a:prstGeom>
    <a:solidFill><a:srgbClr val="' . $lineColor . '"/></a:solidFill>
    <a:ln><a:noFill/></a:ln>
  </p:spPr>
</p:sp>';
        $this->shapes[] = $xml;

        // Left text
        $this->addTextBox(800000, 6420000, 6000000, 300000, [
            ['text' => $leftText, 'size' => 1000, 'bold' => false, 'color' => $color]
        ], 'l');

        // Right text
        $this->addTextBox(6800000, 6420000, 4592000, 300000, [
            ['text' => $rightText, 'size' => 1000, 'bold' => false, 'color' => $color]
        ], 'r');
    }

    public function addTextBox(int $x, int $y, int $w, int $h, array $paragraphs, string $align = 'l', int $topIns = 20000, int $leftIns = 20000) {
        $pXml = '';
        foreach ($paragraphs as $p) {
            // Check if paragraph is array of runs or single item
            $runs = isset($p['runs']) ? $p['runs'] : [$p];
            $pAlign = isset($p['align']) ? $p['align'] : $align;
            $bullet = isset($p['bullet']) && $p['bullet'] ? ' buChar="•"' : '';
            $spcAft = isset($p['spacingAfter']) ? '<a:spcAft><a:spcPts val="' . $p['spacingAfter'] . '"/></a:spcAft>' : '';

            $pXml .= '<a:p>' . "\n";
            $pXml .= '  <a:pPr algn="' . $pAlign . '"' . $bullet . '>' . $spcAft . '</a:pPr>' . "\n";

            foreach ($runs as $r) {
                $text = htmlspecialchars($r['text'] ?? '', ENT_XML1, 'UTF-8');
                $size = $r['size'] ?? 1400;
                $bold = !empty($r['bold']) ? ' b="1"' : '';
                $italic = !empty($r['italic']) ? ' i="1"' : '';
                $color = $r['color'] ?? '1E293B';

                $pXml .= '  <a:r>' . "\n";
                $pXml .= '    <a:rPr lang="en-US" sz="' . $size . '"' . $bold . $italic . '>' . "\n";
                $pXml .= '      <a:solidFill><a:srgbClr val="' . $color . '"/></a:solidFill>' . "\n";
                $pXml .= '    </a:rPr>' . "\n";
                $pXml .= '    <a:t>' . $text . '</a:t>' . "\n";
                $pXml .= '  </a:r>' . "\n";
            }
            $pXml .= '</a:p>' . "\n";
        }

        $xml = '<p:sp>
  <p:nvSpPr><p:cNvPr id="' . ($this->shapeId++) . '" name="TextBox"/><p:cNvSpPr><a:spLocks noGrp="1"/></p:cNvSpPr><p:nvPr/></p:nvSpPr>
  <p:spPr>
    <a:xfrm><a:off x="' . $x . '" y="' . $y . '"/><a:ext cx="' . $w . '" cy="' . $h . '"/></a:xfrm>
    <a:prstGeom prst="rect"><a:avLst/></a:prstGeom>
  </p:spPr>
  <p:txBody>
    <a:bodyPr vert="horz" lIns="' . $leftIns . '" tIns="' . $topIns . '" rIns="' . $leftIns . '" bIns="' . $topIns . '" rtlCol="0"/>
    <a:lstStyle/>
    ' . $pXml . '
  </p:txBody>
</p:sp>';
        $this->shapes[] = $xml;
    }

    public function toXml(): string {
        $spTree = implode("\n", $this->shapes);
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<p:sld xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" xmlns:p="http://schemas.openxmlformats.org/presentationml/2006/main">
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

echo "SlideBuilder ready\n";

