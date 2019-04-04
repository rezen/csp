<?php

$protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,strpos( $_SERVER["SERVER_PROTOCOL"],'/'))).'://';
$baseurl  = "$protocol{$_SERVER['HTTP_HOST']}";
$endpoint = $_SERVER["REQUEST_URI"];
$endpoint = str_replace(['..'], '', $endpoint);
$endpoint = ltrim($endpoint, '/');
$nonce    = uniqid('nonce.', true);
$nonce    = explode(".", $nonce)[1];

require 'inc/lib.php';
require 'inc/elements.php';

$elements   = fixElements($elements, $nonce);
$doc_id     = uniqid();
$report_url = "{$baseurl}/report.php?id={$doc_id}";

$csp = [
  //"default-src 'self'",
	"style-src 'self' 'nonce-{$nonce}' 'report-sample' ",
  // "script-src 'self' 'nonce-{$nonce}' 'report-sample'",
  "script-src 'self' 'unsafe-eval' 'nonce-{$nonce}' 'strict-dynamic' 'report-sample' checkout.stripe.com  cdnjs.cloudflare.com *.jsdelivr.net platform.twitter.com",
  // "font-src",
  "img-src 'self' q.stripe.com 'report-sample'",
 	"object-src 'self' 'report-sample'", // What embeds do we allow?
  "child-src 'report-sample' www.youtube.com player.vimeo.com checkout.stripe.com platform.twitter.com", 
  // "media-src 'self'",
  // "connect-src 'self'",
  // "form-action 'self'",
  // "frame-ancestors 'none'",
  // "sandbox 'report-sample' allow-same-origin allow-scripts",

  "report-uri {$report_url}",
  // "report-to csp",
];


// Content-Security-Policy-Report-Only:
header("Content-Security-Policy: " . implode(";", $csp));
header('Report-To: { "group": "csp","max_age": 10886400,"endpoints": [{ "url": "$report_url&from-report-to=1", "priority": 2 }] }');
header('Cache-Control: no-store');
?>
<html lang="en">
  <head>
    <title>CSP</title>
    <meta data-doc-id="<?php echo $doc_id ?>" />
    <script src="assets/app.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Alfa+Slab+One" />
    <link rel="stylesheet" href="assets/app.css" />
  </head>
  <body>
   <h3>CSP</h3>
   <pre><?php print_r($csp); ?></pre>
   <hr />
   <h3>Request Headers</h3>
    <pre>
    <?php print_r(getallheaders()); ?>
    </pre>
    <hr />
    <h3>Response Headers</h3>
    <pre><?php print_r(headers_list()); ?></pre>

    <div id="hide-with-css">If visible, local external css not loaded</div>
    <table>
      <tbody id="csp-reports"></tbody>
    </table>
    <table border="1">
    <tr>
      <th>label</th>
      <th>el</th>
      <th>code</th>
      <th></th>
    </tr>
    <?php foreach ($elements as $idx => $el): ?>
      <tr data-id="<?php echo $el['id']; ?>">
        <td>
          <?php echo $el['label']; ?>
        </td>
        <td>
          <?php echo $el['html']; ?>
        </td>
        <td>
          <?php echo $el['script']['output']; ?>
          <pre><?php echo trim(str_replace("&gt;&lt;", "&gt;&lt;", htmlentities($el['html']))); ?></pre>
          <?php if (isset($el['script']['src'])): ?>
            js: <?php echo $el['script']['src']; ?>
          <?php endif; ?>
          <pre><?php echo @$el['script']['source']; ?></pre>
        </td>
        <td>
          <?php echo $el['category']; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    <tr>
  </table>
</body>
</html>
