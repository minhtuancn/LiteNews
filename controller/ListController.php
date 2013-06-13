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
		
		$lastUpdate = $this->db->ListLastUpdate($website['name']);
		if($lastUpdate < Config::$listRefreshFreq && $lastUpdate != -1)
			$titles = $this->db->LoadTitles($website['name']);
		else {
			$parser = new Parser(
				$website['name'],
				file_get_contents(str_replace(" ", "+", $website['url'].$website['listPath'])),
				isset($website['rss'])
			);
			
			$titles = $parser->GetTitles();
			$this->db->UpdateTitles($website['name'], $titles);
		}
		
		$content = array();
		
		foreach($titles as $title) {
			if(substr(substr($title['url'], strpos($title['url'], "href")), 5, 4) != "http")
				$content[] = $title;
		}
		
		$this->template->setContent($content);
	}
}
