<?php
require_once("Config.php");
require_once("Database.php");
require_once("Parser.php");
require_once("Template.php");

class LiteNews {
	private $db;
	private $template;
	private $page;
	private $href;
	
	public function __construct($page=NULL, $href=NULL) {
		$this->db = new Database(Config::$mysqlHost, Config::$mysqlUsername, Config::$mysqlPassword, Config::$mysqlDB);
		$this->db->UpdateLog($_SERVER['REQUEST_URI']);
		$this->template = new Template;
		
		$this->page = $page;
		$this->href = $href;
	}
	
	
	public function __toString() {
		if(is_null($this->page))
			$this->InitIndexPage();
		elseif($this->page == "settings" && Config::$allowUserSettings)
			$this->InitSettingsPage();
		elseif(($website = $this->IsValidWebsite($this->page)) != false) {
			if(is_null($this->href))
				$this->InitTitles($website);
			else
				$this->InitArticle($website, $this->href);
		}
		else
			$this->InitErrorPage(404);
		
		return $this->GetLayout();
	}
	
	
	private function GetUserSetting($name) {
		if(isset(Config::$defaultUserSettings[$name])) {
			if(Config::$allowUserSettings && isset($_COOKIE[Config::$userSettingsCookie][$name]))
				return htmlspecialchars($_COOKIE[Config::$userSettingsCookie][$name]);
			
			return Config::$defaultUserSettings[$name];
		}
		
		return false;
	}
	
	
	private function IsValidWebsite($name) {
		foreach(Config::$websites as $website) {
			if($name == $website['name'])
				return $website;
		}
		
		return false;
	}


	private function GetLayout() {
		$content['bgColor'] = "ccc";
		if(isset(Config::$bgColors[$this->GetUserSetting("bgColor")]))
			$content['bgColor'] = Config::$bgColors[$this->GetUserSetting("bgColor")]['hex'];
		
		$content['content'] = $this->template->getHTML();
		
		$layout = new Template("layout", $this->template->getTitle(), $content);
		return $layout->getHTML();
	}
	
	
	private function InitIndexPage() {
		$this->template->setTemplate("main");
		$this->template->setTitle("Valitse sivusto");
		
		$content['websites'] = Config::$websites;
		$content['userSettings'] = Config::$allowUserSettings;
		$this->template->setContent($content);
	}
	
	
	private function InitTitles($website) {
		$this->template->setTemplate("list");
		$this->template->setTitle($website['name']);
		
		$lastUpdate = $this->db->ListLastUpdate($website['name']);
		if($lastUpdate < Config::$listRefreshFreq && $lastUpdate != -1)
			$titles = $this->db->LoadTitles($website['name']);
		else {
			$parser = new Parser(
				$website['name'],
				file_get_contents(str_replace(" ", "+", $website['url'].$website['listPath'])),
				isset($website['rss'])
			);
			$titles = $parser->GetTitles();
			$this->db->UpdateTitles($website['name'], $titles);
		}
		
		$content = array();
		
		foreach($titles as $title) {
			if(substr(substr($title['url'], strpos($title['url'], "href")), 5, 4) != "http")
				$content[] = $title;
		}
		
		$this->template->setContent($content);
	}


	private function InitArticle($website, $articleURL) {
		$this->template->setTemplate("article");
		
		$lastUpdate = $this->db->ArticleLastUpdate($website['name'], $articleURL);
		if($lastUpdate < Config::$articleRefreshFreq && $lastUpdate != -1)
			$contentList = $this->db->LoadArticle($website['name'], $articleURL);
		else {
			$parser = new Parser($website['name'], file_get_contents(str_replace(" ", "+", $website['url'].$articleURL)));
			$contentList = $parser->GetArticle();
			$this->db->UpdateArticle($website['name'], $articleURL, $contentList);
		}
		
		$this->template->setTitle($contentList['title'].' - '.$website['name']);
		
		$content = array();
		$content['title'] = $contentList['title'];
		$content['subTitle'] = $contentList['subTitle'];
		$content['bodyText'] = $contentList['bodyText'];
		$content['url'] = $website['url'].htmlspecialchars($articleURL);
		$this->template->setContent($content);
	}
	
	
	private function InitSettingsPage() {
		$this->template->setTemplate("settings");
		$this->template->setTitle("Asetukset");
		
		$content = array();
		
		foreach(Config::$defaultUserSettings as $settingName => $settingValue) {
			if(isset($_POST[$settingName])) {
				if(setcookie(Config::$userSettingsCookie."[".$settingName."]", $_POST[$settingName])) {
					$content['saveSuccess'] = true;
					$content['saveNotice'] = "Asetukset tallennettu";
					$_COOKIE[Config::$userSettingsCookie][$settingName] = $_POST[$settingName];
				}
				else
					$content['saveNotice'] = "Asetusten tallennus epäonnistui";
			}
		}
		
		$content['bgColorOptions'] = array();
		foreach(Config::$bgColors as $bgColor)
			$content['bgColorOptions'][] = $bgColor['name'];
		
		$this->template->setContent($content);
	}


	private function InitErrorPage($code) {
		$this->template->setTemplate("error");
		$message = "";
		
		switch($code) {
			case 404:
				$message = "Sivua ei löytynyt";
				break;
			
			default:
				$code = htmlspecialchars($code);
				$message = "Tuntematon virhe";
				break;
		}
		
		$this->template->setTitle($message);
		$this->template->setContent(array('code'=>$code, 'message'=>$message));
	}
}