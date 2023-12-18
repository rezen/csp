<?php

header('Content-Type: text/plain');

echo isset($_SERVER['HTTP_X_VERCEL_ID']) ? $_SERVER['HTTP_X_VERCEL_ID'] : "");
echo "\n-------------------------\n";

foreach ($_SERVER as $key => $value) {
    if (strpos($key, 'HTTP_') === 0) {
        echo "$key : $value\n";
    }
}