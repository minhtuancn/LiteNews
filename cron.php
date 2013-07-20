<?php
require_once("LiteNews.php");

$idList = array();
foreach(Config::$websites as $website) {
	$idList[] = $website['id'];
}

$_COOKIE['settings']['collection'] = serialize($idList);
$_SERVER['REQUEST_URI'] = "";
$_SERVER['REMOTE_ADDR'] = "127.0.0.1";

$instance = new LiteNews("collection", NULL);
$instance->__toString();
