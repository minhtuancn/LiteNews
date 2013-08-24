<?php
ini_set("display_errors", "on");
error_reporting(E_ALL);

chdir("..");

require_once("Config.php");
require_once("Database.php");
require_once("Loader.php");

class Debug {
	public $db;
	
	
	public function __construct($dbClass=NULL) {
		if(is_null($dbClass))
			$this->db = new PDO('mysql:host='.Config::$mysqlHost.';dbname='.Config::$mysqlDB.';charset=utf8', Config::$mysqlUsername, Config::$mysqlPassword);
		else {
			$className = $dbClass."SQL";
			$this->db = new $className;
		}
	}
}
