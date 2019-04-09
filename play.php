<?php

$protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,strpos( $_SERVER["SERVER_PROTOCOL"],'/'))).'://';
$baseurl  = "$protocol{$_SERVER['HTTP_HOST']}";
$endpoint = $_SERVER["REQUEST_URI"];
$endpoint = str_replace(['..'], '', $endpoint);
$endpoint = ltrim($endpoint, '/');
$nonce    = uniqid('nonce.', true);
$nonce    = explode(".", $nonce)[1];

require 'vendor/autoload.php';

$ids = isset($_GET['i']) ? explode(',', $_GET['i']) : [];

if (empty($ids)) {
  $ids = [
    "ajax-local",
    "style-self-external-2", 
    "iframe-remote-youtube", 
    "iframe-remote-youtube-2",
    "form-local-1",
    "eval-2",
    "stripe-button",
  ];
}


$elements = file_get_contents("inc/elements.json");
$elements = json_decode($elements, true);
$elements = fixElements($elements, $nonce);
$elements = array_filter($elements, function($el) use ($ids) {
  return in_array($el["id"], $ids);
});
ksort($elements);

$doc_id     = uniqid();
$report_url = "{$baseurl}/report.php?id={$doc_id}";

$hasher = \CSP\SourceHasher::create();
$policy = \CSP\Policy::create();
$csp    = is_array($_POST['csp']) ? $_POST['csp'] : [] ;
$csp    = array_values($csp);

if (empty($csp)) {
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
  ]);
}

foreach ($csp as $directive) {
  $sources = trim($directive['sources']);
  $sources = preg_replace("/{{(\s+)?nonce(\s+)?}}/", "$nonce", $sources);
  $policy->addDirective($directive['name'], $sources);
}

$policy->addDirective("report-uri", [
  $report_url
]);


if ($policy->hasDefaultSelf()) {
  $policy->addDirective("connect-src", [
    "ws://localhost:8110"
  ]);
}

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
require 'views/play.php';
$output = ob_get_contents();
ob_end_clean();

$cleaned = str_replace($nonce, "xyz", $output);
$cleaned = str_replace($doc_id, "id", $cleaned);
$cleaned = preg_replace('/v=[0-9]+/', '', $cleaned);

$page_hash = md5($cleaned);
header("X-Hash: $page_hash");
$output = str_replace('<!--pagehash-->', "<meta pagehash='$page_hash' />", $output);
echo $output;
