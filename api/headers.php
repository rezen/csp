<?php


foreach ($_SERVER as $key => $value) {
    if (strpos($key, 'HTTP_') === 0) {
        $chunks = explode('_', $key);
        $header = '';
        for ($i = 1; $y = sizeof($chunks) - 1, $i < $y; $i++) {
            $header .= ucfirst(strtolower($chunks[$i])).'-';
        }
        $header .= ucfirst(strtolower($chunks[$i])).': '.$value;
        echo $header.'<br>';
    }
}