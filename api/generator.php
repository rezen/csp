<?php


header('Content-Type: application/javascript');
header('Cache-Control: no-cache');

?>

alert(<?php print_r($_GET); ?>);