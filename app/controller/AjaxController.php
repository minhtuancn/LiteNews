<?php
class AjaxController extends Controller {
	// We'll do this later
	protected function InitDB() {
		$this->db = NULL;
	}
	
	
	// Overwriting Controller::GetPage will prevent from loading unnecessary layout HTML
	public function GetPage($page, $href) {
		$action = explode("/", $href);
		if(sizeof($action) < 2 || ($action[0] != "collection" && $this->GetWebsite($action[0]) == false) || !is_numeric($action[1]))
			return null;
		
		$titles = false;
		if($action[0] == "collection") {
			$this->db = new CollectionSQL;
			$titles = $this->db->LoadCollection(unserialize(Config::GetPath("local/collection", true)), $action[1]);
			
			foreach($titles as &$title) {
				foreach(Config::GetPath("website/website", true) as $website) {
					if($title['website'] == $website['id']) {
						$title['website'] = $website['name'];
						$title['url'] = $website['name'].$title['url'];
						break;
					}
				}
			}
		}
		else {
			$this->db = new ListSQL;
			$website = $this->GetWebsite($action[0]);
			$titles = $this->db->LoadTitles($website['id'], $action[1]);
			
			foreach($titles as &$title) {
				$title['url'] = $action[0].$title['url'];
			}
		}
		
		if($titles == false)
			return null;
		
		$html = "";
		foreach($titles as $title) {
			$html .= $this->layout->getBlock("titlelink", $title);
		}
		
		return $html;
	}
}
