<?php
$zip = new ZipArchive();
if ($zip->open(__DIR__ . '/../Presentasi_QCMS_PT_SSC_Works.pptx') === TRUE) {
    echo "Valid PPTX! Total files: " . $zip->numFiles . "\n";
    for ($i = 0; $i < $zip->numFiles; $i++) {
        echo " - " . $zip->getNameIndex($i) . "\n";
    }
} else {
    echo "Invalid PPTX!\n";
}

