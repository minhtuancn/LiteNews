<?php
class AjaxController extends Controller {
	protected function InitDB() {
		$this->db = NULL;
	}
	
	
	// Overwriting Controller::GetPage will prevent from loading unnecessary layout HTML
	public function GetPage($page, $href) {
		$action = explode("/", $href);
		
		if(sizeof($action) == 3 && $action[0] == "weather" && is_numeric($action[1]) && is_numeric($action[2])) {
			$this->db = new WeatherSQL;
			return $this->GetWeatherByCoord($action[1], $action[2]);
		}
		
		if(sizeof($action) == 2 && $action[0] == "weather" && is_string($action[1])) {
			$this->db = new WeatherSQL;
			return $this->GetWeatherByName($action[1]);
		}
		
		if(sizeof($action) == 3 && ($action[1] == "collection" || $this->GetWebsiteByName($action[1]) != false) && is_numeric($action[2])) {
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
			$websites = array();
			foreach(Config::GetPath("website/website", true) as $websiteByLang) {
				if($websiteByLang['language'] == self::GetUserSetting("lang") && (!isset($websites['hideFromCollection']) || !$websites['hideFromCollection'])) {
					$websites[] = $websiteByLang['id'];
				}
			}
			
			$titles = $this->db->LoadTitles($websites, $offset);
			
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
		$href = str_replace(" ", "+", "/".$href[2]);
		$content = $this->db->LoadArticle($websiteID, $href);
		$content['url'] = $this->GetWebsiteByID($websiteID, 'url').htmlspecialchars($href);
		$content['ajax'] = true;
		$html = $this->layout->getBlock("article", $content);
		return $html;
	}
	
	
	protected function GetWeatherByCoord($lat, $lon) {
		$geoCollection = $this->db->GetGeoData();
		$closest = array("", PHP_INT_MAX);
		
		foreach($geoCollection as $geo) {
			$distance = sqrt(pow($lat - $geo['Lat'], 2) + pow($lon - $geo['Lon'], 2));
			if($distance < $closest[1]) {
				$closest = array($geo['City'], $distance);
			}
		}
		
		$weather = $this->db->GetWeather($closest[0]);
		$html = $this->layout->getBlock("article", $weather);
		return $html;
	}
	
	
	protected function GetWeatherByName($city) {
		$weather = $this->db->GetWeather($city);
		$html = $this->layout->getBlock("article", $weather);
		return $html;
	}
}
