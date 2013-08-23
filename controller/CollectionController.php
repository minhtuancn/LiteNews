<?php
require_once("Parser.php");

class CollectionController extends Controller {
	public function InitPage() {
		$this->template->setTemplate("list");
		$this->template->setTitle("Collection");
		
		$limit = Config::GetUserSetting("limit");
		if($limit < 1 || $limit > 50)
			$limit = Config::$collectionLimit;
		
		$collectionWebsites = unserialize(Config::GetUserSetting("collection"));
		
		$titles = $this->db->LoadCollection($limit, $collectionWebsites);
		foreach($titles as &$title) {
			foreach(Config::$websites as $website) {
				if($title['website'] == $website['id']) {
					$title['website'] = $website['name'];
					
					if(substr($title['url'], 0, 4) != "http") {
						if($title['url'][0] != "/")
							$title['url'] = "/".$title['url'];
						
						$title['url'] = "/".$website['name'].$title['url'];
						break;
					}
				}
			}
		}
		
		$this->template->setContent($titles);
	}
}
