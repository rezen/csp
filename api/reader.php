<?php

require 'vendor/autoload.php';

$should_report = (in_array(getenv('USE_REPORTER'), ['1', 'Y', 'y']));

if (!$should_report) {
    return;
}

$doc_id = $_GET['id'] ?? '';

if (!preg_match('/^[a-z0-9]+$/', $doc_id)) {
    exit;
}

echoLogRedis($doc_id);
