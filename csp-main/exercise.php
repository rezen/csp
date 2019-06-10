<?php

require 'vendor/autoload.php';

$protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,strpos( $_SERVER["SERVER_PROTOCOL"],'/'))).'://';
$baseurl  = "$protocol{$_SERVER['HTTP_HOST']}";
$endpoint = $_SERVER["REQUEST_URI"];
$endpoint = str_replace(['..'], '', $endpoint);
$endpoint = ltrim($endpoint, '/');
$nonce    = uniqid('nonce.', true);
$nonce    = explode(".", $nonce)[1];

$answers = [
  [],
  [
    'default-src'  => "'self'",
    'style-src'    => "'self' cdnjs.cloudflare.com",
    'script-src'   => "'nonce-{{nonce}}' 'self'",
    'connect-src'  => "$baseurl ws://" . getenv('REPORTER_WS'),
    'plugin-types' => "application/pdf",
  ]
];

$exercise = getExercise($_GET['e'] ?? 0);
$ids = array_keys($exercise);
sort($ids);

$elements = getElements($nonce);
generateScript($elements);

$elements = array_filter($elements, function($el) use ($exercise) {
  return isset($exercise[$el["id"]]);
});

$elements = array_map(function($el) use ($exercise) {
  $el['goal'] = $exercise[$el['id']] ?? 'allow';
  return $el;
}, $elements);
ksort($elements);

$doc_id     = uniqid();
$report_url = "{$baseurl}/report.php?id={$doc_id}";
$should_report = (in_array(getenv('USE_REPORTER'), ['1', 'Y', 'y']));

$hasher = \CSP\SourceHasher::create();
$policy = \CSP\Policy::create();

$policy = updateCSP($_POST['csp'], $policy, $nonce);

if ($should_report) {
  $policy->addDirective("report-uri", [$report_url]);
}

/*
if ($policy->hasDefaultSelf()) {
  $policy->addDirective("connect-src", [
    "ws://" . getenv('REPORTER_WS')
  ]);
}
*/

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
require 'views/exercise.php';
$output = ob_get_contents();
ob_end_clean();

$cleaned = str_replace($nonce, "xyz", $output);
$cleaned = str_replace($doc_id, "id", $cleaned);
$cleaned = preg_replace('/v=[0-9]+/', '', $cleaned);

$page_hash = md5($cleaned);
header("X-Hash: $page_hash");
$output = str_replace('<!--pagehash-->', "<meta pagehash='$page_hash' />", $output);
echo $output;
