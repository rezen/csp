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
        '});',
        '</script>'
      ];
      $element['script']['output'] = implode("\n", $lines);
      $element['script']['source'] = htmlentities($element['script']['output']);
    } else  if (strpos(@$script['src'], '@') !== false) {
      $parts = explode('@', $script['src']);
      $src = $parts[0];
      $jsfn = $parts[1];
      $source = file_get_contents($src);
      preg_match_all("/start:$jsfn(.*?)\/\/ end:$jsfn/s", $source, $matches);
      $output = trim(@$matches[1][0]);
      $element['script']['source'] = $output;
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