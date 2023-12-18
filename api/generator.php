<?php

function getFiles(string $directory): array
{
    $files = array_diff(scandir($directory), ['.', '..']);
    $allFiles = [];

    foreach ($files as $file) {
        $fullPath = $directory. DIRECTORY_SEPARATOR .$file;
        is_dir($fullPath) ? array_push($allFiles, ...getFiles($fullPath)) : array_push($allFiles, $file);
    }
    return $allFiles;
}


header('Content-Type: application/javascript');
header('Cache-Control: no-cache');

$request_id = preg_replace("/[^A-Za-z0-9 ]/", '', isset($_GET['h']) ?$_GET['h'] : "_" );
$file = "/tmp/{$request_id}";
if (file_exists($file)) {
    echo file_get_contents($file);
} else {
    $files = getFiles("/tmp");
    echo "console.log('Could not find $file - " . json_encode($files) . "');";
}
