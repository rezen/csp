<?php

$doc_id   = uniqid();

$protocol  = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,strpos( $_SERVER["SERVER_PROTOCOL"],'/'))).'://';
$baseurl   = "$protocol{$_SERVER['HTTP_HOST']}";
$endpoint  = $_SERVER["REQUEST_URI"];
$endpoint  = str_replace(['..'], '', $endpoint);
$endpoint  = ltrim($endpoint, '/');
$nonce     = uniqid('nonce.', true);
$nonce     = explode(".", $nonce)[1];
$request_id = md5(isset($_SERVER['HTTP_X_VERCEL_ID']) ? $_SERVER['HTTP_X_VERCEL_ID'] : "");
$asset_dir  = __DIR__ . '/../static';

if (file_exists('vendor/autoload.php')) {
    require 'vendor/autoload.php';
} else {
    require __DIR__ . '/../vendor/autoload.php';
}

$hasher     = \CSP\SourceHasher::create();
