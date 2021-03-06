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
            $websiteFilter = explode(",", $this->GetUserSetting("websiteFilter"));
            
			foreach(Config::GetPath("website/website", true) as $listWebsite) {
				if(
				    $listWebsite['language'] == $this->GetUserSetting("lang")
				    && (!isset($listWebsite['hideFromCollection']) || !$listWebsite['hideFromCollection'])
                    && ($websiteFilter[0] == 0 || empty($websiteFilter[0]) || in_array($listWebsite['id'], $websiteFilter))
                ) {
					$listWebsites[] = $listWebsite['id'];
				}
			}
		}
		else {
			$website = $this->GetWebsiteByName($this->page);
			$this->template->setTitle($website['name']);
			$listWebsites[] = $website['id'];
		}
		
		$category = $this->GetUserSetting("category");
		$titles = $this->db->LoadTitles($listWebsites, 0, $category);
		
		$content = array();
		$tabIndex = 0;
		foreach($titles as $title) {
		    $website = $this->GetWebsiteByID($title['website']);
            if(isset($website['directLinks']) && $website['directLinks']) {
                $title['url'] = $website['name']."/".$title['url'];
            }
            elseif(($title['url'] = $this->FixURL($title['website'], $title['url'])) == false) {
				continue;
            }
			
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
		$url = $website.$url;
		return $url;
	}
}
