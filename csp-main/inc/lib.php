<?php

function fixScript($element, $nonce) {
    $id = $element['id'];
    $script = isset($element['script']) ? $element['script'] : ['inline' => ''] ;
    $element['script']['output'] = '';
    $element['script']['source'] = '';
  
    if (isset($script['inline']) && strlen($script['inline']) > 4) {
      $header = "<script>";
      if ($script['nonce']) {
        $header = "<script nonce=\"{$nonce}\">";
      }
  
      $script['inline'] = str_replace(["{{ id }}", '{{ nonce }}' ], [$id, $nonce], $script['inline']);
      $lines = [
        $header,
        "document.addEventListener('DOMContentLoaded', function() {",
        "  {$script['inline']}",
        "  window.executed['$id']=true;",
        '});',
        '</script>'
      ];
      $element['script']['output'] = implode("\n", $lines);
      $element['script']['source'] = htmlentities($element['script']['output']);
    } else  if (isset($element['script']['domready'])) {
      /*
      $parts = explode('@', $script['src']);
      $src = $parts[0];
      $jsfn = $parts[1];
      $source = file_get_contents($src);
      preg_match_all("/start:$jsfn(.*?)\/\/ end:$jsfn/s", $source, $matches);
      $output = trim(@$matches[1][0]);
      $element['script']['source'] = $output;
      */
      $element['script']['source'] = implode($element['script']['domready'], "\n");
    } else  if (isset($element['script']['domready'])) {
      $element['script']['source'] = implode($element['script']['global'], "\n");
    }
    return $element;
  }
  
  function fixElements($elements, $nonce) {
    return array_map(function($element) use ($nonce) {
      $id = $element['id'];
  
      if (is_array($element['html'])) {
        $element['html'] = implode("\n", $element['html']);
      }
  
      if (!isset($element['label'])) {
        $element['label'] = 'TODO';
      }
  
      $element['label'] = str_replace(["{{ id }}", '{{ nonce }}' ], [$id, $nonce], $element['label']);
      $element['html'] = str_replace(["{{ id }}", '{{ nonce }}' ], [$id, $nonce], $element['html']);
      $element = fixScript($element, $nonce);
      return $element;  
    }, $elements);
  }

function scriptFromElements($elements) {
  $domready = array_reduce($elements, function($aggr, $el) {
    $id = $el['id'];
    $add = $el['script']['domready'] ?? [];
  
    if (is_array($add)) {
      $add = implode($add, "\n");
    }

    if (empty($add)) {
      return $aggr;
    }

    return $aggr . implode([
      'try {',
      "  " . str_replace("\n", "\n\t", $add),
      "} catch (e) {",
      "  app.csp['$id'] = e;",
      "};\n",
      ], "\n");
  }, '');

  $global = array_reduce($elements, function($aggr, $el) {
    $id = $el['id'];
    $add = $el['script']['global'] ?? [];
  
    if (is_array($add)) {
      $add = implode($add, "\n");
    }

    if (empty($add)) {
      return $aggr;
    }

    return $aggr . implode([
      'try {',
      "  " . str_replace("\n", "\n\t", $add),
      "} catch (e) {",
      "  app.csp['$id'] = e;",
      "};\n",
      ], "\n");
  }, '');

  $tests = array_reduce($elements, function($aggr, $el) {
    $id = $el['id'];
    $validate = $el['validate'] ?? [];

    if (empty($validate)) {
      return $aggr;
    }

    if (empty($validate['block']) && empty($validate['allow'])) {
      return $aggr;
    }

    foreach ($validate as $key => $value) {
      $validate[$key] = str_replace("{{ id }}", $id, $value);
    }

    if (!isset($validate['block'])) {
      $validate['block'] = '!allow()';
    }

    if (!isset($validate['allow'])) {
      $validate['allow'] = '!block()';
    }
    
   $script = implode("\n", [ 
     " /* tests for $id */",
     "window.tests['$id'] = function() {",
      "  var block = function() {",
      "    return " . $validate['block'] . ";",
      "  };",
      "  var allow = function() {",
      "     return " . $validate['allow']. ";",
      "  };",
      "  return {block, allow};",
      "}();",
      ""
   ]);
    return $aggr . "\n" . $script;
  }, "\nwindow.tests={};\n");

  return [
    "domready" => $domready, 
    "global"   => $global . "\n" . $tests,
  ];
}

function policyHash($policy) {
    $policy = preg_replace('/nonce-[a-z0-9]+/', 'nonce-', $policy);
    $parts = str_split($policy);
    sort($parts);
    return md5(implode('', $parts));
}

function printSafe($data) {
  if (!is_array($data)) {
    return;
  }

  echo PHP_EOL;
  foreach ($data as $key => $value) {
    $val = strval($value);
    echo "[" . htmlentities($key) . "] => " . htmlentities($val) . PHP_EOL;
  }
}

function getElements($nonce) {
  $elements = file_get_contents("inc/elements.json");
  $elements = json_decode($elements, true);
  return fixElements($elements, $nonce);
}


function generateScript($elements) {
  $embeds = scriptFromElements($elements);
  $script = file_get_contents("assets/generated.tmpl.js");
  
  foreach ($embeds as $key => $embed) {
    $script = str_replace("/*--{$key}--*/", $embed, $script);
  }
  file_put_contents("assets/generated.js", $script);  
}


function getExercise($id) {
  // @todo migrate to json
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

  $exercise = $exercises[$id] ?? $exercises[0];
  ksort($exercise);
  return $exercise;
}

function updateCSP($csp, $policy, $nonce) {
  $csp  = is_array($csp) ? $csp : [];
  $csp  = array_values($csp);
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
      "'self'"
    ])->addDirective("plugin-types", [
      'image/svg+xml'
    ]);
  }
  foreach ($csp as $directive) {
    $sources = trim($directive['sources']);
    $sources = preg_replace("/{{(\s+)?nonce(\s+)?}}/", "$nonce", $sources);
    $policy->addDirective($directive['name'], $sources);
  }

  $policy->addDirective("style-src", ["'self'"]);
  $policy->addDirective("script-src", ["'self'"]);

  return $policy;
}


function cleanupLogs() {
  // Clean up older logs
  $files = glob("logs/*");
  $now   = time();
  $minute  = 60;

  foreach ($files as $file) {
      if (!is_file($file)) {
        continue;
      }
      if ($now - filemtime($file) >= $minute * 5) {
          unlink($file);
      }
  }
}