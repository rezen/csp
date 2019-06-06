<?php

require 'vendor/autoload.php';

$user_agent  = $_SERVER['HTTP_USER_AGENT'];
$doc_id      = $_GET['id'];
$data        = json_decode(file_get_contents('php://input'), true);
$policy      = $data['csp-report']['original-policy'];
$hash        = policyHash(str_replace($doc_id, '', $policy));
$log_dir    = dirname(__FILE__) . "/logs/";

$data['csp-report']['agent'] = $user_agent;
$data['csp-report']['doc_id'] = $doc_id;

unset($data['csp-report']['original-policy']);
$as_string = json_encode($data['csp-report']);

// $client = new Predis\Client('tcp://10.0.0.1:6379');

if (preg_match('/^[a-z0-9]+$/', $doc_id)) {
    $log_file = "$log_dir/doc-$doc_id.log";
    if (!file_exists($log_file)) {
        touch($log_file);
    }

    file_put_contents($log_file, "$as_string\n", FILE_APPEND);
}

if (!file_exists("$log_dir/policy-$hash.log")) {
    file_put_contents("$log_dir/policy-$hash.log", json_encode(["original_policy" => $policy]));
}

file_put_contents("$log_dir/policy-$hash.log", "$as_string\n", FILE_APPEND);

cleanupLogs();