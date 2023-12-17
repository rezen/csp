<?php


header('Content-Type: application/javascript');
header('Cache-Control: no-cache');

$request_id = preg_replace("/[^A-Za-z0-9 ]/", '', isset($_GET['h']) ?$_GET['h'] : "_" );
$file = "/tmp/{$request_id}_generated.js";
if (file_exists($file)) {
    echo file_get_contents($file);
} else {
    echo "console.log('Could not find $file');";
}
