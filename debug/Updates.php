<?php
require_once("Debug.php");
$debug = new Debug;

$timestamps = $debug->db->prepare("SELECT * FROM UpdateTime");
$timestamps->execute();

foreach($timestamps->fetchAll() as $timestamp) {
	echo Config::$websites[$timestamp['WebsiteID'] - 1]['name']."<br />".date("d-m-Y H:i:s", $timestamp['Timestamp'])."<br /><br />";
}