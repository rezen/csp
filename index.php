<?php

$protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,strpos( $_SERVER["SERVER_PROTOCOL"],'/'))).'://';
$baseurl  = "$protocol{$_SERVER['HTTP_HOST']}";
$endpoint = $_SERVER["REQUEST_URI"];
$endpoint = str_replace(['..'], '', $endpoint);
$endpoint = ltrim($endpoint, '/');
$nonce    = uniqid('nonce.', true);
$nonce    = explode(".", $nonce)[1];

require 'vendor/autoload.php';

$elements = file_get_contents("inc/elements.json");
$elements = json_decode($elements, true);
$elements   = fixElements($elements, $nonce);
$doc_id     = uniqid();
$report_url = "{$baseurl}/report.php?id={$doc_id}";

$hasher = \CSP\SourceHasher::create();

$policy = \CSP\Policy::create();
$policy->addDirective("style-src", [
  'self', "nonce-{$nonce}", 'report-sample',
])->addDirective("script-src", [
  'self',
  'unsafe-eval', 
  "nonce-{$nonce}", 
  'report-sample', 
  'checkout.stripe.com',
  'cdnjs.cloudflare.com',
  '*.jsdelivr.net', 
  'platform.twitter.com',
])->addDirective("img-src", [
  'self', 'q.stripe.com'
])->addDirective("child-src", [
  'www.youtube.com', 
  'player.vimeo.com', 
  'checkout.stripe.com',
  'platform.twitter.com'
])->addDirective("form-action", [
  'self'
])->addDirective("report-uri", [
  $report_url
]);

$policy->isReportOnly = isset($_GET['ro']);

if ($policy->isReportOnly) {
  header("Content-Security-Policy-Report-Only: " . $policy->toString());
} else {
  header("Content-Security-Policy: " . $policy->toString());
}

header("Report-To: " . json_encode([
  "group"     => "csp",
  "max_age"   => 10886400,
  "endpoints" => [[ "url" => "$report_url&from-report-to=1", "priority" => 2 ]] 
]));
header('Cache-Control: no-store');


ob_start();
require 'views/main.php';
$output = ob_get_contents();
ob_end_clean();

$cleaned = str_replace($nonce, "xyz", $output);
$cleaned = str_replace($doc_id, "id", $cleaned);
$cleaned = preg_replace('/v=[0-9]+/', '', $cleaned);

$page_hash = md5($cleaned);
file_put_contents("logs/$page_hash.html", $cleaned);


header("X-Hash: $page_hash");
$output = str_replace('<!--pagehash-->', "<meta pagehash='$page_hash' />", $output);
echo $output;
