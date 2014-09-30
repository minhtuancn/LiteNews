<?php
class ListSQL extends Database {
	public function LoadTitles($websites, $offset=0, $category=0) {
		$titles = array();
		$websitesStr = $this->GetWebsiteArray($websites);
		
		if($category > 0) {
			$query = $this->db->prepare("
				SELECT WebsiteID, ListTitle, URL, Timestamp
				FROM Article
				WHERE WebsiteID IN (".$websitesStr.") AND Category=:category
				ORDER BY Timestamp DESC LIMIT :offset, :limit");
			$category = intval($category);
			$query->bindParam(":category", $category, PDO::PARAM_INT);
		}
		else {
			$query = $this->db->prepare("
				SELECT WebsiteID, ListTitle, URL, Timestamp
				FROM Article
				WHERE WebsiteID IN (".$websitesStr.")
				ORDER BY Timestamp DESC LIMIT :offset, :limit");
		}
			
		$offset = intval($offset);
		$query->bindParam(":offset", $offset, PDO::PARAM_INT);
		$limit = intval(Config::GetPath("local/listLimit"));
		$query->bindParam(":limit", $limit, PDO::PARAM_INT);
		
		if(!$query->execute())
			return $titles;
		
		foreach($query->fetchAll() as $row)
			$titles[] = array('website'=>$row['WebsiteID'], 'title'=>$row['ListTitle'], 'url'=>$row['URL'], 'timestamp'=>$row['Timestamp']);
		
		return $titles;
	}
	
	
	public function GetOffset($timestamp, $websites) {
		$websitesStr = $this->GetWebsiteArray($websites);
		$query = $this->db->prepare("SELECT COUNT(*) FROM Article WHERE Timestamp >= ? AND WebsiteID IN (".$websitesStr.")");
		$query->execute(array($timestamp));
		
		return $query->fetchColumn();
	}
	
	
	protected function GetWebsiteArray($websites) {
		return implode(',', $websites);
	}
}
