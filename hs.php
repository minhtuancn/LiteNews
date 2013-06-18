<?php
$localURL = "hs.php?href=";
$defaultURL = "http://www.hs.fi";
$href = isset($_GET['href']) ? $_GET['href'] : NULL;

if($href == NULL || (strlen($href) >= strlen(7) && substr($href, 0, 7) != "http://"))
	$url = $defaultURL;
else
	$url = $href;

$isMedia = false;
$headers = get_headers($url, 1);

if(substr($url, -3, 3) == "ico") {
	$headers['Content-Type'] = 'image/x-icon';
	$isMedia = true;
}

header('Content-Type: '.$headers['Content-Type']);

$content = file_get_contents(str_replace(" ", "+", $url));

if($isMedia)
	die($content);

$needle = 'href="';
$offset = -1;

while($offset !== false) {
	$offset = strpos($content, $needle, ++$offset);
	
	if(substr($content, $offset + strlen($needle), 7) != "http://")
		$content = substr_replace($content, $defaultURL, $offset + strlen($needle), 0);
	
	$content = substr_replace($content, $localURL, $offset + strlen($needle), 0);
}

$content = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $content);

echo $content;
?>
