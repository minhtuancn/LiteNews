<?php
include("LiteNews.php");

$config = Config::GetLocalConfig("cron");
if($config['timeout'] > 0) {
	set_time_limit($config['timeout']);
}

if(!empty($config['errorLog'])) {
	ini_set("log_errors", true);
	ini_set("error_log", $config['errorLog']);
}

$instance = new LiteNews("cron", NULL);
$instance->__toString();
