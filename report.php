<?php
$user_agent  = $_SERVER['HTTP_USER_AGENT'];
$doc_id      = $_GET['id'];
$data        = json_decode(file_get_contents('php://input'), true);
$policy      = $data['csp-report']['original-policy'];
$policy_hash = md5($policy); // @todo sort
$data['csp-report']['agent'] = $user_agent;

unset($data['csp-report']['original-policy']);
$as_string = json_encode($data);

if (preg_match('/^[a-z0-9]+$/', $doc_id)) {
    $log_file = "logs/doc-$doc_id.log";
} else {
    $log_file = "logs/global.log";
}

if (!file_exists("logs/policy-$policy_hash.log")) {
    file_put_contents("logs/policy-$policy_hash.log", "");
}

if (!file_exists($log_file)) {
    file_put_contents($log_file, "");
}

file_put_contents($log_file, "$as_string\n", FILE_APPEND);
file_put_contents("logs/policy-$policy_hash.log", "$as_string\n", FILE_APPEND);

