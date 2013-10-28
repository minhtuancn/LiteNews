<?php
ini_set("display_errors", "on");
error_reporting(E_ALL & ~E_STRICT);

chdir("..");

require_once("config/Config.php");
require_once("app/sql/Abstract.php");
require_once("app/Loader.php");

class Debug {
	public $db;
	
	
	public function __construct($dbClass=NULL) {
		if(is_null($dbClass)) {
			$account = Config::GetDBConfig();
			$this->db = new PDO('mysql:host='.$account['host'].';dbname='.$account['db'].';charset=utf8', $account['username'], $account['password']);
		}
		else {
			$className = $dbClass."SQL";
			$this->db = new $className;
		}
	}
	
	
	public static function Log($data, $array=false) {
		if($array)
			file_put_contents("debug/log.txt", "\n".print_r($data, 1), FILE_APPEND);
		else
			file_put_contents("debug/log.txt", "\n".$data, FILE_APPEND);
	}
}
