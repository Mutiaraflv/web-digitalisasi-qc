<?php
require_once __DIR__ . '/PptxGenerator.php';

$gen = new PptxGenerator(__DIR__ . '/test_arrow.pptx');
$s = new SlideBuilder('FFFFFF');
$s->addBackground();

// Card 1
$s->addCard(800000, 2000000, 2500000, 3000000, 'EFF6FF', 'BAE6FD', 12700, 2000);
$s->addTextBox(950000, 2200000, 2200000, 2600000, [
    ['text' => '01. Input Data', 'size' => 1800, 'bold' => true, 'color' => '0284C7']
]);

// Arrow 1 (using rightArrow shape)
$xmlArrow = '<p:sp>
  <p:nvSpPr><p:cNvPr id="99" name="Arrow1"/><p:cNvSpPr/><p:nvPr/></p:nvSpPr>
  <p:spPr>
    <a:xfrm><a:off x="3450000" y="3200000"/><a:ext cx="450000" cy="500000"/></a:xfrm>
    <a:prstGeom prst="rightArrow"><a:avLst/></a:prstGeom>
    <a:solidFill><a:srgbClr val="0284C7"/></a:solidFill>
    <a:ln><a:noFill/></a:ln>
  </p:spPr>
</p:sp>';

// We can add raw shape directly to SlideBuilder
$refClass = new ReflectionClass($s);
$refProp = $refClass->getProperty('shapes');
$refProp->setAccessible(true);
$shapes = $refProp->getValue($s);
$shapes[] = $xmlArrow;
$refProp->setValue($s, $shapes);

// Card 2
$s->addCard(4050000, 2000000, 2500000, 3000000, 'F0FDF4', 'BBF7D0', 12700, 2000);
$s->addTextBox(4200000, 2200000, 2200000, 2600000, [
    ['text' => '02. Kompilasi A4', 'size' => 1800, 'bold' => true, 'color' => '16A34A']
]);

$gen->addSlide($s->toXml());
$gen->save();

echo "Arrow PPTX generated successfully!\n";

