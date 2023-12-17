<?php

require '_loader.php';


$hasher   = \CSP\SourceHasher::create();


header('Cache-Control: no-store');
header('X-XSS-Protection: 1');


require 'views/about.php';