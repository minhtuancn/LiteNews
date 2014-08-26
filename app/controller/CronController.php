<?php
require_once("app/parser/Abstract.php");

class CronController extends Controller {
	// Overwriting Controller::GetPage will prevent from loading unnecessary layout HTML
	public function GetPage($page, $href) {
		$this->UpdateData();
	}
	
	
	protected function InitDB() {
		$this->db = new CronSQL("cron");
	}
	
	
	protected function UpdateData() {
		$websiteID = $this->db->EldestUpdate();
		if($websiteID == NULL)
			return;
		
		// Refresh timestamp also before the actual update because running script can take several minutes
		$this->db->RefreshUpdateTime($websiteID);
		
		$website = $this->GetWebsiteByID($websiteID);
		$parserName = str_replace(array(" ", "-"), "", $website['name'])."Parser";
		$titles = array();
		
		if(is_array($website['listPath']))
			$listPath = $website['listPath'];
		else
			$listPath = array($website['listPath']);
		
		foreach($listPath as $path) {
			$listHTML = @file_get_contents(str_replace(" ", "+", $website['url'].$path));
			
			if(empty($listHTML)) {
				self::LogError("Failed to fetch list from ".$website['url'].$path);
				// Without deleting we may not notice possible malfunctioning
				$this->db->DeleteArticles($website['id']);
				return;
			}
			
			$parser = new $parserName(
				$listHTML,
				isset($website['rss'])
			);
			
			$titles = array_merge($titles, $parser->GetTitles());
		}
		
		$titles = array_map("unserialize", array_unique(array_map("serialize", $titles)));
		
		foreach($titles as $title) {
			$articleHTML = @file_get_contents(str_replace(" ", "+", $website['url'].$title['url']));
			
			if(empty($articleHTML)) {
				self::LogError("Failed to fetch article: ".$website['url'].$title['url']);
				continue;
			}
			
			$parser = new $parserName($articleHTML);
			$data = $parser->GetArticle();
			$data['listTitle'] = $title['title'];
			
			if(!is_null($data['title']) && !empty($data['bodyText']))
				$this->db->AddArticle($website['id'], $title['url'], $data);
		}
		
		$this->db->RefreshUpdateTime($websiteID);
		
		if(Config::GetPath("local/FPC"))
			$this->UpdateFPC();
	}
	
	
	protected function UpdateFPC() {
		$params = array(array('page'=>"index"), array('page'=>"collection"), array('page'=>"weather"));
		$tempParams = array();
		
		foreach(Config::GetPath("website/website", true) as $website) {
			$params[] = array('page'=>$website['name']);
		}
		
		foreach(array_keys(Config::GetPath("layout/themes/theme", true)) as $theme) {
			foreach($params as $param) {
				$tempParams[] = array_merge($param, array('theme'=>$theme));
			}
		}
		
		$params = $tempParams;
		$tempParams = array();
		
		foreach(Config::GetPath("local/locales/locale", true) as $locale) {
			foreach($params as &$param) {
				$tempParams[] = array_merge($param, array('locale'=>$locale));
			}
		}
		
		$params = $tempParams;
		
		foreach($params as $param) {
			ob_start();
			
			$_GET['page'] = $param['page'];
			$_COOKIE['settings']['theme'] = $param['theme'];
			$_COOKIE['settings']['lang'] = $param['locale'];
			include("index.php");
			
			$output = ob_get_clean();
			$this->db->AddPageCache(serialize($param), $output);
		}
	}
}