<?php
require_once("LiteNews.php");

$config = Config::GetLocalConfig("cron");
if($config['timeout'] > 0) {
	set_time_limit($config['timeout']);
}

$instance = new LiteNews("cron", NULL);
$instance->__toString();
