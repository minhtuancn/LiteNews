<?php
class ListSQL extends Database {
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
