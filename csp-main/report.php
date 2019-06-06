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


if (!preg_match('/^[a-z0-9]+$/', $doc_id)) {
    return;
}

logCspRedis($doc_id, $as_string);

