<?php
header("Content-Type: text/html; charset=utf-8");
date_default_timezone_set("Europe/Helsinki");

require_once("LiteNews.php");

$liteNews = new LiteNews(
	isset($_GET['page']) ? $_GET['page'] : NULL,
	isset($_GET['href']) ? $_GET['href'] : NULL
);

echo $liteNews;
