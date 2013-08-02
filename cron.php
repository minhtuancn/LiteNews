<?php
include("Config.php");
include("Database.php");
include("Parser.php");

$db = new Database(Config::$mysqlHost, Config::$mysqlUsername, Config::$mysqlPassword, Config::$mysqlDB);

$websiteUpdate = $db->EldestUpdate();
if($websiteUpdate == NULL)
	exit;

$currentTime = new DateTime();
$lastUpdate = DateTime::createFromFormat('U', $websiteUpdate['Timestamp']);
$difference = $lastUpdate->diff($currentTime);
if($difference->days*24*60 + $difference->h*60 + $difference->i < Config::$listRefreshFreq)
	exit;

// Refresh timestamp before the actual update because running script can take several minutes
$db->RefreshUpdateTime($websiteUpdate['WebsiteID']);

foreach(Config::$websites as $website) {
	if($websiteUpdate['WebsiteID'] == $website['id'])
		break;
}

$parserName = str_replace(array(" ", "-"), "", $website['name'])."Parser";

$parser = new $parserName(
	file_get_contents(str_replace(" ", "+", $website['url'].$website['listPath'])),
	isset($website['rss'])
);

$db->DeleteArticles($website['id']);

$titles = $parser->GetTitles();
foreach($titles as $title) {
	$parser = new $parserName(@file_get_contents(str_replace(" ", "+", $website['url'].$title['url'])));
	$data = $parser->GetArticle();
	$data['listTitle'] = $title['title'];
	if(!is_null($data['title']))
		$db->AddArticle($website['id'], $title['url'], $data);
}