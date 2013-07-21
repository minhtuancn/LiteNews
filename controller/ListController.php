<?php
require_once("Parser.php");

class ListController extends Controller {
	public function InitPage() {
		if(($website = $this->GetWebsite($this->page)) == false) {
			$this->InitErrorPage();
			return;
		}
		
		$this->template->setTemplate("list");
		$this->template->setTitle($website['name']);
		
		$lastUpdate = $this->db->ListLastUpdate($website['id']);
		if($lastUpdate != -1 && $lastUpdate < Config::$listRefreshFreq)
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
		
		if(($limit = Config::GetUserSetting("limit")) > 0)
			$titles = array_slice($titles, 0, $limit);
		
		$content = array();
		
		foreach($titles as $title) {
			if(substr($title['url'], 0, 4) != "http") {
				if($title['url'][0] != "/")
					$title['url'] = "/".$title['url'];
				
				$title['url'] = "/".$website['name'].$title['url'];
				$content[] = $title;
			}
		}
		
		$this->template->setContent($content);
	}
}
