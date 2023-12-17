<?php

$request_id = md5(isset($_SERVER['HTTP_X-Vercel-Id']) ? $_SERVER['HTTP_X-Vercel-Id'] : "");
$asset_dir = __DIR__ . '/../static';


if (file_exists('vendor/autoload.php')) {
    require 'vendor/autoload.php';
} else {
    require __DIR__ . '/../vendor/autoload.php';
}