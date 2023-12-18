<?php

require '_loader.php';
$elements = getElements("xyz");
[$script, $scriptHash] = generateScript($elements);

header('Content-Type: application/javascript');
header('Cache-Control: no-cache');

echo $script;
echo "/* hash: $scriptHash */";