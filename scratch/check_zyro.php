<?php
$size = getimagesize(__DIR__ . '/../zyro-image.png');
echo "Dimensions: " . $size[0] . "x" . $size[1] . " mime: " . $size['mime'] . "\n";

