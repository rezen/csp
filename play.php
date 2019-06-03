<?php

$protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,strpos( $_SERVER["SERVER_PROTOCOL"],'/'))).'://';
$baseurl  = "$protocol{$_SERVER['HTTP_HOST']}";
$endpoint = $_SERVER["REQUEST_URI"];
$endpoint = str_replace(['..'], '', $endpoint);
$endpoint = ltrim($endpoint, '/');
$nonce    = uniqid('nonce.', true);
$nonce    = explode(".", $nonce)[1];

require 'vendor/autoload.php';

$exercises = [
  [
    "ajax-local"              => 'allow',
    "style-self-external-2"   => 'block', 
    "iframe-remote-youtube"   => 'allow', 
    "iframe-remote-youtube-2" => 'allow',
    "form-local-1"            => '?',
    "eval-2"                  => 'block',
    "embed-pdf"               => 'block',
    "stripe-button"           => 'allow',
    'fonts-2'                 => 'allow',
  ],
  [
    "ajax-local"              => 'allow',
    "iframe-remote-youtube"   => 'block', 
    "iframe-remote-youtube-2" => 'block',
    "style-inline-nonce"      => 'block',
    'inline-js-1'             => 'block', // has no nonce
    'inline-js-2'             => 'allow',
    'external-style'          => 'allow',
    'embed-pdf'               => 'allow',
    'embed-svg'               => 'block',
  ]
];

$answers = [
  [],
  [
    'default-src'  => "'self'",
    'style-src'    => "'self' cdnjs.cloudflare.com",
    'script-src'   => "'nonce-{{nonce}}' 'self'",
    'connect-src'  => "http://localhost:8100 ws://localhost:8110",
    'plugin-types' => "application/pdf",
  ]
];

$exid     = $_GET['e'] ?? 0;
$exercise = $exercises[$exid] ?? $exercises[0];

ksort($exercise);
$ids = array_keys($exercise);
sort($ids);

$elements = file_get_contents("inc/elements.json");
$elements = json_decode($elements, true);
$elements = fixElements($elements, $nonce);

$embeds = scriptFromElements($elements);
$script = file_get_contents("assets/generated.tmpl.js");

foreach ($embeds as $key => $embed) {
  $script = str_replace("/*--{$key}--*/", $embed, $script);
}
file_put_contents("assets/generated.js", $script);


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
    'self',
  ])->addDirective("child-src", [
    'www.youtube.com', 
    'player.vimeo.com', 
    'checkout.stripe.com',
    'platform.twitter.com'
  ])->addDirective("form-action", [
    'self'
  ])->addDirective("object-src", [
    
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
header('X-XSS-Protection: 0');

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
