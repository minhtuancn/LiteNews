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
		
		$content = array();
		
		foreach($titles as $title) {
			if(substr($title['url'], 0, 4) != "http") {
				$title['url'] = "?page=".$website['name']."&amp;href=".$title['url'];
				$content[] = $title;
			}
		}
		
		$this->template->setContent($content);
	}
}