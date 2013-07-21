<?php
require_once("LiteNews.php");

$db = new PDO('mysql:host='.Config::$mysqlHost.';dbname='.Config::$mysqlDB.';charset=utf8', Config::$mysqlUsername, Config::$mysqlPassword);

$query = $db->prepare("SELECT WebsiteID FROM TitleListUpdate ORDER BY LastUpdate ASC LIMIT 1");
if(!$query->execute())
	exit;

$websiteID = $query->fetchColumn();

Config::$defaultUserSettings['collection'] = serialize(array($websiteID));
$_SERVER['REQUEST_URI'] = "/cron.php";
$_SERVER['REMOTE_ADDR'] = "127.0.0.1";

$instance = new LiteNews("collection", NULL);
$instance->__toString();