<?php
class AjaxController extends Controller {
	protected function InitDB() {
		$this->db = NULL;
	}
	
	
	// Overwriting Controller::GetPage will prevent from loading unnecessary layout HTML
	public function GetPage($page, $href) {
		$action = explode("/", $href);
		
		if(sizeof($action) == 3 && ($action[1] == "collection") || $this->GetWebsiteByName($action[1]) != false && is_numeric($action[2])) {
			$this->db = new ListSQL;
			return $this->GetList($action[1], $action[2]);
		}
		
		if($this->GetWebsiteByName($action[1]) != false) {
			$this->db = new ArticleSQL;
			return $this->GetArticle($href);
		}
		return NULL;
	}
	
	
	protected function GetList($website, $offset) {
		$titles = false;
		if($website == "collection") {
			$titles = $this->db->LoadTitles(unserialize(Config::GetPath("local/collection", true)), $offset);
			
			foreach($titles as &$title) {
				$title['website'] = $this->GetWebsiteByID($title['website'], "name");
				$title['url'] = $title['website'].$title['url'];
			}
		}
		else {
			$website = $this->GetWebsiteByName($website);
			$titles = $this->db->LoadTitles(array($website['id']), $offset);
			
			foreach($titles as &$title) {
				$title['url'] = $website.$title['url'];
			}
		}
		
		if($titles == false)
			return null;
		
		$html = "";
		$titles = array_slice($titles, 0, -1);
		$tabIndex = $offset;
		
		foreach($titles as $title) {
			$title['tabIndex'] = ++$tabIndex;
			$html .= $this->layout->getBlock("titlelink", $title);
		}
		
		return $html;
	}
	
	
	protected function GetArticle($href) {
		$href = explode("/", $href, 3);
		$websiteID = $this->GetWebsiteByName($href[1], 'id');
		$href = "/".$href[2];
		$content = $this->db->LoadArticle($websiteID, $href);
		$content['url'] = $this->GetWebsiteByID($websiteID, 'url').htmlspecialchars($href);
		$content['ajax'] = true;
		$html = $this->layout->getBlock("article", $content);
		return $html;
	}
}
