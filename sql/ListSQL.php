<?php
class ListSQL extends Database {
	public function LoadCollection($limit, $websites) {
		$websitesStr = "";
		foreach($websites as $websiteKey => $website) {
			$websitesStr .= $websiteKey != 0 ? "," : " ";
			$websitesStr .= $this->db->quote($website, PDO::PARAM_INT);
		}
		
		if($limit > 0) {
			$query = $this->db->prepare("SELECT WebsiteID, ListTitle, URL, Timestamp FROM Article WHERE WebsiteID IN (".$websitesStr.") ORDER BY Timestamp DESC LIMIT :limit");
			$query->bindParam(":limit", intval($limit), PDO::PARAM_INT);
		}
		else
			$query = $this->db->prepare("SELECT WebsiteID, ListTitle, URL, Timestamp FROM Article WHERE WebsiteID IN (".$websitesStr.") ORDER BY Timestamp DESC");
		
		$query->execute();
		
		$list = array();
		foreach($query->fetchAll() as $article)
			$list[] = array('website'=>$article['WebsiteID'], 'title'=>$article['ListTitle'], 'url'=>$article['URL'], 'timestamp'=>$article['Timestamp']);
		
		return $list;
	}
	
	
	public function LoadTitles($website, $limit) {
		$titles = array();
		
		if($limit > 0) {
			$query = $this->db->prepare("SELECT ListTitle, URL, Timestamp FROM Article WHERE WebsiteID=:WebsiteID ORDER BY Timestamp DESC LIMIT :limit");
			$query->bindParam(":limit", intval($limit), PDO::PARAM_INT);
		}
		else
			$query = $this->db->prepare("SELECT ListTitle, URL, Timestamp FROM Article WHERE WebsiteID = :WebsiteID ORDER BY Timestamp DESC");
		
		$query->bindParam(":WebsiteID", $website, PDO::PARAM_INT);
		
		if(!$query->execute())
			return $titles;
		
		
		foreach($query->fetchAll() as $row)
			$titles[] = array('title'=>$row['ListTitle'], 'url'=>$row['URL'], 'timestamp'=>$row['Timestamp']);
		
		return $titles;
	}
}
