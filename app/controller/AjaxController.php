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
		
		if(sizeof($action) > 1 && $this->GetWebsiteByName($action[1]) != false) {
			$this->db = new ArticleSQL;
			return $this->GetArticle($href);
		}
		
		return "";
	}
	
	
	protected function GetList($website, $offsetTime) {
		$titles = false;
        $category = $this->GetUserSetting("category");
        
		if($website == "collection") {
			$websites = array();
            $websiteFilter = explode(",", $this->GetUserSetting("websiteFilter"));
            
			foreach(Config::GetPath("website/website", true) as $listWebsite) {
				if(
				    $listWebsite['language'] == self::GetUserSetting("lang")
				    && (!isset($listWebsite['hideFromCollection']) || !$listWebsite['hideFromCollection'])
                    && ($websiteFilter[0] == 0 || empty($websiteFilter[0]) || in_array($listWebsite['id'], $websiteFilter))
                ) {
					$websites[] = $listWebsite['id'];
				}
			}
			
			$offset = $this->db->GetOffset($offsetTime, $websites);
			$titles = $this->db->LoadTitles($websites, $offset, $category);
			
			foreach($titles as &$title) {
				$title['website'] = $this->GetWebsiteByID($title['website'], "name");
				$title['url'] = $title['website'].$title['url'];
			}
		}
		else {
			$website = $this->GetWebsiteByName($website);
			$offset = $this->db->GetOffset($offsetTime, $website['id']);
			$titles = $this->db->LoadTitles(array($website['id']), $offset, $category);
			
			foreach($titles as &$title) {
				$title['url'] = $website.$title['url'];
			}
		}
		
		if($titles == false)
			return "";
		
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
		$website = $this->GetWebsiteByName($href[1]);
        
        if(!isset($website['directLinks']) || !$website['directLinks']) {
            $href = str_replace(" ", "+", "/".$href[2]);
        }
        else {
            $href = str_replace("http:/", "http://", $href[2]);
        }
        
		$content = $this->db->LoadArticle($website['id'], $href);
        if(isset($website['directLinks'])) {
            $content['url'] = htmlspecialchars($href);
        }
        else {
            $content['url'] = $this->GetWebsiteByID($website['id'], 'url').htmlspecialchars($href);
        }
		
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
