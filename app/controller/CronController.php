<?php
require_once("app/parser/Abstract.php");

class CronController extends Controller {
	public function GetPage($page, $href) {
		$this->UpdateData();
	}
	
	
	protected function InitDB() {
		$this->db = new CronSQL("cron");
	}
	
	
	protected function UpdateData() {
		$websiteID = $this->db->EldestUpdate();
		if($websiteID == NULL)
			exit;
		
		// Refresh timestamp before the actual update because running script can take several minutes
		$this->db->RefreshUpdateTime($websiteID);
		
		foreach(Config::$websites as $website) {
			if($websiteID == $website['id'])
				break;
		}
		
		$parserName = str_replace(array(" ", "-"), "", $website['name'])."Parser";
		
		$parser = new $parserName(
			file_get_contents(str_replace(" ", "+", $website['url'].$website['listPath'])),
			isset($website['rss'])
		);
		
		$this->db->DeleteArticles($website['id']);
		
		$titles = $parser->GetTitles();
		foreach($titles as $title) {
			$parser = new $parserName(@file_get_contents(str_replace(" ", "+", $website['url'].$title['url'])));
			$data = $parser->GetArticle();
			$data['listTitle'] = $title['title'];
			if(!is_null($data['title']))
				$this->db->AddArticle($website['id'], $title['url'], $data);
		}
	}
}