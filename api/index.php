<?php

require '_loader.php';


$elements = getElements($nonce);
generateScript($elements);

$report_url = "{$baseurl}/report.php?id={$doc_id}";
$policy      = \CSP\Policy::create();
$policy      = updateCSP(isset($_POST['csp']) ?$_POST['csp'] : null, $policy, $nonce);

$should_report = (in_array(getenv('USE_REPORTER'), ['1', 'Y', 'y']));

if ($should_report) {
  $policy->addDirective("report-uri", [$report_url]);
}

$policy->isReportOnly = isset($_GET['ro']);

if ($policy->isReportOnly) {
  header("Content-Security-Policy-Report-Only: " . $policy->toString());
} else {
  header("Content-Security-Policy: " . $policy->toString());
}

if ($should_report) {
  header("Report-To: " . json_encode([
    "group"     => "csp",
    "max_age"   => 10886400,
    "endpoints" => [[ "url" => "$report_url&from-report-to=1", "priority" => 2 ]] 
  ]));
}

header('Cache-Control: no-store');
header('X-XSS-Protection: 0');

ob_start();
require 'views/main.php';
$output = ob_get_contents();
ob_end_clean();

$cleaned = str_replace($nonce, "xyz", $output);
$cleaned = str_replace($doc_id, "id", $cleaned);
$cleaned = preg_replace('/v=[0-9]+/', '', $cleaned);

$page_hash = md5($cleaned);
// Possibly useful for local play ...
// file_put_contents("logs/$page_hash.html", $cleaned);

header("X-Hash: $page_hash");
$output = str_replace('<!--pagehash-->', "<meta pagehash='$page_hash' />", $output);
echo $output;
