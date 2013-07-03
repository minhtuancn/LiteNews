<?php
/*if($_SERVER['REMOTE_ADDR'] != '87.93.22.26')
	die("Developing... (Actually adding timestamps to parsers)");*/

ini_set("display_errors", "on");

header('Content-Type: text/html; charset=utf-8');

require_once("LiteNews.php");

$liteNews = new LiteNews(
	isset($_GET['page']) ? $_GET['page'] : NULL,
	isset($_GET['href']) ? $_GET['href'] : NULL
);

echo $liteNews;