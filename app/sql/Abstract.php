<?php
require_once("app/Config.php");
require_once("app/Loader.php");

abstract class Database {
	protected $db;
	
	
	public function __construct($logData=NULL) {
		$account = Config::GetLocalConfig("database");
		$this->db = new PDO('mysql:host='.$account['host'].';dbname='.$account['db'].';charset=utf8', $account['username'], $account['password']);
		
		if(!is_null($logData) && $logData != "config" && $logData != "cron")
			$this->AddLog($logData);
		if(is_null($logData) && isset($_SERVER['REQUEST_URI']))
			$this->AddLog($_SERVER['REQUEST_URI']);
	}
	
	
	public function __destruct() {
		$logExpire = Config::GetPath("local/logExpire");
		
		if($logExpire > 0) {
			$query = $this->db->prepare("DELETE FROM Log WHERE Timestamp<?");
			$query->execute(array(time() - 60 * 60 * 24 * $logExpire));
		}
	}
	
	
	public function AddLog($data) {
		return $this->db
			->prepare("INSERT INTO Log (IP, Timestamp, URL) VALUES (?, UNIX_TIMESTAMP(), ?)")
			->execute(array($_SERVER['REMOTE_ADDR'], $data));
	}
	
	
	public function GetFPC($params) {
		$query = $this->db->prepare("SELECT Content FROM FPC WHERE Parameters=?");
		$result = $query->execute(array($params));
		return (!$result ? false : $query->fetchColumn());
	}
}
?>
