<?php

header('Content-Type: text/plain');

echo $_SERVER['HTTP_X-Vercel-Id'];
echo "\n-------------------------\n";

foreach ($_SERVER as $key => $value) {
    if (strpos($key, 'HTTP_') === 0) {
        echo "$key : $value\n";
    }
}