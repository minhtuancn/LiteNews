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
		
		$titles = $this->db->LoadTitles($website['id'], Config::GetUserSetting("limit"));
		
		$content = array();
		foreach($titles as $title) {
			if(substr($title['url'], 0, 4) != "http") {
				if($title['url'][0] != "/")
					$title['url'] = "/".$title['url'];
				
				$title['url'] = "/".$website['name'].$title['url'];
				$content[] = $title;
			}
		}
		
		if(isset($website['reverseSort']) && $website['reverseSort'] == true)
			$content = array_reverse($content);
		
		$this->template->setContent($content);
	}
}
