<?php
require_once("app/sql/Abstract.php");

class ConfigSQL extends Database {
	public function Get($name, $parentID=0) {
		$query = $this->db->prepare("SELECT Value FROM Config WHERE Name=? AND ParentID=?");
		$query->execute(array($name, $parentID));
		
		if($query->rowCount() > 1) {
			$result = array();
			foreach($query->fetchAll() as $row)
				$result[] = $row;
		}
		else {
			$result = $query->fetchColumn();
			
			if(is_numeric($result))
				$result = intval($result);
		}
		
		return $result;
	}
	
	
	public function GetRecursive($name, $parentID=0) {
		$result = array();
		
		$query = $this->db->prepare("SELECT ID, Name, Value FROM Config WHERE Name=? AND ParentID=?");
		$query->execute(array($name, $parentID));
		
		$childs = $this->db->prepare("SELECT ID, Name FROM Config WHERE ParentID=?");
		
		foreach($query->fetchAll() as $row) {
			$childs->execute(array($row['ID']));
			
			if($childs->rowCount() > 0) {
				$rowResult = array();
				
				foreach($childs->fetchAll() as $child) {
					$rowResult[$child['Name']] = $this->GetRecursive($child['Name'], $row['ID']);
				}
			}
			else
				$rowResult = is_numeric($row['Value']) ? intval($row['Value']) : $row['Value'];
			
			$result = array_merge($result, array($row['Name']=>$rowResult));
		}
		
		return count($result) > 1 ? $result : reset($result);
	}
	
	
	public function GetID($name, $parentID=0) {
		$query = $this->db->prepare("SELECT ID FROM Config WHERE Name=? AND ParentID=?");
		$query->execute(array($name, $parentID));
		return $query->fetchColumn();
	}
	
	
	public function TruncateConfig() {
		$query = $this->db->prepare("TRUNCATE Config; TRUNCATE UpdateTime");
		$query->execute();
	}
	
	
	public function RecursiveUpdate($name, $value, $parentID=0) {
		$query = $this->db->prepare("INSERT INTO Config (ParentID, Name, Value) VALUES (:ParentID, :Name, :Value) ON DUPLICATE KEY UPDATE ParentID=:ParentID, Name=:Name, Value=:Value");
		$query->bindParam(":ParentID", $parentID, PDO::PARAM_INT);
		$query->bindParam(":Name", $name, PDO::PARAM_STR);
		
		if(!is_array($value)) {
			$query->bindParam(":Value", $value, PDO::PARAM_STR);
			$query->execute();
		}
		else {
			$arrValue = "";
			$query->bindParam(":Value", $arrValue, PDO::PARAM_STR);
			$query->execute();
			$id = $this->db->lastInsertId("Config.ID");
			
			foreach($value as $columnKey=>$columnValue)
				self::RecursiveUpdate($columnKey, $columnValue, $id);
		}
	}
}
