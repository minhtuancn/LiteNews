<?php
class AjaxController extends Controller {
	protected function InitDB() {
		$this->db = new ListSQL;
	}
	
	
	// Overwriting Controller::GetPage will prevent from loading unnecessary layout HTML
	public function GetPage($page, $href) {
		$action = explode("/", $href);
		if(sizeof($action) < 2 || ($action[0] != "collection" && $this->GetWebsiteByName($action[0]) == false) || !is_numeric($action[1]))
			return null;
		
		$titles = false;
		if($action[0] == "collection") {
			$titles = $this->db->LoadTitles(unserialize(Config::GetPath("local/collection", true)), $action[1]);
			
			foreach($titles as &$title) {
				$title['website'] = $this->GetWebsiteByID($title['website'], "name");
				$title['url'] = $title['website'].$title['url'];
			}
		}
		else {
			$website = $this->GetWebsiteByName($action[0]);
			$titles = $this->db->LoadTitles(array($website['id']), $action[1]);
			
			foreach($titles as &$title) {
				$title['url'] = $action[0].$title['url'];
			}
		}
		
		if($titles == false)
			return null;
		
		$html = "";
		$titles = array_slice($titles, 0, -1);
		
		foreach($titles as $title) {
			$html .= $this->layout->getBlock("titlelink", $title);
		}
		
		return $html;
	}
}
