<?php
require_once("config/ConfigParser.php");
require_once("app/sql/ConfigSQL.php");

class Config extends ConfigParser {
	public static $db;
	
	
	public static function GetDBConfig() {
		return self::Parse("database.xml", 1);
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
		$path = explode("/", $path);
		
		if(count($path) > 1) {
			$id = self::GetID($path[0], $parentID);
			unset($path[0]);
			return self::GetPath(implode("/", $path), $array, $id);
		}
		
		if($array)
			return self::GetRecursive($path[0], $parentID);
		else
			return self::Get($path[0], $parentID);
	}
	
	
	public static function UpdateConfig() {
		$path = "config/xml/";
		
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