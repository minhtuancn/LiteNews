<?php
require_once("Config.php");
require_once("ControllerLoader.php");
require_once("Database.php");
require_once("Template.php");

class Controller {
	protected $db;
	protected $layout;
	protected $template;
	protected $page;
	protected $href;
	
	
	public function __construct() {
		$this->db = new Database(Config::$mysqlHost, Config::$mysqlUsername, Config::$mysqlPassword, Config::$mysqlDB);
		
		if(Config::$log)
			$this->db->UpdateLog($_SERVER['REQUEST_URI']);
		
		$this->layout = new Template("layout");
		$this->template = new Template;
	}
	
	
	protected function GetUserSetting($name) {
		if(isset(Config::$defaultUserSettings[$name])) {
			if(Config::$allowUserSettings && isset($_COOKIE[Config::$userSettingsCookie][$name]))
				return htmlspecialchars($_COOKIE[Config::$userSettingsCookie][$name]);
			
			return Config::$defaultUserSettings[$name];
		}
		
		return false;
	}
	
	
	protected function GetWebsite($name) {
		foreach(Config::$websites as $website) {
			if($name == $website['name'])
				return $website;
		}
		
		return false;
	}
	
	
	public function InitPage() {
		$this->InitErrorPage();
	}
	
	
	protected function InitErrorPage() {
		$this->template->setTemplate("error");
		$this->template->setTitle("404 - Sivua ei löytynyt");
		$this->template->setContent(array('code'=>404, 'message'=>"Sivua ei löytynyt"));
	}
	
	
	public function GetPage($page, $href) {
		$this->page = $page;
		$this->href = $href;
		
		$this->InitPage($page, $href);
		
		$this->layout->setTitle($this->template->getTitle());
		$this->layout->setContent(array(
			'bgColor'=>Config::$bgColors[$this->GetUserSetting("bgColor")]['hex'],
			'content'=>$this->template->getHTML()
		));
		
		return $this->layout->getHTML();
	}
}
