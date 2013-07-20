<?php
require_once("Config.php");
require_once("Database.php");
require_once("Loader.php");
require_once("Template.php");

abstract class Controller {
	protected $db;
	protected $layout;
	protected $template;
	protected $page;
	protected $href;
	
	
	public function __construct() {
		$this->db = new Database(Config::$mysqlHost, Config::$mysqlUsername, Config::$mysqlPassword, Config::$mysqlDB);
		
		if(Config::$log)
			$this->db->UpdateLog($_SERVER['REQUEST_URI']);
		
		$this->layout = new Template(Config::GetUserSetting("lang"), "layout");
		$this->template = new Template(Config::GetUserSetting("lang"));
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
