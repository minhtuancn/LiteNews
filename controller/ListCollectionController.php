<?php
require_once("Parser.php");

class ListCollectionController extends Controller {
	public function InitPage() {
		$this->template->setTemplate("list");
		$this->template->setTitle("Collection");
		
		$collectionWebsites = unserialize(Config::GetUserSetting("collection"));
		$titleList = array();
		
		foreach(Config::$websites as $website) {
			if(!in_array($website['id'], $collectionWebsites))
				continue;
			
			$websiteTitles = $this->LoadTitles($website);
			
			foreach($websiteTitles as $websiteTitle) {
				if(substr($websiteTitle['url'], 0, 4) != "http") {
					$websiteTitle['timestamp'] = $this->GetTimestamp($website, $websiteTitle['url']);
					$websiteTitle['info'] = $website['name'];
					$websiteTitle['url'] = "/".$website['name'].$websiteTitle['url'];
					$titleList = array_merge($titleList, array($websiteTitle));
				}
			}
		}
		
		usort(
			$titleList,
			function($a, $b) { return $a['timestamp'] < $b['timestamp']; }
		);
		
		$limit = Config::GetUserSetting("limit");
		if($limit < 1 || $limit > 50)
			$limit = Config::$collectionLimit;
		
		if($limit > 0)
			$titleList = array_slice($titleList, 0, $limit);
		
		$this->template->setContent($titleList);
	}
	
	
	protected function LoadTitles($website) {
		$lastUpdate = $this->db->ListLastUpdate($website['id']);
		
		if($lastUpdate < Config::$listRefreshFreq && $lastUpdate != -1)
			$titles = $this->db->LoadTitles($website['id']);
		else {
			$parserName = str_replace(array(" ", "-"), "", $website['name'])."Parser";
			
			$parser = new $parserName(
				file_get_contents(str_replace(" ", "+", $website['url'].$website['listPath'])),
				isset($website['rss'])
			);
			
			$titles = $parser->GetTitles();
			$this->db->UpdateTitles($website['id'], $titles);
		}
		
		return $titles;
	}
	
	
	protected function GetTimestamp($website, $href) {
		$lastUpdate = $this->db->ArticleLastUpdate($website['id'], $href);
		if($lastUpdate < Config::$articleRefreshFreq && $lastUpdate != -1)
			$contentList = $this->db->LoadArticle($website['id'], $href);
		else {
			$parserName = str_replace(array(" ", "-"), "", $website['name'])."Parser";
			$parser = new $parserName(@file_get_contents(str_replace(" ", "+", $website['url'].$href)));
			
			$contentList = $parser->GetArticle();
			$this->db->UpdateArticle($website['id'], $href, $contentList);
		}
		
		return $contentList['timestamp'];
	}
}
