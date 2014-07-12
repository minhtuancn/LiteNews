<?php
class ListController extends Controller {
	public function InitPage() {
		if($this->page != "collection" && $this->GetWebsiteByName($this->page) == false) {
			$this->InitErrorPage();
			return;
		}
		
		$this->template->setTemplate("list");
		$listWebsites = array();
		
		if($this->page == "collection") {
			$this->template->setTitle("Collection");
			
			foreach(unserialize(Config::GetPath("local/collection")) as $listWebsite) {
				$listWebsites[] = $listWebsite;
			}
		}
		else {
			$website = $this->GetWebsiteByName($this->page);
			$this->template->setTitle($website['name']);
			$listWebsites[] = $website['id'];
		}
		
		$titles = $this->db->LoadTitles($listWebsites);
		
		$content = array();
		$tabIndex = 0;
		foreach($titles as $title) {
			if(($title['url'] = $this->FixURL($title['website'], $title['url'])) == false)
				continue;
			
			if($this->page == "collection")
				$title['website'] = $this->GetWebsiteByID($title['website'], 'name');
			
			$title['tabIndex'] = ++$tabIndex;
			$content[] = $title;
		}
		
		$this->content['tabIndex'] = 0;
		$this->template->setContent($content);
	}
	
	
	protected function FixURL($websiteID, $url) {
		if(substr($url, 0, 4) == "http")
			return false;
		
		if($url[0] != "/")
			$url = "/".$url;
		
		$website = $this->GetWebsiteByID($websiteID, 'name');
		$url = "/".$website.$url;
		return $url;
	}
}
