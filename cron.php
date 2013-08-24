<?php
ini_set("display_errors", "on");
error_reporting(E_ALL);

include("LiteNews.php");

$instance = new LiteNews("cron", NULL);
$instance->__toString();
