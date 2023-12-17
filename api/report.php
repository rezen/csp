<?php

require '_loader.php';


$should_report = (in_array(getenv('USE_REPORTER'), ['1', 'Y', 'y']));

if (!$should_report) {
    return;
}

$doc_id = $_GET['id'] ?? '';

if (!preg_match('/^[a-z0-9]+$/', $doc_id)) {
    return;
}

$data   = json_decode(file_get_contents('php://input'), true);
$policy = $data['csp-report']['original-policy'];
$hash   = policyHash(str_replace($doc_id, '', $policy));

$data['csp-report']['agent'] = $_SERVER['HTTP_USER_AGENT'];
$data['csp-report']['doc_id'] = $doc_id;

unset($data['csp-report']['original-policy']);
$as_string = json_encode($data['csp-report']);

logCspRedis($doc_id, $as_string);
