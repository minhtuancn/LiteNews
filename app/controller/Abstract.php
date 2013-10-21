<?php
require_once("Config.php");
require_once("app/sql/Abstract.php");
require_once("app/Loader.php");
require_once("app/Template.php");

abstract class Controller {
	protected $db;
	protected $layout;
	protected $template;
	protected $page;
	protected $href;
	
	
	public function __construct() {
		$this->InitDB();
		$this->layout = new Template(self::GetUserSetting("lang"), "layout");
		$this->template = new Template(self::GetUserSetting("lang"));
	}
	
	
	public static function GetUserSetting($name) {
		if(isset(Config::$defaultUserSettings[$name])) {
			if(Config::$allowUserSettings && isset($_COOKIE[Config::$userSettingsCookie][$name]))
				return htmlspecialchars($_COOKIE[Config::$userSettingsCookie][$name]);
			
			return Config::$defaultUserSettings[$name];
		}
		
		return false;
	}
	
	
	protected function InitDB() {
		$dbClass = str_replace("Controller", "SQL", get_class($this));
		$this->db = new $dbClass;
	}
	
	
	protected function GetWebsite($name) {
		foreach(Config::$websites as $website) {
			if(str_replace("+", " ", $name) == $website['name'])
				return $website;
		}
		
		return false;
	}
	
	
	public function InitPage() {
		$this->InitErrorPage();
	}
	
	
	protected function InitErrorPage() {
		$this->template->setTemplate("error");
		$this->template->setTitle("Page not found");
		$this->template->setContent(
			array(
				'code'=>404,
				'message'=>"Page not found",
				'feedback'=>Config::$enableFeedback
			)
		);
	}
	
	
	public function GetPage($page, $href) {
		$this->page = $page;
		$this->href = $href;
		
		$this->InitPage($page, $href);
		
		$this->layout->setTitle($this->template->getTitle());
		$this->layout->setContent(array('content'=>$this->template->getHTML()));
		
		return $this->layout->getHTML();
	}
}