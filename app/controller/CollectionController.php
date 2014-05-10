<?php
require_once("app/parser/Abstract.php");

class CollectionController extends Controller {
	public function InitPage() {
		$this->template->setTemplate("list");
		$this->template->setTitle("Collection");
		
		$collectionWebsites = self::GetUserSetting("collection", true);
		
		$titles = $this->db->LoadCollection($collectionWebsites);
		foreach($titles as &$title) {
			foreach(Config::GetPath("website/website", true) as $website) {
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
