<?php
header("Content-Type: text/html; charset=utf-8");

require_once("LiteNews.php");

$liteNews = new LiteNews(
	isset($_GET['page']) ? $_GET['page'] : NULL,
	isset($_GET['href']) ? $_GET['href'] : NULL
);

echo $liteNews;
