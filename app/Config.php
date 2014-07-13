<?php
require_once("app/ConfigParser.php");
require_once("app/sql/ConfigSQL.php");

class Config extends ConfigParser {
	public static $db;
	public static $cache;
	
	
	public static function GetLocalConfig($name) {
		return self::Parse($name.".xml");
	}
	
	
	public static function Get($name, $parentID=0) {
		return self::$db->Get($name, $parentID);
	}
	
	
	public static function GetRecursive($name, $parentID=0) {
		return self::$db->GetRecursive($name, $parentID);
	}
	
	
	public static function GetID($name, $parentID=0) {
		return self::$db->GetID($name, $parentID);
	}
	
	
	public static function GetPath($path, $array=false, $parentID=0) {
		if(isset(self::$cache[$path]) && $parentID == 0)
			return self::$cache[$path];
		
		$pathArr = explode("/", $path);
		
		if(count($pathArr) > 1) {
			$id = self::GetID($pathArr[0], $parentID);
			unset($pathArr[0]);
			$value = self::GetPath(implode("/", $pathArr), $array, $id);
			
			if($value == "true")
				$value = true;
			elseif($value == "false")
				$value = false;
			
			if($parentID == 0)
				self::$cache[$path] = $value;
			
			return $value;
		}
		
		if($array)
			return self::GetRecursive($pathArr[0], $parentID);
		
		return self::Get($pathArr[0], $parentID);
	}
	
	
	public static function UpdateConfig() {
		$path = "config/";
		
		self::$db->TruncateConfig();
		
		foreach(scandir($path) as $file) {
			if(is_dir($path.$file) || $file == "database.xml")
				continue;
			
			$config = ConfigParser::Parse($file);
			self::$db->RecursiveUpdate(substr($file, 0, strpos($file, ".")), $config);
		}
	}
}

Config::$db = new ConfigSQL("config");