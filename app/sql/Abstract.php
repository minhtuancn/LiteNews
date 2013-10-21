<?php
require_once("Config.php");
require_once("app/Loader.php");

abstract class Database {
	protected $db;
	
	
	public function __construct($logData=NULL) {
		$this->db = new PDO('mysql:host='.Config::$mysqlHost.';dbname='.Config::$mysqlDB.';charset=utf8', Config::$mysqlUsername, Config::$mysqlPassword);
		
		if(is_null($logData))
			$this->AddLog($_SERVER['REQUEST_URI']);
		elseif($logData != "cron")
			$this->AddLog($logData);
	}
	
	
	public function __destruct() {
		if(Config::$logExpire > 0) {
			$query = $this->db->prepare("DELETE FROM Log WHERE Timestamp<?");
			$query->execute(array(time() - 60 * Config::$logExpire));
		}
	}
	
	
	public function AddLog($data) {
		return $this->db
			->prepare("INSERT INTO Log (IP, Timestamp, URL) VALUES (?, UNIX_TIMESTAMP(), ?)")
			->execute(array($_SERVER['REMOTE_ADDR'], $data));
	}
}
?>
