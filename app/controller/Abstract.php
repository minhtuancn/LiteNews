<?php
require_once("app/Config.php");
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
		foreach(Config::GetPath("local/userSettings/default", true) as $settingName => $settingValue) {
			if(isset($_POST[$settingName])) {
				$this->SetUserSetting($settingName, $_POST[$settingName]);
			}
		}
		
		$this->InitDB();
		$this->layout = new Template(self::GetUserSetting("lang"), "layout");
		$this->template = new Template(self::GetUserSetting("lang"));
	}
	
	
	public static function LogError($str) {
		$file = substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], "."));
		$file = substr($file, 0, 1) == "/" ? substr($file, 1) : $file;
		$file = "log/".$file.".log";
		
		if(is_writable($file))
			file_put_contents($file, "\n[".date("d-m-Y H:i:s")."] ".$str, FILE_APPEND);
	}
	
	
	public static function LogPHPError($errno, $str) {
		if($errno != 2)
		self::LogError($errno." ".$str);
	}
	
	
	public static function GetUserSetting($name, $array=false) {
		$defaultSettings = Config::GetPath("local/userSettings/default", true);
		
		if(isset($defaultSettings[$name])) {
			if(Config::GetPath("local/userSettings/enable") && isset($_COOKIE[Config::GetPath("local/userSettings/cookie")][$name]) && self::CheckUserSetting($name, $_COOKIE[Config::GetPath("local/userSettings/cookie")][$name])) {
				if(!$array)
					return htmlspecialchars($_COOKIE[Config::GetPath("local/userSettings/cookie")][$name]);
				else {
					$unserializedArray = @unserialize($_COOKIE[Config::GetPath("local/userSettings/cookie")][$name]);
					if($unserializedArray == false)
						return unserialize($defaultSettings[$name]);
					
					foreach($unserializedArray as &$unserializedColumn)
						$unserializedColumn = htmlspecialchars($unserializedColumn);
					
					return $unserializedArray;
				}
			}
			
			if(!$array)
				return $defaultSettings[$name];
			else
				return unserialize($defaultSettings[$name]);
		}
		
		return false;
	}
	
	
	public static function CheckUserSetting($name, $value) {
		$valid = true;
		
		switch($name) {
			case "lang":
				$locales = Config::GetPath("local/locales/locale", true);
				$valid = in_array($value, $locales);
				break;
			
			case "theme":
				$themes = Config::GetPath("local/themes/theme", true);
				$valid = array_key_exists($value, $themes);
				break;
			
			case "limit":
				if(!is_numeric($value) || $value < 0 || $value > 50)
					$valid = false;
				break;
		}
		
		return $valid;
	}
	
	
	protected function SetUserSetting($name, $value) {
		if(!self::CheckUserSetting($name, $value))
			return false;
		
		if(setcookie(Config::GetPath("local/userSettings/cookie")."[".$name."]", $_POST[$name], time() + Config::GetPath("local/userSettings/cookieExpire") * 60, "/")) {
			$_COOKIE[Config::GetPath("local/userSettings/cookie")][$name] = $_POST[$name];
			return true;
		}
		
		return false;
	}
	
	
	protected function InitDB() {
		$dbClass = str_replace("Controller", "SQL", get_class($this));
		$this->db = new $dbClass;
	}
	
	
	protected function GetWebsite($name) {
		foreach(Config::GetPath("website/website", true) as $website) {
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
				'feedback'=>Config::GetPath("local/feedback/enable")
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
