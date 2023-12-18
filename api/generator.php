<?php


header('Content-Type: application/javascript');
header('Cache-Control: no-cache');
[$script, $scriptHash] = generateScript();
echo $script;
echo "/* hash: $scriptHash */";