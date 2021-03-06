<?php

require 'vendor/autoload.php';

$protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,strpos( $_SERVER["SERVER_PROTOCOL"],'/'))).'://';
$baseurl  = "$protocol{$_SERVER['HTTP_HOST']}";
$endpoint = $_SERVER["REQUEST_URI"];
$endpoint = str_replace(['..'], '', $endpoint);
$endpoint = ltrim($endpoint, '/');
$hasher   = \CSP\SourceHasher::create();

header('Cache-Control: no-store');
header('X-XSS-Protection: 1');


require 'views/about.php';