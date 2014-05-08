<?php
require_once("app/parser/Abstract.php");

class CronController extends Controller {
	// Overwriting Controller::GetPage will prevent loading unnecessary layout HTML
	public function GetPage($page, $href) {
		$this->UpdateData();
	}
	
	
	protected function InitDB() {
		$this->db = new CronSQL("cron");
	}
	
	
	protected function UpdateData() {
		$websiteID = $this->db->EldestUpdate();
		if($websiteID == NULL)
			exit;
		
		// Refresh timestamp before the actual update because running script can take several minutes
		$this->db->RefreshUpdateTime($websiteID);
		
		foreach(Config::GetPath("website/website", true) as $website) {
			if($websiteID == $website['id'])
				break;
		}
		
		$parserName = str_replace(array(" ", "-"), "", $website['name'])."Parser";
		$titles = array();
		
		if(is_array($website['listPath']))
			$listPath = $website['listPath'];
		else
			$listPath = array($website['listPath']);
		
		foreach($listPath as $path) {
			$listHTML = @file_get_contents(str_replace(" ", "+", $website['url'].$path));
			
			if(empty($listHTML)) {
				self::LogError("Failed to fetch list from ".$website['url'].$path);
				$this->db->DeleteArticles($website['id']); // Without deleting we may not notice possible malfunctioning
				return;
			}
			
			$parser = new $parserName(
				$listHTML,
				isset($website['rss'])
			);
			
			$titles = array_merge($titles, $parser->GetTitles());
		}
		
		$titles = array_map("unserialize", array_unique(array_map("serialize", $titles)));
		$this->db->DeleteArticles($website['id']);
		
		foreach($titles as $title) {
			$articleHTML = @file_get_contents(str_replace(" ", "+", $website['url'].$title['url']));
			
			if(empty($articleHTML)) {
				self::LogError("Failed to fetch article: ".$website['url'].$title['url']);
				continue;
			}
			
			$parser = new $parserName($articleHTML);
			$data = $parser->GetArticle();
			$data['listTitle'] = $title['title'];
			
			if(!is_null($data['title']) && !empty($data['bodyText']))
				$this->db->AddArticle($website['id'], $title['url'], $data);
		}
	}
}