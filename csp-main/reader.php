<?php

require 'vendor/autoload.php';

$user_agent = $_SERVER['HTTP_USER_AGENT'];
$doc_id     = $_GET['id'];
$hash       = $_GET['h'];
$log_dir    = dirname(__FILE__) . "/logs/";

if (!preg_match('/^[a-z0-9]+$/', $doc_id)) {
    exit;
}

echoLogRedis($doc_id);
