<?php

require 'vendor/autoload.php';


$user_agent = $_SERVER['HTTP_USER_AGENT'];
$doc_id     = $_GET['id'];
$hash       = $_GET['h'];
$log_dir    = dirname(__FILE__) . "/logs/";

if (!preg_match('/^[a-z0-9]+$/', $doc_id)) {
    exit;
}

$log_file = "$log_dir/doc-$doc_id.log";
if (!file_exists($log_file)) {
    exit;
}
echo file_get_contents($log_file);

cleanupLogs();

